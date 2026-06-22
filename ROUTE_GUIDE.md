# Route Structure & Public/Private Separation Guide

## 🗺️ Complete Route Overview

This document explains how routes are organized into **PUBLIC** (unauthenticated) and **PRIVATE** (authenticated) categories.

---

## 📍 Web Routes Structure

### File: `routes/web.php`

```php
// ============================================
// PUBLIC ROUTES (No Authentication Required)
// ============================================
GET  /                          → Welcome page (public landing)

// ============================================
// ADMIN AUTHENTICATION ROUTES (Public)
// These are public because login/logout should be accessible
// ============================================
GET  /admin/login               → Admin login form (public access)
POST /admin/login               → Process login (public access)

// ============================================
// PROTECTED ADMIN ROUTES (Requires Auth + Admin Role)
// Middleware: ['auth', 'admin']
// ============================================
POST /admin/logout              → Logout (authenticated users only)
GET  /admin/dashboard           → Dashboard with stats
GET  /admin/products            → Products list
GET  /admin/products/create     → Create product form
POST /admin/products            → Store product
GET  /admin/products/{id}       → Show product details
GET  /admin/products/{id}/edit  → Edit product form
PUT  /admin/products/{id}       → Update product
DELETE /admin/products/{id}     → Delete product

GET  /admin/categories          → Categories list
GET  /admin/categories/create   → Create category form
POST /admin/categories          → Store category
GET  /admin/categories/{id}     → Show category with products
GET  /admin/categories/{id}/edit → Edit category form
PUT  /admin/categories/{id}     → Update category
DELETE /admin/categories/{id}   → Delete category
```

### Access Control Matrix

| Route | Public? | Auth Required? | Admin Required? | Purpose |
|-------|---------|---|---|---|
| `/` | ✅ YES | ❌ NO | ❌ NO | Public landing page |
| `/admin/login` GET | ✅ YES | ❌ NO | ❌ NO | Display login form |
| `/admin/login` POST | ✅ YES | ❌ NO | ❌ NO | Process login |
| `/admin/logout` | ❌ NO | ✅ YES | ✅ YES | Admin logout |
| `/admin/dashboard` | ❌ NO | ✅ YES | ✅ YES | Admin dashboard |
| `/admin/products/*` | ❌ NO | ✅ YES | ✅ YES | Product management |
| `/admin/categories/*` | ❌ NO | ✅ YES | ✅ YES | Category management |

---

## 📡 API Routes Structure

### File: `routes/api.php`

```php
// ============================================
// PUBLIC API ROUTES (No Authentication)
// Accessible to anyone (mobile apps, frontend, public clients)
// ============================================
GET  /api/products              → List all products (paginated)
GET  /api/products/{product}    → Get product details

GET  /api/categories            → List all categories (paginated)
GET  /api/categories/{category} → Get category details

// ============================================
// PROTECTED API ROUTES (Admin Authentication)
// Requires Bearer token + admin role
// Middleware: ['auth:sanctum', 'admin']
// ============================================
POST   /api/products            → Create new product
PUT    /api/products/{product}  → Update product
PATCH  /api/products/{product}  → Update product (partial)
DELETE /api/products/{product}  → Delete product

POST   /api/categories          → Create new category
PUT    /api/categories/{category} → Update category
PATCH  /api/categories/{category} → Update category (partial)
DELETE /api/categories/{category} → Delete category
```

### API Access Control Matrix

| Endpoint | Method | Public? | Auth Required? | Admin Required? | Purpose |
|----------|--------|---------|---|---|---|
| `/api/products` | GET | ✅ YES | ❌ NO | ❌ NO | List products |
| `/api/products/{id}` | GET | ✅ YES | ❌ NO | ❌ NO | Get product |
| `/api/products` | POST | ❌ NO | ✅ YES | ✅ YES | Create product |
| `/api/products/{id}` | PUT | ❌ NO | ✅ YES | ✅ YES | Update product |
| `/api/products/{id}` | DELETE | ❌ NO | ✅ YES | ✅ YES | Delete product |
| `/api/categories` | GET | ✅ YES | ❌ NO | ❌ NO | List categories |
| `/api/categories/{id}` | GET | ✅ YES | ❌ NO | ❌ NO | Get category |
| `/api/categories` | POST | ❌ NO | ✅ YES | ✅ YES | Create category |
| `/api/categories/{id}` | PUT | ❌ NO | ✅ YES | ✅ YES | Update category |
| `/api/categories/{id}` | DELETE | ❌ NO | ✅ YES | ✅ YES | Delete category |

---

## 🔐 Security Implementation

### 1. Web Routes Security

**AdminMiddleware** (`app/Http/Middleware/AdminMiddleware.php`):
```php
public function handle(Request $request, Closure $next): Response
{
    // Check if user is authenticated AND has admin role
    if (!auth()->check() || auth()->user()->role !== 'admin') {
        return redirect()->route('admin.login');
    }

    return $next($request);
}
```

**Registration** in `bootstrap/app.php`:
```php
$middleware->alias([
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
]);
```

**Usage in Routes**:
```php
Route::middleware(['auth', 'admin'])->group(function () {
    // Protected routes here
});
```

---

### 2. API Routes Security

**Authentication**: Uses Laravel Sanctum for token-based authentication
```php
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Protected API routes here
});
```

**Role Check**: The `admin` middleware checks `user->role === 'admin'`

---

## 🎯 Design Decisions

### Why These Routes are PUBLIC:

