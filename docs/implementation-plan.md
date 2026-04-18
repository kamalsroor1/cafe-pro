# 📋 Cafe Pro ERP — Implementation Plan

> **Stack:** PHP 8.3 · Laravel 12 · Livewire 3 · Tailwind CSS v3 (Dark) · Alpine.js · MySQL 8 · NativePHP
> **Pattern:** Service Layer · RBAC (Spatie) · Blade + Livewire Components · Touch-Optimized UI

---

## 🗺️ Phases Overview

| Phase | Name | Duration | Status |
|---|---|---|---|
| **1** | Foundation, Auth & Setup | Week 1–2 | ⬜ Not Started |
| **2** | Inventory & Recipe Engine | Week 3–4 | ⬜ Not Started |
| **3** | POS & Order Lifecycle | Week 5–6 | ⬜ Not Started |
| **4** | Financials & Reporting | Week 7–8 | ⬜ Not Started |
| **5** | Frontend UI, Printing & Polish | Week 9–10 | ⬜ Not Started |

---

## Phase 1 — Foundation, Auth & Setup

### Goals
- Project scaffolding and package installation
- Database connection & migrations
- Full RBAC: Roles, Permissions, Users
- Category & Product CRUD
- Livewire auth pages (login/logout)

### Packages to Install
```bash
composer require livewire/livewire
composer require spatie/laravel-permission
composer require spatie/laravel-activitylog
npm install -D tailwindcss @tailwindcss/forms alpinejs
```

### Migrations (in order)
1. `users` — add `is_active` column
2. `categories` — self-referential with `parent_id`
3. `products` — with `price`, `cost`, `tax_rate`, soft deletes
4. `product_addons` — extras per product

### Backend Files
| File | Type | Notes |
|---|---|---|
| `app/Models/User.php` | Model | Add `HasRoles`, `SoftDeletes` |
| `app/Models/Category.php` | Model | Self-referential `parent()` |
| `app/Models/Product.php` | Model | `SoftDeletes`, `category()`, `addons()` |
| `app/Models/ProductAddon.php` | Model | `belongsTo(Product)` |
| `app/Services/UserService.php` | Service | create, update, toggleActive |
| `database/seeders/RolesAndPermissionsSeeder.php` | Seeder | 4 roles + all permissions |
| `database/seeders/AdminUserSeeder.php` | Seeder | admin@cafepro.com |
| `config/cafepro.php` | Config | business name, stock_check_enabled |

### Livewire Components
| Component | Notes |
|---|---|
| `Auth/Login.php` | Dark form, email + password |
| `Products/ProductList.php` | Searchable table |
| `Products/ProductForm.php` | Create/edit modal |

### UI Setup
- Configure `tailwind.config.js` with dark color tokens
- Create `resources/views/layouts/app.blade.php`
- Create `components/sidebar.blade.php` — dark, touch-friendly
- Create `components/navbar.blade.php` — dark, with shift badge

### Phase 1 Deliverables
- Working dark login page
- Dark sidebar with role-based nav items
- Admin can manage products & categories
- Seeded: roles, permissions, admin user

---

## Phase 2 — Inventory & Recipe Engine

### Goals
- Raw material tracking with stock levels
- Recipe (BOM) mapping: products ↔ ingredients
- Stock deduction on order completion
- Wastage logging

### Migrations
1. `ingredients` — `stock_qty`, `min_stock_qty`, `cost_per_unit`, `unit`, soft deletes
2. `product_ingredient` — pivot with `amount_needed`
3. `wastage_logs` — `ingredient_id`, `qty_wasted`, `cost_value`, `reason`

### Backend Files
| File | Type | Notes |
|---|---|---|
| `app/Models/Ingredient.php` | Model | `SoftDeletes`, `recipes()` |
| `app/Models/WastageLog.php` | Model | `ingredient()`, `shift()` |
| `app/Services/StockService.php` | Service | `deductForOrder()`, `checkStock()`, `logWastage()` |
| `database/seeders/IngredientSeeder.php` | Seeder | Coffee, Milk, Sugar... |
| `database/seeders/RecipeSeeder.php` | Seeder | Product → ingredient mapping |

### Livewire Components
| Component | Notes |
|---|---|
| `Inventory/IngredientList.php` | Stock level badge (red if below min) |
| `Inventory/IngredientForm.php` | Create/edit modal |
| `Inventory/RecipeEditor.php` | Attach ingredients with qty |
| `Inventory/WastageForm.php` | Log wastage with reason |

