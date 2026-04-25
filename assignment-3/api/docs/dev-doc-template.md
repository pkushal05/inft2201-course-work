# Assignment 3 – Developer Documentation

## 1. Overview

This API provides secure access to mails for a corporate system using authentication, role-based authorization, logging, rate-limitng and centralized error-handling

---

## 2. Authentication

### 2.1 Auth Method

- Scheme: Bearer token (JWT)
- How to obtain a token:
    - Endpoint: `POST /auth/login`

    - Request body format:

        ```json
        {
            "username": "YOUR_USERNAME",
            "password": "YOUR_PASSWORD"
        }
        ```

    - Example success response:

        ```json
        {
            "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6..."
        }
        ```

### 2.2 Using the Token

- Required header for authenticated requests:
    - `Authorization: Bearer <token>`

- Tokens are valid for 1 hr:

```json
{
    "error": "Forbidden",
    "message": "Invalid or Expired Token",
    "statusCode": 403,
    "requestId": "405fbd96-b...",
    "timestamp": "2026-04-1..."
}
```

---

## 3. Roles & Access Rules

- `admin`
    - Can view any mail message.

- `user`
    - Can only view their own mail messages.

| Endpoint      | Method | admin       | user              |
| ------------- | ------ | ----------- | ----------------- |
| `/mail/:id`   | GET    | ✅ all mail | ✅ own mail only  |
| `/auth/login` | POST   | ✅          | ✅                |
| `/status`     | GET    | ✅          | ✅                |

---

## 4. Endpoints

### 4.1 `POST /auth/login`

**Description:**  
Authenticate with username/password and receive a JWT.

**Request Body:**

```json
{
    "username": "YOUR_USERNAME",
    "password": "YOUR_PASSWORD"
}
```

**Success Response (200)**

```json
{
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6..."
}
```

**Error Response (400): Missing Fields**

```json
{
    "error": "BadRequest",
    "message": "Missing Fields",
    "statusCode": 400,
    "requestId": "405fbd96-b...",
    "timestamp": "2026-04-1..."
}
```

---

### 4.2 `GET /mail/:id`

**Description:**
Retrieve a single mail message by ID.

**Authentication:**

- Requires `Authorization: Bearer <token>` header.

**Access Rules:**

- `admin`: may view any mail ID.
- `user`: may view only mail where `mail.userId` matches their own `userId`.

**Example Request:**

```bash
curl http://localhost:3000/mail/2 \
  -H "Authorization: Bearer <token>"
```

**Example Success Response (200):**

```json
{
    "id": 2,
    "userId": 2,
    "subject": "Hello User1",
    "body": "Your report is ready."
}
```

**Example Forbidden Response (when user tries to access someone else’s mail):**

```json
{
    "error": "Unauthorized",
    "message": "User does not have permission to access this resource.",
    "statusCode": 403,
    "requestId": "req-12345",
    "timestamp": "2025-11-30T14:22:00Z"
}
```

---

### 4.3 `GET /status`

**Description:**
Simple health check to confirm the API is running.

**Authentication:**

- None required.

**Example Response (200):**

```json
{
    "status": "ok"
}
```

---

## 5. Rate Limiting

- Keyed by: IP address.
- Limit: e.g. `RATE_LIMIT_MAX` requests per `RATE_LIMIT_WINDOW_SECONDS`.
- What happens when the limit is exceeded:
    - Example response:

            ```json
            {
                "error": "TooManyRequests",
                "message": "Rate limit exceeded. Please try again later.",
                "statusCode": 429,
                "requestId": "req-67890",
                "timestamp": "2025-11-30T14:30:00Z",
                "retryAfter": 55
            }
            ```

---

## 6. Error Response Format

**Standard Error Response Format**

```json
{
    "error": "",
    "message": "",
    "statusCode": 200,
    "requestId": "",
    "timestamp": ""
}
```

**BadRequest**

```json
{
    "error": "BadRequest",
    "message": "Missing Fields",
    "statusCode": 400,
    "requestId": "405fbd96-b...",
    "timestamp": "2026-04-1..."
}
```

**Unauthorized**

```json
{
    "error": "Unauthorized",
    "message": "User does not have permission to access this resource.",
    "statusCode": 403,
    "requestId": "req-12345",
    "timestamp": "2025-11-30T14:22:00Z"
}
```

**NotFound**

```json
{
    "error": "NotFound",
    "message": "Mail not found",
    "statusCode": 404,
    "requestId": "req-12345",
    "timestamp": "2025-11-30T14:22:00Z"
}
```

---

## 7. Example Flows

### 7.1 Happy Path: Login + Access Own Mail

1. `POST /auth/login` as `user1` → receive token.

```bash
curl -X POST http://localhost:3000/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username": "username", "password": "password"}'
```

```json
{
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

2. `GET /mail/2` with that token → receive mail details.

```bash
curl -X GET http://localhost:3000/mail/2 \\
  -H "Authorization: Bearer <TOKEN>"
```

```json
{
    "id": 2,
    "userId": "user1",
    "subject": "Hello User 1",
    "body": "Here are you mails..."
}
```

### 7.2 Error Path: User Accessing Someone Else’s Mail

1. Login as `user1`.

```bash
curl -X POST http://localhost:3000/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username": "user1", "password": "password"}'
```

```json
{
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

2. `GET /mail/3` (which belongs to another user).

```bash
curl -X GET http://localhost:3000/mail/3 \\
  -H "Authorization: Bearer <TOKEN>"
```

3. Show the `403` response.

```json
{
    "error": "Unauthorized",
    "message": "User does not have permission to access this resource.",
    "statusCode": 403,
    "requestId": "req-12345",
    "timestamp": "2025-11-30T14:22:00Z"
}
```

### 7.3 Edge Cases

** There are no edge cases, every error's being handled**
