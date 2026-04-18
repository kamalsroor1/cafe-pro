# ✅ Cafe Pro ERP — Task List

> Track every build task phase by phase. Check off as you go.

---

## Phase 1 — Foundation, Auth & Setup

### 🔧 Project Setup
- [ ] Run `php artisan key:generate`
- [ ] Configure `.env` — DB, Redis, App settings
- [ ] Create `config/cafepro.php`
- [ ] Install & configure Livewire 3
- [ ] Install & configure Tailwind CSS v3 with dark theme tokens
- [ ] Install & configure Alpine.js
- [ ] Add `Inter` font from Google Fonts to layout
- [ ] Configure `tailwind.config.js` with `base`, `surface`, `elevated` colors

### 🗄️ Migrations
- [ ] Add `is_active` column to `users` migration
- [ ] Create `create_categories_table` migration
- [ ] Create `create_products_table` migration
- [ ] Create `create_product_addons_table` migration

### 📦 Packages
- [ ] `composer require spatie/laravel-permission`
- [ ] `composer require spatie/laravel-activitylog`
- [ ] `php artisan vendor:publish` — Spatie Permission
- [ ] `php artisan vendor:publish` — Activity Log
- [ ] `php artisan migrate`

### 🧩 Models
- [ ] `app/Models/User.php` — add `HasRoles`, `SoftDeletes`, `HasApiTokens`
- [ ] `app/Models/Category.php` — `parent()`, `children()`, `products()`
- [ ] `app/Models/Product.php` — `SoftDeletes`, `category()`, `addons()`, `ingredients()`
- [ ] `app/Models/ProductAddon.php` — `belongsTo(Product)`

### ⚙️ Services
- [ ] `app/Services/UserService.php` — `createUser()`, `updateUser()`, `toggleActive()`

### 🌱 Seeders
- [ ] `database/seeders/RolesAndPermissionsSeeder.php`
- [ ] `database/seeders/AdminUserSeeder.php`
- [ ] `database/seeders/CategorySeeder.php`
- [ ] `database/seeders/ProductSeeder.php`
- [ ] Register all seeders in `DatabaseSeeder.php`
- [ ] Run `php artisan db:seed`

### 🖥️ Layouts & Components (Blade)
- [ ] `resources/views/layouts/app.blade.php` — dark sidebar + navbar shell
- [ ] `resources/views/layouts/pos.blade.php` — full-screen, no sidebar
- [ ] `resources/views/components/sidebar.blade.php` — dark, collapsible, 56px items
- [ ] `resources/views/components/navbar.blade.php` — dark, shift badge, user menu
- [ ] `resources/views/components/stat-card.blade.php`
- [ ] `resources/views/components/status-badge.blade.php`

### 🔥 Livewire Components
- [ ] `app/Livewire/Auth/Login.php` + view
- [ ] `app/Livewire/Products/ProductList.php` + view (searchable, paginated)
- [ ] `app/Livewire/Products/ProductForm.php` + view (create/edit modal)

### ✅ Phase 1 Done When
- [ ] Login page works (dark theme)
- [ ] Sidebar shows role-based items
- [ ] Admin can create/edit/delete products
- [ ] `php artisan db:seed` runs without errors
- [ ] Login with `admin@cafepro.com` / `password` works

---

## Phase 2 — Inventory & Recipe Engine

### 🗄️ Migrations
- [ ] Create `create_ingredients_table` migration
- [ ] Create `create_product_ingredient_table` migration (BOM pivot)
- [ ] Create `create_wastage_logs_table` migration
- [ ] Run `php artisan migrate`

### 🧩 Models
- [ ] `app/Models/Ingredient.php` — `SoftDeletes`, `recipes()`, `wastages()`
- [ ] `app/Models/WastageLog.php` — `ingredient()`, `shift()`, `recordedBy()`

### ⚙️ Services
- [ ] `app/Services/StockService.php`
  - [ ] `deductForOrder(Order $order)`
  - [ ] `checkStockForOrder(Order $order): array` — returns shortages
  - [ ] `logWastage(array $data): WastageLog`

### 🌱 Seeders
- [ ] `database/seeders/IngredientSeeder.php`
- [ ] `database/seeders/RecipeSeeder.php`

