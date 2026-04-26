{{-- MOBILE RESPONSIVE: expense-list.blade.php --}}
<div>
    <div class="mb-4 lg:mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <h2 class="text-xl md:text-2xl font-bold text-gray-100">المصروفات</h2>
        <button onclick="Livewire.dispatch('openExpenseModal')" class="min-h-[48px] w-full md:w-auto px-6 py-2 bg-amber-500 text-black font-bold rounded-xl hover:bg-amber-400 transition-colors shadow-lg shadow-amber-500/10 active:scale-95 flex justify-center items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            إضافة مصروف
        </button>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex flex-col md:flex-row gap-3 w-full">
        <select wire:model.live="categoryId" class="min-h-[48px] w-full md:w-64 bg-surface border border-[#2A2A2A] rounded-xl px-4 py-2 text-gray-100 focus:border-amber-500 appearance-none">
            <option value="">جميع الفئات</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        
        <div class="flex items-center gap-2 w-full md:w-auto">
            <input type="date" wire:model.live="dateFrom" class="min-h-[48px] flex-1 md:w-auto bg-surface border border-[#2A2A2A] rounded-xl px-2 md:px-4 py-2 text-gray-100 text-sm md:text-base focus:border-amber-500">
            <span class="text-gray-400 text-sm">إلى</span>
            <input type="date" wire:model.live="dateTo" class="min-h-[48px] flex-1 md:w-auto bg-surface border border-[#2A2A2A] rounded-xl px-2 md:px-4 py-2 text-gray-100 text-sm md:text-base focus:border-amber-500">
        </div>
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden md:block bg-surface border border-[#2A2A2A] rounded-2xl overflow-x-auto">
        <table class="w-full text-right whitespace-nowrap">
            <thead class="bg-elevated border-b border-[#2A2A2A]">
                <tr>
                    <th class="p-4 text-sm font-semibold text-gray-400">التاريخ</th>
                    <th class="p-4 text-sm font-semibold text-gray-400">الفئة</th>
                    <th class="p-4 text-sm font-semibold text-gray-400">الوصف</th>
                    <th class="p-4 text-sm font-semibold text-gray-400">المبلغ</th>
                    <th class="p-4 text-sm font-semibold text-gray-400">بواسطة</th>
                    <th class="p-4 text-sm font-semibold text-gray-400">إجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#2A2A2A]">
                @foreach($expenses as $expense)
                    <tr class="hover:bg-elevated transition-colors">
                        <td class="p-4 text-gray-100">{{ $expense->expense_date->format('M d, Y') }}</td>
                        <td class="p-4 text-gray-300 font-medium whitespace-break-spaces">{{ $expense->category->name }}</td>
                        <td class="p-4 text-gray-400 max-w-xs truncate">{{ $expense->description }}</td>
                        <td class="p-4 text-amber-500 font-bold">${{ number_format($expense->amount, 2) }}</td>
                        <td class="p-4 text-gray-400">{{ $expense->recordedBy->name }}</td>
                        <td class="p-4">
                            <button wire:click="delete({{ $expense->id }})" class="p-2 min-w-[40px] min-h-[40px] text-gray-400 hover:text-red-500 hover:bg-red-500/10 rounded-lg transition-colors border border-transparent hover:border-red-500/20" onclick="return confirm('هل أنت متأكد من الحذف؟') || event.stopImmediatePropagation()">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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

    {{-- Mobile Cards View --}}
    <div class="md:hidden space-y-3">
        @foreach($expenses as $expense)
        <div class="bg-surface rounded-2xl p-4 border border-[#2A2A2A]">
            <div class="flex justify-between items-start mb-2">
                <div class="flex-1 pr-3">
                    <p class="text-lg font-bold text-amber-500">${{ number_format($expense->amount, 2) }}</p>
                    <p class="text-sm font-medium text-gray-100 mt-1">{{ $expense->category->name }}</p>
                </div>
                <div class="text-left shrink-0">
                    <span class="text-xs text-gray-400">{{ $expense->expense_date->format('M d, Y') }}</span>
                </div>
            </div>
            
            @if($expense->description)
            <div class="bg-base rounded-xl p-3 border border-[#2A2A2A] mb-3">
                <p class="text-sm text-gray-400">{{ $expense->description }}</p>
            </div>
            @endif
            
            <div class="flex items-center justify-between pt-3 border-t border-[#2A2A2A]">
                <span class="text-xs text-gray-500 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    {{ $expense->recordedBy->name }}
                </span>
                <button wire:click="delete({{ $expense->id }})" onclick="return confirm('هل أنت متأكد من الحذف؟') || event.stopImmediatePropagation()" class="min-h-[40px] px-4 rounded-xl text-red-400 text-sm font-bold bg-red-500/10 border border-red-500/20 active:scale-95 transition-all">
                    حذف
                </button>
            </div>
        </div>
        @endforeach
        
        @if($expenses->isEmpty())
        <div class="bg-surface rounded-2xl p-8 border border-[#2A2A2A] text-center text-gray-500">
            لا توجد مصروفات للمعايير المحددة.
        </div>
        @endif
    </div>

    <div class="mt-4 md:mt-6">
        {{ $expenses->links() }}
    </div>

    @livewire('expenses.expense-form')
</div>
{{-- 
  CHANGES:
  - Repositioned add button and full width date filters natively using Flexbox wrapping.
  - Implemented Table-to-Card transition strictly for mobile breakpoints.
  - Handled long description data dynamically with internal styling block (`bg-base`).
  - Added Touch minimum targets on date selects and delete buttons.
--}}
