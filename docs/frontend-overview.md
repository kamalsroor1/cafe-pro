# 🖥️ Frontend Overview — Cafe Pro ERP

## Stack

| Layer | Choice | Notes |
|---|---|---|
| Framework | **Vue.js 3** | Composition API |
| Routing | **Inertia.js** | SPA feel with Laravel backend |
| Styling | **Tailwind CSS** | Utility-first |
| State | **Pinia** | Vue 3 state management |
| HTTP | **Axios** | API calls via Inertia or standalone |
| Icons | **Heroicons** | Consistent with Tailwind |
| Charts | **Chart.js** or **ApexCharts** | For financial dashboards |

---

## Page Structure

```
resources/js/
├── app.js                        ← Inertia bootstrap
├── Pages/
│   ├── Auth/
│   │   └── Login.vue
│   ├── Dashboard/
│   │   └── Index.vue
│   ├── Categories/
│   │   ├── Index.vue
│   │   └── Form.vue
│   ├── Products/
│   │   ├── Index.vue
│   │   └── Form.vue
│   ├── Inventory/
│   │   ├── Ingredients/Index.vue
│   │   ├── Ingredients/Form.vue
│   │   └── Recipes/Edit.vue
│   ├── POS/
│   │   ├── Index.vue             ← Main POS screen
│   │   └── ActiveOrder.vue
│   ├── Shifts/
│   │   ├── Open.vue
│   │   ├── Close.vue
│   │   └── History.vue
│   ├── Orders/
│   │   ├── Index.vue
│   │   └── Show.vue
│   ├── Expenses/
│   │   ├── Index.vue
│   │   └── Form.vue
│   └── Reports/
│       ├── Profit.vue
│       └── Dashboard.vue
├── Layouts/
│   ├── AppLayout.vue             ← Main nav + sidebar
│   └── POSLayout.vue             ← Full-screen POS layout
├── Components/
│   ├── Shared/
│   │   ├── DataTable.vue
│   │   ├── Modal.vue
│   │   ├── Pagination.vue
│   │   └── StatusBadge.vue
│   ├── POS/
│   │   ├── ProductGrid.vue
│   │   ├── OrderCart.vue
│   │   ├── PaymentModal.vue
│   │   └── CategoryTabs.vue
│   └── Dashboard/
│       ├── StatCard.vue
│       └── SalesChart.vue
└── Stores/
    ├── auth.js                   ← User, role, permissions
    ├── shift.js                  ← Active shift state
    └── cart.js                   ← POS cart state
```

---

## Pinia Store: `shift.js`

```javascript
import { defineStore } from 'pinia'
import axios from 'axios'

export const useShiftStore = defineStore('shift', {
  state: () => ({
    activeShift: null,
    loading: false,
  }),

  getters: {
    hasOpenShift: (state) => state.activeShift?.status === 'open',
    shiftId: (state) => state.activeShift?.id,
  },

  actions: {
    async fetchActiveShift() {
      try {
        const { data } = await axios.get('/api/v1/shifts/active')
        this.activeShift = data.data
      } catch {
        this.activeShift = null
      }
    },

    async openShift(openingBalance) {
      const { data } = await axios.post('/api/v1/shifts/open', { opening_balance: openingBalance })
      this.activeShift = data.data
      return data.data
    },

    async closeShift(closingBalance, notes) {
      const { data } = await axios.post(`/api/v1/shifts/${this.shiftId}/close`, {
        closing_balance: closingBalance,
        notes,
      })
      this.activeShift = null
      return data.data
    },
  },
})
```

---

## Pinia Store: `cart.js` (POS Cart)

```javascript
import { defineStore } from 'pinia'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],       // { product, qty, addons, notes }
    orderType: 'dine_in',
    tableId: null,
    customerName: '',
  }),

  getters: {
    subtotal: (state) => state.items.reduce((sum, item) => {
      const addonTotal = item.addons.reduce((a, b) => a + b.price, 0)
      return sum + (item.product.price + addonTotal) * item.qty
    }, 0),

    itemCount: (state) => state.items.reduce((sum, item) => sum + item.qty, 0),
  },

  actions: {
    addItem(product, qty = 1, addons = []) {
      const existing = this.items.find(i => i.product.id === product.id)
      if (existing) {
        existing.qty += qty
      } else {
        this.items.push({ product, qty, addons, notes: '' })
      }
    },

    removeItem(productId) {
      this.items = this.items.filter(i => i.product.id !== productId)
    },

    clearCart() {
      this.items = []
      this.tableId = null
      this.customerName = ''
    },

    toOrderPayload() {
      return {
        type: this.orderType,
        table_id: this.tableId,
        customer_name: this.customerName,
        items: this.items.map(item => ({
          product_id: item.product.id,
          qty: item.qty,
          addon_ids: item.addons.map(a => a.id),
          notes: item.notes,
        })),
      }
    },
  },
})
```

---

## Role-Based UI Guards

```vue
<!-- Composable: usePermissions.js -->
<script>
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export function usePermissions() {
  const page = usePage()

  const hasPermission = (permission) =>
    page.props.auth.permissions.includes(permission)

  const hasRole = (role) =>
    page.props.auth.roles.includes(role)

  return { hasPermission, hasRole }
}
</script>

<!-- Usage in template -->
<template>
  <button v-if="hasPermission('manage products')" @click="deleteProduct">
    Delete Product
  </button>
</template>
```
