<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Subskrypcja') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">{{ __('Wybierz plan subskrypcji') }}</h3>

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Plan Podstawowy -->
                    <div class="border rounded-lg p-6 hover:shadow-lg transition duration-300">
                        <h4 class="text-xl font-semibold mb-2">Plan Podstawowy</h4>
                        <p class="text-gray-600 mb-4">Dostęp do wszystkich podstawowych funkcji platformy.</p>
                        <p class="text-2xl font-bold mb-4">99 zł / miesiąc</p>
                        <ul class="mb-6 space-y-2">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Dostęp do katalogu projektów
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Możliwość wyrażenia zainteresowania
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Podstawowe analizy projektów
                            </li>
                        </ul>
                        <button 
                            type="button"
                            class="subscribe-button w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600"
                            data-plan="price_basic">
                            Wybierz plan
                        </button>
                    </div>

                    <!-- Plan Premium -->
                    <div class="border rounded-lg p-6 bg-gray-50 hover:shadow-lg transition duration-300">
                        <h4 class="text-xl font-semibold mb-2">Plan Premium</h4>
                        <p class="text-gray-600 mb-4">Dostęp do wszystkich funkcji platformy z priorytetową obsługą.</p>
                        <p class="text-2xl font-bold mb-4">299 zł / miesiąc</p>
                        <ul class="mb-6 space-y-2">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Wszystko z planu Podstawowego
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Priorytetowy dostęp do nowych projektów
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Zaawansowane analizy i raporty
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Dostęp do ekskluzywnych projektów
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Indywidualne doradztwo
                            </li>
                        </ul>
                        <button 
                            type="button"
                            class="subscribe-button w-full bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700"
                            data-plan="price_premium">
                            Wybierz plan
                        </button>
                    </div>
                </div>

                <!-- Formularz płatności (ukryty na początku) -->
                <div id="payment-form-container" class="hidden mt-8 border-t pt-6">
                    <h3 class="text-lg font-medium mb-4">{{ __('Dane płatności') }}</h3>
                    
                    <form id="payment-form" action="{{ route('subscription.create') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan" id="selected-plan" value="">
                        
                        <div class="mb-4">
                            <div id="card-element" class="border p-3 rounded-md"></div>
                            <div id="card-errors" class="text-red-600 mt-2"></div>
                        </div>
                        
                        <input type="hidden" name="payment_method" id="payment-method-id">
                        
                        <button type="submit" id="submit-button" class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700 w-full">
                            Rozpocznij subskrypcję
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicjalizacja Stripe
            const stripe = Stripe('{{ config('cashier.key') }}');
            const elements = stripe.elements();
            
            // Utworzenie elementu karty
            const cardElement = elements.create('card');
            cardElement.mount('#card-element');
            
            // Obsługa błędów walidacji karty
            cardElement.addEventListener('change', function(event) {
                const displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });
            
            // Przyciski wyboru planu
            const subscribeButtons = document.querySelectorAll('.subscribe-button');
            subscribeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const plan = this.getAttribute('data-plan');
                    document.getElementById('selected-plan').value = plan;
                    
                    // Pokaż formularz płatności
                    document.getElementById('payment-form-container').classList.remove('hidden');
                    
                    // Przewiń do formularza płatności
                    document.getElementById('payment-form-container').scrollIntoView({
                        behavior: 'smooth'
                    });
                    
                    // Zaznacz wybrany plan
                    subscribeButtons.forEach(btn => {
                        btn.closest('div').classList.remove('ring-2', 'ring-indigo-500');
                    });
                    this.closest('div').classList.add('ring-2', 'ring-indigo-500');
                });
            });
            
            // Obsługa formularza
            const form = document.getElementById('payment-form');
            form.addEventListener('submit', async function(event) {
                event.preventDefault();
                
                const submitButton = document.getElementById('submit-button');
                submitButton.disabled = true;
                submitButton.textContent = 'Przetwarzanie...';
                
                // Utworzenie tokenu karty
                const { paymentMethod, error } = await stripe.createPaymentMethod({
                    type: 'card',
                    card: cardElement,
                });
                
                if (error) {
                    document.getElementById('card-errors').textContent = error.message;
                    submitButton.disabled = false;
                    submitButton.textContent = 'Rozpocznij subskrypcję';
                } else {
                    // Zapisz token jako payment_method
                    document.getElementById('payment-method-id').value = paymentMethod.id;
                    
                    // Wyślij formularz
                    form.submit();
                }
            });
        });
    </script>
    @endpush
</x-app-layout> 