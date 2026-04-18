# 🔐 Module 01 — Authentication & RBAC

## Overview

Authentication is handled by **Laravel Sanctum** (token-based for API).  
Authorization is handled by **Spatie Laravel-Permission** (roles + granular permissions).

---

## Roles

| Role | Description |
|---|---|
| `admin` | Full system access |
| `manager` | Reports, stock, menu, expenses |
| `cashier` | POS, shifts, orders, payments |
| `waiter` | Create orders, view menu only |

---

## Permissions List

```
Auth & Users
  view users
  create users
  edit users
  delete users

Categories & Products
  view categories
  manage categories
  view products
  manage products

Inventory
  view ingredients
  manage ingredients
  view recipes
  manage recipes
  log wastage

Shifts
  open shift
  close shift
  view shifts
  view all shifts       ← manager/admin only

Orders
  create orders
  view orders
  update order status
  cancel orders
  view all orders

Payments
  process payments

Expenses
  view expenses
  manage expenses

Reports
  view reports
  export reports

Settings
  manage settings
```

---

## Role → Permission Matrix

| Permission | Admin | Manager | Cashier | Waiter |
|---|:---:|:---:|:---:|:---:|
| manage users | ✅ | ❌ | ❌ | ❌ |
| manage categories | ✅ | ✅ | ❌ | ❌ |
| manage products | ✅ | ✅ | ❌ | ❌ |
| manage ingredients | ✅ | ✅ | ❌ | ❌ |
| manage recipes | ✅ | ✅ | ❌ | ❌ |
| log wastage | ✅ | ✅ | ❌ | ❌ |
| open shift | ✅ | ✅ | ✅ | ❌ |
| close shift | ✅ | ✅ | ✅ | ❌ |
| view all shifts | ✅ | ✅ | ❌ | ❌ |
| create orders | ✅ | ✅ | ✅ | ✅ |
| update order status | ✅ | ✅ | ✅ | ✅ |
| cancel orders | ✅ | ✅ | ✅ | ❌ |
| process payments | ✅ | ✅ | ✅ | ❌ |
| manage expenses | ✅ | ✅ | ❌ | ❌ |
| view reports | ✅ | ✅ | ❌ | ❌ |
| export reports | ✅ | ✅ | ❌ | ❌ |
| manage settings | ✅ | ❌ | ❌ | ❌ |

---

## API Endpoints

| Method | Endpoint | Permission | Description |
|---|---|---|---|
| POST | `/api/v1/auth/login` | public | Login, returns Sanctum token |
| POST | `/api/v1/auth/logout` | authenticated | Revoke current token |
| GET | `/api/v1/auth/me` | authenticated | Get current user with role |
| GET | `/api/v1/users` | view users | List all users |
| POST | `/api/v1/users` | create users | Create new user |
| GET | `/api/v1/users/{id}` | view users | Get user details |
| PUT | `/api/v1/users/{id}` | edit users | Update user |
| DELETE | `/api/v1/users/{id}` | delete users | Soft-delete user |

---

## Controller Spec: `AuthController`

```php
namespace App\Http\Controllers\Api\V1;

class AuthController extends Controller
{
    // POST /auth/login
    public function login(LoginRequest $request): JsonResponse
    {
        // 1. Validate credentials
        // 2. Check user is_active
        // 3. Create Sanctum token
        // 4. Return user + token + role + permissions
    }

    // POST /auth/logout
    public function logout(Request $request): JsonResponse
    {
        // Revoke current token
        $request->user()->currentAccessToken()->delete();
    }

    // GET /auth/me
    public function me(Request $request): JsonResponse
    {
        // Return user with roles and permissions loaded
        return response()->json([
            'user' => $request->user()->load('roles'),
            'permissions' => $request->user()->getAllPermissions()->pluck('name'),
        ]);
    }
}
```

---

## Request Spec: `LoginRequest`

```php
public function rules(): array
{
    return [
        'email'    => ['required', 'email'],
        'password' => ['required', 'string'],
    ];
}
```

---

## Middleware Usage

```php
// routes/api.php

Route::middleware('auth:sanctum')->group(function () {

    // Require specific permission
    Route::middleware('permission:manage products')->group(function () {
        Route::apiResource('products', ProductController::class);
    });

    // Require role
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
    });

});
```

---

## Seeder: `RolesAndPermissionsSeeder`

```php
public function run(): void
{
    // Reset cached roles and permissions
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    $permissions = [
        'view users', 'create users', 'edit users', 'delete users',
        'view categories', 'manage categories',
        'view products', 'manage products',
        'view ingredients', 'manage ingredients',
        'view recipes', 'manage recipes', 'log wastage',
        'open shift', 'close shift', 'view shifts', 'view all shifts',
        'create orders', 'view orders', 'update order status', 'cancel orders', 'view all orders',
        'process payments',
        'view expenses', 'manage expenses',
        'view reports', 'export reports',
        'manage settings',
    ];

    foreach ($permissions as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
    }

    $admin = Role::firstOrCreate(['name' => 'admin']);
    $admin->syncPermissions(Permission::all());

    $manager = Role::firstOrCreate(['name' => 'manager']);
    $manager->syncPermissions([
        'view categories', 'manage categories',
        'view products', 'manage products',
        'view ingredients', 'manage ingredients',
        'view recipes', 'manage recipes', 'log wastage',
        'open shift', 'close shift', 'view shifts', 'view all shifts',
        'create orders', 'view orders', 'update order status', 'cancel orders', 'view all orders',
        'process payments',
        'view expenses', 'manage expenses',
        'view reports', 'export reports',
    ]);

    $cashier = Role::firstOrCreate(['name' => 'cashier']);
    $cashier->syncPermissions([
        'open shift', 'close shift', 'view shifts',
        'create orders', 'view orders', 'update order status', 'cancel orders',
        'process payments',
    ]);

    $waiter = Role::firstOrCreate(['name' => 'waiter']);
    $waiter->syncPermissions([
        'create orders', 'view orders', 'update order status',
    ]);
}
```

---

## Login Response Example

```json
{
  "user": {
    "id": 1,
    "name": "Ahmed Admin",
    "email": "admin@cafepro.com",
    "roles": ["admin"]
  },
  "token": "1|abc123xyz...",
  "permissions": [
    "manage products",
    "manage ingredients",
    "view reports",
    "..."
  ]
}
```
