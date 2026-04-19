# ☕ Cafe Pro - Project Map & Documentation

This document provides a comprehensive overview of the **Cafe Pro** POS system. It is designed to help any external AI or developer understand the technical architecture, business logic, and file structure of the project.

---

## 🚀 1. Technical Stack
- **Framework**: Laravel 12 (latest stable)
- **Frontend Logic**: Livewire 4 & Alpine.js 3
- **Styling**: Tailwind CSS v4 (using CSS-first configuration)
- **Database**: SQLite (Multiple instances: local project and isolated NativePHP instance)
- **Platform**: Desktop Application via [NativePHP](https://nativephp.com/) (Electron-based)
- **State Management**: Server-side via Livewire properties and persistent database drafts for the POS.

---

## 📂 2. Directory Structure Highlights
- `/app/Livewire`: Contains all UI components (reactive modules).
- `/app/Models`: Eloquent models with core relations.
- `/app/Services`: Business logic isolated from controllers/components.
- `/resources/views/livewire`: Blade templates for the components.
- `/resources/css/app.css`: Tailwind v4 theme and custom variant definitions.
- `/database/migrations`: Historical and current database schema.
- `/docs/`: Detailed design documents for specific features.

---

## 🏗️ 3. Core Modules & Business Logic

### 🛒 A. POS (Point of Sale)
- **File**: `App\Livewire\Pos\Terminal.php`
- **Key Concept**: Operates in two modes (`tables` and `order`).
- **Table Management**: Shows a live grid of tables (Available/Occupied).
- **Persistent Cart**: Unlike traditional memory-based carts, this POS saves every change to a `pending` order in the database immediately (`syncCartToDatabase`). This ensures no data loss if the app crashes.
- **Table Timer**: Uses Alpine.js on the frontend to calculate elapsed time for occupied tables in real-time.

### 🕒 B. Shift Management
- **File**: `App\Services\ShiftService.php` & `App\Livewire\Shifts\ShiftManager.php`
- **Flow**: A cashier must "Open Shift" with starting cash before they can checkout orders in the POS.
- **Reconciliation**: Closing a shift requires entering "Ending Cash," which is compared against "Expected Cash" (Starting + Cash Sales).

### 📦 C. Inventory & Recipes
- **Models**: `Product`, `Ingredient`, `WastageLog`.
- **Logic**: Each `Product` can have a recipe (Ingredients with specific quantities).
- **Stock Tracking**: (In progress) Stock is adjusted based on sales if configured.

---

## 🗃️ 4. Data Relationship Map
- **User** has many **Shifts**.
- **Shift** has many **Orders**.
- **Order** belongs to a **RestaurantTable** (identified by name/number).
- **Order** has many **OrderItems**, which belong to a **Product**.
- **Product** belongs to a **Category**.

---

## 🛠️ 5. Key Design Decisions

1.  **Tailwind v4 (CSS-First)**: Config is primarily in `app.css` using `@theme` and `@custom-variant` for dark mode instead of `tailwind.config.js`.
2.  **NativePHP Isolation**: The app runs as a desktop client. It includes a `NativeAppServiceProvider` that handles auto-migrations and seeding for the local desktop database.
3.  **POS Real-time Status**: The POS UI uses Livewire events (`shiftUpdated`) to refresh global states like shift status across different components.
4.  **Draft Preservation**: Orders stay in a `pending` state linked to a table until a `checkout` occurs (status changes to `paid`).

---

## 📋 6. Setup & Development
- **Local Dev**: `php artisan serve` or `npm run dev`.
- **Native Dev**: `php artisan native:serve`.
- **Formatting**: Uses [Laravel Pint](https://laravel.com/docs/pint) for code style enforcement.
- **Fresh Install**:
  ```bash
  php artisan migrate:fresh --seed
  rm -rf ~/.config/laravel-dev # To reset native environment
  ```

---

*This document is the source of truth for the project's current state as of April 2026.*
