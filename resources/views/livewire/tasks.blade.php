<?php

use function Livewire\Volt\layout;
use function Livewire\Volt\mount;
use function Livewire\Volt\state;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use Carbon\Carbon;

layout('layouts.app');

state([
    'user' => null,

    'search' => '',
    'statusFilter' => 'all',
    'categoryFilter' => 'all',
    'priorityFilter' => 'all',
    'sortBy' => 'deadline_asc',

    'showTaskModal' => false,
    'modalMode' => 'add',
    'editingTaskId' => null,

    'task_title' => '',
    'task_description' => '',
    'task_category' => 'venue',
    'task_priority' => 'medium',
    'task_deadline' => '',
    'task_status' => 'pending',
]);

mount(function () {
    $this->user = Auth::user();
});

$openAddTaskModal = function () {
    $this->modalMode = 'add';
    $this->editingTaskId = null;

    $this->task_title = '';
    $this->task_description = '';
    $this->task_category = 'venue';
    $this->task_priority = 'medium';
    $this->task_deadline = '';
    $this->task_status = 'pending';

    $this->showTaskModal = true;
};

$openEditTaskModal = function ($taskId) {
    $task = Task::where('user_id', $this->user->id)->findOrFail($taskId);

    $this->modalMode = 'edit';
    $this->editingTaskId = $task->id;

    $this->task_title = $task->title;
    $this->task_description = $task->description;
    $this->task_category = $task->category ?? 'venue';
    $this->task_priority = $task->priority ?? 'medium';
    $this->task_deadline = $task->deadline ? Carbon::parse($task->deadline)->format('Y-m-d') : '';
    $this->task_status = $task->status ?? 'pending';

    $this->showTaskModal = true;
};

$closeTaskModal = function () {
    $this->showTaskModal = false;
};

$saveTask = function () {
    $this->validate([
        'task_title' => 'required|string|max:255',
        'task_description' => 'nullable|string|max:1000',
        'task_category' => 'required|string|max:100',
        'task_priority' => 'required|in:low,medium,high',
        'task_deadline' => 'required|date',
        'task_status' => 'required|in:pending,completed',
    ]);

    $data = [
        'title' => $this->task_title,
        'description' => $this->task_description,
        'category' => $this->task_category,
        'priority' => $this->task_priority,
        'deadline' => $this->task_deadline,
        'status' => $this->task_status,
    ];

    if ($this->modalMode === 'edit' && $this->editingTaskId) {
        $task = Task::where('user_id', $this->user->id)->findOrFail($this->editingTaskId);
        $task->update($data);

        session()->flash('task_success', 'Task updated successfully.');
    } else {
        Task::create(array_merge($data, [
            'user_id' => $this->user->id,
        ]));

        session()->flash('task_success', 'Task added successfully.');
    }

    $this->showTaskModal = false;
};

$toggleTaskStatus = function ($taskId) {
    $task = Task::where('user_id', $this->user->id)->findOrFail($taskId);

    $task->update([
        'status' => $task->status === 'completed' ? 'pending' : 'completed',
    ]);
};

$deleteTask = function ($taskId) {
    $task = Task::where('user_id', $this->user->id)->findOrFail($taskId);
    $task->delete();

    session()->flash('task_success', 'Task deleted successfully.');
};

?>

