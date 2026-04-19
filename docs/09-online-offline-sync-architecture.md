# Online/Offline & Multi-Device Architecture — Cafe Pro ERP

## What This Document Covers

This document explains the architectural design needed to transform the current system into a robust setup that works **offline** inside the cafe, with the ability to **sync data to the cloud** for the manager's remote dashboard, while also supporting **multiple devices simultaneously** on the local network.

---

## My Opinion on This Architecture

This is a well-thought-out design overall, but I want to be honest about the complexity before you commit to it.

**What's solid:**
- The local-server + Wi-Fi approach for multi-device is genuinely the right call for a cafe environment. Simple, reliable, no internet dependency for daily operations.
- The push/pull sync pattern using Laravel Queues is the correct pattern — it's battle-tested and handles intermittent connectivity gracefully.
- Defaulting to `Eventual Consistency` (sync when internet returns) is the right tradeoff for this use case. You don't need real-time sync for a cafe.

**What I'd challenge or reconsider:**

1. **UUID migration is the biggest risk item.** Changing all PKs from auto-increment to UUID mid-project is a deep structural change. If the project is already in development with relational data, this touches every table, every foreign key, and every query. It's not a small task — plan a full day minimum just for this migration and its tests. Do it **early**, ideally before any real data exists.

2. **The Pull Sync (menu updates from cloud → local) is trickier than it looks.** What happens if the manager deletes a product on the cloud while a cashier has an active pending order with that product on the local server? You need a conflict resolution strategy. My recommendation: **never hard-delete synced records on the local server** — only soft-delete. Let the local server be append-only for products, and mark deleted ones as `is_available = false` instead.

3. **Queue worker reliability on Windows (cashier machine) is painful.** If the cafe runs Windows on the cashier PC, Supervisor isn't available. NSSM (Non-Sucking Service Manager) or Windows Task Scheduler are alternatives, but both are fragile. If possible, push for the local server to be a small Linux box (even a Raspberry Pi 4 handles this workload). This is worth the conversation with the client.

4. **SQLite vs MySQL for local:** SQLite is tempting for simplicity (no installation), but concurrent writes from multiple devices will cause locking issues. Stick with **MySQL locally** — it handles multi-device concurrent writes correctly.

---

## Architecture Overview

```
                    ☁️ CLOUD SERVER
                 (Manager Dashboard)
                  Laravel + MySQL
                      │   ▲
               Pull   │   │  Push
             (menu)   │   │  (orders/expenses)
                      ▼   │
                 🏠 LOCAL SERVER
              (Main Cashier Machine)
               Laravel + MySQL
              ┌────────────────┐
              │   Queue Worker │ ← syncs silently in background
              └────────────────┘
                      │
            ──── Wi-Fi Network ────
            │         │           │
        📱 Tablet  💻 Cashier2  🖥️ Kitchen Screen
         (Waiter)               (Read-only display)
```

---

## Part 1 — Multi-Device on Local Network

### How It Works

The main cashier machine acts as the **local server**. All other devices connect to it over Wi-Fi — no software installation needed on them.

**On the main machine:**
```bash
# Serve the app accessible to all devices on the network
php artisan serve --host=0.0.0.0 --port=80

# Or better: use Nginx/Apache configured to serve the app permanently
```

