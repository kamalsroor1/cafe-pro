<div>
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-100">المصروفات</h2>
        <button onclick="Livewire.dispatch('openExpenseModal')" class="px-6 py-2 bg-amber-500 text-black font-bold rounded-xl hover:bg-amber-400 transition-colors">
            إضافة مصروف
        </button>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex gap-4 flex-wrap">
        <select wire:model.live="categoryId" class="bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500">
            <option value="">جميع الفئات</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        
        <input type="date" wire:model.live="dateFrom" class="bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500">
        <span class="text-gray-400 self-center">إلى</span>
        <input type="date" wire:model.live="dateTo" class="bg-base border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500">
    </div>

    <div class="bg-surface border border-[#2A2A2A] rounded-2xl overflow-hidden">
        <table class="w-full text-right">
            <thead class="bg-base border-b border-[#2A2A2A]">
                <tr>
                    <th class="p-4 text-gray-400 font-medium">التاريخ</th>
                    <th class="p-4 text-gray-400 font-medium">الفئة</th>
                    <th class="p-4 text-gray-400 font-medium">الوصف</th>
                    <th class="p-4 text-gray-400 font-medium">المبلغ</th>
                    <th class="p-4 text-gray-400 font-medium">بواسطة</th>
                    <th class="p-4 text-gray-400 font-medium">إجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#2A2A2A]">
                @foreach($expenses as $expense)
                    <tr class="hover:bg-elevated transition-colors">
                        <td class="p-4 text-gray-100">{{ $expense->expense_date->format('M d, Y') }}</td>
                        <td class="p-4 text-gray-300 font-medium">{{ $expense->category->name }}</td>
                        <td class="p-4 text-gray-400">{{ Str::limit($expense->description, 30) }}</td>
                        <td class="p-4 text-amber-500 font-bold">${{ number_format($expense->amount, 2) }}</td>
                        <td class="p-4 text-gray-400">{{ $expense->recordedBy->name }}</td>
                        <td class="p-4">
                            <button wire:click="delete({{ $expense->id }})" class="text-red-400 hover:text-red-300 transition-colors" onclick="return confirm('هل أنت متأكد من الحذف؟') || event.stopImmediatePropagation()">
                                حذف
                            </button>
                        </td>
                    </tr>
                @endforeach
                @if($expenses->isEmpty())
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">لا توجد مصروفات للمعايير المحددة.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $expenses->links() }}
    </div>

    @livewire('expenses.expense-form')
</div>
