<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use App\Models\User;

class StripeController extends Controller
{
    // Lista cenników produktów
    protected $priceIds = [
        'prod_RxFr1ajRyqgFqa' => 'price_free', // I-Free - darmowy plan dla inwestorów
        'prod_RxFs58AVJVHqx2' => 'price_1RxFtY2BTxeaIpqRB3AcVn3q', // I-Premium - plan dla inwestorów 500zł/m
        'prod_RxG8yaXSS7WZoE' => 'price_1RxG9F2BTxeaIpqRBxFN1KHc', // O-Premium - plan dla właścicieli projektów 1000zł/m
    ];

    /**
     * Wyświetla formularz subskrypcji.
     *
     * @return \Illuminate\View\View
     */
    public function showSubscription()
    {
        return view('stripe.subscription');
    }

    /**
     * Tworzy subskrypcję dla użytkownika.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createSubscription(Request $request)
    {
        $request->validate([
            'plan' => 'required',
            'payment_method' => 'required',
        ]);

        $user = $request->user();
        $productId = $request->plan;
        
        // Sprawdź, czy wybrano darmowy plan
        if ($productId === 'prod_RxFr1ajRyqgFqa') {
            // Dla darmowego planu nie tworzymy subskrypcji Stripe
            $user->stripe_subscription_status = 'active';
            $user->save();
            
            return redirect()->route('dashboard')->with('success', 'Aktywowano darmowy plan subskrypcji.');
        }
        
        // Pobierz cenę na podstawie ID produktu
        $priceId = $this->priceIds[$productId] ?? null;
        
        if (!$priceId) {
            return back()->withErrors(['error' => 'Wybrany plan nie istnieje.']);
        }

        // Ustaw metodę płatności jako domyślną
        $user->updateDefaultPaymentMethod($request->payment_method);

        try {
            // Utwórz subskrypcję
            $subscription = $user->newSubscription('default', $priceId)
                ->create($request->payment_method);

            // Zaktualizuj status subskrypcji użytkownika
            $user->stripe_subscription_status = 'active';
            $user->save();

            return redirect()->route('dashboard')->with('success', 'Subskrypcja została utworzona pomyślnie.');
        } catch (IncompletePayment $exception) {
            return redirect()->route('cashier.payment', [
                $exception->payment->id, 'redirect' => route('dashboard')
            ]);
        } catch (\Exception $e) {
            Log::error('Błąd tworzenia subskrypcji: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Wystąpił błąd podczas tworzenia subskrypcji: ' . $e->getMessage()]);
        }
    }

    /**
     * Anuluje subskrypcję użytkownika.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelSubscription(Request $request)
    {
        $user = $request->user();

        // Sprawdź czy użytkownik ma aktywną subskrypcję płatną
        if ($user->subscription('default')) {
            // Anuluj subskrypcję na koniec okresu rozliczeniowego
            $user->subscription('default')->cancel();
        }

        // Zaktualizuj status subskrypcji użytkownika
        $user->stripe_subscription_status = 'cancelled';
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Subskrypcja została anulowana. Pozostanie aktywna do końca okresu rozliczeniowego.');
    }

    /**
     * Przekierowuje użytkownika do portalu płatności Stripe.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function billingPortal(Request $request)
    {
        return $request->user()->redirectToBillingPortal(route('dashboard'));
    }

    /**
     * Rozpoczyna proces weryfikacji KYC.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startKycVerification(Request $request)
    {
        $user = $request->user();

        try {
            // Tworzenie sesji weryfikacji Identity w Stripe
            $stripe = new \Stripe\StripeClient(config('cashier.secret'));
            
            $session = $stripe->identity->verificationSessions->create([
                'type' => 'document',
                'metadata' => [
                    'user_id' => $user->id,
                ],
            ]);

            // Przekierowanie użytkownika do strony weryfikacji Stripe
            return redirect($session->url);
        } catch (ApiErrorException $e) {
            Log::error('Błąd podczas inicjowania weryfikacji KYC: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Nie udało się rozpocząć weryfikacji KYC. Spróbuj ponownie później.']);
        }
    }

    /**
     * Obsługuje webhook Stripe dla weryfikacji KYC.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handleKycWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('cashier.webhook.secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );

            if ($event->type === 'identity.verification_session.verified') {
                $session = $event->data->object;
                $userId = $session->metadata->user_id;

                $user = User::findOrFail($userId);
                $user->kyc_status = 'verified';
                $user->save();
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Błąd podczas przetwarzania webhooka KYC: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
