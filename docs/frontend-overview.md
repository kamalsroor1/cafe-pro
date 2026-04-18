# 🖥️ Frontend Overview — Cafe Pro ERP (Livewire 3)

## Stack

| Layer | Choice | Notes |
|---|---|---|
| Framework | **Livewire 3** | Full-stack reactive, no separate JS framework |
| Templating | **Blade** | Laravel native templates |
| Styling | **Tailwind CSS v3** | Dark theme, touch-optimized |
| Icons | **Heroicons** | SVG icons, consistent with Tailwind |
| Charts | **ApexCharts** (JS) | Loaded via CDN for financial dashboards |
| Reactivity | **Alpine.js** | Lightweight JS for dropdowns, modals |

---

## 🎨 Dark Color System

All components use this palette — configured in `tailwind.config.js`:

| Variable | Hex | Usage |
|---|---|---|
| `bg-base` | `#0D0D0D` | App background |
| `bg-surface` | `#161616` | Cards, sidebar, panels |
| `bg-elevated` | `#1F1F1F` | Modals, dropdowns |
| `border-dark` | `#2A2A2A` | Dividers, card borders |
| `amber-500` | `#F59E0B` | Primary CTA, active nav items |
| `emerald-500` | `#10B981` | Success, completed status |
| `red-500` | `#EF4444` | Danger, cancel, error |
| `blue-500` | `#3B82F6` | Info, pending status |
| `gray-100` | `#F5F5F5` | Primary text |
| `gray-400` | `#9CA3AF` | Muted text, placeholders |

```js
// tailwind.config.js
module.exports = {
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        base:     '#0D0D0D',
        surface:  '#161616',
        elevated: '#1F1F1F',
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      },
    },
  },
}
```

---

## 📁 Component Structure

```
app/Livewire/
├── Auth/
│   └── Login.php
├── Dashboard/
│   └── Index.php
├── Pos/
│   ├── PosTerminal.php       ← Main POS (full-screen, touch layout)
│   ├── ProductGrid.php       ← Product cards filtered by category
│   ├── OrderCart.php         ← Cart panel with live totals
│   └── PaymentModal.php      ← Payment dialog (Cash/Card/Split)
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
│   ├── app.blade.php         ← Dark navbar + collapsible sidebar
│   └── pos.blade.php         ← Full-screen POS (no sidebar)
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

## 🗂️ App Layout — Dark Sidebar

```blade
{{-- resources/views/layouts/app.blade.php --}}
<body class="bg-base text-gray-100 font-sans flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    @include('components.sidebar')

    <div class="flex flex-col flex-1 overflow-hidden">
        {{-- Navbar --}}
        @include('components.navbar')

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>
    </div>

</body>
```

### Sidebar Component Rules

- **Background**: `bg-surface` (`#161616`) with right border `border-r border-[#2A2A2A]`
- **Width**: `w-64` expanded → `w-16` collapsed (icon-only)
- **Active item**: `border-l-4 border-amber-500 text-amber-400 bg-elevated`
- **Inactive item**: `text-gray-400 hover:text-gray-100 hover:bg-elevated`
- **Min tap height**: `min-h-[56px]` for every nav link (touch target)
- **Transition**: `transition-all duration-200`

```blade
{{-- components/sidebar.blade.php --}}
<aside class="w-64 bg-surface border-r border-[#2A2A2A] flex flex-col">
    <div class="p-4 flex items-center gap-3 border-b border-[#2A2A2A]">
        <span class="text-amber-500 text-2xl">☕</span>
        <span class="font-bold text-gray-100 text-lg">Cafe Pro</span>
    </div>

    <nav class="flex-1 py-2">
        @foreach ($navItems as $item)
            <a href="{{ $item['route'] }}"
               class="flex items-center gap-3 px-4 min-h-[56px]
                      {{ request()->routeIs($item['active']) 
                         ? 'border-l-4 border-amber-500 text-amber-400 bg-elevated'
                         : 'text-gray-400 hover:text-gray-100 hover:bg-elevated border-l-4 border-transparent' }}
                      transition-all duration-200">
                <x-icon :name="$item['icon']" class="w-6 h-6 shrink-0" />
                <span class="text-sm font-medium">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="p-4 border-t border-[#2A2A2A]">
        <span class="text-gray-400 text-sm">{{ auth()->user()->name }}</span>
    </div>
</aside>
```

---

## 🔝 Navbar Component Rules