**On tablets and secondary devices:**
- Connect to the same Wi-Fi network
- Open a browser and navigate to: `http://192.168.1.XXX` (the local server's IP)
- The full POS interface loads — orders are saved directly to the main machine's database in real time

### Network Setup Recommendation

| Device | Role | Access |
|---|---|---|
| Main Cashier PC | Local Server + Cashier POS | Full access |
| Waiter Tablet | Waiter order entry | Waiter role only |
| Kitchen Screen | Order display (read-only) | Kitchen view only |
| Manager Laptop | Local dashboard + cloud sync | Manager role |

**Assign a static local IP to the main machine** (via router settings) so the address never changes and tablets don't need to reconfigure.

### Required Code Change: Bind to Network Interface

In `.env` on the local server, ensure the app URL reflects the local IP:
```ini
APP_URL=http://192.168.1.150
```

No other deep code changes are needed for this part — this is the easiest win.

---

## Part 2 — Online/Offline Sync Architecture

### The Two Servers

| | Local Server (Cafe) | Cloud Server (Manager) |
|---|---|---|
| **Purpose** | POS operations | Reporting & menu management |
| **Works offline?** | ✅ Yes — fully independent | ❌ Requires internet |
| **Database** | MySQL (local) | MySQL (cloud) |
| **Users** | Cashiers, waiters, kitchen | Manager, owner |

### Sync Direction: Push (Local → Cloud)

**Triggered when:**
- An order is marked `completed`
- An expense is saved
- A shift is closed

**Mechanism:**
```php
// After order completion in OrderService.php
dispatch(new SyncOrderToCloud($order));

// The Job handles the API call
class SyncOrderToCloud implements ShouldQueue
{
    public function handle(): void
    {
        Http::withToken(config('sync.cloud_api_key'))
            ->post(config('sync.cloud_url') . '/api/sync/orders', [
                'order' => $this->order->toSyncArray(),
            ]);
    }

    // If internet is down, Laravel retries automatically
    public int $tries = 10;
    public int $backoff = 60; // retry every 60 seconds
}
```

**If internet is down:** The job stays in the `jobs` table. When connectivity returns, the Queue Worker processes all pending jobs in order. No data is lost.

### Sync Direction: Pull (Cloud → Local)

**Triggered by:**
- A scheduled Cron job every 5 minutes
- OR a manual "Sync Menu" button the cashier can press

**What gets pulled:**
- New or updated products (name, price, tax, availability)
- New or updated categories
- Deleted products (set to `is_available = false` locally — **never hard delete**)

```php
// app/Console/Commands/PullMenuFromCloud.php
class PullMenuFromCloud extends Command
{
    protected $signature = 'sync:pull-menu';

    public function handle(): void
    {
        $response = Http::withToken(config('sync.cloud_api_key'))
            ->get(config('sync.cloud_url') . '/api/sync/menu', [
                'updated_after' => $this->getLastSyncTimestamp(),
            ]);

        foreach ($response->json('products') as $productData) {
            Product::updateOrCreate(
                ['id' => $productData['id']],  // UUID match
                $productData
            );
        }

        $this->setLastSyncTimestamp(now());
    }
}
```

In `Console/Kernel.php`:
```php
$schedule->command('sync:pull-menu')->everyFiveMinutes();
```

---

## Part 3 — Required Technical Changes

### 3.1 — Switch All IDs to UUID ⚠️ DO THIS FIRST

**Why it's critical:** Auto-increment IDs (`1, 2, 3...`) will collide when records are created independently on two databases. UUID (`550e8400-e29b-41d4-a716-446655440000`) is globally unique by design.

**Implementation:**

```php
// In every migration, change:
$table->id();
// To:
$table->uuid('id')->primary();
$table->foreignUuid('product_id')->constrained();
```

In every Model, add:
```php
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Order extends Model
{
    use HasUuids; // Laravel 9+ built-in — auto-generates UUID on create
}
```

> ⚠️ **Do this before any production data exists.** Migrating existing auto-increment data to UUID requires a custom migration script and is error-prone.

### 3.2 — Sync API Endpoints on the Cloud Server

Add a dedicated sync route group in `routes/api.php` on the **cloud server**:

```php
Route::prefix('sync')->middleware('auth.sync')->group(function () {

    // Local → Cloud: receive completed orders
    Route::post('/orders',   [SyncController::class, 'receiveOrder']);
    Route::post('/expenses', [SyncController::class, 'receiveExpense']);
    Route::post('/shifts',   [SyncController::class, 'receiveShift']);

    // Cloud → Local: send menu updates
    Route::get('/menu',      [SyncController::class, 'exportMenu']);
});
```

Protect with a dedicated sync API key (not user tokens):
```php
// app/Http/Middleware/AuthenticateSyncRequest.php
public function handle(Request $request, Closure $next): Response
{
    if ($request->header('X-Sync-Key') !== config('sync.secret_key')) {
        abort(401, 'Invalid sync key.');
    }
    return $next($request);
}
```

In `.env` on both servers:
```ini
SYNC_SECRET_KEY=your-very-long-random-secret-here
CLOUD_SYNC_URL=https://cloud.cafepro.com
```

### 3.3 — Queue Worker Setup

**On Linux (recommended):** Use Supervisor:
```ini
; /etc/supervisor/conf.d/cafepro-worker.conf
[program:cafepro-worker]
command=php /var/www/cafepro/artisan queue:work --sleep=3 --tries=10
autostart=true
autorestart=true
```

**On Windows (if necessary):** Use NSSM to wrap the queue worker as a Windows Service:
```bash
nssm install CafeProWorker "php" "C:\cafepro\artisan queue:work"
nssm start CafeProWorker
```

**My strong recommendation:** Run the local server on a small **Linux machine** (mini PC or Raspberry Pi 4). Cheaper, more stable, and Supervisor works perfectly.

### 3.4 — Track Sync Status Per Record

Add a `synced_at` column to orders, expenses, and shifts so you can always see what has and hasn't reached the cloud:

```php
// Migration
$table->timestamp('synced_at')->nullable(); // null = not yet synced

// In SyncOrderToCloud Job, after successful push:
$this->order->update(['synced_at' => now()]);
```

This also lets you build a simple "Pending Sync" indicator on the local dashboard.

---

## Conflict Resolution Rules

| Scenario | Resolution |
|---|---|
| Manager updates product price on cloud | Pull sync overwrites local price (cloud wins for menu data) |
| Cashier has pending order with a product the manager deleted on cloud | Product remains active locally; deleted flag only hides it from new orders |
| Same order somehow created on both | Impossible with UUID — IDs are unique per origin |
| Manager creates product offline (shouldn't happen) | Cloud server is always online; this scenario is avoided by design |

---

## Implementation Task Checklist

### Phase 0 — UUID Migration (Do First, Before Anything Else)
- [ ] Update all migrations to use `uuid` as primary key type
- [ ] Add `HasUuids` trait to all models: `Order`, `Product`, `Ingredient`, `Category`, `Expense`, `Shift`, `OrderItem`
- [ ] Update all foreign key columns to `foreignUuid()`
- [ ] Test all model creation and relationships with UUID keys
- [ ] Verify `order_number` (the human-readable `ORD-20240101-0001`) still exists separately — UUID is the DB key, order_number is for display

### Phase 1 — Local Multi-Device Setup
- [ ] Set a static IP on the main cashier machine via router settings
- [ ] Configure Nginx or Apache to serve the app on port 80 (not `artisan serve` for production)
- [ ] Update `APP_URL` in `.env` to the local IP
- [ ] Test access from a second device on the same Wi-Fi
- [ ] Verify concurrent order creation from two devices works without conflicts
- [ ] Assign correct roles to each device's login (waiter, cashier, kitchen)

### Phase 2 — Cloud Server Setup
- [ ] Provision a cloud server (DigitalOcean, Hetzner, or any VPS)
- [ ] Deploy the same Laravel codebase to the cloud server
- [ ] Run migrations on cloud DB
- [ ] Set up the sync API routes and `AuthenticateSyncRequest` middleware
- [ ] Generate and share the `SYNC_SECRET_KEY` between local and cloud `.env`
- [ ] Add SSL certificate to cloud server (HTTPS required for secure sync)

### Phase 3 — Push Sync (Local → Cloud)
- [ ] Create `SyncOrderToCloud` Job class with retry logic
- [ ] Create `SyncExpenseToCloud` Job class
- [ ] Create `SyncShiftToCloud` Job class
- [ ] Dispatch sync jobs from `OrderService`, `ExpenseController`, `ShiftService` after each save
- [ ] Add `synced_at` column to `orders`, `expenses`, `shifts` tables
- [ ] Set up Queue Worker via Supervisor (Linux) or NSSM (Windows)
- [ ] Test: complete an order locally → verify it appears on cloud dashboard
- [ ] Test: disconnect internet → complete 3 orders → reconnect → verify all 3 sync

### Phase 4 — Pull Sync (Cloud → Local)
- [ ] Create `PullMenuFromCloud` Artisan command
- [ ] Implement `updated_after` filter on the cloud's `/api/sync/menu` endpoint
- [ ] Store `last_sync_at` timestamp locally (in `settings` table or cache)
- [ ] Schedule command to run every 5 minutes via Laravel Scheduler
- [ ] Ensure Cron is running on the local machine: `* * * * * php /path/to/artisan schedule:run`
- [ ] Add a "Sync Menu Now" button to the local POS dashboard
- [ ] Test: update a product price on cloud → wait 5 min → verify local price updated
- [ ] Test: soft-delete a product on cloud → verify it becomes unavailable locally

### Phase 5 — Monitoring & Hardening
- [ ] Add a "Sync Status" widget to the local dashboard (shows pending/synced counts)
- [ ] Add failed job notifications (email/Slack alert if sync fails > 5 times)
- [ ] Log all sync operations to a `sync_logs` table for debugging
- [ ] Test full power-cut scenario: mid-order, power returns, verify no data loss
- [ ] Document the local server IP and recovery steps for the cafe owner

---

## Summary

| Capability | Complexity | Priority |
|---|---|---|
| Multi-device on Wi-Fi | 🟢 Low — just networking config | Do now |
| UUID migration | 🔴 High — touches entire codebase | Do immediately if not in production |
| Push sync (orders to cloud) | 🟡 Medium — Queues + API | Phase 3 |
| Pull sync (menu from cloud) | 🟡 Medium — Cron + API | Phase 4 |
| Queue worker as a service | 🟢 Low on Linux, 🟡 Medium on Windows | Phase 3 |
| Conflict resolution | 🟢 Low — cloud wins for menu, local wins for orders | Built into sync logic |