1. **`/` (Home)** - Unauthenticated users need a landing page
2. **`/admin/login` GET** - Users need to see the login form
3. **`/admin/login` POST** - Users need to submit credentials
4. **`/api/products` GET** - Mobile apps and frontend need to display products
5. **`/api/categories` GET** - Need public access to browse categories

### Why These Routes are PROTECTED:

1. **`/admin/logout`** - Only authenticated admins should logout
2. **`/admin/dashboard`** - Dashboard contains sensitive statistics
3. **`/admin/products/*` (POST, PUT, DELETE)** - Only admins can modify products
4. **`/admin/categories/*` (POST, PUT, DELETE)** - Only admins can modify categories
5. **`/api/products` (POST, PUT, DELETE)** - API write operations require admin auth
6. **`/api/categories` (POST, PUT, DELETE)** - API write operations require admin auth

---

## 📊 Flow Diagrams

### Public User Flow:
```
┌─────────────────────┐
│  Public User        │
└──────────┬──────────┘
           │
           ├─→ GET  /               (View homepage)
           │
           ├─→ GET  /api/products   (List products)
           │
           ├─→ GET  /api/categories (List categories)
           │
           └─→ GET  /api/products/1 (View product details)
```

### Admin User Flow:
```
┌─────────────────────┐
│  Admin User         │
└──────────┬──────────┘
           │
           ├─→ GET  /admin/login              (Not authenticated yet)
           │
           ├─→ POST /admin/login              (Authenticate)
           │
           ├─→ GET  /admin/dashboard          (View dashboard)
           │
           ├─→ GET  /admin/products           (Manage products)
           ├─→ POST /admin/products           (Create product)
           ├─→ PUT  /admin/products/{id}      (Update product)
           ├─→ DELETE /admin/products/{id}    (Delete product)
           │
           ├─→ GET  /admin/categories         (Manage categories)
           ├─→ POST /admin/categories         (Create category)
           ├─→ PUT  /admin/categories/{id}    (Update category)
           ├─→ DELETE /admin/categories/{id}  (Delete category)
           │
           └─→ POST /admin/logout             (Logout)
```

### API Authentication Flow:
```
┌──────────────────────┐
│  Mobile App / Client │
└──────────┬───────────┘
           │
           ├─→ GET /api/products       ✅ (No token needed)
           │
           ├─→ POST /api/products      ❌ (Needs token + admin)
           │     ├─ Missing token → 401 Unauthorized
           │     └─ Non-admin token → 403 Forbidden
           │
           └─→ Bearer Token Usage:
               Authorization: Bearer {TOKEN}
```

---

## 🛠️ Implementation Details

### User Model Role Field:
```php
// Database: users table
protected $fillable = ['name', 'email', 'password', 'role'];
```

**Role Values:**
- `'admin'` - Administrator with full access
- `'user'` - Regular user with no admin access

### Authentication Guard:

**Web Routes:** Use Laravel's default `web` guard (session-based)
```php
Route::middleware(['auth'])->group(...); // Uses web guard
```

**API Routes:** Use Sanctum guard (token-based)
```php
Route::middleware(['auth:sanctum'])->group(...); // Uses sanctum
```

---

## 🔄 Token Generation for API (Future Implementation)

To implement API token generation, add this endpoint:

```php
// In routes/api.php - PUBLIC endpoint
POST /api/login
  ├─ Request: { email, password }
  ├─ Validation: Check credentials
  ├─ Check: user->role === 'admin'
  └─ Response: { token: user.createToken().plainTextToken }
```

---

## 📋 Middleware Execution Order

When accessing `/admin/products` (protected route):

```
Request
   ↓
Route Middleware: ['auth', 'admin']
   ↓
Auth Middleware (checks if authenticated)
   ├─ Not authenticated? → Redirect to login
   └─ Authenticated? → Continue
   ↓
Admin Middleware (checks if role === 'admin')
   ├─ Not admin? → Redirect to login
   └─ Is admin? → Continue
   ↓
Controller (ProductController)
   ↓
Response
```

---

## 📌 Best Practices Implemented

1. ✅ **Separation of Concerns:** Public and protected routes are clearly separated
2. ✅ **Role-Based Access:** Admin middleware checks user role
3. ✅ **Consistent Response Format:** API responses follow standard JSON format
4. ✅ **RESTful Design:** API follows REST principles
5. ✅ **Blade Templates:** Web routes use traditional server-side rendering
6. ✅ **CSRF Protection:** Web forms protected with CSRF tokens
7. ✅ **Input Validation:** All inputs validated before processing
8. ✅ **Error Handling:** Proper error codes and messages

---

## 🔗 Quick Reference

### To Access Admin Dashboard:
```
1. Navigate to: http://localhost:8000/admin/login
2. Enter: admin@example.com / password
3. Access: http://localhost:8000/admin/dashboard
```

### To Test API (Public):
```bash
curl http://localhost:8000/api/products
curl http://localhost:8000/api/categories
```

### To Test API (Protected):
```bash
# Without token → 401 Unauthorized
curl -X POST http://localhost:8000/api/products

# With token (future)
curl -H "Authorization: Bearer {TOKEN}" \
     -X POST http://localhost:8000/api/products
```

---

## 🎓 Learning Resources

- **Laravel Middleware:** https://laravel.com/docs/routing#middleware
- **Laravel Sanctum:** https://laravel.com/docs/sanctum
- **RESTful API Design:** https://restfulapi.net
- **HTTP Status Codes:** https://httpwg.org/specs/rfc7231.html#status.codes
