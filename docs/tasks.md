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
- [x] Run `php artisan key:generate`
- [x] Configure `.env` — DB, Redis, App settings
- [x] Create `config/cafepro.php`
- [x] Install & configure Livewire 3
- [x] Install & configure Tailwind CSS v3 with dark theme tokens
- [x] Install & configure Alpine.js
- [x] Add `Inter` font from Google Fonts to layout
- [x] Configure `tailwind.config.js` with `base`, `surface`, `elevated` colors

### 🗄️ Migrations
- [x] Add `is_active` column to `users` migration
- [x] Create `create_categories_table` migration
- [x] Create `create_products_table` migration
- [x] Create `create_product_addons_table` migration

### 📦 Packages
- [x] `composer require spatie/laravel-permission`
- [x] `composer require spatie/laravel-activitylog`
- [x] `php artisan vendor:publish` — Spatie Permission
- [x] `php artisan vendor:publish` — Activity Log
- [x] `php artisan migrate`

### 🧩 Models
- [x] `app/Models/User.php` — add `HasRoles`, `SoftDeletes`, `HasApiTokens`
- [x] `app/Models/Category.php` — `parent()`, `children()`, `products()`
- [x] `app/Models/Product.php` — `SoftDeletes`, `category()`, `addons()`, `ingredients()`
- [x] `app/Models/ProductAddon.php` — `belongsTo(Product)`

### ⚙️ Services
- [x] `app/Services/UserService.php` — `createUser()`, `updateUser()`, `toggleActive()`

### 🌱 Seeders
- [x] `database/seeders/RolesAndPermissionsSeeder.php`
- [x] `database/seeders/AdminUserSeeder.php`
- [x] `database/seeders/CategorySeeder.php`
- [x] `database/seeders/ProductSeeder.php`
- [x] Register all seeders in `DatabaseSeeder.php`
- [x] Run `php artisan db:seed`

### 🖥️ Layouts & Components (Blade)
- [x] `resources/views/layouts/app.blade.php` — dark sidebar + navbar shell
- [x] `resources/views/layouts/pos.blade.php` — full-screen, no sidebar
- [x] `resources/views/components/sidebar.blade.php` — dark, collapsible, 56px items
- [x] `resources/views/components/navbar.blade.php` — dark, shift badge, user menu
- [x] `resources/views/components/stat-card.blade.php`
- [x] `resources/views/components/status-badge.blade.php`

### 🔥 Livewire Components
- [x] `app/Livewire/Auth/Login.php` + view
- [x] `app/Livewire/Products/ProductList.php` + view (searchable, paginated)
- [x] `app/Livewire/Products/ProductForm.php` + view (create/edit modal)

### ✅ Phase 1 Done When
- [x] Login page works (dark theme)
- [x] Sidebar shows role-based items
- [x] Admin can create/edit/delete products
- [x] `php artisan db:seed` runs without errors
- [x] Login with `admin@cafepro.com` / `password` works

---

## Phase 2 — Inventory & Recipe Engine

### 🗄️ Migrations
- [x] Create `create_ingredients_table` migration
- [x] Create `create_product_ingredient_table` migration (BOM pivot)
- [x] Create `create_wastage_logs_table` migration
- [x] Run `php artisan migrate`

### 🧩 Models
- [x] `app/Models/Ingredient.php` — `SoftDeletes`, `recipes()`, `wastages()`
- [x] `app/Models/WastageLog.php` — `ingredient()`, `shift()`, `recordedBy()`

### ⚙️ Services
- [x] `app/Services/StockService.php`
  - [x] `deductForOrder(Order $order)`
  - [x] `checkStockForOrder(Order $order): array` — returns shortages
  - [x] `logWastage(array $data): WastageLog`

### 🌱 Seeders
- [x] `database/seeders/IngredientSeeder.php`
- [x] `database/seeders/RecipeSeeder.php`

### 🔥 Livewire Components
- [x] `app/Livewire/Inventory/IngredientList.php` + view
- [x] `app/Livewire/Inventory/IngredientForm.php` + view (modal)
- [x] `app/Livewire/Inventory/RecipeEditor.php` + view
- [x] `app/Livewire/Inventory/WastageForm.php` + view

### ✅ Phase 2 Done When
- [x] Admin can add "Coffee Beans" to inventory
- [x] Admin can set "Latte" recipe = 18g Beans + 200ml Milk
- [x] Wastage form successfully deducts stock
- [x] Inventory table shows Red text when `stock_qty` <= `min_stock_qty`

---

## Phase 3 — POS & Order Lifecycle

