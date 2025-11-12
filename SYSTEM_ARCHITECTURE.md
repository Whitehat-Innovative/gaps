# System Architecture

## Overview

The `gap` Laravel application integrates three core systems:

1. **Laravel Breeze** - Authentication scaffolding (login, register, reset)
2. **Laravel Sanctum** - API token authentication
3. **Spatie Roles & Permissions** - Fine-grained access control

## Component Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                        REQUEST ROUTING                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────────────────┐         ┌──────────────────────────┐ │
│  │  Web Routes          │         │  API Routes              │ │
│  │  (routes/web.php)    │         │  (routes/api.php)        │ │
│  │                      │         │                          │ │
│  │  • /                 │         │  • /api/user             │ │
│  │  • /admin            │         │  • /api/admin/stats      │ │
│  │  • /auth/login       │         │  • /auth/login (token)   │ │
│  │  • /auth/register    │         │  • /auth/register (token)│ │
│  └──────────────────────┘         └──────────────────────────┘ │
│           ▲                                ▲                     │
│           │ Session                       │ Bearer Token         │
│           │ Cookie                        │ (Sanctum)            │
│           │                               │                     │
└─────────────────────────────────────────────────────────────────┘
            │                               │
            ▼                               ▼
┌─────────────────────────────────────────────────────────────────┐
│               AUTHENTICATION MIDDLEWARE STACK                    │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Web Auth Chain:                  API Auth Chain:              │
│  ├─ auth:web                      ├─ auth:sanctum             │
│  ├─ verified (optional)           └─ (Bearer token validation)│
│  └─ custom middleware (if any)                                │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
            │                               │
            └─────────────────┬─────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│            AUTHORIZATION & ROLE/PERMISSION CHECK                │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Custom Middleware: role_or_permission                         │
│  Location: app/Http/Middleware/EnsureHasRoleOrPermission.php  │
│                                                                 │
│  Usage:                                                         │
│  ├─ middleware('role_or_permission:admin')                    │
│  ├─ middleware('role_or_permission:admin|editor')             │
│  ├─ middleware('role_or_permission:create articles')          │
│  └─ middleware('role_or_permission:admin,edit articles')      │
│                                                                 │
│  Spatie Service: Role & Permission Provider                    │
│  └─ Checks if authenticated user has role/permission          │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
            │
            ▼
┌─────────────────────────────────────────────────────────────────┐
│                    CONTROLLER ACTION                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ✓ Request.user()              // Authenticated user          │
│  ✓ Auth::user()->roles()       // User's roles               │
│  ✓ Auth::user()->permissions() // User's permissions         │
│                                                                 │
│  Data returned to client with appropriate format:             │
│  ├─ JSON (for API requests)                                  │
│  └─ HTML/Redirect (for web requests)                         │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## Data Flow Examples

### Example 1: Web Session Login

```
User Browser
    │
    │ POST /auth/login (email, password)
    ▼
Laravel Auth Guard (web)
    │
    ├─ Validate credentials
    ├─ Create session
    └─ Set session cookie
    │
    ▼
Browser receives Set-Cookie header
    │
    ├─ Cookie stored in browser
    └─ Automatic inclusion in subsequent requests
    │
    ▼
GET /admin (with session cookie)
    │
    ├─ Session validated
    ├─ User retrieved from session
    ├─ role_or_permission:admin checked
    └─ AdminController accessed
```

### Example 2: API Token Authentication

```
Mobile App / Client
    │
    │ POST /api/auth/login (email, password)
    ▼
Laravel Auth Guard (sanctum)
    │
    ├─ Validate credentials
    ├─ Generate personal access token
    └─ Return token to client
    │
    ▼
Client stores token securely
    │
    ├─ Token kept in secure storage
    └─ Used in future requests
    │
    ▼
GET /api/admin/stats (Authorization: Bearer TOKEN)
    │
    ├─ Token validated via Sanctum
    ├─ User retrieved from token
    ├─ role_or_permission:admin checked
    └─ JSON response sent
```

## Database Schema

### Users Table
```sql
users
├─ id (PK)
├─ name
├─ email (unique)
├─ email_verified_at
├─ password (hashed)
├─ remember_token
├─ created_at
└─ updated_at
```

