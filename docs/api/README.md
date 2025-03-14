# API Documentation

## Informacje Ogólne

### Autentykacja
API wykorzystuje Laravel Sanctum do autentykacji. Wszystkie zapytania muszą zawierać token w nagłówku:
```
Authorization: Bearer {token}
```

### Format Odpowiedzi
Wszystkie odpowiedzi są w formacie JSON:
```json
{
    "status": "success|error",
    "data": {},
    "message": "string",
    "errors": {}
}
```

## Endpointy

### Autentykacja

#### POST /api/auth/register
Rejestracja nowego użytkownika.
```json
{
    "name": "string",
    "email": "string",
    "password": "string",
    "password_confirmation": "string",
    "user_type": "investor|project_owner"
}
```

#### POST /api/auth/login
Logowanie użytkownika.
```json
{
    "email": "string",
    "password": "string"
}
```

### KYC

#### POST /api/kyc/initiate
Inicjacja procesu weryfikacji KYC.
```json
{
    "document_type": "id|passport|driving_license",
    "country": "string"
}
```

#### GET /api/kyc/status
Sprawdzenie statusu weryfikacji KYC.

### Projekty

#### GET /api/projects
Lista projektów z filtrowaniem i paginacją.

Parametry:
- page: int
- per_page: int
- category: string
- status: string
- search: string

#### POST /api/projects
Utworzenie nowego projektu.
```json
{
    "title": "string",
    "description": "string",
    "target_amount": "decimal",
    "category_id": "int",
    "documents": ["file"]
}
```

### Subskrypcje

#### GET /api/subscriptions/plans
Lista dostępnych planów subskrypcyjnych.

#### POST /api/subscriptions/subscribe
Subskrypcja planu.
```json
{
    "plan_id": "string",
    "payment_method": "string"
}
```

### Komunikacja

#### GET /api/messages
Lista wiadomości.

#### POST /api/messages
Wysłanie wiadomości.
```json
{
    "recipient_id": "int",
    "content": "string",
    "attachments": ["file"]
}
```

## Webhooks

### Stripe Webhooks
Endpoint: `/webhooks/stripe`

Obsługiwane wydarzenia:
- `customer.subscription.created`
- `customer.subscription.updated`
- `customer.subscription.deleted`
- `payment_intent.succeeded`
- `payment_intent.failed`

### KYC Webhooks
Endpoint: `/webhooks/kyc`

Obsługiwane wydarzenia:
- `verification.success`
- `verification.failed`
- `verification.pending`

## Rate Limiting

API posiada limity zapytań:
- 60 zapytań na minutę dla endpointów publicznych
- 120 zapytań na minutę dla zalogowanych użytkowników
- 1000 zapytań na minutę dla użytkowników Enterprise

## Kody Błędów

- 400 - Bad Request
- 401 - Unauthorized
- 403 - Forbidden
- 404 - Not Found
- 422 - Validation Error
- 429 - Too Many Requests
- 500 - Internal Server Error

## Przykłady Użycia

### Curl
```bash
# Logowanie
curl -X POST https://api.platform.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Lista projektów
curl https://api.platform.com/api/projects \
  -H "Authorization: Bearer {token}"
```

### PHP
```php
$response = Http::withToken($token)
    ->get('https://api.platform.com/api/projects');

$projects = $response->json();
```

### JavaScript
```javascript
const response = await fetch('https://api.platform.com/api/projects', {
    headers: {
        'Authorization': `Bearer ${token}`
    }
});
const projects = await response.json();
``` 