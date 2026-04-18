<div class="flex h-screen bg-gray-100 p-3 gap-3 overflow-hidden" dir="rtl">
    
    <div class="w-2/3 flex flex-col gap-3">
        <div class="bg-white p-4 rounded-xl shadow-sm flex justify-between items-center">
            <h1 class="text-2xl font-black text-gray-800 underline decoration-blue-500">قائمة المشروبات</h1>
            <input type="text" placeholder="بحث سريع..." class="border rounded-lg px-4 py-2 w-64 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div class="grid grid-cols-3 lg:grid-cols-4 gap-4 overflow-y-auto pb-10">
            @foreach($products as $product)
                <button 
                    wire:key="prod-{{ $product->id }}"
                    wire:click="addToCart({{ $product->id }})"
                    class="group relative h-40 bg-white p-4 rounded-2xl shadow-sm hover:shadow-md active:scale-95 transition-all flex flex-col items-center justify-between border-2 border-transparent hover:border-blue-400">
                    
                    <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center text-2xl group-hover:bg-blue-500 group-hover:text-white transition-colors">
                        ☕
                    </div>
                    
                    <div class="text-center">
                        <div class="font-bold text-gray-800 text-lg">{{ $product->name }}</div>
                        <div class="text-blue-600 font-black">{{ number_format($product->price, 2) }} ج.م</div>
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    <div class="w-1/3 flex flex-col bg-white rounded-2xl shadow-lg border border-gray-200">
        <div class="p-5 border-b flex justify-between items-center bg-gray-50 rounded-t-2xl">
            <h2 class="text-xl font-bold text-gray-700">طلب جديد</h2>
            <button wire:click="clearCart" class="text-red-500 text-sm hover:underline">مسح الكل</button>
        </div>

        <div class="flex-grow overflow-y-auto p-4 space-y-3">
            @forelse($cart as $id => $item)
                <div class="flex justify-between items-center bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <div class="flex items-center gap-3">
                        <button wire:click="removeFromCart({{ $id }})" class="bg-red-100 text-red-600 w-8 h-8 rounded-lg font-bold">-</button>
                        <div>
                            <div class="font-bold text-gray-800">{{ $item['name'] }}</div>
                            <div class="text-xs text-gray-500">ج.م {{ $item['price'] }} × {{ $item['quantity'] }}</div>
                        </div>
                    </div>
                    <div class="font-black text-blue-700">
                        {{ number_format($item['price'] * $item['quantity'], 2) }}
                    </div>
                </div>
            @empty
                <div class="h-full flex flex-col items-center justify-center text-gray-400 opacity-50">
                    <span class="text-6xl mb-4">🛒</span>
                    <p>السلة فارغة حالياً</p>
                </div>
            @endforelse
        </div>

        <div class="p-5 bg-gray-50 rounded-b-2xl border-t">
            <div class="flex justify-between items-center mb-6">
                <span class="text-gray-600 font-bold">الإجمالي النهائي</span>
                <span class="text-3xl font-black text-green-600">{{ number_format($total, 2) }} ج.م</span>
            </div>

            <button 
                @if($total == 0) disabled @endif
                class="w-full py-5 bg-green-600 text-white rounded-2xl text-2xl font-black shadow-lg hover:bg-green-700 disabled:bg-gray-300 disabled:shadow-none active:scale-95 transition-all">
                دفع وطباعة (F10)
            </button>
        </div>
    </div>
</div>