### 🔥 Livewire Components
- [ ] `app/Livewire/Inventory/IngredientList.php` + view
- [ ] `app/Livewire/Inventory/IngredientForm.php` + view (modal)
- [ ] `app/Livewire/Inventory/RecipeEditor.php` + view
- [ ] `app/Livewire/Inventory/WastageForm.php` + view

### ✅ Phase 2 Done When
- [ ] Ingredient CRUD works
- [ ] Recipe editor can attach ingredients with qty to a product
- [ ] `StockService::deductForOrder()` tested manually
- [ ] Low-stock badge shows red when stock < min_stock_qty

---

## Phase 3 — POS & Order Lifecycle

### 🗄️ Migrations
- [ ] Create `create_tables_table` migration (restaurant tables)
- [ ] Create `create_shifts_table` migration
- [ ] Create `create_orders_table` migration
- [ ] Create `create_order_items_table` migration
- [ ] Create `create_order_item_addons_table` migration
- [ ] Create `create_payments_table` migration
- [ ] Run `php artisan migrate`

### 🧩 Enums
- [ ] `app/Enums/OrderStatus.php` — pending, preparing, ready, completed, cancelled
- [ ] `app/Enums/PaymentMethod.php` — cash, card, split
- [ ] `app/Enums/OrderType.php` — dine_in, takeaway, delivery

### 🧩 Models
- [ ] `app/Models/Shift.php` — `orders()`, `user()`, scope `open()`
- [ ] `app/Models/Order.php` — `SoftDeletes`, `items()`, `payments()`, `table()`, `shift()`
- [ ] `app/Models/OrderItem.php` — `addons()`, `product()`
- [ ] `app/Models/OrderItemAddon.php`
- [ ] `app/Models/Payment.php` — `order()`
- [ ] `app/Models/RestaurantTable.php`

### ⚙️ Services
- [ ] `app/Services/ShiftService.php`
  - [ ] `openShift(User $user, float $openingBalance): Shift`
  - [ ] `closeShift(Shift $shift, float $closingBalance, ?string $notes): Shift`
  - [ ] `getActiveShiftForUser(User $user): ?Shift`
  - [ ] `calculateExpectedBalance(Shift $shift): float`
- [ ] `app/Services/OrderService.php`
  - [ ] `createOrder(array $data, Shift $shift, User $user): Order`
  - [ ] `addItemsToOrder(Order $order, array $items): void`
  - [ ] `transitionStatus(Order $order, string $newStatus, User $user): void`
  - [ ] `cancelOrder(Order $order, string $reason, User $user): void`
  - [ ] `recalculateTotals(Order $order): void`
  - [ ] `generateOrderNumber(): string`
  - [ ] `validateStatusTransition(string $current, string $new): void`
- [ ] `app/Services/PaymentService.php`
  - [ ] `recordPayment(Order $order, array $data): Payment`
  - [ ] `calculateChange(float $total, float $paid): float`

### 🛡️ Middleware
- [ ] `app/Http/Middleware/EnsureShiftIsOpen.php`
- [ ] Register middleware alias `shift.open` in `bootstrap/app.php`

### 🌱 Seeders
- [ ] `database/seeders/TableSeeder.php` (T1–T20)

### 🔥 Livewire Components
- [ ] `app/Livewire/Shifts/OpenShift.php` + view (touch-friendly big button)
- [ ] `app/Livewire/Shifts/CloseShift.php` + view (expected vs actual)
- [ ] `app/Livewire/Pos/PosTerminal.php` + view (full-screen master)
- [ ] `app/Livewire/Pos/ProductGrid.php` + view (category tabs + cards min 140px)
- [ ] `app/Livewire/Pos/OrderCart.php` + view (live cart + totals)
- [ ] `app/Livewire/Pos/PaymentModal.php` + view (cash/card/split + change)
- [ ] `app/Livewire/Orders/OrderList.php` + view (filters: status, date)
- [ ] `app/Livewire/Orders/OrderDetail.php` + view

### ✅ Phase 3 Done When
- [ ] Can't access POS without open shift
- [ ] POS: select products → cart updates live → pay → order saved
- [ ] Stock auto-deducts when order → completed
- [ ] Table status changes on order create/complete/cancel
- [ ] Payment modal calculates change correctly

---

## Phase 4 — Financials & Reporting

