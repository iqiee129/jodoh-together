<?php

use function Livewire\Volt\layout;
use function Livewire\Volt\mount;
use function Livewire\Volt\state;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Expense;
use App\Models\WeddingDetail;

layout('layouts.app');

state([
    'user' => null,

    'search' => '',
    'categoryFilter' => 'all',
    'statusFilter' => 'all',

    'showExpenseModal' => false,
    'modalMode' => 'add',
    'editingExpenseId' => null,

    'expense_title' => '',
    'expense_description' => '',
    'expense_category' => 'venue',
    'expense_amount' => '',
    'expense_date' => '',
    'expense_status' => 'pending',

    'showBudgetModal' => false,
    'edit_total_budget' => '',
    'showDateRangeModal' => false,
'startDate' => '',
'endDate' => '',
]);

mount(function () {
    $this->user = Auth::user();
});

$openAddExpenseModal = function () {
    $this->modalMode = 'add';
    $this->editingExpenseId = null;

    $this->expense_title = '';
    $this->expense_description = '';
    $this->expense_category = 'venue';
    $this->expense_amount = '';
    $this->expense_date = '';
    $this->expense_status = 'pending';

    $this->showExpenseModal = true;
};

$openEditExpenseModal = function ($expenseId) {
    $expense = Expense::where('user_id', $this->user->id)->findOrFail($expenseId);

    $this->modalMode = 'edit';
    $this->editingExpenseId = $expense->id;

    $this->expense_title = $expense->title;
    $this->expense_description = $expense->description;
    $this->expense_category = $expense->category ?? 'others';
$this->expense_status = $expense->status ?? 'pending';
    $this->expense_amount = $expense->amount;
    $this->expense_date = $expense->expense_date ? Carbon::parse($expense->expense_date)->format('Y-m-d') : '';
    

    $this->showExpenseModal = true;
};

$closeExpenseModal = function () {
    $this->showExpenseModal = false;
};

$saveExpense = function () {
    $this->validate([
        'expense_title' => 'required|string|max:255',
        'expense_description' => 'nullable|string|max:1000',
        'expense_category' => 'required|string|max:100',
        'expense_amount' => 'required|numeric|min:0',
        'expense_date' => 'required|date',
        'expense_status' => 'required|in:paid,pending',
    ]);

    $data = [
    'name' => $this->expense_title,
    'title' => $this->expense_title,
    'description' => $this->expense_description,
    'category' => $this->expense_category,
    'amount' => $this->expense_amount,
    'expense_date' => $this->expense_date,
    'status' => $this->expense_status,
];

    if ($this->modalMode === 'edit' && $this->editingExpenseId) {
        $expense = Expense::where('user_id', $this->user->id)->findOrFail($this->editingExpenseId);
        $expense->update($data);

        session()->flash('budget_success', 'Expense updated successfully.');
    } else {
        Expense::create(array_merge($data, [
            'user_id' => $this->user->id,
        ]));

        session()->flash('budget_success', 'Expense added successfully.');
    }

    $this->showExpenseModal = false;
};

$deleteExpense = function ($expenseId) {
    $expense = Expense::where('user_id', $this->user->id)->findOrFail($expenseId);
    $expense->delete();

    session()->flash('budget_success', 'Expense deleted successfully.');
};

$openBudgetModal = function () {
    $weddingDetail = $this->user?->weddingDetail;

    $this->edit_total_budget = $weddingDetail?->total_budget ?? $this->user?->budget ?? 0;
    $this->showBudgetModal = true;
};

$closeBudgetModal = function () {
    $this->showBudgetModal = false;
};

$saveTotalBudget = function () {
    $this->validate([
        'edit_total_budget' => 'required|numeric|min:0',
    ]);

    $this->user->update([
        'budget' => $this->edit_total_budget,
    ]);

    WeddingDetail::updateOrCreate(
        ['user_id' => $this->user->id],
        ['total_budget' => $this->edit_total_budget]
    );

    $this->user = Auth::user()->fresh();

    $this->showBudgetModal = false;

    session()->flash('budget_success', 'Total budget updated successfully.');
};
$openDateRangeModal = function () {
    $this->showDateRangeModal = true;
};

$closeDateRangeModal = function () {
    $this->showDateRangeModal = false;
};

$applyDateRange = function () {
    $this->validate([
        'startDate' => 'nullable|date',
        'endDate' => 'nullable|date',
    ]);

    if ($this->startDate && $this->endDate) {
        if (Carbon::parse($this->endDate)->lt(Carbon::parse($this->startDate))) {
            $this->addError('endDate', 'End date must be after or same as start date.');
            return;
        }
    }

    $this->showDateRangeModal = false;
};

