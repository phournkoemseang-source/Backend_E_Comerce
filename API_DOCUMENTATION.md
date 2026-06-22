# E-Commerce Admin API Documentation

## Overview
This API provides complete CRUD operations for managing products and categories in the e-commerce platform. The API is organized into **PUBLIC** and **PROTECTED** endpoints.

---

## 📋 Route Structure & Access Control

### PUBLIC ROUTES (No Authentication Required)
These endpoints can be accessed by anyone, including frontend applications and mobile clients.

```
GET  /api/products          - List all products (paginated)
GET  /api/products/{id}     - Get product details
GET  /api/categories        - List all categories (paginated)
GET  /api/categories/{id}   - Get category details
```

### PROTECTED ROUTES (Admin Authentication Required)
These endpoints require authentication with an admin token and admin role.

```
POST   /api/products        - Create new product (Admin only)
PUT    /api/products/{id}   - Update product (Admin only)
DELETE /api/products/{id}   - Delete product (Admin only)

POST   /api/categories      - Create new category (Admin only)
PUT    /api/categories/{id} - Update category (Admin only)
DELETE /api/categories/{id} - Delete category (Admin only)
```

---

## 🔐 Authentication

### Obtaining Admin Token (Sanctum)

**Endpoint:** `POST /api/token` (You need to implement this or use web login first)

For this implementation, use the **Web Admin Login** to access the dashboard, then implement token authentication for API.

**Web Login Credentials:**
```
Email: admin@example.com
Password: password
```

---

## 📚 API Endpoints Details

### 1. GET /api/products
**Description:** Retrieve paginated list of all products

**Authentication:** Not required
**Method:** GET
**URL:** `http://localhost:8000/api/products`

**Response (Success - 200):**
```json
{
  "success": true,
  "message": "Products retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "category_id": 1,
        "name": "Electronics Product 1",
        "description": "This is a high-quality Electronics product...",
        "price": "45.23",
        "stock": 50,
        "created_at": "2026-06-22T20:10:00.000000Z",
        "updated_at": "2026-06-22T20:10:00.000000Z",
        "category": {
          "id": 1,
          "name": "Electronics",
          "slug": "electronics",
          "created_at": "2026-06-22T20:10:00.000000Z",
          "updated_at": "2026-06-22T20:10:00.000000Z"
        }
      }
    ],
    "from": 1,
    "last_page": 3,
    "per_page": 15,
    "to": 15,
    "total": 25
  }
}
```

---

### 2. GET /api/products/{id}
**Description:** Get details of a specific product

**Authentication:** Not required
**Method:** GET
**URL:** `http://localhost:8000/api/products/1`

**Response (Success - 200):**
```json
{
  "success": true,
  "message": "Product retrieved successfully",
  "data": {
    "id": 1,
    "category_id": 1,
    "name": "Electronics Product 1",
    "description": "This is a high-quality Electronics product...",
    "price": "45.23",
    "stock": 50,
    "created_at": "2026-06-22T20:10:00.000000Z",
    "updated_at": "2026-06-22T20:10:00.000000Z",
    "category": {
      "id": 1,
      "name": "Electronics",
      "slug": "electronics"
    }
  }
}
```

---

### 3. POST /api/products
**Description:** Create a new product

