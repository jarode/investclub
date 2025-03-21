<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use App\Models\User;

class StripeController extends Controller
{
    // Lista cenników produktów
    protected $priceIds = [
        'prod_RxFr1ajRyqgFqa' => 'price_1R3LLrBTxeaIpqRB1gppSxzK', // I-Free - darmowy plan dla inwestorów
        'prod_RxFs58AVJVHqx2' => 'price_1R3LMKBTxeaIpqRBHDccX2U1', // I-Premium - plan dla inwestorów 500zł/m
        'prod_RxG8yaXSS7WZoE' => 'price_1R3Lc4BTxeaIpqRBtNBnHwcH', // O-Premium - plan dla właścicieli projektów 1000zł/m
    ];

    /**
     * Utwórz instancję klienta Stripe.
     * 
     * @return \Stripe\StripeClient
     */
    protected function stripe()
    {
        return new \Stripe\StripeClient(config('stripe.secret'));
    }

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
     * Tworzy sesję Stripe Checkout dla subskrypcji.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createCheckoutSession(Request $request)
    {
        try {
            $user = $request->user();
            $priceId = $request->input('price_id');
            
            // Sprawdź, czy użytkownik ma już aktywną subskrypcję
            if ($user->stripe_subscription_id) {
                // Pobierz aktualną subskrypcję ze Stripe
                $subscription = \Stripe\Subscription::retrieve($user->stripe_subscription_id);
                
                // Jeśli użytkownik ma już aktywną subskrypcję, utwórz sesję z parametrami do aktualizacji
                $session = \Stripe\Checkout\Session::create([
                    'customer' => $user->stripe_customer_id,
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price' => $priceId,
                        'quantity' => 1,
                    ]],
                    'mode' => 'subscription',
                    'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('subscription'),
                    'subscription_data' => [
                        'trial_settings' => [
                            'end_behavior' => [
                                'missing_payment_method' => 'cancel',
                            ],
                        ],
                    ],
                    'allow_promotion_codes' => true,
                    'billing_address_collection' => 'required',
                    'customer_update' => [
                        'address' => 'auto',
                        'shipping' => 'auto',
                    ],
                    'metadata' => [
                        'user_id' => $user->id,
                        'price_id' => $priceId,
                    ],
                    'subscription' => $user->stripe_subscription_id,
                    'proration_behavior' => 'always_invoice',
                ]);
            } else {
                // Jeśli użytkownik nie ma subskrypcji, utwórz nową
                $session = \Stripe\Checkout\Session::create([
                    'customer' => $user->stripe_customer_id,
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price' => $priceId,
                        'quantity' => 1,
                    ]],
                    'mode' => 'subscription',
                    'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('subscription'),
                    'subscription_data' => [
                        'trial_settings' => [
                            'end_behavior' => [
                                'missing_payment_method' => 'cancel',
                            ],
                        ],
                    ],
                    'allow_promotion_codes' => true,
                    'billing_address_collection' => 'required',
                    'customer_update' => [
                        'address' => 'auto',
                        'shipping' => 'auto',
                    ],
                    'metadata' => [
                        'user_id' => $user->id,
                        'price_id' => $priceId,
                    ],
                ]);
            }
            
            // Zapisz ID sesji w bazie danych
            $user->checkout_session_id = $session->id;
            $user->save();
            
            return response()->json(['url' => $session->url]);
        } catch (\Exception $e) {
            \Log::error('Błąd podczas tworzenia sesji Stripe: ' . $e->getMessage());
            return response()->json(['error' => 'Wystąpił błąd podczas tworzenia sesji płatności.'], 500);
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
        Log::info('Żądanie anulowania subskrypcji dla użytkownika: ' . $user->id);

        // Sprawdź czy użytkownik ma aktywną subskrypcję w Stripe
        if ($user->stripe_subscription_id) {
            try {
                // Anuluj subskrypcję na koniec okresu rozliczeniowego w Stripe
                $stripe = $this->stripe();
                $subscription = $stripe->subscriptions->retrieve($user->stripe_subscription_id);
                
                if ($subscription && $subscription->status !== 'canceled') {
                    // Sprawdź czy to darmowa subskrypcja (cena 0 zł)
                    $isDarmowaPlan = false;
                    if (isset($subscription->items->data[0]->price) && 
                        $subscription->items->data[0]->price->id === (config('stripe.products.free_investor.price_id') ?: 'price_1R3LLrBTxeaIpqRB1gppSxzK')) {
                        $isDarmowaPlan = true;
                    }
                    
                    if ($isDarmowaPlan) {
                        // Dla darmowego planu anulujemy natychmiast
                        $stripe->subscriptions->cancel($user->stripe_subscription_id);
                        
                        // Aktualizujemy dane użytkownika
                        $user->stripe_subscription_status = 'inactive';
                        $user->stripe_subscription_id = null;
                        $user->plan_type = null;
                        $user->cancellation_requested = false;
                        $user->save();
                        
                        Log::info('Darmowa subskrypcja anulowana natychmiast dla użytkownika: ' . $user->id);
                        return redirect()->route('dashboard')->with('success', 
                            'Twój darmowy plan został anulowany. Aby ponownie korzystać z platformy, wybierz plan subskrypcji.');
                    } else {
                        // Dla płatnych planów anulujemy na koniec okresu rozliczeniowego
                        $stripe->subscriptions->update($user->stripe_subscription_id, [
                            'cancel_at_period_end' => true
                        ]);
                        
                        // Zapisujemy informację o planowanym anulowaniu, ale zachowujemy status active i typ planu
                        $user->cancellation_requested = true;
                        $user->stripe_subscription_status = 'active'; // zachowujemy dostęp
                        $user->save();
                        
                        // Określamy datę końca okresu rozliczeniowego
                        $endDate = date('d.m.Y', $subscription->current_period_end);
                        
                        Log::info('Subskrypcja anulowana na koniec okresu dla użytkownika: ' . $user->id . ', data końca: ' . $endDate);
                        return redirect()->route('dashboard')->with('success', 
                            'Subskrypcja została anulowana. Pozostanie aktywna do końca okresu rozliczeniowego (' . $endDate . '). 
                            Po tym terminie zostanie automatycznie przypisany plan darmowy.');
                    }
                }
            } catch (\Exception $e) {
                Log::error('Błąd anulowania subskrypcji: ' . $e->getMessage());
                
                // W przypadku błędu po stronie Stripe, i tak anulujemy subskrypcję lokalnie
                $user->stripe_subscription_status = 'inactive';
                $user->stripe_subscription_id = null;
                $user->cancellation_requested = false;
                $user->plan_type = null; // Usuwamy przypisanie planu
                $user->save();
                
                return redirect()->route('dashboard')->with('warning', 
                    'Wystąpił błąd komunikacji ze Stripe, ale subskrypcja została anulowana lokalnie.');
            }
        }

        // Dla przypadku gdy nie znaleziono subskrypcji w Stripe
        $user->stripe_subscription_status = 'inactive';
        $user->stripe_subscription_id = null;
        $user->cancellation_requested = false;
        $user->plan_type = null; // Usuwamy przypisanie planu zamiast ustawiać 'free'
        $user->save();
        
        Log::info('Subskrypcja anulowana lokalnie dla użytkownika: ' . $user->id);
        return redirect()->route('dashboard')->with('success', 'Twoja subskrypcja została anulowana.');
    }

    /**
     * Przekierowuje użytkownika do portalu płatności Stripe.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function billingPortal(Request $request)
    {
        $user = $request->user();
        
        try {
            // Sprawdź czy użytkownik ma ID klienta Stripe
            if (!$user->stripe_customer_id) {
                // Jeśli nie ma, utwórz klienta Stripe dla użytkownika
                $stripe = $this->stripe();
                $customer = $stripe->customers->create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'metadata' => [
                        'user_id' => $user->id
                    ],
                ]);
                
                // Zapisz ID klienta
                $user->stripe_customer_id = $customer->id;
                $user->save();
                
                Log::info('Utworzono klienta Stripe dla użytkownika: ' . $user->id . ', stripe_customer_id: ' . $customer->id);
                
                // Jeśli użytkownik nie ma aktywnej subskrypcji płatnej, przekieruj na stronę subskrypcji
                if (!$user->stripe_subscription_id) {
                    return redirect()->route('subscription')
                        ->with('warning', 'Nie masz aktywnej płatnej subskrypcji, którą można zarządzać.');
                }
            }
            
            // Przed użyciem portalu płatności, sprawdzamy czy subskrypcja istnieje
            $stripe = $this->stripe();
            
            try {
                // Spróbuj pobrać subskrypcję z Stripe
                if ($user->stripe_subscription_id) {
                    $subscription = $stripe->subscriptions->retrieve($user->stripe_subscription_id);
                    
                    // Użyjemy portalu klienta Stripe, który pozwala na zarządzanie subskrypcją
                    // Trwają problemy z konfiguracją, więc możemy użyć bezpośredniego zarządzania
                    $host = $request->getHost();
                    $port = $request->getPort();
                    $scheme = $request->getScheme();
                    
                    $returnUrl = $port == 80 || $port == 443 
                        ? "{$scheme}://{$host}/subscription" 
                        : "{$scheme}://{$host}:{$port}/subscription";
                    
                    // Utwórz sesję portalu klienta
                    try {
                        // Najpierw spróbuj użyć portalu klienta Stripe
                        $session = $stripe->billingPortal->sessions->create([
                            'customer' => $user->stripe_customer_id,
                            'return_url' => $returnUrl,
                        ]);
                        
                        return redirect($session->url);
                    } catch (\Exception $portalException) {
                        // Jeśli portal klienta nie jest skonfigurowany, skorzystaj z Custom Flow
                        Log::warning('Błąd podczas tworzenia sesji portalu: ' . $portalException->getMessage());
                        
                        // Przekieruj do strony subskrypcji z informacją jak zarządzać
                        return redirect()->route('subscription')
                            ->with('warning', 'Portal zarządzania płatnościami nie jest w pełni skonfigurowany. Możesz anulować subskrypcję używając przycisku "Anuluj subskrypcję".');
                    }
                }
            } catch (\Exception $innerException) {
                Log::error('Błąd podczas pobierania informacji o subskrypcji: ' . $innerException->getMessage());
                // Kontynuuj do próby portalu płatności poniżej
            }
            
            // Jeśli powyższe nie zadziałało, spróbuj tradycyjnego portalu płatności
            try {
                $session = $stripe->billingPortal->sessions->create([
                    'customer' => $user->stripe_customer_id,
                    'return_url' => route('subscription'),
                ]);
                
                return redirect($session->url);
            } catch (\Exception $portalException) {
                // Jeśli portal płatności nie jest dostępny, przekieruj użytkownika do opcji anulowania
                Log::error('Błąd tworzenia sesji portalu płatności: ' . $portalException->getMessage());
                return redirect()->route('subscription')
                    ->with('warning', 'Portal płatności Stripe nie jest skonfigurowany. Możesz zarządzać subskrypcją za pomocą przycisku "Anuluj subskrypcję".');
            }
        } catch (\Exception $e) {
            Log::error('Błąd podczas przekierowywania do portalu płatności: ' . $e->getMessage());
            return redirect()->route('subscription')
                ->with('error', 'Wystąpił błąd podczas przekierowywania do portalu płatności: ' . $e->getMessage());
        }
    }

    /**
     * Wyświetla stronę z informacją o statusie weryfikacji KYC.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showKycStatus(Request $request)
    {
        $user = $request->user();
        return view('stripe.kyc', ['kycStatus' => $user->kyc_status]);
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
            $stripe = $this->stripe();
            
            // Definiujemy pełny adres URL z poprawnym hostem i portem
            $host = $request->getHost();
            $port = $request->getPort();
            $scheme = $request->getScheme();
            
            // Tworzymy URL powrotu z uwzględnieniem portu (jeśli jest niestandardowy)
            $returnUrl = $port == 80 || $port == 443 
                ? "{$scheme}://{$host}/kyc/completed" 
                : "{$scheme}://{$host}:{$port}/kyc/completed";
            
            $session = $stripe->identity->verificationSessions->create([
                'type' => 'document',
                'metadata' => [
                    'user_id' => $user->id,
                ],
                'return_url' => $returnUrl,
            ]);

            // Zapisz ID sesji weryfikacji
            $user->verification_session_id = $session->id;
            $user->save();

            // Przekierowanie użytkownika do strony weryfikacji Stripe
            return redirect($session->url);
        } catch (ApiErrorException $e) {
            Log::error('Błąd podczas inicjowania weryfikacji KYC: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Nie udało się rozpocząć weryfikacji KYC. Spróbuj ponownie później.']);
        }
    }

    /**
     * Obsługuje powrót użytkownika po procesie weryfikacji KYC.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function kycCompleted(Request $request)
    {
        $user = $request->user();
        
        // Sprawdź status weryfikacji w Stripe
        try {
            $stripe = $this->stripe();
            
            if ($user->verification_session_id) {
                $session = $stripe->identity->verificationSessions->retrieve($user->verification_session_id);
                
                if ($session->status === 'verified') {
                    $user->kyc_status = 'verified';
                    $user->save();
                    return redirect()->route('dashboard')->with('success', 'Weryfikacja KYC zakończona pomyślnie.');
                } elseif ($session->status === 'requires_input') {
                    return redirect()->route('kyc.verify')->with('warning', 'Weryfikacja KYC wymaga dodatkowych informacji.');
                }
            }
            
            return redirect()->route('kyc.verify')->with('warning', 'Status weryfikacji KYC nie może być określony. Spróbuj ponownie.');
        } catch (\Exception $e) {
            Log::error('Błąd podczas sprawdzania statusu weryfikacji KYC: ' . $e->getMessage());
            return redirect()->route('kyc.verify')->with('error', 'Wystąpił błąd podczas sprawdzania statusu weryfikacji KYC.');
        }
    }

    /**
     * Obsługuje sukces płatności w Stripe Checkout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleCheckoutSuccess(Request $request)
    {
        $sessionId = $request->session_id;
        
        if (!$sessionId) {
            return redirect()->route('dashboard')->with('error', 'Brak identyfikatora sesji.');
        }
        
        try {
            $stripe = $this->stripe();
            $session = $stripe->checkout->sessions->retrieve($sessionId, [
                'expand' => ['subscription'],
            ]);
            
            if (!$session || $session->payment_status !== 'paid') {
                return redirect()->route('dashboard')->with('error', 'Płatność nie została zrealizowana.');
            }
            
            // Znajdź użytkownika na podstawie ID z sesji
            $user = User::findOrFail($session->metadata->user_id);
            
            // Zaktualizuj status subskrypcji
            $user->stripe_subscription_status = 'active';
            $user->cancellation_requested = false;
            
            // Określ typ planu
            if (stripos($session->subscription->plan->id, config('stripe.products.premium_investor.price_id')) !== false) {
                $user->plan_type = 'premium-investor';
            } elseif (stripos($session->subscription->plan->id, config('stripe.products.premium_owner.price_id')) !== false) {
                $user->plan_type = 'premium-owner';
            } else {
                $user->plan_type = 'premium';
            }
            
            // Zapisz ID subskrypcji i klienta, jeśli istnieją
            if (!empty($session->subscription)) {
                $user->stripe_subscription_id = $session->subscription->id;
            }
            
            if (!empty($session->customer)) {
                $user->stripe_customer_id = $session->customer;
            }
            
            $user->save();
            
            Log::info('Subskrypcja aktywowana dla użytkownika: ' . $user->id . 
                     ', ID subskrypcji: ' . $user->stripe_subscription_id . 
                     ', Typ planu: ' . $user->plan_type);
            
            return redirect()->route('dashboard')->with('success', 'Subskrypcja została utworzona pomyślnie.');
        } catch (\Exception $e) {
            Log::error('Błąd przetwarzania sukcesu płatności: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Wystąpił błąd podczas przetwarzania płatności: ' . $e->getMessage());
        }
    }

    /**
     * Obsługuje webhook Stripe dla subskrypcji.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handleSubscriptionWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('stripe.webhook.secret');

        Log::info('Otrzymano webhook Stripe dla subskrypcji: ' . $request->getContent());

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );

            Log::info('Typ wydarzenia subskrypcji: ' . $event->type);

            // Obsługa różnych typów wydarzeń
            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    
                    if ($session->mode === 'subscription' && isset($session->metadata->user_id)) {
                        $user = User::findOrFail($session->metadata->user_id);
                        $user->stripe_subscription_status = 'active';
                        $user->stripe_customer_id = $session->customer;
                        $user->stripe_subscription_id = $session->subscription;
                        $user->save();
                        
                        Log::info('Subskrypcja aktywowana dla użytkownika: ' . $user->id);
                    }
                    break;
                    
                case 'customer.subscription.updated':
                    $subscription = $event->data->object;
                    $user = User::where('stripe_subscription_id', $subscription->id)->first();
                    
                    if ($user) {
                        // Aktualizacja statusu w zależności od statusu subskrypcji
                        switch ($subscription->status) {
                            case 'active':
                                $user->stripe_subscription_status = 'active';
                                $user->cancellation_requested = false;
                                
                                // Aktualizacja plan_type na podstawie ID produktu
                                $priceId = $subscription->items->data[0]->price->id;
                                $planType = '';
                                if ($priceId === (config('stripe.products.premium_investor.price_id') ?: 'price_1R3LMKBTxeaIpqRBHDccX2U1')) {
                                    $planType = 'premium-investor';
                                } elseif ($priceId === (config('stripe.products.premium_owner.price_id') ?: 'price_1R3Lc4BTxeaIpqRBtNBnHwcH')) {
                                    $planType = 'premium-owner';
                                } elseif ($priceId === (config('stripe.products.free_investor.price_id') ?: 'price_1R3LLrBTxeaIpqRB1gppSxzK')) {
                                    $planType = 'free-investor';
                                } else {
                                    Log::warning('Nieznane ID ceny w webhookach: ' . $priceId);
                                }
                                break;
                            case 'past_due':
                                $user->stripe_subscription_status = 'past_due';
                                break;
                            case 'canceled':
                                $user->stripe_subscription_status = 'cancelled';
                                
                                // Jeśli subskrypcja została anulowana, nie zmieniaj planu od razu,
                                // żeby użytkownik miał dostęp do końca okresu rozliczeniowego
                                $user->cancellation_requested = true;
                                break;
                            default:
                                $user->stripe_subscription_status = $subscription->status;
                        }
                        
                        $user->plan_type = $planType;
                        $user->save();
                        Log::info('Status subskrypcji zaktualizowany dla użytkownika: ' . $user->id . ' na: ' . $user->stripe_subscription_status . ', plan: ' . $user->plan_type);
                    }
                    break;
                    
                case 'customer.subscription.deleted':
                    $subscription = $event->data->object;
                    $user = User::where('stripe_subscription_id', $subscription->id)->first();
                    
                    if ($user) {
                        $user->stripe_subscription_status = 'cancelled';
                        
                        // Po całkowitym usunięciu subskrypcji, możemy zresetować plan na darmowy
                        if (!$user->cancellation_requested) {
                            $user->plan_type = 'free-investor';
                        }
                        
                        $user->save();
                        Log::info('Subskrypcja anulowana dla użytkownika: ' . $user->id);
                    }
                    break;
                
                case 'identity.verification_session.completed':
                    $session = $event->data->object;
                    
                    if (isset($session->metadata->user_id)) {
                        $user = User::findOrFail($session->metadata->user_id);
                        $user->kyc_status = $session->status === 'verified' ? 'verified' : 'rejected';
                        $user->save();
                        
                        Log::info('Weryfikacja KYC zakończona dla użytkownika: ' . $user->id . ' ze statusem: ' . $user->kyc_status);
                    }
                    break;
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Błąd podczas przetwarzania webhooka subskrypcji: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
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
        $endpoint_secret = config('stripe.webhook.secret');

        Log::info('Otrzymano webhook Stripe dla KYC: ' . $request->getContent());

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );

            Log::info('Typ wydarzenia KYC: ' . $event->type);

            if ($event->type === 'identity.verification_session.verified') {
                $session = $event->data->object;
                
                if (isset($session->metadata->user_id)) {
                    $user = User::findOrFail($session->metadata->user_id);
                    $user->kyc_status = 'verified';
                    $user->save();
                    
                    Log::info('Weryfikacja KYC zakończona pomyślnie dla użytkownika: ' . $user->id);
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Błąd podczas przetwarzania webhooka KYC: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