### Roles & Permissions (Spatie)
```sql
roles
├─ id (PK)
├─ name (unique per guard)
├─ guard_name
├─ created_at
└─ updated_at

permissions
├─ id (PK)
├─ name (unique per guard)
├─ guard_name
├─ created_at
└─ updated_at

model_has_roles
├─ role_id (FK → roles)
├─ model_id (FK → users.id)
├─ model_type (usually 'App\Models\User')

model_has_permissions
├─ permission_id (FK → permissions)
├─ model_id (FK → users.id)
├─ model_type (usually 'App\Models\User')

role_has_permissions
├─ permission_id (FK → permissions)
├─ role_id (FK → roles)
```

### Sanctum Tokens
```sql
personal_access_tokens
├─ id (PK)
├─ tokenable_type (usually 'App\Models\User')
├─ tokenable_id (FK → users.id)
├─ name (token identifier)
├─ token (hashed)
├─ abilities (JSON array of scopes)
├─ last_used_at
├─ expires_at
├─ created_at
└─ updated_at
```

## Configuration Files

### Key Configuration Points

**config/auth.php**
```php
'guards' => [
    'web' => ['driver' => 'session', 'provider' => 'users'],
    'api' => ['driver' => 'sanctum', 'provider' => 'users'],
],
```

**config/sanctum.php**
- `stateful` - Domains receiving session cookies
- `guard` - Auth guards checked before token auth
- `expiration` - Token expiration time
- `token_prefix` - Security scanning prefix

**config/permission.php** (Spatie)
- `database.table_names` - Customize table names
- `cache_key` - Cache key for role/permission data
- `queue_connection` - Queue for async jobs

**app/Http/Kernel.php**
```php
protected $routeMiddleware = [
    'role_or_permission' => \App\Http\Middleware\EnsureHasRoleOrPermission::class,
];
```

## Request Lifecycle

### 1. Request Arrival
- Request enters nginx (Docker)
- Routed to PHP-FPM container
- Laravel bootstrap initializes

### 2. Route Matching
- Routes matched (web.php or api.php)
- Middleware stack determined

### 3. Middleware Execution (Global)
- TrustProxies
- HandleCors
- PreventRequestsDuringMaintenance
- ValidatePostSize
- TrimStrings
- ConvertEmptyStringsToNull

### 4. Middleware Execution (Group)
- EncryptCookies
- AddQueuedCookiesToResponse
- StartSession
- ShareErrorsFromSession
- VerifyCsrfToken
- SubstituteBindings

### 5. Route-Specific Middleware
- `auth:sanctum` or `auth:web`
- `verified` (email verification if enabled)
- `role_or_permission:admin` (custom)

### 6. Controller Execution
- Authenticated user available via `Auth::user()`
- Response generated (JSON or HTML)

### 7. Response Sent
- Middleware "after" hooks run
- Headers/cookies set
- Response sent to client

## Security Considerations

### 1. Token Storage
- Never store tokens in cookies or localStorage
- Use secure HTTP-only cookies or secure storage
- Rotate tokens periodically

### 2. CORS Configuration
- Whitelist only necessary frontend domains
- Use credentials mode for SPAs
- Test CORS headers in requests

### 3. Rate Limiting
- Add rate limiting middleware for public endpoints
- Configure per-user or per-IP limits
- Sanctuary built-in throttling support

### 4. CSRF Protection
- Enabled by default for web routes
- API routes don't need CSRF if stateless
- Configure in config/sanctum.php for SPAs

### 5. Role-Based Access Control
- Always check permissions server-side
- Never trust client-side role indicators
- Use granular permissions, not just roles

## Scaling Considerations

### Caching
- Role/permission data cached by Spatie
- Cache invalidated on role/permission changes
- Enable Redis cache for distributed systems

### Database
- Indexes on frequently queried columns
- Denormalize if needed for performance
- Monitor role/permission queries

### Tokens
- Personal access tokens can grow large
- Consider token pruning strategy
- Archive old tokens periodically

## Extension Points

### Adding New Roles
```php
$role = Role::create(['name' => 'editor']);
$role->givePermissionTo('edit articles', 'publish articles');
```

### Adding New Permissions
```php
Permission::create(['name' => 'edit articles']);
Permission::create(['name' => 'delete articles']);
```

### Custom Middleware
```php
Route::middleware('custom-auth')->group(function () {
    // Protected routes
});
```

### API Resource Transformations
```php
Route::apiResource('articles', ArticleController::class)
    ->middleware('auth:sanctum', 'verified');
```

## Monitoring & Logging

### Important Events to Log
- Failed login attempts
- Token generation/revocation
- Permission changes
- Unauthorized access attempts

### Audit Trail
- Log who assigned which roles/permissions
- Log when tokens are created/revoked
- Log failed authorization checks

### Performance Metrics
- Auth check latency
- Token validation time
- Permission lookup time

