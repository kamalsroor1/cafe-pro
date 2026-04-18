# ☕ Cafe Pro ERP — AI Build Documentation

Welcome to the complete technical documentation for **Cafe Pro ERP** — a niche ERP/POS system for Cafe & Restaurant management.

---

## 📁 Documentation Structure

```
cafe-pro-docs/
├── README.md                        ← You are here
│
├── 01-overview/
│   ├── project-overview.md          ← Project goals, stack, and scope
│   └── development-roadmap.md       ← Phase-by-phase build plan
│
├── 02-database/
│   ├── database-schema.md           ← Full schema with all tables & columns
│   ├── migrations-guide.md          ← Migration order & conventions
│   └── relationships-map.md         ← Entity relationship overview
│
├── 03-modules/
│   ├── 01-auth-rbac.md              ← Auth, Roles & Permissions module
│   ├── 02-categories-products.md    ← Menu, Categories & Products module
│   ├── 03-inventory-recipes.md      ← Ingredients, BOM & Stock module
│   ├── 04-pos-orders.md             ← POS Interface & Order Lifecycle
│   ├── 05-shift-management.md       ← Shift open/close cycle
│   ├── 06-expenses-financials.md    ← Expenses, COGS & Net Profit
│   └── 07-invoicing-printing.md     ← Receipts, Thermal Print & QR
│
├── 04-business-logic/
│   ├── service-layer.md             ← All Service classes & their methods
│   ├── stock-deduction-logic.md     ← Auto stock deduction on order complete
│   ├── shift-lock-logic.md          ← Shift-gated transaction rules
│   └── profit-calculation.md        ← Net Profit formula & COGS logic
│
├── 05-api/
│   ├── api-conventions.md           ← REST conventions, auth headers, errors
│   └── endpoints-reference.md       ← All API routes by module
│
├── 06-frontend/
│   ├── frontend-overview.md         ← Livewire 3 structure & dark UI design
│   └── pos-ui-spec.md               ← POS touch screen layout & interactions
│
└── 07-deployment/
    ├── environment-setup.md         ← .env config, packages, seeder
    └── testing-guide.md             ← Feature tests per module
```

---

## 🚀 Quick Start for AI Code Generation

When using this documentation to generate code, follow this order:

1. Start with `01-overview/project-overview.md` for stack context
2. Read `02-database/database-schema.md` before writing any migrations
3. Follow `02-database/migrations-guide.md` for migration run order
4. Use each file in `03-modules/` as a self-contained spec per module
5. Reference `04-business-logic/service-layer.md` before writing controllers
6. Use `05-api/endpoints-reference.md` for route definitions
7. Follow `06-frontend/frontend-overview.md` for Livewire component structure

---

## ⚙️ Tech Stack Summary

| Layer | Technology |
|---|---|
| Backend | PHP 8.3, Laravel 12 |
| Database | MySQL 8.0 |
| Frontend | **Livewire 3** (full-stack, no separate JS framework) |
| Styling | **Tailwind CSS v3** — dark theme, touch-optimized |
| Auth | Laravel Breeze (Livewire stack) + Spatie Permission |
| Real-time | Livewire polling / Laravel Echo + Pusher (optional) |
| Queue | Laravel Queues (Redis) |
| Printing | Laravel PDF / Raw Thermal ESC/POS |
| Testing | PHPUnit / Pest |
| Desktop App | NativePHP / Electron (optional) |

---

## 🎨 UI Design System — Dark Theme

The entire UI is built with a **dark, touch-friendly design system** using Tailwind CSS.

### Color Palette

| Token | Hex | Usage |
|---|---|---|
| `bg-base` | `#0D0D0D` | Main app background |
| `bg-surface` | `#161616` | Cards, panels, sidebar |
| `bg-elevated` | `#1F1F1F` | Modals, dropdowns, hover states |
| `border` | `#2A2A2A` | Dividers & card borders |
| `accent-primary` | `#F59E0B` | Amber — CTA buttons, highlights (coffee brand color) |
| `accent-secondary` | `#10B981` | Emerald — success, completed orders |
| `accent-danger` | `#EF4444` | Red — cancellations, errors |
| `accent-info` | `#3B82F6` | Blue — info, pending status |
| `text-primary` | `#F5F5F5` | Main headings & labels |
| `text-muted` | `#9CA3AF` | Subtext, placeholders |

### Tailwind Config Extensions (tailwind.config.js)