<div class="app-wrapper">

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

            <a href="{{ url('tasks') }}" class="nav-link active" wire:navigate>
                <i class="fa-regular fa-square-check"></i> Tasks
            </a>

            <a href="{{ url('budget') }}" class="nav-link" wire:navigate>
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

        <header class="page-header">
            <div>
                <h1>Tasks</h1>
                <p>Organize your wedding checklist, deadlines, and completed tasks.</p>
            </div>

            <div class="user-controls">
                <x-app-notifications />

                <div class="profile-wrap" id="profileWrap">
                    <button class="profile-btn" id="profileBtn" type="button" aria-haspopup="true" aria-expanded="false">
                        <div class="avatar-initials">
                            {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                        </div>

                        <span>{{ $user?->name ?? 'User' }}</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>

                    <div class="profile-dropdown" id="profileMenu">
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

        @php
    $baseQuery = Task::where('user_id', $user?->id);

    $totalTasks = (clone $baseQuery)->count();
    $pendingTasks = (clone $baseQuery)->where('status', 'pending')->count();
    $completedTasks = (clone $baseQuery)->where('status', 'completed')->count();

    $today = now()->toDateString();

$dueSoonTasks = Task::where('user_id', auth()->id())
    ->where('status', '!=', 'completed')
    ->whereNotNull('deadline')
    ->whereDate('deadline', '>', $today)
    ->whereDate('deadline', '<=', now()->addDays(7)->toDateString())
    ->count();
@endphp

<section class="summary-grid">
    <article class="summary-card">
        <div class="summary-icon coral">
            <i class="fa-regular fa-square-check"></i>
        </div>

        <div>
            <p>Total Tasks</p>
            <h2>{{ $totalTasks }}</h2>
        </div>
    </article>

    <article class="summary-card">
        <div class="summary-icon teal">
            <i class="fa-regular fa-clock"></i>
        </div>

        <div>
            <p>Pending</p>
            <h2>{{ $pendingTasks }}</h2>
        </div>
    </article>

    <article class="summary-card">
        <div class="summary-icon green">
            <i class="fa-solid fa-check"></i>
        </div>

        <div>
            <p>Completed</p>
            <h2>{{ $completedTasks }}</h2>
        </div>
    </article>

    <article class="summary-card">
        <div class="summary-icon gold">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>

        <div>
            <p>Due Soon</p>
            <h2>{{ $dueSoonTasks }}</h2>
        </div>
    </article>
</section>

        @php
    $taskQuery = Task::where('user_id', $user?->id);

    if (filled($search)) {
        $taskQuery->where(function ($query) use ($search) {
            $query->where('title', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhere('category', 'like', '%' . $search . '%');
        });
    }

    if ($statusFilter !== 'all') {
        $taskQuery->where('status', $statusFilter);
    }

    if ($categoryFilter !== 'all') {
        $taskQuery->where('category', $categoryFilter);
    }

    if ($priorityFilter !== 'all') {
        $taskQuery->where('priority', $priorityFilter);
    }

    if ($sortBy === 'deadline_desc') {
        $taskQuery->orderBy('deadline', 'desc');
    } elseif ($sortBy === 'priority_high') {
        $taskQuery->orderByRaw("FIELD(priority, 'high', 'medium', 'low')");
    } elseif ($sortBy === 'priority_low') {
        $taskQuery->orderByRaw("FIELD(priority, 'low', 'medium', 'high')");
    } else {
        $taskQuery->orderBy('deadline', 'asc');
    }

    $taskRows = $taskQuery->get();

    $categoryLabels = [
        'venue' => 'Venue',
        'catering' => 'Catering',
        'invitations' => 'Invitations',
        'attire' => 'Attire',
        'photography' => 'Photography',
        'decoration' => 'Decoration',
        'others' => 'Others',
    ];
@endphp

<section class="card tasks-panel">
    <div class="tasks-panel-header">
        <div>
            <p class="eyebrow">Task Management</p>
            <h2>Planning Tasks</h2>
        </div>

        <button type="button" class="add-task-btn" wire:click="openAddTaskModal">
            <i class="fa-solid fa-plus"></i>
            Add Task
        </button>
    </div>

    @if (session('task_success'))
        <div class="success-message">
            {{ session('task_success') }}
        </div>
    @endif

    <div class="task-filters">
    <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" wire:model.live="search" placeholder="Search tasks...">
    </div>

    <select class="filter-select" wire:model.live="statusFilter">
        <option value="all">All Status</option>
        <option value="pending">Pending</option>
        <option value="completed">Completed</option>
    </select>

    <select class="filter-select" wire:model.live="categoryFilter">
        <option value="all">All Categories</option>
        <option value="venue">Venue</option>
        <option value="catering">Catering</option>
        <option value="photography">Photography</option>
        <option value="decoration">Decoration</option>
        <option value="attire">Attire</option>
        <option value="others">Others</option>
    </select>

    <select class="filter-select" wire:model.live="priorityFilter">
        <option value="all">All Priorities</option>
        <option value="high">High</option>
        <option value="medium">Medium</option>
        <option value="low">Low</option>
    </select>

    <select class="filter-select" wire:model.live="sortBy">
        <option value="deadline_soonest">Sort: Deadline Soonest</option>
        <option value="deadline_latest">Sort: Deadline Latest</option>
        <option value="priority_high">Sort: High Priority</option>
        <option value="created_latest">Sort: Newest</option>
    </select>
</div>

    <div class="task-table-wrapper">
        <table class="task-table">
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Category</th>
                    <th>Deadline</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th class="actions-col">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($taskRows as $task)
                    <tr>
                        <td>
                            <div class="task-name-cell">
                                <label class="custom-checkbox">
                                    <input 
                                        type="checkbox" 
                                        wire:click="toggleTaskStatus({{ $task->id }})"
                                        @checked($task->status === 'completed')
                                    >
                                    <span></span>
                                </label>

                                <div>
                                    <h3>{{ $task->title }}</h3>
                                    <p>{{ $task->description ?: 'No description added.' }}</p>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="category-pill">
                                <i class="fa-solid fa-tag"></i>
                                {{ $categoryLabels[$task->category] ?? ucfirst($task->category ?? 'Others') }}
                            </span>
                        </td>

                        <td>
                            <div class="deadline-cell">
                                <strong>
                                    {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d M Y') : 'No date' }}
                                </strong>

                                @if ($task->deadline)
                                    <span>{{ \Carbon\Carbon::parse($task->deadline)->diffForHumans() }}</span>
                                @endif
                            </div>
                        </td>

                        <td>
                            <span class="priority-badge {{ $task->priority ?? 'medium' }}">
                                {{ ucfirst($task->priority ?? 'Medium') }}
                            </span>
                        </td>

                        <td>
    @php
        $isOverdue = $task->status !== 'completed'
            && $task->deadline
            && \Carbon\Carbon::parse($task->deadline)->toDateString() < now()->toDateString();

        $displayStatus = $isOverdue ? 'overdue' : $task->status;
    @endphp

    <span class="status-badge {{ $displayStatus }}">
        {{ ucfirst($displayStatus) }}
    </span>
</td>

                        <td>
                            <div class="table-actions">
                                <button 
                                    type="button" 
                                    class="icon-action edit" 
                                    title="Edit task"
                                    wire:click="openEditTaskModal({{ $task->id }})"
                                >
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <button 
                                    type="button" 
                                    class="icon-action delete" 
                                    title="Delete task"
                                    onclick="return confirm('Delete this task?')"
                                    wire:click="deleteTask({{ $task->id }})"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fa-regular fa-circle-check"></i>
                                <h3>No tasks found</h3>
                                <p>Add your first wedding task or adjust the filters.</p>

                                <button type="button" class="add-task-btn" wire:click="openAddTaskModal">
                                    <i class="fa-solid fa-plus"></i>
                                    Add Task
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@if ($showTaskModal)
    <div class="modal-backdrop" wire:click="closeTaskModal">
        <div class="edit-modal" wire:click.stop>
            <div class="modal-header">
                <div>
                    <p class="eyebrow">Task Management</p>
                    <h2>{{ $modalMode === 'edit' ? 'Edit Task' : 'Add New Task' }}</h2>
                </div>

                <button type="button" class="modal-close-btn" wire:click="closeTaskModal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveTask" class="task-form">
                <div class="form-group full-line">
                    <label>Task Title</label>
                    <input type="text" wire:model="task_title" placeholder="Example: Confirm catering menu">
                    @error('task_title') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group full-line">
                    <label>Description</label>
                    <textarea wire:model="task_description" placeholder="Add task description..."></textarea>
                    @error('task_description') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <select wire:model="task_category">
                        <option value="venue">Venue</option>
                        <option value="catering">Catering</option>
                        <option value="invitations">Invitations</option>
                        <option value="attire">Attire</option>
                        <option value="photography">Photography</option>
                        <option value="decoration">Decoration</option>
                        <option value="others">Others</option>
                    </select>
                    @error('task_category') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Priority</label>
                    <select wire:model="task_priority">
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                    @error('task_priority') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Deadline</label>
                    <input type="date" wire:model="task_deadline">
                    @error('task_deadline') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select wire:model="task_status">
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                    </select>
                    @error('task_status') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="modal-actions">
                    <button type="button" class="cancel-btn" wire:click="closeTaskModal">
                        Cancel
                    </button>

                    <button type="submit" class="save-btn" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            {{ $modalMode === 'edit' ? 'Save Changes' : 'Save Task' }}
                        </span>

                        <span wire:loading>Saving...</span>
                    </button>
                </div>
            </form>
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
        * {
            box-sizing: border-box;
        }

        .app-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
            background: #f7f3ef;
            color: #111827;
        }

        .sidebar {
            width: 275px;
            min-height: 100vh;
            background: #1b1c22;
            color: #ffffff;
            padding: 30px 22px;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
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
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            font-family: inherit;
        }

        .main-content {
            flex: 1;
            padding: 44px 48px;
            overflow-x: hidden;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 28px;
        }

        .page-header h1 {
            font-size: 38px;
            font-weight: 900;
            margin: 0 0 8px;
            color: #111827;
            letter-spacing: -0.7px;
        }

        .page-header p {
            margin: 0;
            color: #6b7280;
            font-size: 16px;
        }

        .user-controls {
            display: flex;
            align-items: center;
            gap: 20px;
        }

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
            color: #111827;
        }

        .avatar-initials {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #d95f4a, #f5b4a8);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
        }

        .profile-dropdown {
            position: absolute;
            top: 56px;
            right: 0;
            width: 230px;
            background: #ffffff;
            border: 1px solid #eeeeee;
            border-radius: 16px;
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.12);
            padding: 12px;
            display: none;
            z-index: 10;
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
            color: #6b7280;
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
            background: #fff1ee;
            color: #d95f4a;
        }

        .dropdown-link.logout {
            color: #e3342f;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 26px;
        }

        .summary-card {
            background: #ffffff;
            border: 1px solid rgba(225, 225, 225, 0.9);
            border-radius: 20px;
            padding: 22px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 12px 35px rgba(31, 41, 55, 0.07);
        }

        .summary-icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .summary-icon.coral {
            background: #fff1ee;
            color: #d95f4a;
        }

        .summary-icon.teal {
            background: #d9f4f3;
            color: #087b7b;
        }

        .summary-icon.green {
            background: #dcfce7;
            color: #15803d;
        }

        .summary-icon.gold {
            background: #fef3c7;
            color: #b45309;
        }

        .summary-card p {
            margin: 0 0 4px;
            color: #6b7280;
            font-size: 14px;
            font-weight: 700;
        }

        .summary-card h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 900;
            color: #111827;
        }

        .card {
            background: rgba(255, 255, 255, 0.97);
            border: 1px solid rgba(225, 225, 225, 0.9);
            border-radius: 20px;
            box-shadow: 0 12px 35px rgba(31, 41, 55, 0.07);
        }

        .tasks-panel {
            padding: 28px;
        }

        .tasks-panel-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 24px;
        }

        .eyebrow {
            margin: 0 0 6px;
            color: #d95f4a;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .tasks-panel-header h2 {
            margin: 0;
            font-size: 26px;
            font-weight: 900;
            color: #111827;
        }

        .add-task-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            color: #ffffff;
            background: #d95f4a;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 800;
            cursor: pointer;
            font-family: inherit;
            transition: 0.2s ease;
        }

        .add-task-btn:hover {
            background: #c94f3d;
        }

        .task-toolbar {
            display: grid;
            grid-template-columns: 1fr 220px;
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
        .task-toolbar select {
            width: 100%;
            height: 44px;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #ffffff;
            font-size: 14px;
            outline: none;
        }

        .search-box input {
            padding: 0 14px 0 42px;
        }

        .task-toolbar select {
            padding: 0 14px;
        }

        .search-box input:focus,
        .task-toolbar select:focus {
            border-color: #d95f4a;
            box-shadow: 0 0 0 4px rgba(217, 95, 74, 0.12);
        }

        .task-list {
            display: grid;
            gap: 14px;
        }

        .task-card {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            padding: 18px;
            border: 1px solid #eeeeee;
            border-radius: 18px;
            background: #fafafa;
            transition: 0.2s ease;
        }

        .task-card:hover {
            background: #fff7f4;
            border-color: #ffd6cf;
            transform: translateY(-1px);
        }

        .task-left {
            display: flex;
            gap: 14px;
            min-width: 0;
        }

        .task-status-dot {
            width: 14px;
            height: 14px;
            border-radius: 999px;
            margin-top: 6px;
            flex-shrink: 0;
        }

        .task-status-dot.pending {
            background: #11a6a6;
            box-shadow: 0 0 0 5px #e0f7f7;
        }

        .task-status-dot.completed {
            background: #22c55e;
            box-shadow: 0 0 0 5px #dcfce7;
        }

        .task-title-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 6px;
        }

        .task-title-row h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 900;
            color: #111827;
        }

        .task-card p {
            margin: 0 0 10px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.5;
        }

        .task-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            color: #374151;
            font-size: 13px;
            font-weight: 700;
        }

        .task-meta i {
            color: #d95f4a;
            margin-right: 6px;
        }

        .status-badge {
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .status-badge.teal {
            background: #d9f4f3;
            color: #087b7b;
        }

        .status-badge.green {
            background: #dcfce7;
            color: #15803d;
        }
        .status-badge.overdue {
    background: #fee2e2;
    color: #dc2626;
}

        .task-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .icon-action,
        .action-btn {
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-family: inherit;
            font-weight: 800;
            transition: 0.2s ease;
        }

        .icon-action {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .icon-action.edit {
            background: #fff1ee;
            color: #d95f4a;
        }

        .icon-action.delete {
            background: #fee2e2;
            color: #dc2626;
        }

        .icon-action.edit:hover {
            background: #d95f4a;
            color: #ffffff;
        }

        .icon-action.delete:hover {
            background: #dc2626;
            color: #ffffff;
        }

        .action-btn {
            padding: 10px 14px;
            font-size: 13px;
        }

        .complete-btn {
            background: #dcfce7;
            color: #15803d;
        }

        .pending-btn {
            background: #d9f4f3;
            color: #087b7b;
        }

        .complete-btn:hover {
            background: #22c55e;
            color: #ffffff;
        }

        .pending-btn:hover {
            background: #11a6a6;
            color: #ffffff;
        }

        @media (max-width: 1150px) {
            .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 900px) {
            .app-wrapper {
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
                padding: 30px 22px;
            }

            .page-header {
                flex-direction: column;
            }

            .task-toolbar {
                grid-template-columns: 1fr;
            }

            .task-card {
                flex-direction: column;
            }

            .task-actions {
                justify-content: flex-start;
            }
        }

        @media (max-width: 600px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }

            .nav-menu {
                grid-template-columns: 1fr;
            }

            .tasks-panel-header {
                flex-direction: column;
            }

            .add-task-btn {
                width: 100%;
            }

            .task-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .icon-action,
            .action-btn {
                width: 100%;
            }
        }
        .task-toolbar {
    display: grid;
    grid-template-columns: 1fr 180px 180px 180px;
    gap: 14px;
    margin-bottom: 24px;
}

.task-table-wrapper {
    width: 100%;
    overflow-x: auto;
    border: 1px solid #eeeeee;
    border-radius: 18px;
    background: #ffffff;
}

.task-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 950px;
}

