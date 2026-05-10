<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\Shift;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ExpenseManager extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingExpense = null;
    public $title = '';
    public $amount = '';
    public $notes = '';
    public $search = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0.01',
        'notes' => 'nullable|string|max:1000',
    ];

    public function mount()
    {
        // التحقق من أن المستخدم هو OWNER أو CASHIER
        if (!in_array(Auth::user()->role, ['owner', 'cashier'])) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }
    }

    public function openModal($expenseId = null)
    {
        $this->resetValidation();
        $this->reset(['title', 'amount', 'notes']);

        if ($expenseId) {
            $expense = Expense::findOrFail($expenseId);
            $this->editingExpense = $expense;
            $this->title = $expense->title;
            $this->amount = $expense->amount;
            $this->notes = $expense->notes;
        } else {
            $this->editingExpense = null;
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingExpense = null;
        $this->reset(['title', 'amount', 'notes']);
    }

    public function saveExpense()
    {
        $this->validate();

        // الحصول على الوردية النشطة الحالية
        $activeShift = Shift::where('user_id', Auth::id())
            ->whereNull('closed_at')
            ->first();

        if (!$activeShift) {
            $this->addError('shift', 'يجب أن تكون هناك وردية مفتوحة لإضافة مصروفات');
            return;
        }

        if ($this->editingExpense) {
            $this->editingExpense->update([
                'title' => $this->title,
                'amount' => $this->amount,
                'notes' => $this->notes,
            ]);
            session()->flash('message', 'تم تحديث المصروف بنجاح.');
        } else {
            Expense::create([
                'title' => $this->title,
                'amount' => $this->amount,
                'notes' => $this->notes,
                'created_by' => Auth::id(),
                'shift_id' => $activeShift->id,
            ]);
            session()->flash('message', 'تم إضافة المصروف بنجاح.');
        }

        $this->closeModal();
    }

    public function deleteExpense($expenseId)
    {
        $expense = Expense::findOrFail($expenseId);
        $expense->delete();
        session()->flash('message', 'تم حذف المصروف بنجاح.');
    }

    public function render()
    {
        $activeShift = Shift::where('user_id', Auth::id())
            ->whereNull('closed_at')
            ->first();

        $expenses = Expense::with(['creator', 'shift'])
            ->when($activeShift, fn ($q) => $q->where('shift_id', $activeShift->id))
            ->when(! $activeShift && Auth::user()?->role === 'cashier', fn ($q) => $q->whereRaw('1 = 0'))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('notes', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $todayExpensesQuery = Expense::query()->latest();
        if ($activeShift) {
            $todayExpensesQuery->where('shift_id', $activeShift->id);
        } elseif (Auth::user()?->role === 'cashier') {
            $todayExpensesQuery->whereRaw('1 = 0');
        } else {
            $todayExpensesQuery->whereDate('created_at', today());
        }

        $todayExpenses = $todayExpensesQuery->get();

        $todayTotalExpenses = (float) $todayExpenses->sum('amount');

        return view('livewire.expense-manager', compact('expenses', 'todayExpenses', 'todayTotalExpenses', 'activeShift'));
    }
}