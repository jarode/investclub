<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use App\Models\User;

class StripeController extends Controller
{
    protected $stripe;

    public function __construct(\Stripe\StripeClient $stripe)
    {
        $this->stripe = $stripe;
    }

    /**
     * Utwórz instancję klienta Stripe.
     * 
     * @return \Stripe\StripeClient
     */
    protected function stripe()
    {
        return $this->stripe;
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
            
            Log::info('Rozpoczęcie tworzenia sesji checkout', [
                'user_id' => $user->id,
                'price_id' => $priceId
            ]);
            
            // Walidacja price_id
            $validPriceIds = [
                config('stripe.products.free_investor.price_id'),
                config('stripe.products.premium_investor.price_id'),
                config('stripe.products.premium_owner.price_id')
            ];
            
            if (!in_array($priceId, $validPriceIds)) {
                Log::error('Nieprawidłowy price_id', [
                    'provided_price_id' => $priceId,
                    'valid_price_ids' => $validPriceIds
                ]);
                return response()->json(['error' => 'Nieprawidłowy identyfikator planu'], 400);
            }
            
            // Sprawdź czy to darmowy plan
            if ($priceId === config('stripe.products.free_investor.price_id')) {
                Log::info('Aktywacja darmowego planu', ['user_id' => $user->id]);
                
                // Sprawdź czy użytkownik ma ID klienta Stripe
                if (!$user->stripe_customer_id) {
                    Log::info('Tworzenie nowego klienta Stripe dla darmowego planu', [
                        'user_id' => $user->id,
                        'email' => $user->email
                    ]);
                    
                    $customer = $this->stripe()->customers->create([
                        'email' => $user->email,
                        'name' => $user->name,
                        'metadata' => ['user_id' => $user->id]
                    ]);
                    
                    Log::info('Utworzono klienta Stripe', [
                        'user_id' => $user->id,
                        'stripe_customer_id' => $customer->id
                    ]);
                    
                    $user->stripe_customer_id = $customer->id;
                }
                
                // Utwórz subskrypcję dla darmowego planu
                $subscription = $this->stripe()->subscriptions->create([
                    'customer' => $user->stripe_customer_id,
                    'items' => [
                        ['price' => $priceId],
                    ],
                    'metadata' => [
                        'user_id' => $user->id,
                        'subscription_type' => 'free-investor'
                    ]
                ]);
                
                $user->stripe_subscription_id = $subscription->id;
                $user->stripe_subscription_status = 'active';
                $user->plan_type = 'free-investor';
                $user->save();
                
                return response()->json(['url' => route('dashboard')]);
            }
            
            // Sprawdź czy użytkownik ma ID klienta Stripe
            if (!$user->stripe_customer_id) {
                Log::info('Tworzenie nowego klienta Stripe', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
                
                $customer = $this->stripe()->customers->create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'metadata' => ['user_id' => $user->id]
                ]);
                
                Log::info('Utworzono klienta Stripe', [
                    'user_id' => $user->id,
                    'stripe_customer_id' => $customer->id
                ]);
                
                $user->stripe_customer_id = $customer->id;
                $user->save();
            }
            
            // Określ typ subskrypcji na podstawie wybranego planu
            $subscriptionType = $priceId === config('stripe.products.premium_investor.price_id') 
                ? 'premium-investor' 
                : 'premium-owner';
            