### Phase 2 Deliverables
- Ingredient CRUD with stock tracking
- Recipe editor functional
- `StockService` tested and ready
- Low-stock visual indicators

---

## Phase 3 — POS & Order Lifecycle

> Core phase — POS terminal must be fully touch-optimized.

### Goals
- Shift open/close with balance tracking
- Full POS terminal (touch UI)
- Order creation: Dine-in, Takeaway, Delivery
- Order status: Pending → Preparing → Ready → Completed
- Payment: Cash, Card, Split
- Auto stock deduction on completion

### Migrations
1. `tables` — restaurant tables, `status` enum
2. `shifts` — `opening_balance`, `closing_balance`, `expected_balance`, `difference`
3. `orders` — full schema with soft deletes
4. `order_items` — with price/name/cost snapshots
5. `order_item_addons` — addon snapshots
6. `payments` — `method`, `amount`, `reference`

### Backend Files
| File | Type | Notes |
|---|---|---|
| `app/Models/Shift.php` | Model | `orders()`, `user()` |
| `app/Models/Order.php` | Model | `SoftDeletes`, `items()`, `payments()` |
| `app/Models/OrderItem.php` | Model | `addons()` |
| `app/Models/Payment.php` | Model | `order()` |
| `app/Models/RestaurantTable.php` | Model | status management |
| `app/Services/ShiftService.php` | Service | `openShift()`, `closeShift()`, `getActiveShift()` |
| `app/Services/OrderService.php` | Service | `createOrder()`, `transitionStatus()`, `cancelOrder()` |
| `app/Services/PaymentService.php` | Service | `recordPayment()`, `calculateChange()` |
| `app/Http/Middleware/EnsureShiftIsOpen.php` | Middleware | Block POS if no shift |
| `app/Enums/OrderStatus.php` | Enum | pending, preparing, ready, completed, cancelled |
| `app/Enums/PaymentMethod.php` | Enum | cash, card, split |
| `database/seeders/TableSeeder.php` | Seeder | T1–T20 |

### Livewire Components (Touch Priority)
| Component | Notes |
|---|---|
| `Shifts/OpenShift.php` | Opening balance, big tap button |
| `Shifts/CloseShift.php` | Expected vs actual summary |
| `Pos/PosTerminal.php` | Master component, full-screen |
| `Pos/ProductGrid.php` | Category tabs + product cards (min 140px) |
| `Pos/OrderCart.php` | Live cart, qty controls, totals |
| `Pos/PaymentModal.php` | Cash/Card/Split + change calc |
| `Orders/OrderList.php` | History with status badges |
| `Orders/OrderDetail.php` | Full order breakdown |

### POS Touch UI Rules
```
Left panel:  Category tabs (48px min-height) + Product grid (3 cols)
Right panel: Cart + PAY button (64px height, amber background)

Active category tab:  bg-amber-500 text-black
Product card hover:   border-amber-500
Pay button:           bg-amber-500 text-black font-bold text-xl
Completed badge:      bg-emerald-500/20 text-emerald-400
Cancel/danger:        bg-red-500/20 text-red-400
```

### Phase 3 Deliverables
- Shift guard active
- POS: browse → add to cart → pay → order created
- Stock auto-deducted on completion
- Table status updates
- Order history with filters

---

## Phase 4 — Financials & Reporting

### Goals
- Expense tracking (Fixed, Variable, Wastage)
- COGS auto-calculation
- Net Profit dashboard
- Reports by shift / day / week / month

### Migrations
1. `expense_categories` — `type` enum (fixed, variable, wastage)
2. `expenses` — linked to `shift_id` and `expense_category_id`

### Backend Files
| File | Type | Notes |
|---|---|---|
| `app/Models/ExpenseCategory.php` | Model | `expenses()` |
| `app/Models/Expense.php` | Model | `category()`, `shift()` |
| `app/Services/ProfitService.php` | Service | `calculateNetProfit()`, `calculateCOGS()` |
| `app/Services/ReportService.php` | Service | Aggregate by date range |
| `database/seeders/ExpenseCategorySeeder.php` | Seeder | Rent, Salaries, Utilities |