### 🗄️ Migrations
- [ ] Create `create_expense_categories_table` migration
- [ ] Create `create_expenses_table` migration
- [ ] Run `php artisan migrate`

### 🧩 Models
- [ ] `app/Models/ExpenseCategory.php` — `expenses()`
- [ ] `app/Models/Expense.php` — `category()`, `shift()`, `recordedBy()`

### ⚙️ Services
- [ ] `app/Services/ProfitService.php`
  - [ ] `calculateNetProfit(Carbon $from, Carbon $to): float`
  - [ ] `calculateCOGS(Carbon $from, Carbon $to): float`
  - [ ] `getSalesTotal(Carbon $from, Carbon $to): float`
  - [ ] `getExpensesTotal(Carbon $from, Carbon $to): float`
- [ ] `app/Services/ReportService.php`
  - [ ] `getDailySummary(Carbon $date): array`
  - [ ] `getShiftSummary(Shift $shift): array`

### 🌱 Seeders
- [ ] `database/seeders/ExpenseCategorySeeder.php`

### 🔥 Livewire Components
- [ ] `app/Livewire/Expenses/ExpenseList.php` + view
- [ ] `app/Livewire/Expenses/ExpenseForm.php` + view (modal)
- [ ] `app/Livewire/Dashboard/Index.php` + view (4 KPI stat cards)
- [ ] `app/Livewire/Reports/ProfitReport.php` + view (date range filter)
- [ ] `app/Livewire/Reports/ShiftReport.php` + view

### ✅ Phase 4 Done When
- [ ] Expense CRUD works (Manager/Admin only)
- [ ] Dashboard shows: Sales, COGS, Expenses, Net Profit
- [ ] Profit report filters by date range correctly
- [ ] Net Profit formula: Sales − COGS − Expenses − Wastage

---

## Phase 5 — UI Polish, Printing & Production

### 📦 Packages
- [ ] `composer require barryvdh/laravel-dompdf`
- [ ] `composer require simplesoftwareio/simple-qrcode`
- [ ] `composer require maatwebsite/excel`
- [ ] `composer require mike42/escpos-php` (optional)

### ⚙️ Services & Controllers
- [ ] `app/Services/ReceiptService.php` — `buildReceiptData(Order $order): array`
- [ ] `app/Http/Controllers/InvoiceController.php` — PDF download

### 🎨 UI Polish Tasks
- [ ] Add `active:scale-95 transition-transform` to all action buttons
- [ ] Add `wire:loading` spinner to every form submit / action button
- [ ] Add Alpine.js toast notification system (success / error)
- [ ] Add sidebar collapse toggle (icon-only mode)
- [ ] Make POS layout stack vertically on small screens (< 768px)
- [ ] Confirm all nav items: min-height 56px
- [ ] Confirm all POS product cards: min 120×140px
- [ ] Confirm category tabs: min-height 48px
- [ ] Confirm PAY button: min-height 64px

### 🖨️ Printing
- [ ] Create receipt Blade view `resources/views/receipts/thermal.blade.php`
- [ ] Generate PDF receipt on order detail page
- [ ] Embed QR code (Tax Number + Order Number)
- [ ] Add "Print Receipt" button on PaymentModal after payment

### 📊 Exports
- [ ] Add Excel export to ProfitReport component
- [ ] Add Excel export to ShiftReport component

### 🔒 Security & Performance
- [ ] Add database indexes (see `database-schema.md` → Key Indexes)
- [ ] Rate-limit login route
- [ ] Review all Livewire components for N+1 queries (use `->with()` eager loading)
- [ ] Test all `@can` / `@role` Blade guards

### ✅ Phase 5 Done When
- [ ] PDF receipt downloads correctly
- [ ] QR code visible in receipt
- [ ] Excel export works for reports
- [ ] All touch targets ≥ 48px on POS screen
- [ ] Tap feedback animation on all buttons
- [ ] Toast shows on order complete / payment success
- [ ] App tested on touch screen device

---

## Global Checks (All Phases)

- [ ] All models with financial data use `SoftDeletes`
- [ ] All business logic is in `app/Services/` — NOT in Livewire components
- [ ] All Blade guards (`@can`, `@role`) tested per role
- [ ] `wire:loading` added to every async action
- [ ] Dark theme consistent across all pages
- [ ] No `console.log` or debug output left in production
