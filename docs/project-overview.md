# 📋 Project Overview — Cafe Pro ERP

## 1. Project Identity

| Field | Value |
|---|---|
| **Name** | Cafe Pro |
| **Type** | Niche ERP / POS — Cafe & Restaurant |
| **Primary Goal** | Manage daily sales, inventory, staff shifts, and expenses to calculate **True Net Profit** |
| **Target Users** | Cafe owners, managers, cashiers, waiters |

---

## 2. Technology Stack

### Backend
- **PHP 8.2+** with **Laravel 11**
- **MySQL 8.0** — relational database
- **Laravel Sanctum** — API authentication
- **Spatie Laravel-Permission** — RBAC (Roles & Permissions)
- **Laravel Queues** with Redis — background jobs (stock deduction, notifications)
- **Laravel Telescope** — local debugging (dev only)

### Frontend
- **Vue.js 3** with **Inertia.js** (recommended) OR **Livewire 3** (alternative)
- **Tailwind CSS** — utility-first styling
- **Pinia** — state management (Vue)

### Infrastructure
- **Laravel Horizon** — queue monitoring
- **Spatie Media Library** — image/file uploads
- **barryvdh/laravel-dompdf** or **mike42/escpos-php** — receipt printing

---

## 3. System Roles (RBAC)

| Role | Access Scope |
|---|---|
| **Admin** | Full access — all modules, settings, users |
| **Manager** | Reports, stock management, expenses, menu |
| **Cashier** | POS, shifts, payments, order history |
| **Waiter** | Create orders, view table status only |

> Permissions are granular and defined per route/endpoint using Spatie.

---

## 4. Core Modules Summary

### A. Authentication & RBAC
Role-based access using Spatie Laravel-Permission. Every route is guarded.

### B. Menu & Category Management
Hierarchical structure: Categories → Sub-categories → Products with price, cost, tax, and add-ons.

### C. Inventory & Recipe System (BOM — Bill of Materials)
- Track raw materials (ingredients) with units and stock quantities
- Map products to ingredients (recipes)
- Auto-deduct stock when an order is completed
- Track wastage as an expense

### D. POS & Order Lifecycle
- Support Dine-in (table), Takeaway, Delivery
- Order statuses: Pending → Preparing → Ready → Completed / Cancelled
- Payment methods: Cash, Card, Split Payment

### E. Shift Management
- Cashier must open a shift before accessing POS
- Opening balance declared at shift start
- Closing balance recorded at shift end
- System calculates Expected vs Actual cash (Over/Short)

### F. Financial Management
- Expense categories: Fixed, Variable, Wastage
- Net Profit = Total Sales − (COGS + Operating Expenses)
- COGS is automatically calculated from ingredient costs

### G. Invoicing & Thermal Printing
- Clean thermal-receipt format
- QR Code for tax authority compliance

---

## 5. Non-Functional Requirements

| Requirement | Detail |
|---|---|
| **Soft Deletes** | Products, Ingredients, Orders must use `SoftDeletes` |
| **Service Layer** | All business logic in `app/Services/`, not Controllers |
| **API Design** | RESTful JSON API — versioned under `/api/v1/` |
| **Validation** | Use Laravel Form Requests for all input |
| **Authorization** | Use Laravel Policies + Spatie middleware |
| **Stock Guard** | (Configurable) Block checkout if stock is insufficient |
| **Shift Guard** | Block all financial transactions if no active shift |
| **Audit Trail** | Use `spatie/laravel-activitylog` for critical actions |

---

## 6. Project Conventions

### Naming Conventions
```
Models:       PascalCase        → Product, OrderItem, RawMaterial
Controllers:  PascalCase + Type → ProductController, Api/V1/OrderController
Services:     PascalCase + Type → OrderService, StockService, ProfitService
Requests:     Descriptive       → StoreProductRequest, UpdateShiftRequest
Migrations:   snake_case        → create_products_table, add_cost_to_products
```

### Folder Structure
```
app/
├── Http/
│   ├── Controllers/Api/V1/    ← All API controllers
│   ├── Requests/              ← Form Request classes
│   └── Resources/             ← API Resources (transformers)
├── Models/                    ← Eloquent models
├── Services/                  ← Business logic services
├── Policies/                  ← Authorization policies
└── Enums/                     ← PHP 8.1+ Enums (OrderStatus, PaymentType...)
```