### Net Profit Formula
```
Net Profit = Total Sales
           − COGS (ingredient costs × qty sold)
           − Operating Expenses (fixed + variable)
           − Wastage Cost
```

### Livewire Components
| Component | Notes |
|---|---|
| `Expenses/ExpenseList.php` | Category + date filters |
| `Expenses/ExpenseForm.php` | Create/edit modal |
| `Dashboard/Index.php` | 4 KPI stat cards |
| `Reports/ProfitReport.php` | Date-range profit table |
| `Reports/ShiftReport.php` | Per-shift summary |

### Phase 4 Deliverables
- Expense CRUD
- Dashboard KPIs: Sales, COGS, Expenses, Net Profit
- Profit report with date range filter
- Shift summary report

---

## Phase 5 — UI Polish, Printing & Production

### Goals
- Thermal receipt (PDF + ESC/POS)
- QR code for tax compliance
- Full UI polish (toasts, loading, animations)
- Excel export for reports
- NativePHP packaging

### Additional Packages
```bash
composer require barryvdh/laravel-dompdf
composer require simplesoftwareio/simple-qrcode
composer require maatwebsite/excel
composer require mike42/escpos-php
```

### Backend Files
| File | Type | Notes |
|---|---|---|
| `app/Services/ReceiptService.php` | Service | Build receipt data |
| `app/Http/Controllers/InvoiceController.php` | Controller | PDF download |

### UI Polish Checklist
- All buttons: `active:scale-95 transition-transform` (tap feedback)
- `wire:loading` spinner on every action button
- Alpine.js toast notifications on success/error
- Sidebar collapse animation (`transition-all duration-300`)
- Mobile-responsive POS (stacked layout on small screens)
- Shift status badge always visible in Navbar

### Receipt Layout
```
┌───────────────────────┐
│      ☕ CAFE PRO       │
│   Downtown Branch     │
├───────────────────────┤
│ Order: ORD-20260418-1 │
│ Date: 18/04/2026      │
│ Cashier: Ahmed        │
│ Table: T5 (Dine-in)   │
├───────────────────────┤
│ Latte      ×2    50   │
│ Cake       ×1    30   │
├───────────────────────┤
│ Subtotal:        80   │
│ Tax (14%):    11.20   │
│ TOTAL:        91.20   │
├───────────────────────┤
│ Paid: Cash   100.00   │
│ Change:        8.80   │
├───────────────────────┤
│       [QR CODE]       │
│ Tax No: TAX-123456    │
└───────────────────────┘
```

### Phase 5 Deliverables
- PDF receipt download
- QR code in receipt
- Excel export for reports
- All touch targets ≥ 48px confirmed
- NativePHP build working on Windows

---

## Dependency Graph

```
Phase 1 — Foundation & Auth
        ↓
Phase 2 — Inventory & Recipes  (needs: products)
        ↓
Phase 3 — POS & Orders         (needs: StockService)
        ↓
Phase 4 — Financials           (needs: orders + expenses)
        ↓
Phase 5 — Polish & Print       (needs: all phases)
```

---

## Final Folder Structure

```
app/
├── Enums/         OrderStatus · PaymentMethod · OrderType
├── Http/
│   ├── Controllers/InvoiceController.php
│   ├── Middleware/ EnsureShiftIsOpen.php
│   └── Requests/  (per module)
├── Livewire/
│   ├── Auth/      Login
│   ├── Dashboard/ Index
│   ├── Pos/       PosTerminal · ProductGrid · OrderCart · PaymentModal
│   ├── Products/  ProductList · ProductForm
│   ├── Inventory/ IngredientList · RecipeEditor · WastageForm
│   ├── Shifts/    OpenShift · CloseShift
│   ├── Orders/    OrderList · OrderDetail
│   ├── Expenses/  ExpenseList · ExpenseForm
│   └── Reports/   ProfitReport · ShiftReport
├── Models/        (see database-schema.md — 22 models)
├── Services/
│   ├── StockService · OrderService · ShiftService
│   ├── PaymentService · ProfitService · ReportService
│   ├── ReceiptService · UserService
└── Policies/      (per model)

resources/views/
├── layouts/       app.blade.php · pos.blade.php
├── components/    sidebar · navbar · stat-card · status-badge
└── livewire/      (mirrors Livewire/ structure)
```

---

> 💡 **AI Hint:** Always build Services before Livewire components. Never put business logic inside a component — call `$this->service->method()` instead.