**Authentication:** Required (Admin token)
**Method:** POST
**URL:** `http://localhost:8000/api/products`

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {ADMIN_TOKEN}
```

**Request Body:**
```json
{
  "category_id": 1,
  "name": "New Laptop",
  "description": "High-performance laptop with 16GB RAM and SSD storage",
  "price": 1299.99,
  "stock": 25
}
```

**Response (Success - 201):**
```json
{
  "success": true,
  "message": "Product created successfully",
  "data": {
    "category_id": 1,
    "name": "New Laptop",
    "description": "High-performance laptop with 16GB RAM and SSD storage",
    "price": "1299.99",
    "stock": 25,
    "updated_at": "2026-06-22T20:15:00.000000Z",
    "created_at": "2026-06-22T20:15:00.000000Z",
    "id": 26,
    "category": {
      "id": 1,
      "name": "Electronics",
      "slug": "electronics"
    }
  }
}
```

**Validation Errors (422):**
```json
{
  "message": "The category id field is required. (and 1 more error)",
  "errors": {
    "category_id": ["The category id field is required."],
    "price": ["The price field is required."]
  }
}
```

---

### 4. PUT /api/products/{id}
**Description:** Update an existing product

**Authentication:** Required (Admin token)
**Method:** PUT
**URL:** `http://localhost:8000/api/products/1`

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {ADMIN_TOKEN}
```

**Request Body:**
```json
{
  "name": "Updated Laptop",
  "price": 1199.99,
  "stock": 20
}
```

**Response (Success - 200):**
```json
{
  "success": true,
  "message": "Product updated successfully",
  "data": {
    "id": 1,
    "category_id": 1,
    "name": "Updated Laptop",
    "description": "High-performance laptop...",
    "price": "1199.99",
    "stock": 20,
    "created_at": "2026-06-22T20:10:00.000000Z",
    "updated_at": "2026-06-22T20:16:00.000000Z",
    "category": { /* category data */ }
  }
}
```

---

### 5. DELETE /api/products/{id}
**Description:** Delete a product

**Authentication:** Required (Admin token)
**Method:** DELETE
**URL:** `http://localhost:8000/api/products/1`

**Headers:**
```
Authorization: Bearer {ADMIN_TOKEN}
```

**Response (Success - 200):**
```json
{
  "success": true,
  "message": "Product deleted successfully"
}
```

---

### 6. GET /api/categories
**Description:** Retrieve paginated list of all categories

**Authentication:** Not required
**Method:** GET
**URL:** `http://localhost:8000/api/categories`

**Response (Success - 200):**
```json
{
  "success": true,
  "message": "Categories retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Electronics",
        "slug": "electronics",
        "products_count": 5,
        "created_at": "2026-06-22T20:10:00.000000Z",
        "updated_at": "2026-06-22T20:10:00.000000Z"
      }
    ],
    "from": 1,
    "last_page": 1,
    "per_page": 15,
    "to": 5,
    "total": 5
  }
}
```

---

### 7. GET /api/categories/{id}
**Description:** Get details of a specific category

**Authentication:** Not required
**Method:** GET
**URL:** `http://localhost:8000/api/categories/1`

**Response (Success - 200):**
```json
{
  "success": true,
  "message": "Category retrieved successfully",
  "data": {
    "id": 1,
    "name": "Electronics",
    "slug": "electronics",
    "products_count": 5,
    "created_at": "2026-06-22T20:10:00.000000Z",
    "updated_at": "2026-06-22T20:10:00.000000Z"
  }
}
```

---

### 8. POST /api/categories
**Description:** Create a new category

**Authentication:** Required (Admin token)
**Method:** POST
**URL:** `http://localhost:8000/api/categories`

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {ADMIN_TOKEN}
```

**Request Body:**
```json
{
  "name": "Furniture"
}
```

**Response (Success - 201):**
```json
{
  "success": true,
  "message": "Category created successfully",
  "data": {
    "name": "Furniture",
    "slug": "furniture",
    "updated_at": "2026-06-22T20:17:00.000000Z",
    "created_at": "2026-06-22T20:17:00.000000Z",
    "id": 6,
    "products_count": 0
  }
}
```

---

### 9. PUT /api/categories/{id}
**Description:** Update an existing category

**Authentication:** Required (Admin token)
**Method:** PUT
**URL:** `http://localhost:8000/api/categories/1`

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {ADMIN_TOKEN}
```

**Request Body:**
```json
{
  "name": "Computer & Electronics"
}
```

**Response (Success - 200):**
```json
{
  "success": true,
  "message": "Category updated successfully",
  "data": {
    "id": 1,
    "name": "Computer & Electronics",
    "slug": "computer-electronics",
    "products_count": 5,
    "created_at": "2026-06-22T20:10:00.000000Z",
    "updated_at": "2026-06-22T20:18:00.000000Z"
  }
}
```

---

### 10. DELETE /api/categories/{id}
**Description:** Delete a category (Only if it has no products)

**Authentication:** Required (Admin token)
**Method:** DELETE
**URL:** `http://localhost:8000/api/categories/6`

