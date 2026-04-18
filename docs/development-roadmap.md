# 🗺️ Development Roadmap — Cafe Pro ERP

> Build phases are ordered by dependency. Each phase must be completed and tested before starting the next.

---

## Phase 1 — Foundation & Auth (Week 1–2)

### Goals
- Laravel project setup with all base packages
- Database connection and initial migrations
- Complete RBAC with Roles & Permissions
- Basic Category and Product management

### Tasks
- [ ] Install Laravel 11 fresh project
- [ ] Install packages: `spatie/laravel-permission`, `laravel/sanctum`, `spatie/laravel-activitylog`
- [ ] Configure `.env` (DB, Queue, Mail)
- [ ] Create and run migrations in correct order (see `migrations-guide.md`)
- [ ] Seed roles: Admin, Manager, Cashier, Waiter
- [ ] Seed permissions per module (see `auth-rbac.md`)
- [ ] Build `AuthController` — login, logout, refresh token
- [ ] Build `CategoryController` — CRUD with parent/sub-category support
- [ ] Build `ProductController` — CRUD with tax, price, cost fields
- [ ] Write Feature Tests for Auth and Category modules

### Deliverables
- Working login API with role-based token
- Protected `/api/v1/categories` and `/api/v1/products` endpoints
- Seeded database with test roles and users

---

## Phase 2 — Inventory & Recipe Engine (Week 3–4)

### Goals
- Raw material (ingredient) tracking
- Recipe (BOM) mapping — products ↔ ingredients
- Stock deduction logic on order completion
- Wastage management

### Tasks
- [ ] Create `ingredients` table migration and `Ingredient` model
- [ ] Create `product_ingredient` (recipe) pivot table
- [ ] Build `IngredientController` — CRUD with unit and stock_qty
- [ ] Build `RecipeController` — attach/detach ingredients to products
- [ ] Build `StockService` — `deductStock()`, `checkStock()`, `logWastage()`
- [ ] Build `WastageController` — record damaged/expired stock
- [ ] Write Feature Tests for stock deduction scenarios

### Deliverables
- Ingredient management endpoints
- Recipe assignment per product
- `StockService` ready to be triggered by order completion

---

## Phase 3 — POS & Order Lifecycle (Week 5–6)

### Goals
- Full POS interface backend
- Order creation, update, and lifecycle management
- Shift open/close cycle
- Payment processing (Cash, Card, Split)

### Tasks
- [ ] Create `shifts`, `orders`, `order_items` migrations
- [ ] Build `ShiftController` — open shift, close shift
- [ ] Build `ShiftService` — validate active shift, calculate over/short
- [ ] Implement **Shift Guard Middleware** — block transactions without active shift
- [ ] Build `OrderController` — create order, add items, update status
- [ ] Build `OrderService` — status transitions, trigger `StockService` on completion
- [ ] Build `PaymentController` — handle Cash, Card, Split Payment
- [ ] Write Feature Tests for full order lifecycle

### Deliverables
- `/api/v1/shifts` open/close endpoints
- `/api/v1/orders` full CRUD with status transitions
- Stock auto-deducted when order status → Completed

---

## Phase 4 — Financials & Reporting (Week 7–8)

### Goals
- Expense tracking (Fixed, Variable, Wastage)
- Automatic COGS calculation
- Net Profit dashboard data
- Financial reports by shift, day, week, month

### Tasks
- [ ] Create `expense_categories`, `expenses` migrations
- [ ] Build `ExpenseController` — CRUD with category and shift linkage
- [ ] Build `ProfitService` — `calculateNetProfit()`, `calculateCOGS()`, `getSalesTotal()`
- [ ] Build `ReportController` — profit report, sales report, stock report
- [ ] Build `DashboardController` — summary metrics for Admin/Manager
- [ ] Write Feature Tests for profit calculation scenarios

### Deliverables
- Expense management endpoints
- `/api/v1/reports/profit` endpoint with Net Profit formula
- Dashboard summary API

---

## Phase 5 — Printing, Export & Polish (Week 9–10)

### Goals
- Thermal-ready receipt generation
- QR code for tax compliance
- PDF/Excel export for reports
- Final polish and production hardening

### Tasks
- [ ] Integrate `mike42/escpos-php` or `barryvdh/laravel-dompdf` for receipts
- [ ] Integrate `simplesoftwareio/simple-qrcode` for QR generation
- [ ] Build `ReceiptService` — generate receipt data structure
- [ ] Build `InvoiceController` — download receipt as PDF
- [ ] Integrate `maatwebsite/excel` for Excel report exports
- [ ] Add `spatie/laravel-activitylog` audit logs on critical actions
- [ ] Performance: add database indexes on hot query columns
- [ ] Security audit: rate limiting, input sanitization review
- [ ] Write End-to-End tests

### Deliverables
- Thermal receipt PDF generation
- QR code embedded in receipt
- Excel export for financial reports
- Audit log for all critical actions
- Production-ready hardened API

---

## Dependency Graph

```
Phase 1 (Auth + Menu)
    ↓
Phase 2 (Inventory + Recipes)
    ↓
Phase 3 (POS + Orders + Shifts)    ← Depends on Phase 1 & 2
    ↓
Phase 4 (Financials + Reports)     ← Depends on Phase 3
    ↓
Phase 5 (Printing + Export)        ← Depends on Phase 4
```

---

## Package Reference

```bash
# Core
composer require spatie/laravel-permission
composer require laravel/sanctum
composer require spatie/laravel-activitylog

# Phase 5
composer require barryvdh/laravel-dompdf
composer require simplesoftwareio/simple-qrcode
composer require maatwebsite/excel
composer require mike42/escpos-php
```