$clearDateRange = function () {
    $this->startDate = '';
    $this->endDate = '';
    $this->showDateRangeModal = false;
};
?>

@php
    $weddingDetail = $user?->weddingDetail;

    $totalBudget = $weddingDetail?->total_budget ?? $user?->budget ?? 0;

    $expenseQuery = Expense::where('user_id', $user?->id);

    if (filled($search)) {
        $expenseQuery->where(function ($query) use ($search) {
            $query->where('title', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhere('category', 'like', '%' . $search . '%');
        });
    }

    if ($categoryFilter !== 'all') {
        $expenseQuery->where('category', $categoryFilter);
    }

    if ($statusFilter !== 'all') {
        $expenseQuery->where('status', $statusFilter);
    }

    if (filled($startDate)) {
    $expenseQuery->whereDate('expense_date', '>=', $startDate);
}

if (filled($endDate)) {
    $expenseQuery->whereDate('expense_date', '<=', $endDate);
}

    $expenses = $expenseQuery
        ->orderBy('expense_date', 'desc')
        ->get();

    $allExpenses = Expense::where('user_id', $user?->id)->get();

    $totalSpent = Expense::where('user_id', auth()->id())
    ->sum('amount');
    $remainingBudget = max($totalBudget - $totalSpent, 0);

    $spentPercentage = $totalBudget > 0
        ? min(round(($totalSpent / $totalBudget) * 100), 100)
        : 0;

    $remainingPercentage = max(100 - $spentPercentage, 0);

    $spentDegrees = $totalBudget > 0
        ? min(($totalSpent / $totalBudget) * 360, 360)
        : 0;

    $categories = [
        'venue' => ['label' => 'Venue', 'icon' => 'fa-building-columns', 'class' => 'venue'],
        'catering' => ['label' => 'Catering', 'icon' => 'fa-bell-concierge', 'class' => 'catering'],
        'attire' => ['label' => 'Attire', 'icon' => 'fa-shirt', 'class' => 'attire'],
        'photography' => ['label' => 'Photography', 'icon' => 'fa-camera', 'class' => 'photo'],
        'decoration' => ['label' => 'Decoration', 'icon' => 'fa-tree', 'class' => 'decoration'],
        'invitations' => ['label' => 'Invitations', 'icon' => 'fa-envelope', 'class' => 'others'],
        'entertainment' => ['label' => 'Entertainment', 'icon' => 'fa-music', 'class' => 'others'],
        'others' => ['label' => 'Others', 'icon' => 'fa-ellipsis', 'class' => 'others'],
    ];
@endphp

<div class="budget-page-wrapper">

    <aside class="sidebar">
        <div class="logo">
            <i class="fa-solid fa-heart"></i>
            <span>Jodoh Together</span>
        </div>

        <nav class="nav-menu">
            <a href="{{ url('dashboard') }}" class="nav-link" wire:navigate>
                <i class="fa-solid fa-house"></i> Dashboard
            </a>

            <a href="{{ url('my/wedding') }}" class="nav-link" wire:navigate>
                <i class="fa-regular fa-calendar-check"></i> My Wedding
            </a>

            <a href="{{ url('tasks') }}" class="nav-link" wire:navigate>
                <i class="fa-regular fa-square-check"></i> Tasks
            </a>

            <a href="{{ url('budget') }}" class="nav-link active" wire:navigate>
                <i class="fa-solid fa-dollar-sign"></i> Budget
            </a>

            <a href="{{ url('vendors') }}" class="nav-link" wire:navigate>
                <i class="fa-solid fa-store"></i> Vendors
            </a>

            <a href="{{ url('calendar') }}" class="nav-link" wire:navigate>
                <i class="fa-regular fa-calendar"></i> Calendar
            </a>
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="nav-link logout-link">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
            </button>
        </form>
    </aside>

    <main class="main-content">
        <header>
            <div class="page-title">
                <h1>Budget</h1>
                <p>Track your wedding expenses and manage your budget.</p>
            </div>

            <div class="header-right">
                <button class="add-btn" type="button" wire:click="openAddExpenseModal">
                    <i class="fa-solid fa-plus"></i> Add Expense
                </button>

                <x-app-notifications />

                <div class="profile-wrap" id="profileWrap">
                    <button class="profile-btn" id="profileBtn" type="button" aria-haspopup="true" aria-expanded="false">
                        <div class="avatar-initials">
                            {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                        </div>

                        <span>{{ $user?->name ?? 'User' }}</span>
                        <i class="fa-solid fa-chevron-down chevron"></i>
                    </button>

                    <div class="profile-dropdown">
                        <div class="profile-summary">
                            <strong>{{ $user?->name ?? 'User' }}</strong>
                            <span>{{ $user?->email ?? 'No email' }}</span>
                        </div>

                        <a href="{{ url('profile') }}" class="dropdown-link" wire:navigate>
                            <i class="fa-regular fa-user"></i> My Profile
                        </a>

                        

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-link logout">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>
@if (session('budget_success'))
    <div class="success-message">
        {{ session('budget_success') }}
    </div>
@endif
        <div class="budget-cards">
    <div class="budget-card" wire:click="openBudgetModal" style="cursor: pointer;">
        <div class="card-icon red">
            <i class="fa-solid fa-wallet"></i>
        </div>

        <div class="budget-card-content">
            <span class="budget-label">Total Budget</span>
            <span class="budget-amount">RM {{ number_format($totalBudget, 0) }}</span>
            <span class="budget-subtext">Click to edit budget</span>
        </div>
    </div>

    <div class="budget-card">
        <div class="card-icon orange">
            <i class="fa-solid fa-coins"></i>
        </div>

        <div class="budget-card-content">
            <span class="budget-label">Total Spent</span>
            <span class="budget-amount">RM {{ number_format($totalSpent, 0) }}</span>
            <span class="budget-subtext">{{ $spentPercentage }}% of budget</span>
        </div>
    </div>

    <div class="budget-card">
        <div class="card-icon green">
            <i class="fa-solid fa-wallet"></i>
        </div>

        <div class="budget-card-content">
            <span class="budget-label">Remaining Budget</span>
            <span class="budget-amount">RM {{ number_format($remainingBudget, 0) }}</span>
            <span class="budget-subtext">{{ $remainingPercentage }}% left</span>
        </div>
    </div>
</div>

        <div class="content-grid">
            <div class="card-wrap" style="padding-bottom: 16px;">
                <div class="filters">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" wire:model.live="search" placeholder="Search expenses...">
                    </div>

                   <select class="filter-select" wire:model.live="categoryFilter">
    <option value="all">All Categories</option>
    <option value="venue">Venue</option>
    <option value="catering">Catering</option>
    <option value="attire">Attire</option>
    <option value="photography">Photography</option>
    <option value="decoration">Decoration</option>
    <option value="invitations">Invitations</option>
    <option value="entertainment">Entertainment</option>
    <option value="others">Others</option>
</select>

                    <select class="filter-select" wire:model.live="statusFilter">
    <option value="all">All Status</option>
    <option value="paid">Paid</option>
    <option value="pending">Pending</option>
</select>

                    <button class="filter-btn" type="button" wire:click="openDateRangeModal">
    <i class="fa-regular fa-calendar"></i>

    @if ($startDate || $endDate)
        {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M') : 'Start' }}
        -
        {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M') : 'End' }}
    @else
        Date Range
    @endif
</button>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Expense</th>
                                <th>Category</th>
                                <th>Date</th>
                                <th>Amount (RM)</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
    @forelse ($expenses as $expense)
        @php
            $category = $categories[$expense->category] ?? $categories['others'];
        @endphp

        <tr>
            <td>
                <div class="expense-name">{{ $expense->title }}</div>

                @if ($expense->description)
                    <div style="font-size: 12px; color: #777; margin-top: 4px;">
                        {{ $expense->description }}
                    </div>
                @endif
            </td>

            <td>
                <div class="category-cell">
                    <div class="cat-icon-wrap {{ $category['class'] }}">
                        <i class="fa-solid {{ $category['icon'] }}"></i>
                    </div>
                    {{ $category['label'] }}
                </div>
            </td>

            <td>
                {{ $expense->expense_date 
                    ? \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') 
                    : 'No date' 
                }}
            </td>

            <td style="font-weight: 600;">
                RM {{ number_format($expense->amount, 0) }}
            </td>

            <td>
                @if ($expense->status === 'paid')
                    <div class="status-icon-badge paid">
                        <i class="fa-solid fa-check"></i> Paid
                    </div>
                @else
                    <div class="status-icon-badge pending">
                        <i class="fa-solid fa-xmark"></i> Pending
                    </div>
                @endif
            </td>

            <td>
                <div class="action-btns">
                    <button 
                        class="action-btn icon-only" 
                        type="button"
                        wire:click="openEditExpenseModal({{ $expense->id }})"
                    >
                        <i class="fa-solid fa-pen"></i>
                    </button>

                    <button 
                        class="action-btn icon-only delete" 
                        type="button"
                        onclick="return confirm('Delete this expense?')"
                        wire:click="deleteExpense({{ $expense->id }})"
                    >
                        <i class="fa-regular fa-trash-can"></i>
                    </button>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" style="text-align: center; padding: 40px; color: #777;">
                No expenses found. Add your first wedding expense.
            </td>
        </tr>
    @endforelse
</tbody>
                    </table>
                </div>

                <div class="table-footer">
                    <i class="fa-solid fa-circle-info" style="color: #bbb;"></i>
                    All amounts are in Malaysian Ringgit (RM)
                </div>
            </div>

            <div class="right-col">
                <div class="card-wrap">
    <div class="card-title">Budget Overview</div>

    <div class="donut-chart-wrap">
        <div
            class="donut-chart"
            style="background: conic-gradient(#dc2626 0deg {{ $spentDegrees }}deg, #22c55e {{ $spentDegrees }}deg 360deg);"
        >
            <div class="donut-inner">
                <span class="donut-val">{{ $spentPercentage }}%</span>
                <span class="donut-label">Spent</span>
            </div>
        </div>
    </div>

    <div class="legend-list">
        <div class="legend-item">
            <div class="legend-left">
                <div class="legend-dot spent-dot"></div>
                <span style="color: #555;">Spent</span>
            </div>

            <span class="legend-val">
                RM {{ number_format($totalSpent, 0) }} ({{ $spentPercentage }}%)
            </span>
        </div>

        <div class="legend-item">
            <div class="legend-left">
                <div class="legend-dot remaining-dot"></div>
                <span style="color: #555;">Remaining</span>
            </div>

            <span class="legend-val">
                RM {{ number_format($remainingBudget, 0) }} ({{ $remainingPercentage }}%)
            </span>
        </div>
    </div>

    <div class="legend-divider"></div>

    <div class="legend-total">
        <span>Total Budget</span>
        <span>RM {{ number_format($totalBudget, 0) }}</span>
    </div>
</div>

                <div class="card-wrap">
                    <div class="card-title">By Category</div>

                    <div class="category-list">
    @foreach ($categories as $key => $category)
        @php
            $categoryTotal = $allExpenses
                ->where('category', $key)
                ->where('status', 'paid')
                ->sum('amount');

            $categoryPercentage = $totalSpent > 0
                ? round(($categoryTotal / $totalSpent) * 100)
                : 0;
        @endphp

        <div class="category-item">
            <div class="category-info">
                <div class="cat-icon-wrap {{ $category['class'] }}">
                    <i class="fa-solid {{ $category['icon'] }}"></i>
                </div>

                <div>
                    <div class="category-name">{{ $category['label'] }}</div>
                    <div class="category-desc">
                        RM {{ number_format($categoryTotal, 0) }} ({{ $categoryPercentage }}%)
                    </div>
                </div>
            </div>

            <div class="category-amount">
                RM {{ number_format($categoryTotal, 0) }}
            </div>
        </div>
    @endforeach
</div>
                </div>
            </div>
        </div>
        @if ($showExpenseModal)
    <div class="modal-backdrop" wire:click="closeExpenseModal">
        <div class="edit-modal" wire:click.stop>
            <div class="modal-header">
                <div>
                    <p class="eyebrow">Budget Tracker</p>
                    <h2>{{ $modalMode === 'edit' ? 'Edit Expense' : 'Add Expense' }}</h2>
                </div>

                <button type="button" class="modal-close-btn" wire:click="closeExpenseModal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveExpense" class="expense-form">
                <div class="form-group full-line">
                    <label>Expense Title</label>
                    <input type="text" wire:model="expense_title" placeholder="Example: Venue deposit">
                    @error('expense_title') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group full-line">
                    <label>Description</label>
                    <textarea wire:model="expense_description" placeholder="Add expense description..."></textarea>
                    @error('expense_description') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <select wire:model="expense_category">
                        <option value="venue">Venue</option>
                        <option value="catering">Catering</option>
                        <option value="attire">Attire</option>
                        <option value="photography">Photography</option>
                        <option value="decoration">Decoration</option>
                        <option value="invitations">Invitations</option>
                        <option value="entertainment">Entertainment</option>
                        <option value="others">Others</option>
                    </select>
                    @error('expense_category') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select wire:model="expense_status">
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                    </select>
                    @error('expense_status') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Amount (RM)</label>
                    <input type="number" wire:model="expense_amount" min="0" step="0.01">
                    @error('expense_amount') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Expense Date</label>
                    <input type="date" wire:model="expense_date">
                    @error('expense_date') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="modal-actions">
                    <button type="button" class="cancel-btn" wire:click="closeExpenseModal">
                        Cancel
                    </button>

                    <button type="submit" class="save-btn" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            {{ $modalMode === 'edit' ? 'Save Changes' : 'Save Expense' }}
                        </span>
                        <span wire:loading>Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

@if ($showBudgetModal)
    <div class="modal-backdrop" wire:click="closeBudgetModal">
        <div class="edit-modal small-modal" wire:click.stop>
            <div class="modal-header">
                <div>
                    <p class="eyebrow">Budget Tracker</p>
                    <h2>Edit Total Budget</h2>
                </div>

                <button type="button" class="modal-close-btn" wire:click="closeBudgetModal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveTotalBudget" class="expense-form single-form">
                <div class="form-group full-line">
                    <label>Total Budget (RM)</label>
                    <input type="number" wire:model="edit_total_budget" min="0" step="0.01">
                    @error('edit_total_budget') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="modal-actions">
                    <button type="button" class="cancel-btn" wire:click="closeBudgetModal">
                        Cancel
                    </button>

                    <button type="submit" class="save-btn" wire:loading.attr="disabled">
                        <span wire:loading.remove>Save Budget</span>
                        <span wire:loading>Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
@if ($showDateRangeModal)
    <div class="modal-backdrop" wire:click="closeDateRangeModal">
        <div class="date-modal" wire:click.stop>
            <div class="modal-header">
                <div>
                    <p class="eyebrow">Filter Expenses</p>
                    <h2>Date Range</h2>
                </div>

                <button type="button" class="modal-close-btn" wire:click="closeDateRangeModal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="date-range-form">
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" wire:model="startDate">
                    @error('startDate') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" wire:model="endDate">
                    @error('endDate') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="cancel-btn" wire:click="clearDateRange">
                    Clear
                </button>

                <button type="button" class="cancel-btn" wire:click="closeDateRangeModal">
                    Cancel
                </button>

                <button type="button" class="save-btn" wire:click="applyDateRange">
                    Apply Filter
                </button>
            </div>
        </div>
    </div>
@endif
    </main>

    <script>
        function initProfileDropdown() {
            const profileWrap = document.getElementById("profileWrap");
            const profileBtn = document.getElementById("profileBtn");

            if (!profileWrap || !profileBtn || profileBtn.dataset.ready === "true") {
                return;
            }

            profileBtn.dataset.ready = "true";

            profileBtn.addEventListener("click", (event) => {
                event.stopPropagation();
                const isOpen = profileWrap.classList.toggle("open");
                profileBtn.setAttribute("aria-expanded", isOpen);
            });

            document.addEventListener("click", (event) => {
                if (!profileWrap.contains(event.target)) {
                    profileWrap.classList.remove("open");
                    profileBtn.setAttribute("aria-expanded", "false");
                }
            });

            document.addEventListener("keydown", (event) => {
                if (event.key === "Escape") {
                    profileWrap.classList.remove("open");
                    profileBtn.setAttribute("aria-expanded", "false");
                }
            });
        }

        document.addEventListener("DOMContentLoaded", initProfileDropdown);
        document.addEventListener("livewire:navigated", initProfileDropdown);
    </script>
<style>
    :root {
        --bg: #f7f3ef;
        --dark: #1b1c22;
        --text: #111827;
        --muted: #6b7280;
        --border: #eeeeee;

        --coral: #d95f4a;
        --coral-dark: #b94e3e;
        --coral-light: #fff1ee;

        --green: #15803d;
        --green-bg: #dcfce7;

        --yellow: #b45309;
        --yellow-bg: #fef3c7;

        --red: #dc2626;
        --red-bg: #fee2e2;

        --shadow: 0 12px 35px rgba(31, 41, 55, 0.07);
        --shadow-hover: 0 16px 42px rgba(31, 41, 55, 0.1);
        --radius: 20px;
    }

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        background: var(--bg);
        color: var(--text);
    }

    .budget-page-wrapper {
        display: flex;
        min-height: 100vh;
        width: 100%;
        background: var(--bg);
    }

    /* Sidebar */
    .sidebar {
        width: 275px;
        min-height: 100vh;
        background: var(--dark);
        color: #ffffff;
        padding: 30px 22px;
        display: flex;
        flex-direction: column;
        position: sticky;
        top: 0;
        flex-shrink: 0;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 14px;
        font-size: 26px;
        font-weight: 800;
        line-height: 1.05;
        margin-bottom: 46px;
    }

    .logo i {
        color: #e5654f;
        font-size: 30px;
    }

    .nav-menu {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 15px 16px;
        border-radius: 16px;
        color: #c9cbd1;
        text-decoration: none;
        font-size: 16px;
        transition: 0.2s ease;
    }

    .nav-link i {
        width: 22px;
        text-align: center;
        font-size: 18px;
    }

    .nav-link:hover,
    .nav-link.active {
        background: #df6048;
        color: #ffffff;
    }

    .logout-form {
        margin-top: auto;
    }

    .logout-link {
        width: 100%;
        border: none;
        background: none;
        text-align: left;
        cursor: pointer;
        font-family: inherit;
    }

    /* Main */
    .main-content {
        flex: 1;
        min-width: 0;
        padding: 44px 48px 60px;
        background: var(--bg);
        overflow-x: hidden;
    }

    header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 24px;
        margin-bottom: 28px;
    }

    .page-title h1 {
        margin: 0 0 8px;
        font-size: 38px;
        font-weight: 900;
        color: var(--text);
        letter-spacing: -0.7px;
    }

    .page-title p {
        margin: 0;
        color: var(--muted);
        font-size: 16px;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 18px;
        flex-wrap: wrap;
    }

    /* Buttons */
    .add-btn,
    .filter-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 12px;
        font-family: inherit;
        font-weight: 800;
        cursor: pointer;
        transition: 0.2s ease;
    }

    .add-btn {
        border: none;
        background: var(--coral);
        color: #ffffff;
        padding: 13px 22px;
        box-shadow: 0 12px 28px rgba(217, 95, 74, 0.24);
    }

    .add-btn:hover {
        background: #c94f3d;
        transform: translateY(-1px);
    }

    .filter-btn {
        height: 44px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        color: #374151;
    }

    .filter-btn:hover {
        border-color: var(--coral);
        color: var(--coral);
        background: #fff7f4;
    }

    /* Notification + profile */
    .notification {
        position: relative;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.2s ease;
    }

    .notification:hover {
        background: #ffffff;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }

    .notification i {
        font-size: 22px;
        color: var(--text);
    }

    .badge {
        position: absolute;
        top: 2px;
        right: 2px;
        min-width: 18px;
        height: 18px;
        border-radius: 999px;
        background: #ff4e42;
        color: #ffffff;
        font-size: 11px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 5px;
    }

    .profile-wrap {
        position: relative;
    }

    .profile-btn {
        border: none;
        background: #ffffff;
        border-radius: 999px;
        padding: 7px 12px 7px 7px;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        font-weight: 700;
        color: var(--text);
        font-family: inherit;
    }

    .avatar-initials {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--coral), #f5b4a8);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        flex-shrink: 0;
    }

    .profile-dropdown {
        position: absolute;
        top: 56px;
        right: 0;
        width: 230px;
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: 0 18px 45px rgba(0, 0, 0, 0.12);
        padding: 12px;
        display: none;
        z-index: 20;
    }

    .profile-wrap.open .profile-dropdown {
        display: block;
    }

    .profile-summary {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
        margin-bottom: 8px;
    }

    .profile-summary strong,
    .profile-summary span {
        display: block;
    }

    .profile-summary span {
        color: var(--muted);
        font-size: 13px;
        margin-top: 4px;
    }

    .dropdown-link {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 11px 12px;
        border-radius: 10px;
        color: #374151;
        text-decoration: none;
        border: none;
        background: none;
        font-family: inherit;
        font-size: 14px;
        cursor: pointer;
    }

    .dropdown-link:hover {
        background: var(--coral-light);
        color: var(--coral);
    }

    .dropdown-link.logout {
        color: #e3342f;
    }

    /* Success message */
    .success-message {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
        padding: 12px 14px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 800;
        margin-bottom: 18px;
    }

    /* Top budget cards */
    .budget-cards {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 22px;
        margin-bottom: 26px;
    }

    .budget-card {
        min-height: 145px;
        padding: 24px;
        border-radius: var(--radius);
        background: linear-gradient(135deg, var(--coral), var(--coral-dark));
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 16px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: 0.25s ease;
    }

    .budget-card::after {
        content: "";
        position: absolute;
        right: -45px;
        top: -55px;
        width: 170px;
        height: 170px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        pointer-events: none;
    }

    .budget-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 42px rgba(185, 78, 62, 0.22);
    }

    .budget-card,
    .budget-card * {
        color: #ffffff;
    }

    .card-icon {
        width: 58px;
        height: 58px;
        min-width: 58px;
        border-radius: 17px;
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 23px;
        position: relative;
        z-index: 2;
    }

    .budget-card-content {
        min-width: 0;
        position: relative;
        z-index: 2;
    }

    .budget-label {
        display: block;
        font-size: 14px;
        font-weight: 800;
        opacity: 0.95;
        margin-bottom: 5px;
    }

    .budget-amount {
        display: block;
        font-size: 31px;
        font-weight: 900;
        line-height: 1.1;
        word-break: break-word;
    }

    .budget-subtext {
        display: block;
        margin-top: 6px;
        font-size: 13px;
        font-weight: 700;
        opacity: 0.95;
    }

    /* Layout */
    .content-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.7fr) minmax(320px, 0.8fr);
        gap: 26px;
        align-items: start;
    }

    .right-col {
        display: grid;
        gap: 26px;
    }

    .card-wrap {
        min-width: 0;
        padding: 24px;
        border-radius: var(--radius);
        background: rgba(255, 255, 255, 0.97);
        border: 1px solid rgba(225, 225, 225, 0.9);
        box-shadow: var(--shadow);
        transition: 0.25s ease;
    }

    .card-wrap:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
    }

    .card-title {
        font-size: 22px;
        font-weight: 900;
        color: var(--text);
        margin-bottom: 20px;
    }

    /* Filters */
    .filters {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 180px 150px 150px;
        gap: 14px;
        margin-bottom: 22px;
    }

    .search-box {
        position: relative;
    }

    .search-box i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }

    .search-box input,
    .filter-select {
        width: 100%;
        height: 44px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
        font-size: 14px;
        outline: none;
        font-family: inherit;
    }

    .search-box input {
        padding: 0 14px 0 42px;
    }

    .filter-select {
        padding: 0 14px;
        color: #374151;
    }

    .search-box input:focus,
    .filter-select:focus {
        border-color: var(--coral);
        box-shadow: 0 0 0 4px rgba(217, 95, 74, 0.12);
    }

    /* Table */
    .table-wrap {
        width: 100%;
        overflow-x: auto;
        border: 1px solid var(--border);
        border-radius: 18px;
        background: #ffffff;
    }

    table {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
    }

    thead {
        background: #fafafa;
    }

    th,
    td {
        padding: 18px 20px;
        text-align: left;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }

    th {
        color: var(--text);
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }

    td {
        color: #374151;
        font-size: 14px;
    }

    tbody tr:hover {
        background: #fff7f4;
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    .expense-name {
        font-weight: 900;
        color: var(--text);
        font-size: 15px;
    }

    .expense-description {
        font-size: 12px;
        color: #777;
        margin-top: 4px;
    }

    .category-cell {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        color: #374151;
    }

    .cat-icon-wrap {
        width: 36px;
        height: 36px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #ffffff;
    }

    .cat-icon-wrap i {
        color: #ffffff;
    }

    .cat-icon-wrap.venue {
        background: var(--coral);
    }

    .cat-icon-wrap.catering {
        background: #f59e0b;
    }

    .cat-icon-wrap.attire {
        background: #8b5cf6;
    }

    .cat-icon-wrap.photo {
        background: #2563eb;
    }

    .cat-icon-wrap.decoration {
        background: #16a34a;
    }

    .cat-icon-wrap.others {
        background: #6b7280;
    }

    .status-icon-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border-radius: 999px;
        padding: 7px 12px;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
    }

    .status-icon-badge.paid {
        background: var(--green-bg);
        color: var(--green);
    }

    .status-icon-badge.pending {
        background: var(--yellow-bg);
        color: var(--yellow);
    }

    .action-btns {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .action-btn {
        height: 38px;
        border: none;
        border-radius: 12px;
        background: #f3f4f6;
        color: #374151;
        padding: 0 12px;
        cursor: pointer;
        font-family: inherit;
        font-weight: 800;
        transition: 0.2s ease;
    }

    .action-btn:hover {
        background: var(--coral-light);
        color: var(--coral);
    }

    .action-btn.icon-only {
        width: 38px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .action-btn.delete {
        background: #fee2e2;
        color: var(--red);
    }

    .action-btn.delete:hover {
        background: var(--red);
        color: #ffffff;
    }

    .table-footer {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--muted);
        font-size: 13px;
        font-weight: 700;
        margin-top: 14px;
    }

    .empty-table {
        text-align: center;
        padding: 34px 20px;
        color: var(--muted);
    }

    .empty-table i {
        font-size: 34px;
        color: var(--coral);
        margin-bottom: 10px;
    }

    .empty-table h3 {
        margin: 0 0 6px;
        color: var(--text);
        font-size: 18px;
    }

    .empty-table p {
        margin: 0 0 18px;
    }

    /* Budget overview */
    .donut-chart-wrap {
        display: flex;
        justify-content: center;
        margin: 18px 0 24px;
    }

    .donut-chart {
        width: 190px;
        height: 190px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .donut-inner {
        width: 118px;
        height: 118px;
        border-radius: 50%;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.03);
    }

    .donut-val {
        font-size: 28px;
        font-weight: 900;
        color: var(--text);
        line-height: 1;
    }

    .donut-label {
        margin-top: 5px;
        color: var(--muted);
        font-size: 13px;
        font-weight: 800;
    }

    .legend-list {
        display: grid;
        gap: 12px;
    }

    .legend-item,
    .legend-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        font-size: 14px;
    }

    .legend-left {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #555;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .spent-dot {
        background: var(--red);
    }

    .remaining-dot {
        background: #22c55e;
    }

    .legend-val,
    .legend-total {
        color: var(--text);
        font-weight: 900;
    }

    .legend-divider {
        height: 1px;
        background: var(--border);
        margin: 18px 0;
    }

    /* By category */
    .category-list {
        display: grid;
        gap: 14px;
    }

    .category-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        padding: 14px;
        border-radius: 16px;
        border: 1px solid var(--border);
        background: #fafafa;
        transition: 0.2s ease;
    }

    .category-item:hover {
        background: #fff7f4;
        border-color: #ffd6cf;
    }

    .category-info {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .category-name,
    .category-amount {
        font-weight: 900;
        color: var(--text);
    }

    .category-desc {
        color: var(--muted);
        font-size: 13px;
        font-weight: 700;
    }

    .category-amount {
        white-space: nowrap;
    }

    /* Modal */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, 0.58);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 24px;
    }

    .edit-modal {
        width: 100%;
        max-width: 720px;
        background: #ffffff;
        border-radius: 22px;
        padding: 28px;
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
    }

    .small-modal {
        max-width: 520px;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 24px;
    }

    .modal-header h2 {
        margin: 0;
        font-size: 26px;
        font-weight: 900;
        color: var(--text);
    }

    .eyebrow {
        margin: 0 0 6px;
        color: var(--coral);
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    .modal-close-btn {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        border: none;
        background: var(--coral-light);
        color: var(--coral);
        cursor: pointer;
        font-size: 18px;
    }

    .expense-form {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .single-form {
        grid-template-columns: 1fr;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .form-group.full-line {
        grid-column: 1 / -1;
    }

    .form-group label {
        font-size: 13px;
        font-weight: 800;
        color: #374151;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 0 14px;
        font-size: 14px;
        outline: none;
        background: #ffffff;
        font-family: inherit;
    }

    .form-group input,
    .form-group select {
        height: 42px;
    }

    .form-group textarea {
        height: 90px;
        padding-top: 12px;
        resize: vertical;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: var(--coral);
        box-shadow: 0 0 0 4px rgba(217, 95, 74, 0.12);
    }

    .error-msg {
        color: var(--red);
        font-size: 12px;
        font-weight: 700;
    }

    .modal-actions {
        grid-column: 1 / -1;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 10px;
    }

    .cancel-btn,
    .save-btn {
        border: none;
        border-radius: 12px;
        padding: 12px 22px;
        font-weight: 800;
        cursor: pointer;
        font-family: inherit;
    }

    .cancel-btn {
        background: #f3f4f6;
        color: #374151;
    }

    .save-btn {
        background: var(--coral);
        color: #ffffff;
    }

    .save-btn:hover {
        background: #c94f3d;
    }

    .cancel-btn:hover {
        background: #e5e7eb;
    }

    /* Responsive */
    @media (max-width: 1180px) {
        .budget-cards,
        .content-grid {
            grid-template-columns: 1fr;
        }

        .filters {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 900px) {
        .budget-page-wrapper {
            flex-direction: column;
        }

        .sidebar {
            width: 100%;
            min-height: auto;
            position: static;
        }

        .nav-menu {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
        }

        .main-content {
            padding: 30px 22px 50px;
        }

        header {
            flex-direction: column;
        }
    }

    @media (max-width: 700px) {
        .expense-form {
            grid-template-columns: 1fr;
        }

        .form-group.full-line,
        .modal-actions {
            grid-column: auto;
        }

        .modal-actions {
            flex-direction: column;
        }

        .cancel-btn,
        .save-btn {
            width: 100%;
        }
    }

    @media (max-width: 650px) {
        .nav-menu,
        .filters {
            grid-template-columns: 1fr;
        }

        .budget-card {
            min-height: 120px;
        }

        .header-right,
        .add-btn {
            width: 100%;
        }
    }
    /* ===== Make budget card icons clearly visible ===== */

.budget-card .card-icon {
    background: #ffffff !important;
    color: #d95f4a !important;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

.budget-card .card-icon i {
    color: #d95f4a !important;
    opacity: 1 !important;
}

/* Optional: different icon colours for each card */
.budget-cards .budget-card:nth-child(1) .card-icon i {
    color: #d95f4a !important;
}

.budget-cards .budget-card:nth-child(2) .card-icon i {
    color: #f59e0b !important;
}

.budget-cards .budget-card:nth-child(3) .card-icon i {
    color: #16a34a !important;
}
/* ===== Date Range Modal ===== */

.date-modal {
    width: 100%;
    max-width: 520px;
    background: #ffffff;
    border-radius: 22px;
    padding: 28px;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
}

.date-range-form {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

@media (max-width: 650px) {
    .date-range-form {
        grid-template-columns: 1fr;
    }
}
</style>
</div>
