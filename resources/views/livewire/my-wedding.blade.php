<?php

use function Livewire\Volt\layout;
use function Livewire\Volt\mount;
use function Livewire\Volt\state;
use function Livewire\Volt\usesFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\WeddingDetail;
use App\Models\Task;



usesFileUploads();

layout('layouts.app');

state([
    'user' => null,
    'weddingDetail' => null,
    'daysToGo' => 0,
    'weddingDateText' => 'Not set',
    'weddingDateShort' => 'Not set',
    'upcomingTasks' => [],
    'notificationCount' => 0,
    'showEditWeddingModal' => false,
'showPhotoModal' => false,

'edit_groom_name' => '',
'edit_bride_name' => '',
'edit_wedding_date' => '',
'edit_venue' => '',
'edit_theme' => '',
'edit_estimated_guests' => '',
'edit_total_budget' => '',
'showTaskModal' => false,

'task_title' => '',
'task_description' => '',
'task_category' => 'venue',
'task_priority' => 'medium',
'task_deadline' => '',
'task_status' => 'pending',
'photo_upload' => null,
]);

mount(function () {
    $this->user = Auth::user();

    $this->weddingDetail = $this->user?->weddingDetail;

    $weddingDate = $this->weddingDetail?->wedding_date ?? $this->user?->wedding_date;

    if ($weddingDate) {
        $date = Carbon::parse($weddingDate);

        $this->daysToGo = max((int) now()->startOfDay()->diffInDays($date->copy()->startOfDay(), false), 0);
        $this->weddingDateText = $date->format('d F Y (l)');
        $this->weddingDateShort = $date->format('d/m/y');
    }

    $this->upcomingTasks = Task::where('user_id', $this->user?->id)
        ->whereDate('deadline', '>=', now())
        ->orderBy('deadline')
        ->limit(5)
        ->get();

    $this->notificationCount = $this->upcomingTasks->count();
});
$openEditWeddingModal = function () {
    $weddingDate = $this->weddingDetail?->wedding_date ?? $this->user?->wedding_date;

    $this->edit_groom_name = $this->user?->name ?? '';
    $this->edit_bride_name = $this->weddingDetail?->partner_name ?? '';
    $this->edit_wedding_date = $weddingDate ? Carbon::parse($weddingDate)->format('Y-m-d') : '';
    $this->edit_venue = $this->weddingDetail?->venue ?? '';
    $this->edit_theme = $this->weddingDetail?->theme ?? '';
    $this->edit_estimated_guests = $this->weddingDetail?->estimated_guests ?? '';
    $this->edit_total_budget = $this->weddingDetail?->total_budget ?? $this->user?->budget ?? '';

    $this->showEditWeddingModal = true;
};

$closeEditWeddingModal = function () {
    $this->showEditWeddingModal = false;
};

$saveWeddingDetails = function () {
    $this->validate([
        'edit_groom_name' => 'required|string|max:255',
        'edit_bride_name' => 'required|string|max:255',
        'edit_wedding_date' => 'required|date',
        'edit_venue' => 'nullable|string|max:255',
        'edit_theme' => 'nullable|string|max:255',
        'edit_estimated_guests' => 'nullable|integer|min:0',
        'edit_total_budget' => 'nullable|numeric|min:0',
    ]);

    $this->user->update([
        'name' => $this->edit_groom_name,
        'wedding_date' => $this->edit_wedding_date,
        'budget' => $this->edit_total_budget ?: 0,
    ]);

    WeddingDetail::updateOrCreate(
        ['user_id' => $this->user->id],
        [
            'partner_name' => $this->edit_bride_name,
            'wedding_date' => $this->edit_wedding_date,
            'venue' => $this->edit_venue,
            'theme' => $this->edit_theme,
            'estimated_guests' => $this->edit_estimated_guests ?: null,
            'total_budget' => $this->edit_total_budget ?: 0,
            'photo' => $this->weddingDetail?->photo,
        ]
    );

    $this->user = Auth::user()->fresh();
    $this->weddingDetail = $this->user->weddingDetail;

    $date = Carbon::parse($this->edit_wedding_date);
    $this->daysToGo = max((int) now()->startOfDay()->diffInDays($date->copy()->startOfDay(), false), 0);
    $this->weddingDateText = $date->format('d F Y (l)');
    $this->weddingDateShort = $date->format('d/m/y');

    $this->showEditWeddingModal = false;

    session()->flash('success', 'Wedding details updated successfully.');
};