### 🗄️ Migrations
- [x] Create `create_tables_table` migration (restaurant tables)
- [x] Create `create_shifts_table` migration
- [x] Create `create_orders_table` migration
- [x] Create `create_order_items_table` migration
- [x] Create `create_order_item_addons_table` migration
- [x] Create `create_payments_table` migration
- [x] Run `php artisan migrate`

### 🧩 Enums
- [x] `app/Enums/OrderStatus.php` — pending, preparing, ready, completed, cancelled
- [x] `app/Enums/PaymentMethod.php` — cash, card, split
- [x] `app/Enums/OrderType.php` — dine_in, takeaway, delivery

### 🧩 Models
- [x] `app/Models/Shift.php` — `orders()`, `user()`, scope `open()`
- [x] `app/Models/Order.php` — `SoftDeletes`, `items()`, `payments()`, `table()`, `shift()`
- [x] `app/Models/OrderItem.php` — `addons()`, `product()`
- [x] `app/Models/OrderItemAddon.php`
- [x] `app/Models/Payment.php` — `order()`
- [x] `app/Models/RestaurantTable.php`

### ⚙️ Services
- [x] `app/Services/ShiftService.php`
  - [x] `openShift(User $user, float $openingBalance): Shift`
  - [x] `closeShift(Shift $shift, float $closingBalance, ?string $notes): Shift`
  - [x] `getActiveShiftForUser(User $user): ?Shift`
  - [x] `calculateExpectedBalance(Shift $shift): float`
- [x] `app/Services/OrderService.php`
  - [x] `createOrder(array $data, Shift $shift, User $user): Order`
  - [x] `addItemsToOrder(Order $order, array $items): void`
  - [x] `transitionStatus(Order $order, string $newStatus, User $user): void`
  - [x] `cancelOrder(Order $order, string $reason, User $user): void`
  - [x] `recalculateTotals(Order $order): void`
  - [x] `generateOrderNumber(): string`
  - [x] `validateStatusTransition(string $current, string $new): void`
- [x] `app/Services/PaymentService.php`
  - [x] `recordPayment(Order $order, array $data): Payment`
  - [x] `calculateChange(float $total, float $paid): float`

### 🛡️ Middleware
- [x] `app/Http/Middleware/EnsureShiftIsOpen.php`
- [x] Register middleware alias `shift.open` in `bootstrap/app.php`

### 🌱 Seeders
- [x] `database/seeders/TableSeeder.php` (T1–T20)

### 🔥 Livewire Components
- [x] `app/Livewire/Shifts/OpenShift.php` + view (Implemented in ShiftManager)
- [x] `app/Livewire/Shifts/CloseShift.php` + view (Implemented in ShiftManager)
- [x] `app/Livewire/Pos/PosTerminal.php` + view (Implemented in Terminal)
- [x] `app/Livewire/Pos/ProductGrid.php` + view (Implemented in Terminal)
- [x] `app/Livewire/Pos/OrderCart.php` + view (Implemented in Terminal)
- [x] `app/Livewire/Pos/PaymentModal.php` + view (Implemented in Terminal)
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
- [x] Create `create_expense_categories_table` migration
- [x] Create `create_expenses_table` migration
- [x] Run `php artisan migrate`

### 🧩 Models
- [x] `app/Models/ExpenseCategory.php` — `expenses()`
- [x] `app/Models/Expense.php` — `category()`, `shift()`, `recordedBy()`

### ⚙️ Services
- [x] `app/Services/ProfitService.php`
  - [x] `calculateNetProfit(Carbon $from, Carbon $to): float`
  - [x] `calculateCOGS(Carbon $from, Carbon $to): float`
  - [x] `getSalesTotal(Carbon $from, Carbon $to): float`
  - [x] `getExpensesTotal(Carbon $from, Carbon $to): float`
- [x] `app/Services/ReportService.php`
  - [x] `getDailySummary(Carbon $date): array`
  - [x] `getShiftSummary(Shift $shift): array`

### 🌱 Seeders
- [x] `database/seeders/ExpenseCategorySeeder.php`

### 🔥 Livewire Components
- [x] `app/Livewire/Expenses/ExpenseList.php` + view
- [x] `app/Livewire/Expenses/ExpenseForm.php` + view (modal)
- [x] `app/Livewire/Dashboard/Index.php` + view (4 KPI stat cards)
- [x] `app/Livewire/Reports/ProfitReport.php` + view (date range filter)
- [x] `app/Livewire/Reports/ShiftReport.php` + view

### ✅ Phase 4 Done When
- [x] Expense CRUD works (Manager/Admin only)
- [x] Dashboard shows: Sales, COGS, Expenses, Net Profit
- [x] Profit report filters by date range correctly
- [x] Net Profit formula: Sales − COGS − Expenses − Wastage

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