- **Background**: `bg-surface` with bottom border
- **Height**: `h-16` minimum (64px)
- **Contains**: Hamburger toggle, page title, shift status badge, user menu
- **Shift badge**: green `bg-emerald-500/20 text-emerald-400` if open, red if closed

```blade
{{-- components/navbar.blade.php --}}
<header class="h-16 bg-surface border-b border-[#2A2A2A] flex items-center px-6 gap-4">
    <button class="text-gray-400 hover:text-gray-100 p-2 rounded-lg hover:bg-elevated">
        <x-heroicon-o-bars-3 class="w-6 h-6" />
    </button>

    <h1 class="text-gray-100 font-semibold text-lg flex-1">{{ $title ?? '' }}</h1>

    {{-- Shift Status --}}
    @if($activeShift)
        <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400">
            Shift Open
        </span>
    @else
        <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-500/20 text-red-400">
            No Shift
        </span>
    @endif

    {{-- User --}}
    <span class="text-gray-400 text-sm">{{ auth()->user()->name }}</span>
</header>
```

---

## 🖥️ POS Terminal — Touch Layout

```blade
{{-- resources/views/livewire/pos/pos-terminal.blade.php --}}
<div class="flex h-screen bg-base overflow-hidden">

    {{-- Left: Category Tabs + Product Grid --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Category Tabs (large touch targets) --}}
        <div class="flex gap-2 p-4 bg-surface border-b border-[#2A2A2A] overflow-x-auto">
            @foreach ($categories as $cat)
                <button wire:click="selectCategory({{ $cat->id }})"
                    class="px-5 py-3 rounded-xl text-sm font-semibold whitespace-nowrap min-h-[48px]
                           {{ $selectedCategory === $cat->id
                              ? 'bg-amber-500 text-black'
                              : 'bg-elevated text-gray-300 hover:bg-[#2A2A2A]' }}
                           transition-colors duration-150">
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>

        {{-- Product Grid --}}
        <div class="flex-1 overflow-y-auto p-4 grid grid-cols-3 gap-3 content-start">
            @foreach ($products as $product)
                <button wire:click="addToCart({{ $product->id }})"
                    class="bg-surface border border-[#2A2A2A] rounded-2xl p-4 flex flex-col items-center gap-2
                           hover:border-amber-500 hover:bg-elevated active:scale-95 transition-all
                           min-h-[140px] touch-manipulation">
                    <span class="text-3xl">{{ $product->emoji ?? '☕' }}</span>
                    <span class="text-sm font-medium text-gray-100 text-center">{{ $product->name }}</span>
                    <span class="text-amber-400 font-bold text-base">{{ number_format($product->price, 2) }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- Right: Order Cart --}}
    <div class="w-96 bg-surface border-l border-[#2A2A2A] flex flex-col">
        @livewire('pos.order-cart')
    </div>

</div>
```

---

## 🛒 OrderCart Livewire Component

```php
// app/Livewire/Pos/OrderCart.php
class OrderCart extends Component
{
    public array $items = [];
    public string $orderType = 'dine_in';
    public ?int $tableId = null;

    public function addItem(int $productId, int $qty = 1): void
    {
        $product = Product::find($productId);
        $key = "product_{$productId}";

        if (isset($this->items[$key])) {
            $this->items[$key]['qty'] += $qty;
        } else {
            $this->items[$key] = [
                'id'    => $product->id,
                'name'  => $product->name,
                'price' => $product->price,
                'qty'   => $qty,
            ];
        }
    }

    public function removeItem(string $key): void
    {
        unset($this->items[$key]);
    }

    public function getSubtotalProperty(): float
    {
        return collect($this->items)->sum(fn($i) => $i['price'] * $i['qty']);
    }

    public function render(): View
    {
        return view('livewire.pos.order-cart');
    }
}
```

---

## 🔐 Role-Based UI (Blade + Spatie)

```blade
@role('admin')
    <a href="{{ route('settings') }}">⚙️ Settings</a>
@endrole

@can('manage products')
    <button wire:click="deleteProduct({{ $product->id }})"
        class="px-4 py-2 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30">
        Delete
    </button>
@endcan
```

---

> 💡 **AI Hint**: Always use `wire:click`, `wire:model.live`, and `wire:loading` directives. Keep JS minimal — Alpine.js only for pure UI toggling (dropdowns, mobile menu). All business logic stays in Livewire PHP classes.
