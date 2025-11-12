# Quick Start Guide

## Installation & Setup

### Prerequisites
- Docker & Docker Compose
- Git

### Clone & Start

```bash
cd /path/to/gap
docker-compose up -d
```

This starts:
- PHP-FPM application server
- Nginx web server (port 8899)
- MySQL database
- phpMyAdmin (port 8080)

### Verify Installation

```bash
# Check containers are running
docker-compose ps

# Run migrations (if needed)
docker-compose exec -T app php artisan migrate

# Seed default data
docker-compose exec -T app php artisan db:seed
```

## Web Authentication

### Access the App

1. Open http://localhost:8899 in your browser
2. Click "Register" to create a new account
3. Or login with test credentials:
   - Email: `test@example.com`
   - Password: `password`

### Web Routes

- `/` - Home page
- `/auth/register` - Create new account
- `/auth/login` - Login
- `/admin` - Admin dashboard (requires admin role)

## API Authentication

### Generate Token

```bash
# Get a token for the test user
docker-compose exec -T app php artisan tinker

# Inside tinker:
$user = App\Models\User::find(1);
$token = $user->createToken('my-token')->plainTextToken;
echo $token;
```

### Use Token in Requests

```bash
# Get current user
curl -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost:8899/api/user

# Access admin endpoint
curl -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost:8899/api/admin/stats
```

## Common Tasks

### Create a New Role

```bash
docker-compose exec -T app php artisan tinker

# Inside tinker:
$role = Spatie\Permission\Models\Role::create(['name' => 'editor']);
```

### Assign Role to User

```bash
# Inside tinker:
$user = App\Models\User::find(1);
$user->assignRole('editor');

// Or multiple roles:
$user->syncRoles(['admin', 'editor']);
```

### Create a New Permission

```bash
# Inside tinker:
Spatie\Permission\Models\Permission::create(['name' => 'edit articles']);
```

### Give Permission to Role

```bash
# Inside tinker:
$role = Spatie\Permission\Models\Role::findByName('editor');
$role->givePermissionTo('edit articles');
```

### Check User's Roles/Permissions

```bash
# Inside tinker:
$user = App\Models\User::find(1);
$user->roles;           // Get user's roles
$user->permissions;     // Get user's permissions
$user->hasRole('admin');        // Check if has role
$user->hasPermissionTo('edit articles'); // Check permission
```

## Database

### Connect via phpMyAdmin

1. Open http://localhost:8080 in browser
2. Username: `root`
3. Password: `root`
4. Select database: `laravel`

### Useful Queries

```sql
-- View all users
SELECT * FROM users;

-- View all roles
SELECT * FROM roles;

-- View user roles
SELECT u.email, r.name 
FROM users u
JOIN model_has_roles mhr ON u.id = mhr.model_id
JOIN roles r ON mhr.role_id = r.id;

-- View all personal access tokens
SELECT * FROM personal_access_tokens;
```

## Artisan Commands

### Authentication & Authorization

```bash
# Create a new user
docker-compose exec -T app php artisan tinker
# Then: User::create(['name' => 'John', 'email' => 'john@example.com', 'password' => bcrypt('password')])

# Create roles/permissions
docker-compose exec -T app php artisan tinker
# Then: Role::create(['name' => 'admin'])
```

### Database

```bash
# Run migrations
docker-compose exec -T app php artisan migrate

# Seed database
docker-compose exec -T app php artisan db:seed

# Rollback migrations
docker-compose exec -T app php artisan migrate:rollback

# Fresh migration + seed
docker-compose exec -T app php artisan migrate:fresh --seed
```

### Cache & Config

```bash
# Clear all caches
docker-compose exec -T app php artisan optimize:clear

# Clear specific caches
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan route:clear
```

### Testing

```bash
# Run tests
docker-compose exec -T app php artisan test

# Run tests with coverage
docker-compose exec -T app php artisan test --coverage

# Run specific test file
docker-compose exec -T app php artisan test tests/Feature/Auth/AuthenticationTest.php
```

## Development Workflow

### Make a Code Change

1. Edit file in `gap-app/` directory
2. Laravel auto-reloads in development
3. Test in browser or API

### Add New API Endpoint

1. Create route in `routes/api.php`:
   ```php
   Route::middleware(['auth:sanctum', 'role_or_permission:admin'])->group(function () {
       Route::get('/api/reports', [ReportController::class, 'index']);
   });
   ```

2. Create controller:
   ```bash
   docker-compose exec -T app php artisan make:controller ReportController
   ```

3. Implement the action and test with curl

### Run Tests

```bash
docker-compose exec -T app php artisan test

# Or run specific test
docker-compose exec -T app php artisan test tests/Feature/AdminTest.php
```

## Environment Configuration

### Key .env Variables

```env
# App
APP_NAME="GAP"
APP_DEBUG=true
APP_URL=http://localhost:8899

# Database
DB_HOST=mysql
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=root

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost:3000,localhost:8000,127.0.0.1:3000,127.0.0.1:8000
SANCTUM_TOKEN_PREFIX=

# Mail (optional)
MAIL_MAILER=log
```

## Troubleshooting

### Port Already in Use

If port 8899 is in use, change in `docker-compose.yaml`:
```yaml
ports:
  - "8900:80"  # Change to 8900
```

### Container Won't Start

```bash
# Check logs
docker-compose logs app

# Rebuild container
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Database Connection Error

```bash
# Verify MySQL is running
docker-compose ps mysql

# Check database exists
docker-compose exec -T mysql mysql -uroot -proot -e "SHOW DATABASES;"
```

### Permission Issues

```bash
# Ensure app user can write to storage
docker-compose exec -T app chmod -R 775 storage bootstrap/cache
```

## Useful Resources

- **Laravel Docs**: https://laravel.com/docs
- **Breeze Docs**: https://laravel.com/docs/11.x/starter-kits#breeze
- **Sanctum Docs**: https://laravel.com/docs/11.x/sanctum
- **Spatie Permissions**: https://spatie.be/docs/laravel-permission/v6/introduction

## Next Steps

- [ ] Set up frontend (Vue, React, etc.)
- [ ] Configure CORS for your frontend domain
- [ ] Add more roles and permissions
- [ ] Create feature tests for new endpoints
- [ ] Set up error logging (Sentry, etc.)
- [ ] Configure email notifications
- [ ] Deploy to staging/production

## Support

For issues or questions:
1. Check the documentation files in the repo
2. Review Laravel documentation
3. Check Docker logs: `docker-compose logs [service-name]`
4. Use `php artisan tinker` for database inspection