$openPhotoModal = function () {
    $this->photo_upload = null;
    $this->showPhotoModal = true;
};

$closePhotoModal = function () {
    $this->photo_upload = null;
    $this->showPhotoModal = false;
};

$saveWeddingPhoto = function () {
    $this->validate([
        'photo_upload' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $path = $this->photo_upload->store('wedding-photos', 'public');

    if ($this->weddingDetail?->photo) {
        Storage::disk('public')->delete($this->weddingDetail->photo);
    }

    WeddingDetail::updateOrCreate(
        ['user_id' => $this->user->id],
        ['photo' => $path]
    );

    $this->user = Auth::user()->fresh();
    $this->weddingDetail = $this->user->weddingDetail;

    $this->photo_upload = null;
    $this->showPhotoModal = false;

    session()->flash('success', 'Wedding photo updated successfully.');
};

$removeWeddingPhoto = function () {
    if ($this->weddingDetail?->photo) {
        Storage::disk('public')->delete($this->weddingDetail->photo);

        $this->weddingDetail->update([
            'photo' => null,
        ]);
    }

    $this->user = Auth::user()->fresh();
    $this->weddingDetail = $this->user->weddingDetail;

    $this->showPhotoModal = false;

    session()->flash('success', 'Wedding photo removed successfully.');
};

$openTaskModal = function () {
    $this->task_title = '';
    $this->task_description = '';
    $this->task_category = 'venue';
    $this->task_priority = 'medium';
    $this->task_deadline = '';
    $this->task_status = 'pending';

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

    Task::create([
        'user_id' => $this->user->id,
        'title' => $this->task_title,
        'description' => $this->task_description,
        'category' => $this->task_category,
        'priority' => $this->task_priority,
        'deadline' => $this->task_deadline,
        'status' => $this->task_status,
    ]);

    $this->upcomingTasks = Task::where('user_id', $this->user->id)
        ->where('status', 'pending')
        ->whereDate('deadline', '>=', now())
        ->orderBy('deadline')
        ->limit(5)
        ->get();

    $this->notificationCount = $this->upcomingTasks->count();

    $this->showTaskModal = false;

    session()->flash('success', 'Task added successfully.');
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

            <a href="{{ url('my/wedding') }}" class="nav-link active" wire:navigate>
                <i class="fa-regular fa-calendar-check"></i> My Wedding
            </a>

            <a href="{{ url('tasks') }}" class="nav-link" wire:navigate>
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
                <h1>My Wedding</h1>
                <p>Manage your wedding details, countdown, tasks and important information.</p>
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

        <section class="layout">

            <div class="left-stack">

                <article class="card wedding-card">
    <button 
    class="couple-photo" 
    type="button" 
    wire:click="openPhotoModal"
    aria-label="Change wedding photo"
    style="background-image: linear-gradient(135deg, rgba(109, 137, 99, 0.38), rgba(242, 238, 227, 0.55)), url('{{ $weddingDetail?->photo ? asset('storage/' . $weddingDetail->photo) : 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?q=80&w=1200' }}');"
>
    <div class="photo-overlay">
        <i class="fa-solid fa-camera"></i>
        <span>{{ $weddingDetail?->photo ? 'Change Photo' : 'Add Wedding Photo' }}</span>
    </div>
</button>

    <div class="wedding-details">
        <div class="title-row">
            <div>
                <p class="eyebrow">Wedding Profile</p>

                <h2>
                    {{ $user?->name ?? 'Groom' }} &amp; {{ $weddingDetail?->partner_name ?? 'Bride' }}
                </h2>
            </div>

            <span class="status-badge teal">Upcoming</span>
        </div>

        <div class="info-list">
            <div class="info-row">
                <i class="fa-regular fa-calendar"></i>

                <div>
                    <span class="label">Wedding Date</span>
                    <strong>{{ $weddingDateText }}</strong>
                </div>
            </div>

            <div class="info-row">
                <i class="fa-solid fa-location-dot"></i>

                <div>
                    <span class="label">Venue</span>
                    <strong>{{ $weddingDetail?->venue ?? 'Not set yet' }}</strong>
                </div>
            </div>
        </div>

        <div class="quick-actions">
            <button class="edit-btn" type="button" wire:click="openEditWeddingModal">
    <i class="fa-solid fa-pen"></i>
    Edit Details
</button>

            <a href="{{ url('calendar') }}" class="secondary-btn" wire:navigate>
                <i class="fa-regular fa-calendar"></i>
                View Calendar
            </a>
        </div>
    </div>
</article>

                <article class="card tasks-card">
    <div class="tasks-card-header">
        <div>
            <p class="eyebrow">Planning Timeline</p>
            <h2>Upcoming Tasks</h2>
        </div>

        <div class="task-header-actions">
            <button type="button" class="add-task-btn" wire:click="openTaskModal">
                <i class="fa-solid fa-plus"></i>
                Add Task
            </button>

            <a href="{{ route('tasks') }}" class="view-tasks-btn" wire:navigate>
                View All Tasks
            </a>
        </div>
    </div>

    <div class="timeline-list">
        @forelse ($upcomingTasks as $task)
            <div class="timeline-item">
                <div class="date-pill">
                    {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d M Y') : 'No date' }}
                </div>

                <span class="node {{ $task->status === 'completed' ? 'completed' : '' }}"></span>

                <div class="task-info">
                    <p class="milestone-title">{{ $task->title }}</p>
                    <p class="milestone-time">
                        {{ $task->description ?: 'No description added' }}
                    </p>
                </div>

                <span class="status-badge {{ $task->status === 'completed' ? 'green' : 'teal' }}">
                    {{ ucfirst($task->status ?? 'Upcoming') }}
                </span>
            </div>
        @empty
            <div class="empty-state">
                <i class="fa-regular fa-circle-check"></i>
                <h3>No upcoming tasks yet</h3>
                <p>Add your first wedding task to start your planning timeline.</p>

                <button type="button" class="view-tasks-btn" wire:click="openTaskModal">
                    Add First Task
                </button>
            </div>
        @endforelse
    </div>
</article>

            </div>

            <div class="right-stack">

                <article class="card countdown-card">
                    <div class="countdown-content">
                        <p class="eyebrow">Wedding Countdown</p>
                        <h2>Your big day is coming</h2>

                        <div class="countdown-main">
                            <strong>{{ $daysToGo }}</strong>
                            <span>days to go</span>
                        </div>

                        <div class="countdown-date">
                            <i class="fa-regular fa-calendar"></i>
                            <span>{{ $weddingDateShort }}</span>
                        </div>
                    </div>

                    <div class="hearts-decoration" aria-hidden="true">
                        <i class="fa-solid fa-heart"></i>
                        <i class="fa-solid fa-heart"></i>
                        <i class="fa-solid fa-heart"></i>
                    </div>
                </article>

                <article class="card information-card">
    <div class="card-title-row">
        <div>
            <p class="eyebrow">Details</p>
            <h2>Wedding Information</h2>
        </div>

        <button 
            class="small-icon-btn" 
            type="button" 
            title="Edit information"
            wire:click="openEditWeddingModal"
        >
            <i class="fa-solid fa-pen"></i>
        </button>
    </div>

    <div class="details-list">
        <div class="detail-item">
            <i class="fa-solid fa-location-dot"></i>
            <div>
                <p>Venue</p>
                <strong>{{ $weddingDetail?->venue ?? 'Not set' }}</strong>
            </div>
        </div>

        <div class="detail-item">
            <i class="fa-solid fa-palette"></i>
            <div>
                <p>Theme</p>
                <strong>{{ $weddingDetail?->theme ?? 'Not set' }}</strong>
            </div>
        </div>

        <div class="detail-item">
            <i class="fa-solid fa-users"></i>
            <div>
                <p>Estimated Guests</p>
                <strong>{{ $weddingDetail?->estimated_guests ?? 'Not set' }}</strong>
            </div>
        </div>

        <div class="detail-item">
            <i class="fa-solid fa-dollar-sign"></i>
            <div>
                <p>Total Budget</p>
                <strong>
                    RM {{ number_format($weddingDetail?->total_budget ?? $user?->budget ?? 0, 0) }}
                </strong>
            </div>
        </div>
    </div>

    <button 
        class="edit-info-btn" 
        type="button"
        wire:click="openEditWeddingModal"
    >
        <i class="fa-solid fa-pen"></i>
        Edit Information
    </button>
</article>

            </div>

        </section>
@if ($showEditWeddingModal)
    <div class="modal-backdrop" wire:click="closeEditWeddingModal">
        <div class="edit-modal" wire:click.stop>
            <div class="modal-header">
                <div>
                    <p class="eyebrow">Edit Wedding Profile</p>
                    <h2>Update Wedding Details</h2>
                </div>

                <button type="button" class="modal-close-btn" wire:click="closeEditWeddingModal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveWeddingDetails" class="edit-wedding-form">
                <div class="form-group">
                    <label>Groom's Name</label>
                    <input type="text" wire:model="edit_groom_name">
                    @error('edit_groom_name') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Bride's Name</label>
                    <input type="text" wire:model="edit_bride_name">
                    @error('edit_bride_name') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Wedding Date</label>
                    <input type="date" wire:model="edit_wedding_date">
                    @error('edit_wedding_date') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Venue</label>
                    <input type="text" wire:model="edit_venue" placeholder="Example: Glass House, Penang">
                    @error('edit_venue') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Theme</label>
                    <input type="text" wire:model="edit_theme" placeholder="Example: Minimalist Elegance">
                    @error('edit_theme') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Estimated Guests</label>
                    <input type="number" wire:model="edit_estimated_guests" min="0">
                    @error('edit_estimated_guests') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group full-line">
                    <label>Total Budget (RM)</label>
                    <input type="number" wire:model="edit_total_budget" min="0">
                    @error('edit_total_budget') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="modal-actions">
                    <button type="button" class="cancel-btn" wire:click="closeEditWeddingModal">
                        Cancel
                    </button>

                    <button type="submit" class="save-btn" wire:loading.attr="disabled">
                        <span wire:loading.remove>Save Changes</span>
                        <span wire:loading>Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

@if ($showPhotoModal)
    <div class="modal-backdrop" wire:click="closePhotoModal">
        <div class="photo-modal" wire:click.stop>
            <div class="modal-header">
                <div>
                    <p class="eyebrow">Wedding Photo</p>
                    <h2>Update Wedding Photo</h2>
                </div>

                <button type="button" class="modal-close-btn" wire:click="closePhotoModal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="photo-preview-box">
                @if ($photo_upload)
                    <img src="{{ $photo_upload->temporaryUrl() }}" alt="New wedding photo preview">
                @elseif ($weddingDetail?->photo)
                    <img src="{{ asset('storage/' . $weddingDetail->photo) }}" alt="Current wedding photo">
                @else
                    <div class="empty-photo-preview">
                        <i class="fa-solid fa-image"></i>
                        <p>No wedding photo uploaded yet.</p>
                    </div>
                @endif
            </div>

            <form wire:submit.prevent="saveWeddingPhoto" class="photo-form">
                <label class="upload-box">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Choose photo</span>
                    <input type="file" wire:model="photo_upload" accept="image/*">
                </label>

                @error('photo_upload') 
                    <span class="error-msg">{{ $message }}</span> 
                @enderror

                <div class="modal-actions">
                    @if ($weddingDetail?->photo)
                        <button type="button" class="remove-btn" wire:click="removeWeddingPhoto">
                            Remove Photo
                        </button>
                    @endif

                    <button type="button" class="cancel-btn" wire:click="closePhotoModal">
                        Cancel
                    </button>

                    <button type="submit" class="save-btn" wire:loading.attr="disabled">
                        <span wire:loading.remove>Save Photo</span>
                        <span wire:loading>Uploading...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
@if ($showTaskModal)
    <div class="modal-backdrop" wire:click="closeTaskModal">
        <div class="edit-modal" wire:click.stop>
            <div class="modal-header">
                <div>
                    <p class="eyebrow">Planning Timeline</p>
                    <h2>Add New Task</h2>
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
                        <span wire:loading.remove>Save Task</span>
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

        .layout {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(340px, 0.95fr);
            gap: 26px;
            align-items: start;
        }

        .left-stack,
        .right-stack {
            display: flex;
            flex-direction: column;
            gap: 26px;
        }

        .card {
            background: rgba(255, 255, 255, 0.97);
            border: 1px solid rgba(225, 225, 225, 0.9);
            border-radius: 20px;
            box-shadow: 0 12px 35px rgba(31, 41, 55, 0.07);
            transition: 0.25s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 42px rgba(31, 41, 55, 0.1);
        }

        .eyebrow {
            margin: 0 0 6px;
            color: #d95f4a;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .wedding-card {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
            padding: 28px;
            min-height: 300px;
            align-items: center;
        }

        .couple-photo {
            width: 250px;
            height: 250px;
            border-radius: 18px;
            background:
                linear-gradient(135deg, rgba(109, 137, 99, 0.78), rgba(242, 238, 227, 0.9)),
                url('https://images.unsplash.com/photo-1519225421980-715cb0215aed?q=80&w=1200') center / cover;
            position: relative;
            overflow: hidden;
        }

        .photo-overlay {
            position: absolute;
            inset: auto 16px 16px 16px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 14px;
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #374151;
            font-weight: 800;
            font-size: 13px;
        }

        .photo-overlay i {
            color: #d95f4a;
        }

        .wedding-details {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .title-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
        }

        .title-row h2 {
            font-size: 32px;
            font-weight: 900;
            color: #111827;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .status-badge {
            border-radius: 999px;
            padding: 7px 13px;
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

        .status-badge.red {
            background: #fee2e2;
            color: #dc2626;
        }

        .info-list {
            display: grid;
            gap: 12px;
        }

        .info-row {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 14px;
            background: #faf7f4;
            border: 1px solid #f0e7e2;
            border-radius: 14px;
        }

        .info-row i {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: #ffffff;
            color: #d95f4a;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .info-row .label {
            display: block;
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 3px;
        }

        .info-row strong {
            color: #111827;
            font-size: 15px;
        }

        .quick-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .edit-btn,
        .secondary-btn,
        .view-tasks-btn,
        .edit-info-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid #ff6b5f;
            color: #e74c3c;
            background: #ffffff;
            padding: 12px 22px;
            border-radius: 12px;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
            transition: 0.2s ease;
            font-family: inherit;
        }

        .edit-btn:hover,
        .view-tasks-btn:hover,
        .edit-info-btn:hover {
            background: #ff6b5f;
            color: #ffffff;
        }

        .secondary-btn {
            border-color: #e5e7eb;
            color: #374151;
        }

        .secondary-btn:hover {
            border-color: #111827;
            background: #111827;
            color: #ffffff;
        }

        .tasks-card {
            padding: 28px;
        }

        .tasks-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            margin-bottom: 22px;
        }

        .tasks-card-header h2,
        .information-card h2,
        .countdown-card h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 900;
            color: #111827;
            letter-spacing: -0.3px;
        }

        .timeline-list {
            display: grid;
            gap: 2px;
        }

        .timeline-item {
            display: grid;
            grid-template-columns: 112px 22px 1fr auto;
            gap: 14px;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .timeline-item:last-child {
            border-bottom: none;
        }

        .date-pill {
            background: #f4eee8;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 13px;
            font-weight: 900;
            color: #111827;
            text-align: center;
        }

        .node {
            width: 14px;
            height: 14px;
            background: #11a6a6;
            border-radius: 50%;
            display: block;
            box-shadow: 0 0 0 5px #e0f7f7;
        }

        .node.completed {
            background: #22c55e;
            box-shadow: 0 0 0 5px #dcfce7;
        }

        .milestone-title {
            margin: 0 0 5px;
            font-weight: 800;
            color: #111827;
        }

        .milestone-time {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }

        .empty-state {
            text-align: center;
            padding: 38px 20px;
            background: #fafafa;
            border: 1px dashed #e5e7eb;
            border-radius: 16px;
        }

        .empty-state i {
            font-size: 38px;
            color: #11a6a6;
            margin-bottom: 12px;
        }

        .empty-state h3 {
            margin: 0 0 6px;
            font-size: 19px;
        }

        .empty-state p {
            margin: 0 0 18px;
            color: #6b7280;
        }

        .countdown-card {
            position: relative;
            min-height: 250px;
            padding: 30px;
            overflow: hidden;
            background: linear-gradient(135deg, #ffffff, #fff4f0);
        }

        .countdown-content {
            position: relative;
            z-index: 2;
        }

        .countdown-main {
            display: flex;
            align-items: baseline;
            gap: 13px;
            margin: 24px 0 20px;
        }

        .countdown-main strong {
            font-size: 76px;
            line-height: 1;
            font-weight: 950;
            color: #111827;
            letter-spacing: -3px;
        }

        .countdown-main span {
            font-size: 24px;
            color: #d92316;
            font-weight: 900;
        }

        .countdown-date {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 14px;
            background: #ffffff;
            font-weight: 800;
            color: #111827;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        .countdown-date i {
            font-size: 21px;
            color: #d95f4a;
        }

        .hearts-decoration {
            position: absolute;
            right: -28px;
            bottom: -20px;
            color: #ef6f61;
            opacity: 0.16;
            font-size: 76px;
            display: flex;
            gap: 8px;
            transform: rotate(-12deg);
        }

        .information-card {
            padding: 28px;
        }

        .card-title-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 22px;
            gap: 12px;
        }

        .small-icon-btn {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            border: 1px solid #ffe0da;
            background: #fff4f1;
            color: #d95f4a;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .small-icon-btn:hover {
            background: #d95f4a;
            color: #ffffff;
        }

        .details-list {
            display: grid;
            gap: 14px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 15px;
            border-radius: 16px;
            background: #fafafa;
            border: 1px solid #eeeeee;
            transition: 0.2s ease;
        }

        .detail-item:hover {
            background: #fff7f4;
            border-color: #ffd6cf;
        }

        .detail-item i {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: #fff1ee;
            color: #d95f4a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            flex-shrink: 0;
        }

        .detail-item p {
            margin: 0 0 5px;
            color: #6b7280;
            font-size: 13px;
        }

        .detail-item strong {
            color: #111827;
            font-size: 15px;
        }

        .edit-info-btn {
            width: 100%;
            margin-top: 20px;
        }

        @media (max-width: 1180px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .right-stack {
                display: grid;
                grid-template-columns: 1fr 1fr;
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

            .wedding-card {
                grid-template-columns: 1fr;
            }

            .couple-photo {
                width: 100%;
                height: 230px;
            }

            .right-stack {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .nav-menu {
                grid-template-columns: 1fr;
            }

            .timeline-item {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .node {
                display: none;
            }

            .tasks-card-header {
                flex-direction: column;
                align-items: stretch;
            }

            .view-tasks-btn {
                width: 100%;
            }

            .countdown-main strong {
                font-size: 58px;
            }

            .countdown-main span {
                font-size: 20px;
            }
        }
        .couple-photo {
    border: none;
    cursor: pointer;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.couple-photo:hover .photo-overlay {
    transform: translateY(-2px);
    background: rgba(255, 255, 255, 0.96);
}

.success-message {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
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

.edit-modal,
.photo-modal {
    width: 100%;
    max-width: 720px;
    background: #ffffff;
    border-radius: 22px;
    padding: 28px;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
}

.photo-modal {
    max-width: 560px;
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

.edit-wedding-form {
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

.form-group input {
    width: 100%;
    height: 42px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 0 14px;
    font-size: 14px;
    outline: none;
}

.form-group input:focus {
    border-color: #d95f4a;
    box-shadow: 0 0 0 4px rgba(217, 95, 74, 0.12);
}

.error-msg {
    color: #dc2626;
    font-size: 12px;
    font-weight: 700;
}

.photo-preview-box {
    width: 100%;
    height: 280px;
    border-radius: 18px;
    overflow: hidden;
    background: #f7f3ef;
    border: 1px dashed #e5e7eb;
    margin-bottom: 18px;
}

.photo-preview-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.empty-photo-preview {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #6b7280;
}

.empty-photo-preview i {
    font-size: 42px;
    color: #d95f4a;
    margin-bottom: 10px;
}

.upload-box {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    min-height: 54px;
    border: 1px dashed #d95f4a;
    border-radius: 14px;
    background: #fff7f4;
    color: #d95f4a;
    font-weight: 800;
    cursor: pointer;
}

.upload-box input {
    display: none;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 18px;
    flex-wrap: wrap;
}

.cancel-btn,
.save-btn,
.remove-btn {
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

.remove-btn {
    background: #fee2e2;
    color: #dc2626;
}

.save-btn:hover {
    background: #c94f3d;
}

.cancel-btn:hover {
    background: #e5e7eb;
}

.remove-btn:hover {
    background: #fecaca;
}

@media (max-width: 700px) {
    .edit-wedding-form {
        grid-template-columns: 1fr;
    }

    .form-group.full-line {
        grid-column: auto;
    }

    .modal-actions {
        flex-direction: column;
    }

    .cancel-btn,
    .save-btn,
    .remove-btn {
        width: 100%;
    }
}
.task-header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
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

.form-group select {
    width: 100%;
    height: 42px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 0 14px;
    font-size: 14px;
    outline: none;
    background: #ffffff;
}

.form-group select:focus {
    border-color: #d95f4a;
    box-shadow: 0 0 0 4px rgba(217, 95, 74, 0.12);
}
.task-form {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.form-group textarea {
    width: 100%;
    height: 90px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 12px 14px;
    font-size: 14px;
    outline: none;
    background: #ffffff;
    font-family: inherit;
    resize: vertical;
}

.form-group textarea:focus {
    border-color: #d95f4a;
    box-shadow: 0 0 0 4px rgba(217, 95, 74, 0.12);
}
    </style>

</div>
