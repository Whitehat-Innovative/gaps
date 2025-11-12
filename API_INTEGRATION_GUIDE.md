# API Integration Guide

## Overview

Your Laravel application now integrates **Laravel Breeze**, **Sanctum**, and **Spatie Roles & Permissions** to provide a unified authentication and authorization system for both web and API access.

## Architecture

### Authentication Layers

1. **Web Authentication** (Session-based)
   - Traditional Laravel session cookies
   - Protected by CSRF tokens
   - Used for browser-based access

2. **API Authentication** (Token-based)
   - Sanctum personal access tokens
   - Bearer token in `Authorization` header
   - Used for programmatic/mobile access

3. **Authorization** (Role & Permission-based)
   - Spatie roles & permissions
   - Middleware: `role_or_permission:admin`
   - Works on both web and API routes

## Getting Started

### 1. Create an API Token

For a user (e.g., `test@example.com`), generate a token via Tinker:

```bash
docker-compose exec -T app php artisan tinker

$user = App\Models\User::find(1); // or find by email
$token = $user->createToken('my-app-token')->plainTextToken;
echo $token;
```

Store the token securely in your client application.

### 2. Authenticate API Requests

Include the token in the `Authorization` header:

```bash
curl -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  http://localhost:8899/api/user
```

### 3. Use Role-Protected Endpoints

Endpoints like `/api/admin/stats` require both authentication AND the `admin` role:

```bash
# This will succeed if the user has the admin role
curl -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  http://localhost:8899/api/admin/stats

# Example response:
{
  "message": "Admin statistics endpoint",
  "user": { ... },
  "timestamp": "2025-11-12T08:40:20.940706Z"
}
```

## Available Endpoints

### Public Endpoints

- `POST /auth/register` - Register a new user
- `POST /auth/login` - Login with email/password

### Authenticated Endpoints (Web & API)

- `GET /api/user` - Get current authenticated user
- `POST /auth/logout` - Logout (web session)

### Admin-Protected Endpoints

- `GET /api/admin/stats` - Admin statistics (requires `admin` role)
- `GET /admin` - Admin dashboard (web, requires `admin` role)

## Middleware

### Route Middleware

- `auth:sanctum` - Authenticates using Sanctum tokens
- `role_or_permission:admin` - Checks if user has `admin` role or permission
- `role_or_permission:admin|manager` - Checks for any of these roles

### Example Protected Route

```php
Route::middleware(['auth:sanctum', 'role_or_permission:admin'])->group(function () {
    Route::get('/api/admin/stats', [AdminController::class, 'stats']);
});
```

## Database

### Tables

- `users` - User accounts
- `roles` - Role definitions (e.g., 'admin', 'editor')
- `permissions` - Permission definitions
- `model_has_roles` - User-to-role assignments
- `model_has_permissions` - User-to-permission assignments
- `personal_access_tokens` - Sanctum API tokens

### Default Test Data

The `RolePermissionSeeder` creates:

- Role: `admin`
- User: `test@example.com` with password `password` and `admin` role

## Token Management

### Create a Token

```php
$token = $user->createToken('token-name')->plainTextToken;
```

### List User's Tokens

```php
$tokens = $user->tokens;
```

### Revoke a Token

```php
$user->tokens()->where('name', 'token-name')->delete();
```

### Revoke All Tokens

```php
$user->tokens()->delete();
```

## Configuration

### Sanctum Config (`config/sanctum.php`)

- `stateful` - Domains that receive session cookies (includes localhost:3000, localhost:8000, etc.)
- `guard` - Authentication guards checked before token auth (default: `web`)
- `expiration` - Token expiration in minutes (default: `null` = never expires)
- `token_prefix` - Security scanning prefix (default: empty)

### Roles & Permissions Config (`config/permission.php`)

- `database.table_names` - Customize table names if needed
- `permission_models` - Customize permission model if needed

## Development Workflow

### Add a New Protected API Endpoint

```php
// routes/api.php
Route::middleware(['auth:sanctum', 'role_or_permission:editor'])->group(function () {
    Route::post('/api/articles', [ArticleController::class, 'store']);
});
```

### Create a New Role

```php
// Via Seeder
app('admin')->givePermissionTo('edit articles');
$editor = Role::create(['name' => 'editor']);
$editor->givePermissionTo('edit articles');
```

### Assign Role to User

```php
$user->assignRole('admin');
// or
$user->syncRoles(['admin', 'editor']);
```

## Security Best Practices

1. **Store tokens securely** - Never commit tokens to version control
2. **Use HTTPS** - Always use HTTPS in production (tokens in Authorization headers)
3. **Rotate tokens** - Regularly revoke and regenerate tokens
4. **Limit token scope** - Create tokens with minimal required permissions
5. **CORS** - Configure CORS properly for your frontend domain
6. **Rate limiting** - Add rate limiting to API endpoints in production

## Testing

### Feature Test Example

```php
public function test_admin_can_access_stats_endpoint()
{
    $user = User::factory()->create();
    $user->assignRole('admin');
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->getJson('/api/admin/stats', [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['message', 'user', 'timestamp']);
}
```

## Troubleshooting

### Token Not Working

1. Check token format: `Bearer YOUR_TOKEN`
2. Verify token hasn't expired (check `expires_at` in DB)
3. Confirm user still exists and is active
4. Check middleware stack in route

### Role Check Fails

1. Verify user has the role: `$user->hasRole('admin')`
2. Check database: `select * from model_has_roles where model_id = X;`
3. Ensure middleware alias is registered: `php artisan route:list`

### CORS Issues

1. Check `config/cors.php` - ensure your frontend domain is whitelisted
2. Sanctum automatically handles CORS for stateful requests
3. For SPA: configure `SANCTUM_STATEFUL_DOMAINS` in `.env`

## Next Steps

- [ ] Add more roles (e.g., `editor`, `viewer`)
- [ ] Create permissions (e.g., `edit articles`, `delete users`)
- [ ] Build frontend SPA with authentication flow
- [ ] Set up rate limiting on API endpoints
- [ ] Add API documentation (Swagger/OpenAPI)
- [ ] Configure CORS for your frontend domain
- [ ] Set up token expiration and refresh flow