.task-table thead {
    background: #fafafa;
}

.task-table th {
    padding: 18px 20px;
    text-align: left;
    color: #111827;
    font-size: 13px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    border-bottom: 1px solid #eeeeee;
}

.task-table td {
    padding: 18px 20px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
}

.task-table tbody tr:hover {
    background: #fff7f4;
}

.task-table tbody tr:last-child td {
    border-bottom: none;
}

.task-name-cell {
    display: flex;
    align-items: flex-start;
    gap: 14px;
}

.task-name-cell h3 {
    margin: 0 0 5px;
    font-size: 16px;
    font-weight: 900;
    color: #111827;
}

.task-name-cell p {
    margin: 0;
    color: #6b7280;
    font-size: 14px;
    line-height: 1.45;
}

.custom-checkbox {
    position: relative;
    width: 22px;
    height: 22px;
    margin-top: 3px;
    flex-shrink: 0;
}

.custom-checkbox input {
    display: none;
}

.custom-checkbox span {
    width: 22px;
    height: 22px;
    border: 2px solid #d1d5db;
    border-radius: 7px;
    display: block;
    background: #ffffff;
}

.custom-checkbox input:checked + span {
    border-color: #22c55e;
    background: #22c55e;
}

.custom-checkbox input:checked + span::after {
    content: "✓";
    color: #ffffff;
    font-size: 14px;
    font-weight: 900;
    position: absolute;
    top: 0px;
    left: 5px;
}

