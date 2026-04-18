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
│   ├── frontend-overview.md         ← Vue.js / Livewire structure
│   └── pos-ui-spec.md               ← POS screen layout & interactions
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

---

## ⚙️ Tech Stack Summary

| Layer | Technology |
|---|---|
| Backend | PHP 8.2, Laravel 11 |
| Database | MySQL 8.0 |
| Frontend | Vue.js 3 + Inertia.js (or Livewire 3) |
| Auth | Laravel Sanctum + Spatie Permission |
| Queue | Laravel Queues (Redis) |
| Printing | Laravel PDF / Raw Thermal ESC/POS |
| Testing | PHPUnit / Pest |
