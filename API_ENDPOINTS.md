# API Endpoints for Next.js Integration

This document describes the API endpoints created for communication with the Next.js frontend.

## Base URL
All endpoints are prefixed with `/api`

## Authentication

### POST /api/login
Authenticates a user and returns a token with user information.

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "token": "1|abcdefghijklmnopqrstuvwxyz...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "role": "client",
    "phone": "+1234567890"
  }
}
```

**Error (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["Les identifiants sont incorrects."]
  }
}
```

## Public Endpoints (No Authentication Required)

### GET /api/menus
Returns a list of active meals/menu items.

**Response (200):**
```json
[
  {
    "id": 1,
    "name": "Grilled Chicken",
    "description": "Delicious grilled chicken with vegetables",
    "category": "Main Course",
    "price": 15.50,
    "price_fc": 31000.00,
    "image": "https://example.com/storage/meals/chicken.jpg",
    "is_available": true
  }
]
```

### GET /api/plans
Returns a list of active subscription plans.

**Response (200):**
```json
[
  {
    "id": 1,
    "name": "Weekly Plan",
    "description": "5 meals per week",
    "type": "weekly",
    "meals_per_day": 2,
    "price": 100.00,
    "price_fc": 200000.00,
    "duration_days": 30,
    "total_deliveries": 10
  }
]
```

## Protected Endpoints (Requires Authentication via Bearer Token)

### GET /api/orders
Returns the authenticated user's orders.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
[
  {
    "id": 1,
    "code": "ORD-001",
    "status": "delivered",
    "total_amount": 45.50,
    "total_amount_fc": 91000.00,
    "delivery_date": "2024-01-15 12:00",
    "created_at": "2024-01-15 10:00",
    "items": [
      {
        "id": 1,
        "meal_name": "Grilled Chicken",
        "quantity": 2,
        "unit_price": 15.50,
        "unit_price_fc": 31000.00,
        "total_price": 31.00,
        "total_price_fc": 62000.00
      }
    ]
  }
]
```

### GET /api/orders/{id}
Returns details of a specific order.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "code": "ORD-001",
  "status": "delivered",
  "total_amount": 45.50,
  "total_amount_fc": 91000.00,
  "delivery_address": "123 Main Street",
  "delivery_date": "2024-01-15 12:00",
  "client_validation_code": "ABC123",
  "created_at": "2024-01-15 10:00",
  "items": [...]
}
```

**Error (403):**
```json
{
  "error": "Non autorisé"
}
```

### POST /api/subscriptions
Creates a new subscription.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "subscription_type_id": 1,
  "delivery_address": "123 Main Street",
  "phone": "+1234567890"
}
```

**Response (201):**
```json
{
  "message": "Abonnement créé avec succès",
  "subscription": {
    "id": 1,
    "status": "pending",
    "start_date": "2024-01-15",
    "end_date": "2024-02-14",
    "remaining_deliveries": 10
  }
}
```

### POST /api/subscriptions/{id}/renew
Renews an existing subscription.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Abonnement renouvelé avec succès",
  "subscription": {
    "id": 1,
    "status": "active",
    "start_date": "2024-01-15",
    "end_date": "2024-02-14",
    "remaining_deliveries": 10
  }
}
```

### POST /api/quotes
Creates a new quote request.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "message": "I am interested in your services",
  "subscription_type_id": 1
}
```

**Response (201):**
```json
{
  "message": "Demande de devis envoyée avec succès",
  "quote": {
    "id": 1,
    "status": "pending"
  }
}
```

## Authentication Flow

1. Frontend calls `POST /api/login` with credentials
2. Backend returns token and user data
3. Frontend stores token (e.g., in localStorage or cookies)
4. For protected endpoints, frontend includes `Authorization: Bearer {token}` header
5. Laravel Sanctum validates the token and grants access

## Error Responses

All endpoints may return standard error responses:

**401 Unauthorized:**
```json
{
  "message": "Unauthenticated."
}
```

**422 Validation Error:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["error message"]
  }
}
```

**403 Forbidden:**
```json
{
  "error": "Non autorisé"
}
```

**404 Not Found:**
```json
{
  "message": "No query results for model [App\\Models\\Order] {id}"
}
```

## Notes

- All monetary values are returned as floats
- Dates are formatted as `Y-m-d H:i` for datetime fields and `Y-m-d` for date fields
- Images include full URLs via `asset()` helper
- Empty collections return empty arrays `[]`
- The `/api/menus` and `/api/plans` endpoints are public and don't require authentication
- All other endpoints require a valid Sanctum token