.category-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #374151;
    font-size: 14px;
    font-weight: 700;
}

.category-pill i {
    color: #6b7280;
}

.deadline-cell strong {
    display: block;
    font-size: 14px;
    color: #111827;
    margin-bottom: 3px;
}

.deadline-cell span {
    color: #087b7b;
    font-size: 13px;
    font-weight: 800;
}

.priority-badge {
    display: inline-flex;
    align-items: center;
    border-radius: 8px;
    padding: 7px 12px;
    font-size: 12px;
    font-weight: 900;
}

.priority-badge.high {
    background: #fee2e2;
    color: #dc2626;
}

.priority-badge.medium {
    background: #fef3c7;
    color: #b45309;
}

.priority-badge.low {
    background: #dbeafe;
    color: #2563eb;
}

.actions-col {
    text-align: center !important;
}

.table-actions {
    display: flex;
    justify-content: center;
    gap: 10px;
}

@media (max-width: 1100px) {
    .task-toolbar {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 650px) {
    .task-toolbar {
        grid-template-columns: 1fr;
    }
}
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

.empty-state {
    text-align: center;
    padding: 40px 20px;
}

.empty-state i {
    font-size: 38px;
    color: #11a6a6;
    margin-bottom: 12px;
}

.empty-state h3 {
    margin: 0 0 6px;
    font-size: 19px;
    color: #111827;
}

.empty-state p {
    margin: 0 0 18px;
    color: #6b7280;
}

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
    color: #111827;
}