            // Podstawowe parametry sesji Checkout
            $sessionParams = [
                'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscription'),
                'mode' => 'subscription',
                'customer' => $user->stripe_customer_id,
                'line_items' => [
                    [
                        'price' => $priceId,
                        'quantity' => 1,
                    ],
                ],
                'metadata' => [
                    'user_id' => $user->id,
                    'subscription_type' => $subscriptionType
                ],
                'locale' => 'pl'
            ];
            
            Log::info('Parametry sesji checkout', ['params' => $sessionParams]);
            
            $session = $this->stripe()->checkout->sessions->create($sessionParams);
            
            Log::info('Utworzono sesję checkout', [
                'session_id' => $session->id,
                'url' => $session->url
            ]);

            return response()->json(['url' => $session->url]);
        } catch (\Exception $e) {
            Log::error('Błąd podczas tworzenia sesji checkout', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Wystąpił błąd podczas tworzenia sesji płatności: ' . $e->getMessage()], 500);
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

        try {
            if (!$user->stripe_subscription_id) {
                return redirect()->route('dashboard')
                    ->with('error', 'Nie znaleziono aktywnej subskrypcji.');
            }

            // Pobierz subskrypcję ze Stripe
            $subscription = $this->stripe()->subscriptions->retrieve($user->stripe_subscription_id);
            Log::info('Pobrano subskrypcję', ['subscription' => $subscription]);

            // Anuluj subskrypcję na koniec okresu rozliczeniowego
            $updatedSubscription = $this->stripe()->subscriptions->update($user->stripe_subscription_id, [
                'cancel_at_period_end' => true
            ]);
            Log::info('Zaktualizowano subskrypcję', ['subscription' => $updatedSubscription]);

            // Oznacz subskrypcję jako oczekującą na anulowanie
            $user->cancellation_requested = true;
            $user->save();
            Log::info('Zaktualizowano użytkownika', ['user' => $user->toArray()]);

            return redirect()->route('dashboard')
                ->with('success', 'Twoja subskrypcja zostanie anulowana na koniec okresu rozliczeniowego.');

        } catch (\Exception $e) {
            Log::error('Błąd podczas anulowania subskrypcji: ' . $e->getMessage(), [
                'exception' => $e,
                'user' => $user->toArray()
            ]);
            return redirect()->route('dashboard')
                ->with('error', 'Wystąpił błąd podczas anulowania subskrypcji.');
        }
    }

    /**
     * Przekierowuje użytkownika do portalu klienta Stripe.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function billingPortal(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user->stripe_customer_id) {
                return response()->json(['error' => 'Nie znaleziono identyfikatora klienta Stripe.'], 400);
            }
            
            // Podstawowe parametry sesji portalu
            $portalParams = [
                'customer' => $user->stripe_customer_id,
                'return_url' => route('dashboard')
            ];
            
            // Sprawdź, czy istnieje konfiguracja portalu
            $configurationId = config('stripe.customer_portal.configuration_id');
            if ($configurationId) {
                $portalParams['configuration'] = $configurationId;
            } else {
                // Jeśli nie ma konfiguracji, użyj domyślnych ustawień
                $portalParams['features'] = [
                    'payment_method_update' => ['enabled' => true],
                    'subscription_cancel' => ['enabled' => true],
                    'subscription_update' => ['enabled' => true],
                    'invoice_history' => ['enabled' => true]
                ];
            }
            
            try {
                $session = $this->stripe()->billingPortal->sessions->create($portalParams);
                return response()->json(['url' => $session->url]);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                // Jeśli wystąpi błąd API, spróbuj utworzyć sesję bez konfiguracji
                Log::warning('Błąd podczas tworzenia sesji portalu z konfiguracją: ' . $e->getMessage());
                
                // Spróbuj bez konfiguracji
                unset($portalParams['configuration']);
                $portalParams['features'] = [
                    'payment_method_update' => ['enabled' => true],
                    'subscription_cancel' => ['enabled' => true]
                ];
                
                $session = $this->stripe()->billingPortal->sessions->create($portalParams);
                return response()->json(['url' => $session->url]);
            }
        } catch (\Exception $e) {
            Log::error('Błąd podczas tworzenia sesji portalu: ' . $e->getMessage());
            return response()->json(['error' => 'Nie udało się utworzyć sesji portalu.'], 500);
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

        // Sprawdź, czy użytkownik już ma weryfikację w toku
        if ($user->kyc_status === 'pending' || $user->kyc_status === 'requires_input') {
            return redirect()->route('kyc.verify')
                ->with('warning', 'Masz już trwającą weryfikację KYC. Poczekaj na zakończenie procesu lub skontaktuj się z obsługą klienta.');
        }

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

            // Zapisz ID sesji weryfikacji i aktualizuj status
            $user->verification_session_id = $session->id;
            $user->kyc_status = 'pending';
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
                } elseif ($session->status === 'processing') {
                    $user->kyc_status = 'pending';
                    $user->save();
                    return redirect()->route('kyc.verify')->with('warning', 'Weryfikacja KYC jest w trakcie przetwarzania. Proszę sprawdzić status później.');
                } elseif ($session->status === 'requires_action') {
                    return redirect()->route('kyc.verify')->with('warning', 'Weryfikacja KYC wymaga dodatkowych działań. Proszę spróbować ponownie.');
                } elseif ($session->status === 'canceled') {
                    $user->kyc_status = 'canceled';
                    $user->save();
                    return redirect()->route('kyc.verify')->with('error', 'Weryfikacja KYC została anulowana. Proszę spróbować ponownie.');
                } else {
                    // Obsługa innych statusów, w tym 'rejected'
                    $user->kyc_status = 'rejected';
                    $user->save();
                    return redirect()->route('kyc.verify')->with('error', 'Weryfikacja KYC została odrzucona. Proszę skontaktować się z obsługą klienta.');
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
        try {
            $sessionId = $request->get('session_id');
            if (!$sessionId) {
                return redirect()->route('dashboard')->with('error', 'Brak identyfikatora sesji.');
            }

            $session = $this->stripe()->checkout->sessions->retrieve($sessionId, [
                'expand' => ['subscription', 'customer']
            ]);

            if (!$session) {
                return redirect()->route('dashboard')->with('error', 'Nie znaleziono sesji.');
            }

            $user = $request->user();
            
            // Sprawdź czy sesja dotyczy tego użytkownika
            if ($session->customer && $session->customer->id !== $user->stripe_customer_id) {
                Log::error('Niezgodność klientów', [
                    'session_customer_id' => $session->customer->id,
                    'user_stripe_customer_id' => $user->stripe_customer_id
                ]);
                return redirect()->route('dashboard')->with('error', 'Nieprawidłowy identyfikator klienta.');
            }

            // Pobierz subskrypcję
            $subscription = $session->subscription;
            if (!$subscription) {
                Log::error('Brak subskrypcji w sesji', [
                    'session_id' => $session->id
                ]);
                return redirect()->route('dashboard')->with('error', 'Nie znaleziono subskrypcji.');
            }
            
            // Pobierz typ subskrypcji z metadanych sesji
            $subscriptionType = $session->metadata->subscription_type ?? null;
            if (!$subscriptionType) {
                // Jeśli brak w metadanych sesji, sprawdź metadane subskrypcji
                $subscriptionType = $subscription->metadata->subscription_type ?? null;
            }
            
            // Jeśli nadal brak typu, wywnioskuj na podstawie ceny
            if (!$subscriptionType) {
                $items = $subscription->items->data;
                if (count($items) > 0) {
                    $priceId = $items[0]->price->id;
                    if ($priceId === config('stripe.products.premium_investor.price_id')) {
                        $subscriptionType = 'premium-investor';
                    } elseif ($priceId === config('stripe.products.premium_owner.price_id')) {
                        $subscriptionType = 'premium-owner';
                    } elseif ($priceId === config('stripe.products.free_investor.price_id')) {
                        $subscriptionType = 'free-investor';
                    }
                }
            }

            // Aktualizuj status subskrypcji użytkownika
            $user->stripe_subscription_id = $subscription->id;
            $user->stripe_subscription_status = $subscription->status;
            $user->plan_type = $subscriptionType;
            $user->save();
            
            Log::info('Aktualizacja użytkownika po udanej płatności', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'subscription_status' => $subscription->status,
                'plan_type' => $subscriptionType
            ]);

            return redirect()->route('dashboard')->with('success', 'Subskrypcja została aktywowana. Teraz możesz korzystać z pełni funkcji.');
        } catch (\Exception $e) {
            Log::error('Błąd podczas obsługi sukcesu checkoutu: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('dashboard')->with('error', 'Wystąpił błąd podczas aktywacji subskrypcji: ' . $e->getMessage());
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

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );

            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    if ($session->mode === 'subscription') {
                        $user = User::findOrFail($session->metadata->user_id);
                        $user->subscription_status = 'active';
                        $user->subscription_type = $session->metadata->subscription_type;
                        $user->stripe_subscription_id = $session->subscription;
                        $user->save();
                    }
                    break;

                case 'customer.subscription.updated':
                    $subscription = $event->data->object;
                    $user = User::where('stripe_subscription_id', $subscription->id)->first();
                    
                    if ($user) {
                        $user->stripe_subscription_status = $subscription->status;
                        
                        // Sprawdź, czy metadane zawierają typ subskrypcji
                        if (isset($subscription->metadata->subscription_type)) {
                            $user->plan_type = $subscription->metadata->subscription_type;
                        } else {
                            // Jeśli nie ma metadanych, określ typ planu na podstawie priceId
                            $priceId = $subscription->items->data[0]->price->id;
                            
                            if ($priceId === config('stripe.products.free_investor.price_id')) {
                                $user->plan_type = 'free-investor';
                            } elseif ($priceId === config('stripe.products.premium_investor.price_id')) {
                                $user->plan_type = 'premium-investor';
                            } elseif ($priceId === config('stripe.products.premium_owner.price_id')) {
                                $user->plan_type = 'premium-owner';
                            }
                            
                            // Aktualizuj metadane w Stripe
                            try {
                                $this->stripe()->subscriptions->update($subscription->id, [
                                    'metadata' => ['subscription_type' => $user->plan_type]
                                ]);
                                
                                Log::info('Zaktualizowano metadane subskrypcji w Stripe', [
                                    'subscription_id' => $subscription->id,
                                    'subscription_type' => $user->plan_type
                                ]);
                            } catch (\Exception $e) {
                                Log::error('Błąd podczas aktualizacji metadanych subskrypcji', [
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }
                        
                        $user->save();
                        
                        Log::info('Subskrypcja zaktualizowana', [
                            'user_id' => $user->id,
                            'subscription_id' => $subscription->id,
                            'status' => $subscription->status,
                            'plan_type' => $user->plan_type
                        ]);
                    }
                    break;

                case 'customer.subscription.deleted':
                    $subscription = $event->data->object;
                    $user = User::where('stripe_subscription_id', $subscription->id)->first();
                    
                    if ($user) {
                        $user->stripe_subscription_status = 'cancelled';
                        $user->plan_type = null;
                        $user->stripe_subscription_id = null;
                        $user->save();
                        
                        Log::info('Subskrypcja anulowana', [
                            'user_id' => $user->id,
                            'subscription_id' => $subscription->id
                        ]);
                    }
                    break;

                case 'invoice.payment_failed':
                    $invoice = $event->data->object;
                    $user = User::where('stripe_customer_id', $invoice->customer)->first();
                    
                    if ($user) {
                        $user->subscription_status = 'past_due';
                        $user->save();
                        
                        Log::warning('Płatność nieudana', [
                            'user_id' => $user->id,
                            'invoice_id' => $invoice->id
                        ]);
                    }
                    break;
            }
            
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Błąd podczas przetwarzania webhooka: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 200);
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
            } elseif ($event->type === 'identity.verification_session.requires_input') {
                $session = $event->data->object;
                
                if (isset($session->metadata->user_id)) {
                    $user = User::findOrFail($session->metadata->user_id);
                    $user->kyc_status = 'requires_input';
                    $user->save();
                    
                    Log::info('Weryfikacja KYC wymaga dodatkowych informacji od użytkownika: ' . $user->id);
                }
            } elseif ($event->type === 'identity.verification_session.canceled') {
                $session = $event->data->object;
                
                if (isset($session->metadata->user_id)) {
                    $user = User::findOrFail($session->metadata->user_id);
                    $user->kyc_status = 'canceled';
                    $user->save();
                    
                    Log::info('Weryfikacja KYC została anulowana dla użytkownika: ' . $user->id);
                }
            } elseif ($event->type === 'identity.verification_session.processing') {
                $session = $event->data->object;
                
                if (isset($session->metadata->user_id)) {
                    $user = User::findOrFail($session->metadata->user_id);
                    $user->kyc_status = 'pending';
                    $user->save();
                    
                    Log::info('Weryfikacja KYC jest w trakcie przetwarzania dla użytkownika: ' . $user->id);
                }
            } elseif ($event->type === 'identity.verification_session.redacted') {
                $session = $event->data->object;
                
                if (isset($session->metadata->user_id)) {
                    Log::info('Dane weryfikacji KYC zostały usunięte dla użytkownika: ' . $session->metadata->user_id);
                }
            } else {
                // Wszystkie inne zdarzenia, w tym odrzucenie weryfikacji
                $session = $event->data->object;
                
                if (isset($session->metadata->user_id)) {
                    $user = User::findOrFail($session->metadata->user_id);
                    
                    if ($event->type === 'identity.verification_session.created') {
                        $user->kyc_status = 'pending';
                        Log::info('Utworzono sesję weryfikacji KYC dla użytkownika: ' . $user->id);
                    } else {
                        $user->kyc_status = 'rejected';
                        Log::info('Odrzucono lub wystąpił inny problem z weryfikacją KYC dla użytkownika: ' . $user->id);
                    }
                    
                    $user->save();
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Błąd podczas przetwarzania webhooka KYC: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 200);
        }
    }

    /**
     * Przekierowuje użytkownika do widoku aktualizacji subskrypcji w portalu klienta Stripe.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSubscriptionPortal(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user->stripe_customer_id || !$user->stripe_subscription_id) {
                return response()->json(['error' => 'Nie znaleziono subskrypcji.'], 400);
            }
            
            // Podstawowe parametry sesji portalu
            $portalParams = [
                'customer' => $user->stripe_customer_id,
                'return_url' => route('subscription'),
                'flow_data' => [
                    'type' => 'subscription_update',
                    'subscription_update' => [
                        'subscription' => $user->stripe_subscription_id
                    ]
                ]
            ];
            
            $session = $this->stripe()->billingPortal->sessions->create($portalParams);
            
            Log::info('Utworzono sesję aktualizacji subskrypcji', [
                'session_id' => $session->id,
                'url' => $session->url
            ]);
            
            return response()->json(['url' => $session->url]);
        } catch (\Exception $e) {
            Log::error('Błąd podczas tworzenia sesji aktualizacji subskrypcji: ' . $e->getMessage());
            return response()->json(['error' => 'Nie udało się utworzyć sesji aktualizacji subskrypcji.'], 500);
        }
    }

    /**
     * Aktualizuje subskrypcję użytkownika do wybranego planu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSubscription(Request $request)
    {
        $plan = $request->query('plan');
        
        if (!in_array($plan, ['free-investor', 'premium-investor', 'premium-owner'])) {
            return redirect()->route('subscription')
                ->with('error', 'Nieprawidłowy typ planu.');
        }
        
        $user = $request->user();
        
        try {
            if (!$user->stripe_customer_id) {
                return redirect()->route('subscription')
                    ->with('error', 'Nie znaleziono konta Stripe. Aktywuj najpierw dowolny plan.');
            }
            
            // Pobierz odpowiedni identyfikator ceny
            $priceId = null;
            switch ($plan) {
                case 'free-investor':
                    $priceId = config('stripe.products.free_investor.price_id');
                    break;
                case 'premium-investor':
                    $priceId = config('stripe.products.premium_investor.price_id');
                    break;
                case 'premium-owner':
                    $priceId = config('stripe.products.premium_owner.price_id');
                    break;
            }
            
            if (!$priceId) {
                return redirect()->route('subscription')
                    ->with('error', 'Nie znaleziono ceny dla wybranego planu.');
            }
            
            Log::info('Rozpoczęcie aktualizacji subskrypcji', [
                'user_id' => $user->id,
                'current_plan' => $user->plan_type,
                'new_plan' => $plan,
                'price_id' => $priceId
            ]);
            
            // Jeśli użytkownik ma już subskrypcję, zaktualizuj ją
            if ($user->stripe_subscription_id) {
                $subscription = $this->stripe()->subscriptions->retrieve($user->stripe_subscription_id);
                
                // Pobierz ID pierwszego elementu subskrypcji
                $itemId = $subscription->items->data[0]->id;
                
                // Zaktualizuj subskrypcję
                $updatedSubscription = $this->stripe()->subscriptions->update($user->stripe_subscription_id, [
                    'items' => [
                        [
                            'id' => $itemId,
                            'price' => $priceId,
                        ],
                    ],
                    'metadata' => [
                        'user_id' => $user->id,
                        'subscription_type' => $plan
                    ]
                ]);
                
                Log::info('Zaktualizowano subskrypcję', [
                    'subscription_id' => $updatedSubscription->id,
                    'status' => $updatedSubscription->status
                ]);
                
                // Zaktualizuj dane użytkownika
                $user->plan_type = $plan;
                $user->stripe_subscription_status = $updatedSubscription->status;
                $user->save();
                
                return redirect()->route('subscription')
                    ->with('success', 'Subskrypcja została zaktualizowana pomyślnie.');
            } else {
                // Jeśli użytkownik nie ma subskrypcji, utwórz nową
                $subscription = $this->stripe()->subscriptions->create([
                    'customer' => $user->stripe_customer_id,
                    'items' => [
                        ['price' => $priceId],
                    ],
                    'metadata' => [
                        'user_id' => $user->id,
                        'subscription_type' => $plan
                    ]
                ]);
                
                Log::info('Utworzono nową subskrypcję', [
                    'subscription_id' => $subscription->id,
                    'status' => $subscription->status
                ]);
                
                // Zaktualizuj dane użytkownika
                $user->stripe_subscription_id = $subscription->id;
                $user->stripe_subscription_status = $subscription->status;
                $user->plan_type = $plan;
                $user->save();
                
                return redirect()->route('subscription')
                    ->with('success', 'Subskrypcja została utworzona pomyślnie.');
            }
        } catch (\Exception $e) {
            Log::error('Błąd podczas aktualizacji subskrypcji: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('subscription')
                ->with('error', 'Wystąpił błąd podczas aktualizacji subskrypcji: ' . $e->getMessage());
        }
    }

    /**
     * Tworzy sesję portalu klienta Stripe.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createPortalSession(Request $request)
    {
        try {
            $user = $request->user();
            
            Log::info('Tworzenie sesji portalu klienta', [
                'user_id' => $user->id,
                'email' => $user->email,
                'plan_type' => $user->plan_type,
                'stripe_customer_id' => $user->stripe_customer_id
            ]);

            // Jeśli użytkownik nie ma customer_id, utwórz go
            if (!$user->stripe_customer_id) {
                Log::info('Tworzenie nowego klienta Stripe', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
                
                $customer = $this->stripe()->customers->create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'metadata' => ['user_id' => $user->id]
                ]);
                
                $user->stripe_customer_id = $customer->id;
                $user->save();
                
                Log::info('Utworzono klienta Stripe', [
                    'user_id' => $user->id,
                    'stripe_customer_id' => $customer->id
                ]);
                
                // Jeśli użytkownik ma darmowy plan, dodaj subskrypcję do konta Stripe
                if ($user->plan_type === 'free-investor') {
                    $subscription = $this->stripe()->subscriptions->create([
                        'customer' => $customer->id,
                        'items' => [
                            ['price' => config('stripe.products.free_investor.price_id')],
                        ],
                        'metadata' => [
                            'user_id' => $user->id,
                            'subscription_type' => 'free-investor'
                        ]
                    ]);
                    
                    $user->stripe_subscription_id = $subscription->id;
                    $user->save();
                    
                    Log::info('Utworzono darmową subskrypcję', [
                        'user_id' => $user->id,
                        'subscription_id' => $subscription->id
                    ]);
                }
            }

            // Sprawdź, czy użytkownik ma subskrypcję
            if (!$user->stripe_subscription_id && $user->plan_type === 'free-investor') {
                // Dla darmowego planu bez subskrypcji, utwórz ją
                $subscription = $this->stripe()->subscriptions->create([
                    'customer' => $user->stripe_customer_id,
                    'items' => [
                        ['price' => config('stripe.products.free_investor.price_id')],
                    ],
                    'metadata' => [
                        'user_id' => $user->id,
                        'subscription_type' => 'free-investor'
                    ]
                ]);
                
                $user->stripe_subscription_id = $subscription->id;
                $user->save();
                
                Log::info('Utworzono darmową subskrypcję przed portalem', [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id
                ]);
            }

            // Podstawowe parametry sesji portalu
            $portalParams = [
                'customer' => $user->stripe_customer_id,
                'return_url' => route('subscription')
            ];
            
            // Sprawdź, czy istnieje konfiguracja portalu
            $configurationId = config('stripe.customer_portal.configuration_id');
            if ($configurationId) {
                $portalParams['configuration'] = $configurationId;
            } else {
                // Jeśli nie ma konfiguracji, użyj domyślnych ustawień
                $portalParams['features'] = [
                    'payment_method_update' => ['enabled' => true],
                    'subscription_cancel' => ['enabled' => true],
                    'subscription_update' => ['enabled' => true],
                    'invoice_history' => ['enabled' => true]
                ];
            }
            
            Log::info('Parametry sesji portalu', ['params' => $portalParams]);

            // Utwórz sesję portalu
            try {
                $session = $this->stripe()->billingPortal->sessions->create($portalParams);
                
                Log::info('Utworzono sesję portalu', [
                    'session_id' => $session->id,
                    'url' => $session->url
                ]);
                
                return response()->json(['url' => $session->url]);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                // Jeśli wystąpi błąd API, spróbuj utworzyć sesję bez konfiguracji
                Log::warning('Błąd podczas tworzenia sesji portalu z konfiguracją: ' . $e->getMessage());
                
                // Spróbuj bez konfiguracji
                unset($portalParams['configuration']);
                $portalParams['features'] = [
                    'payment_method_update' => ['enabled' => true],
                    'subscription_cancel' => ['enabled' => true]
                ];
                
                $session = $this->stripe()->billingPortal->sessions->create($portalParams);
                
                Log::info('Utworzono sesję portalu bez konfiguracji', [
                    'session_id' => $session->id,
                    'url' => $session->url
                ]);
                
                return response()->json(['url' => $session->url]);
            }
        } catch (\Exception $e) {
            Log::error('Błąd podczas tworzenia sesji portalu', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Wystąpił błąd podczas tworzenia sesji portalu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obsługuje webhooki od Stripe.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('stripe.webhook.secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );

            Log::info('Otrzymano webhook Stripe', ['type' => $event->type]);

            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    if ($session->mode === 'subscription') {
                        $user = User::findOrFail($session->metadata->user_id);
                        $subscription = $this->stripe()->subscriptions->retrieve($session->subscription);
                        
                        $user->stripe_subscription_id = $subscription->id;
                        $user->stripe_subscription_status = $subscription->status;
                        $user->plan_type = $subscription->metadata->subscription_type;
                        $user->stripe_customer_id = $session->customer;
                        $user->save();
                        
                        Log::info('Subskrypcja utworzona', [
                            'user_id' => $user->id,
                            'subscription_id' => $subscription->id
                        ]);
                    }
                    break;

                case 'customer.subscription.updated':
                    $subscription = $event->data->object;
                    $user = User::where('stripe_subscription_id', $subscription->id)->first();
                    
                    if ($user) {
                        $user->stripe_subscription_status = $subscription->status;
                        
                        // Sprawdź, czy metadane zawierają typ subskrypcji
                        if (isset($subscription->metadata->subscription_type)) {
                            $user->plan_type = $subscription->metadata->subscription_type;
                        } else {
                            // Jeśli nie ma metadanych, określ typ planu na podstawie priceId
                            $priceId = $subscription->items->data[0]->price->id;
                            
                            if ($priceId === config('stripe.products.free_investor.price_id')) {
                                $user->plan_type = 'free-investor';
                            } elseif ($priceId === config('stripe.products.premium_investor.price_id')) {
                                $user->plan_type = 'premium-investor';
                            } elseif ($priceId === config('stripe.products.premium_owner.price_id')) {
                                $user->plan_type = 'premium-owner';
                            }
                            
                            // Aktualizuj metadane w Stripe
                            try {
                                $this->stripe()->subscriptions->update($subscription->id, [
                                    'metadata' => ['subscription_type' => $user->plan_type]
                                ]);
                                
                                Log::info('Zaktualizowano metadane subskrypcji w Stripe', [
                                    'subscription_id' => $subscription->id,
                                    'subscription_type' => $user->plan_type
                                ]);
                            } catch (\Exception $e) {
                                Log::error('Błąd podczas aktualizacji metadanych subskrypcji', [
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }
                        
                        $user->save();
                        
                        Log::info('Subskrypcja zaktualizowana', [
                            'user_id' => $user->id,
                            'subscription_id' => $subscription->id,
                            'status' => $subscription->status,
                            'plan_type' => $user->plan_type
                        ]);
                    }
                    break;

                case 'customer.subscription.deleted':
                    $subscription = $event->data->object;
                    $user = User::where('stripe_subscription_id', $subscription->id)->first();
                    
                    if ($user) {
                        $user->stripe_subscription_status = 'cancelled';
                        $user->plan_type = null;
                        $user->stripe_subscription_id = null;
                        $user->save();
                        
                        Log::info('Subskrypcja anulowana', [
                            'user_id' => $user->id,
                            'subscription_id' => $subscription->id
                        ]);
                    }
                    break;

                case 'invoice.payment_failed':
                    $invoice = $event->data->object;
                    $user = User::where('stripe_customer_id', $invoice->customer)->first();
                    
                    if ($user) {
                        $user->stripe_subscription_status = 'past_due';
                        $user->save();
                        
                        Log::warning('Płatność nieudana', [
                            'user_id' => $user->id,
                            'invoice_id' => $invoice->id
                        ]);
                    }
                    break;

                case 'invoice.payment_succeeded':
                    $invoice = $event->data->object;
                    $user = User::where('stripe_customer_id', $invoice->customer)->first();
                    
                    if ($user) {
                        $user->stripe_subscription_status = 'active';
                        $user->save();
                        
                        Log::info('Płatność zakończona sukcesem', [
                            'user_id' => $user->id,
                            'invoice_id' => $invoice->id
                        ]);
                    }
                    break;
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Błąd podczas przetwarzania webhooka: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 200);
        }
    }
}