```js
theme: {
  extend: {
    colors: {
      base: '#0D0D0D',
      surface: '#161616',
      elevated: '#1F1F1F',
      border: '#2A2A2A',
      amber: { DEFAULT: '#F59E0B', dark: '#D97706' },
      emerald: { DEFAULT: '#10B981', dark: '#059669' },
    },
    fontFamily: {
      sans: ['Inter', 'sans-serif'],
    },
  },
},
```

---

## 📱 Touch UI Design Rules

All screens — especially the **POS page**, **Sidebar**, and **Navbar** — must comply with:

| Rule | Minimum Value |
|---|---|
| Minimum tap target size | **48×48px** |
| Font size for touch labels | **16px minimum** |
| Button padding | **px-5 py-3** or larger |
| POS product card size | **min 120×140px** |
| Sidebar icon size | **24px** |
| Spacing between touch elements | **8px minimum** |

### POS Screen Layout (Touch-Optimized)

```
┌─────────────────────────────────────────────────────┐
│  NAVBAR  [☰ Menu] [Shift Status] [User] [Clock Out] │
├────────────────────────┬────────────────────────────┤
│  CATEGORY TABS         │                            │
│  [Coffee][Food][Juice] │     ORDER CART             │
├────────────────────────┤                            │
│                        │  Item 1 ........... 25 EGP │
│   PRODUCT GRID         │  Item 2 ........... 40 EGP │
│                        │  ─────────────────────     │
│  ┌──────┐  ┌──────┐   │  Subtotal:        65 EGP   │
│  │ Img  │  │ Img  │   │  Tax (14%):        9 EGP   │
│  │Latte │  │Cake  │   │  Total:           74 EGP   │
│  │25EGP │  │30EGP │   │                            │
│  └──────┘  └──────┘   │  [  💳 PAY NOW  ]          │
│                        │  [Cancel Order]            │
└────────────────────────┴────────────────────────────┘
```

### Sidebar Design Rules

```
┌──────────────────┐
│  ☕ CAFE PRO     │  ← Logo / brand (collapsed: icon only)
├──────────────────┤
│  🏠 Dashboard    │  ← Active: amber highlight bg
│  🖥️ POS          │
│  📦 Inventory    │
│  📊 Reports      │
│  💸 Expenses     │
│  ⚙️ Settings     │
├──────────────────┤
│  👤 [User Name]  │  ← Bottom user section
│  🔒 Logout       │
└──────────────────┘
```

- Sidebar collapses to **icon-only mode** on small screens
- Active route highlighted with **amber left border + amber text**
- Each menu item: min height **56px** for touch
- Background: `bg-surface (#161616)` with subtle right border

---

## 🧩 Livewire Component Architecture

```
app/Livewire/
├── Auth/
│   └── Login.php
├── Dashboard/
│   └── Index.php
├── Pos/
│   ├── PosTerminal.php     ← Main POS component (full-screen)
│   ├── ProductGrid.php     ← Product listing with category filter
│   ├── OrderCart.php       ← Cart sidebar with totals
│   └── PaymentModal.php    ← Payment dialog
├── Orders/
│   ├── OrderList.php
│   └── OrderDetail.php
├── Products/
│   ├── ProductList.php
│   └── ProductForm.php
├── Inventory/
│   ├── IngredientList.php
│   └── RecipeEditor.php
├── Shifts/
│   ├── OpenShift.php
│   └── CloseShift.php
├── Expenses/
│   └── ExpenseList.php
└── Reports/
    └── ProfitReport.php

resources/views/
├── layouts/
│   ├── app.blade.php       ← Main layout: dark navbar + sidebar
│   └── pos.blade.php       ← Full-screen POS layout (no sidebar)
├── livewire/
│   ├── pos/
│   │   ├── pos-terminal.blade.php
│   │   ├── product-grid.blade.php
│   │   ├── order-cart.blade.php
│   │   └── payment-modal.blade.php
│   └── ...
└── components/
    ├── sidebar.blade.php
    ├── navbar.blade.php
    ├── stat-card.blade.php
    └── status-badge.blade.php
```

---

## 🔐 Role-Based UI Guards (Blade)

Use Spatie's Blade directives to conditionally show UI elements:

```blade
@role('admin')
    <a href="{{ route('settings') }}">Settings</a>
@endrole

@can('manage products')
    <button wire:click="deleteProduct({{ $product->id }})">Delete</button>
@endcan
```

---

> 💡 **AI Hint**: When generating Livewire components, always use `wire:click`, `wire:model`, and `wire:loading` directives. Avoid JavaScript-heavy implementations — let Livewire handle reactivity server-side.