**Headers:**
```
Authorization: Bearer {ADMIN_TOKEN}
```

**Response (Success - 200):**
```json
{
  "success": true,
  "message": "Category deleted successfully"
}
```

**Error Response (400):** If category has products
```json
{
  "success": false,
  "message": "Cannot delete category with products"
}
```

---

## 🌐 Web Routes (Admin Dashboard)

The web routes are protected and require admin authentication via session.

### Login & Dashboard
```
GET  /admin/login             - Admin login page
POST /admin/login             - Login submission
POST /admin/logout            - Logout
GET  /admin/dashboard         - Admin dashboard
```

### Products Management
```
GET    /admin/products        - Products list
GET    /admin/products/create - Create product form
POST   /admin/products        - Store product
GET    /admin/products/{id}   - Show product
GET    /admin/products/{id}/edit - Edit form
PUT    /admin/products/{id}   - Update product
DELETE /admin/products/{id}   - Delete product
```

### Categories Management
```
GET    /admin/categories        - Categories list
GET    /admin/categories/create - Create category form
POST   /admin/categories        - Store category
GET    /admin/categories/{id}   - Show category with products
GET    /admin/categories/{id}/edit - Edit form
PUT    /admin/categories/{id}   - Update category
DELETE /admin/categories/{id}   - Delete category
```

---

## 📮 Postman Collection URLs

### PUBLIC Endpoints (Test without authentication)
- **List Products:** `GET http://localhost:8000/api/products`
- **Get Product:** `GET http://localhost:8000/api/products/1`
- **List Categories:** `GET http://localhost:8000/api/categories`
- **Get Category:** `GET http://localhost:8000/api/categories/1`

### PROTECTED Endpoints (Test with admin authentication)
- **Create Product:** `POST http://localhost:8000/api/products`
- **Update Product:** `PUT http://localhost:8000/api/products/1`
- **Delete Product:** `DELETE http://localhost:8000/api/products/1`
- **Create Category:** `POST http://localhost:8000/api/categories`
- **Update Category:** `PUT http://localhost:8000/api/categories/1`
- **Delete Category:** `DELETE http://localhost:8000/api/categories/6`

---

## 🔑 Testing Admin Routes

### Step 1: Login to Dashboard
Navigate to: `http://localhost:8000/admin/login`
- Email: `admin@example.com`
- Password: `password`

### Step 2: Create/Manage Products
- Products List: `http://localhost:8000/admin/products`
- Create: `http://localhost:8000/admin/products/create`
- Edit: `http://localhost:8000/admin/products/1/edit`

### Step 3: Create/Manage Categories
- Categories List: `http://localhost:8000/admin/categories`
- Create: `http://localhost:8000/admin/categories/create`
- Edit: `http://localhost:8000/admin/categories/1/edit`

---

## 📊 Response Format

All API responses follow this standard format:

**Success Response:**
```json
{
  "success": true,
  "message": "Operation description",
  "data": { /* operation data */ }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error description",
  "errors": { /* validation errors if applicable */ }
}
```

---

## 🛡️ Security Notes

1. **Public Routes:** Only allow reading products and categories
2. **Protected Routes:** Require both authentication AND admin role
3. **CORS:** Configure as needed for frontend integration
4. **Rate Limiting:** Consider adding rate limiting for production
5. **Input Validation:** All inputs are validated on the backend
6. **Password Hashing:** All passwords are bcrypt hashed

---

## 🚀 Getting Started

1. **Start the server:**
   ```bash
   php artisan serve
   ```

2. **Access Admin Dashboard:**
   - URL: `http://localhost:8000/admin/login`
   - Credentials: `admin@example.com` / `password`

3. **Test API in Postman:**
   - Import the endpoints listed above
   - For protected routes, you'll need to implement token generation

---

## 📝 Notes

- All dates are returned in ISO 8601 format
- Pagination: Default 15 items per page
- Prices are decimal(10, 2) format
- Categories auto-generate slugs from names (slug cannot be manually changed)