.modal-close-btn {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    border: none;
    background: #fff1ee;
    color: #d95f4a;
    cursor: pointer;
    font-size: 18px;
}

.task-form {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
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
    border-color: #d95f4a;
    box-shadow: 0 0 0 4px rgba(217, 95, 74, 0.12);
}

.error-msg {
    color: #dc2626;
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
    background: #d95f4a;
    color: #ffffff;
}

.save-btn:hover {
    background: #c94f3d;
}

.cancel-btn:hover {
    background: #e5e7eb;
}

@media (max-width: 700px) {
    .task-form {
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
/* ===== Floating hover effect for Tasks page ===== */

.summary-card,
.card,
.tasks-panel,
.task-table-wrapper,
.task-card {
    transition: 0.25s ease;
}

.summary-card:hover,
.card:hover,
.tasks-panel:hover {
    transform: translateY(-3px);
    box-shadow: 0 16px 42px rgba(31, 41, 55, 0.1);
}

.task-table-wrapper:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 34px rgba(31, 41, 55, 0.09);
}

.task-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(31, 41, 55, 0.1);
    background: #fff7f4;
    border-color: #ffd6cf;
}

/* Smooth table row hover */
.task-table tbody tr {
    transition: 0.2s ease;
}

.task-table tbody tr:hover {
    background: #fff7f4;
}

/* Make summary icons feel cleaner */
.summary-icon {
    transition: 0.25s ease;
}

.summary-card:hover .summary-icon {
    transform: scale(1.06);
}
/* ===== Tasks filter inline layout ===== */

.task-filters {
    display: grid;
    grid-template-columns: minmax(280px, 2fr) minmax(150px, 0.8fr) minmax(170px, 0.9fr) minmax(160px, 0.9fr) minmax(210px, 1fr);
    gap: 14px;
    align-items: center;
    margin-bottom: 26px;
}

.task-filters .search-box {
    position: relative;
    width: 100%;
}

.task-filters .search-box i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 15px;
}

.task-filters .search-box input,
.task-filters .filter-select {
    width: 100%;
    height: 48px;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    background: #ffffff;
    font-size: 14px;
    outline: none;
    font-family: inherit;
    color: #111827;
}

.task-filters .search-box input {
    padding: 0 16px 0 44px;
}

.task-filters .filter-select {
    padding: 0 14px;
}

.task-filters .search-box input:focus,
.task-filters .filter-select:focus {
    border-color: #d95f4a;
    box-shadow: 0 0 0 4px rgba(217, 95, 74, 0.12);
}

/* Remove old sort row spacing if your old CSS has it */
.sort-row,
.sort-filter,
.sort-box {
    width: 100%;
}

@media (max-width: 1250px) {
    .task-filters {
        grid-template-columns: 1fr 1fr;
    }

    .task-filters .search-box {
        grid-column: 1 / -1;
    }
}

@media (max-width: 650px) {
    .task-filters {
        grid-template-columns: 1fr;
    }

    .task-filters .search-box {
        grid-column: auto;
    }
}
.status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 82px;
    height: 32px;
    padding: 0 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 900;
    line-height: 1;
    text-transform: capitalize;
}

.status-badge.pending {
    background: #ccfbf1;
    color: #0f766e;
}

.status-badge.completed {
    background: #dcfce7;
    color: #15803d;
}

.status-badge.overdue {
    background: #fee2e2;
    color: #dc2626;
}
    </style>

</div>
