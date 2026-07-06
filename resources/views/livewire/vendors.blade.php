<?php

use function Livewire\Volt\layout;
use function Livewire\Volt\mount;
use function Livewire\Volt\state;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendor;
use App\Models\Expense;

layout('layouts.app');

state([
    'user' => null,

    'search' => '',
    'categoryFilter' => 'all',
    'stateFilter' => 'all',
    'sortBy' => 'default',

    'showVendorModal' => false,
    'selectedVendorId' => null,
]);

mount(function () {
    $this->user = Auth::user();
});

$openVendorDetails = function ($vendorId) {
    $this->selectedVendorId = $vendorId;
    $this->showVendorModal = true;
};

$closeVendorDetails = function () {
    $this->showVendorModal = false;
    $this->selectedVendorId = null;
};

$addVendorToBudget = function ($vendorId) {
    $vendor = Vendor::where('is_active', true)->findOrFail($vendorId);

    $existingExpense = Expense::where('user_id', $this->user->id)
        ->where('vendor_id', $vendor->id)
        ->first();

    if ($existingExpense) {
        session()->flash('vendor_success', 'This vendor is already added to your budget.');
        return;
    }

    $expenseCategoryMap = [
        'photography' => 'photography',
        'venue' => 'venue',
        'catering' => 'catering',
        'decoration' => 'decoration',
        'entertainment' => 'entertainment',
        'attire' => 'attire',
        'makeup' => 'others',
        'invitation' => 'invitations',
        'transportation' => 'others',
        'others' => 'others',
    ];

    $category = $expenseCategoryMap[$vendor->category] ?? 'others';

    Expense::create([
        'user_id' => $this->user->id,
        'vendor_id' => $vendor->id,
        'name' => $vendor->name,
        'title' => $vendor->name,
        'description' => 'Vendor service: ' . ($vendor->description ?? $vendor->category),
        'category' => $category,
        'amount' => $vendor->price ?? 0,
        'expense_date' => now()->toDateString(),
        'status' => 'pending',
    ]);

    session()->flash('vendor_success', 'Vendor added to your budget.');
};

?>

@php
    $vendorQuery = Vendor::where('is_active', true);

    if (filled($search)) {
        $vendorQuery->where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('category', 'like', '%' . $search . '%')
                ->orWhere('location', 'like', '%' . $search . '%')
                ->orWhere('state', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%');
        });
    }

    if ($categoryFilter !== 'all') {
        $vendorQuery->where('category', $categoryFilter);
    }

    if ($stateFilter !== 'all') {
        $vendorQuery->where('state', $stateFilter);
    }

    if ($sortBy === 'price_low') {
        $vendorQuery->orderBy('price', 'asc');
    } elseif ($sortBy === 'price_high') {
        $vendorQuery->orderBy('price', 'desc');
    } else {
        $vendorQuery->latest();
    }

    $vendors = $vendorQuery->get();

    $availableVendors = Vendor::where('is_active', true)->count();

    $selectedVendorExpenses = Expense::where('user_id', $user?->id)
        ->whereNotNull('vendor_id')
        ->get();

    $selectedVendors = $selectedVendorExpenses->count();
    $estimatedVendorCost = $selectedVendorExpenses->sum('amount');

    $selectedVendorIds = $selectedVendorExpenses
        ->pluck('vendor_id')
        ->filter()
        ->toArray();

    $categories = [
        'photography' => ['label' => 'Photography', 'icon' => 'fa-camera', 'class' => 'photo'],
        'venue' => ['label' => 'Venue', 'icon' => 'fa-building-columns', 'class' => 'venue'],
        'catering' => ['label' => 'Catering', 'icon' => 'fa-bell-concierge', 'class' => 'catering'],
        'decoration' => ['label' => 'Decoration', 'icon' => 'fa-seedling', 'class' => 'decoration'],
        'entertainment' => ['label' => 'Entertainment', 'icon' => 'fa-music', 'class' => 'music'],
        'attire' => ['label' => 'Attire', 'icon' => 'fa-shirt', 'class' => 'attire'],
        'makeup' => ['label' => 'Makeup', 'icon' => 'fa-wand-magic-sparkles', 'class' => 'makeup'],
        'invitation' => ['label' => 'Invitation', 'icon' => 'fa-envelope', 'class' => 'invitation'],
        'transportation' => ['label' => 'Transportation', 'icon' => 'fa-car', 'class' => 'transportation'],
        'others' => ['label' => 'Others', 'icon' => 'fa-ellipsis', 'class' => 'others'],
    ];

    $states = Vendor::where('is_active', true)
        ->whereNotNull('state')
        ->where('state', '!=', '')
        ->select('state')
        ->distinct()
        ->orderBy('state')
        ->pluck('state');

    $selectedVendor = $showVendorModal && $selectedVendorId
        ? Vendor::where('is_active', true)->find($selectedVendorId)
        : null;
@endphp

<div class="vendors-page-wrapper">

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

            <a href="{{ url('budget') }}" class="nav-link" wire:navigate>
                <i class="fa-solid fa-dollar-sign"></i> Budget
            </a>

            <a href="{{ url('vendors') }}" class="nav-link active" wire:navigate>
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
                <h1>Vendors</h1>
                <p>Browse wedding vendors and add suitable services to your budget.</p>
            </div>

            <div class="header-right">
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

        @if (session('vendor_success'))
            <div class="success-message">
                {{ session('vendor_success') }}
            </div>
        @endif

        <section class="vendor-summary-grid">
            <article class="summary-card">
                <div class="summary-icon">
                    <i class="fa-solid fa-store"></i>
                </div>

                <div>
                    <p>Available Vendors</p>
                    <h2>{{ $availableVendors }}</h2>
                    <span>Vendors listed by admin</span>
                </div>
            </article>

            <article class="summary-card">
                <div class="summary-icon">
                    <i class="fa-solid fa-bookmark"></i>
                </div>

                <div>
                    <p>Selected Vendors</p>
                    <h2>{{ $selectedVendors }}</h2>
                    <span>Added to your budget</span>
                </div>
            </article>

            <article class="summary-card">
                <div class="summary-icon">
                    <i class="fa-solid fa-wallet"></i>
                </div>

                <div>
                    <p>Estimated Vendor Cost</p>
                    <h2>RM {{ number_format($estimatedVendorCost, 0) }}</h2>
                    <span>Total selected vendor cost</span>
                </div>
            </article>
        </section>

        <section class="filter-card">
            <div class="filters">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" wire:model.live="search" placeholder="Search vendors...">
                </div>

                <select class="filter-select" wire:model.live="categoryFilter">
                    <option value="all">All Categories</option>
                    @foreach ($categories as $key => $category)
                        <option value="{{ $key }}">{{ $category['label'] }}</option>
                    @endforeach
                </select>

                <select class="filter-select" wire:model.live="stateFilter">
                    <option value="all">All States</option>
                    @foreach ($states as $state)
                        <option value="{{ $state }}">{{ $state }}</option>
                    @endforeach
                </select>

                <select class="filter-select" wire:model.live="sortBy">
                    <option value="default">Sort by Latest</option>
                    <option value="price_low">Lowest Price</option>
                    <option value="price_high">Highest Price</option>
                </select>
            </div>
        </section>

        <section class="vendors-grid">
            @forelse ($vendors as $vendor)
                @php
                    $category = $categories[$vendor->category] ?? $categories['others'];
                    $isSelected = in_array($vendor->id, $selectedVendorIds);

                    $image = $vendor->image_url
                        ?: 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=500&h=320&fit=crop';
                @endphp

                <article class="vendor-card">
                    <div class="vendor-image-wrap">
                        <img src="{{ $image }}" alt="{{ $vendor->name }}">
                        <span class="vendor-badge">{{ $category['label'] }}</span>
                    </div>

                    <div class="vendor-body">
                        <div class="vendor-heading">
                            <div class="vendor-icon {{ $category['class'] }}">
                                <i class="fa-solid {{ $category['icon'] }}"></i>
                            </div>

                            <div>
                                <h3>{{ $vendor->name }}</h3>
                                <p>
                                    <i class="fa-solid fa-location-dot"></i>
                                    {{ $vendor->location ?: 'Location not set' }}
                                    @if ($vendor->state)
                                        , {{ $vendor->state }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <p class="vendor-desc">
                            {{ $vendor->description ?: 'No description added for this vendor yet.' }}
                        </p>

                        <div class="vendor-meta">
                            <span>
                                <i class="fa-solid fa-phone"></i>
                                {{ $vendor->phone ?: 'No phone number' }}
                            </span>

                            <span>
                                <i class="fa-regular fa-envelope"></i>
                                {{ $vendor->email ?: 'No email' }}
                            </span>
                        </div>

                        <div class="vendor-footer">
                            <strong>RM {{ number_format($vendor->price ?? 0, 0) }}</strong>

                            <div class="vendor-actions">
                                <button
                                    class="outline-btn"
                                    type="button"
                                    wire:click="openVendorDetails({{ $vendor->id }})"
                                >
                                    View
                                </button>

                                @if ($isSelected)
                                    <button class="added-btn" type="button" disabled>
                                        <i class="fa-solid fa-check"></i>
                                        Added
                                    </button>
                                @else
                                    <button
                                        class="solid-btn"
                                        type="button"
                                        wire:click="addVendorToBudget({{ $vendor->id }})"
                                    >
                                        Add to Budget
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="empty-vendors">
                    <i class="fa-regular fa-folder-open"></i>
                    <h3>No vendors found</h3>
                    <p>No vendors match your search or filters.</p>
                </div>
            @endforelse
        </section>

        <div class="page-footer">
            <i class="fa-solid fa-circle-info"></i>
            Vendor prices are estimated and can be added into your Budget page.
        </div>

        @if ($showVendorModal && $selectedVendor)
            @php
                $modalCategory = $categories[$selectedVendor->category] ?? $categories['others'];
                $modalIsSelected = in_array($selectedVendor->id, $selectedVendorIds);
                $modalImage = $selectedVendor->image_url
                    ?: 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=800&h=450&fit=crop';
            @endphp

            <div class="modal-backdrop" wire:click="closeVendorDetails">
                <div class="vendor-modal" wire:click.stop>
                    <div class="modal-image">
                        <img src="{{ $modalImage }}" alt="{{ $selectedVendor->name }}">
                        <button type="button" class="modal-close-btn" wire:click="closeVendorDetails">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <div class="modal-content">
                        <div class="modal-heading">
                            <div class="vendor-icon {{ $modalCategory['class'] }}">
                                <i class="fa-solid {{ $modalCategory['icon'] }}"></i>
                            </div>

                            <div>
                                <p class="eyebrow">{{ $modalCategory['label'] }}</p>
                                <h2>{{ $selectedVendor->name }}</h2>
                                <span>
                                    <i class="fa-solid fa-location-dot"></i>
                                    {{ $selectedVendor->location ?: 'Location not set' }}
                                    @if ($selectedVendor->state)
                                        , {{ $selectedVendor->state }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <p class="modal-desc">
                            {{ $selectedVendor->description ?: 'No description added for this vendor yet.' }}
                        </p>

                        <div class="modal-info-grid">
                            <div class="info-box">
                                <span>Phone</span>
                                <strong>{{ $selectedVendor->phone ?: 'No phone number' }}</strong>
                            </div>

                            <div class="info-box">
                                <span>Email</span>
                                <strong>{{ $selectedVendor->email ?: 'No email' }}</strong>
                            </div>

                            <div class="info-box">
                                <span>Estimated Price</span>
                                <strong>RM {{ number_format($selectedVendor->price ?? 0, 0) }}</strong>
                            </div>
                        </div>

                        <div class="modal-actions">
                            <button type="button" class="cancel-btn" wire:click="closeVendorDetails">
                                Close
                            </button>

                            @if ($modalIsSelected)
                                <button class="added-btn large" type="button" disabled>
                                    <i class="fa-solid fa-check"></i>
                                    Already Added
                                </button>
                            @else
                                <button
                                    class="solid-btn large"
                                    type="button"
                                    wire:click="addVendorToBudget({{ $selectedVendor->id }})"
                                >
                                    Add to Budget
                                </button>
                            @endif
                        </div>
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

        .vendors-page-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
            background: var(--bg);
        }

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

        .vendor-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
            margin-bottom: 26px;
        }

        .summary-card {
            min-height: 135px;
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
        }

        .summary-card::after {
            content: "";
            position: absolute;
            right: -45px;
            top: -55px;
            width: 170px;
            height: 170px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 50%;
        }

        .summary-icon {
            width: 58px;
            height: 58px;
            min-width: 58px;
            border-radius: 17px;
            background: #ffffff;
            color: var(--coral);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 23px;
            position: relative;
            z-index: 2;
        }

        .summary-card div:last-child {
            position: relative;
            z-index: 2;
        }

        .summary-card p,
        .summary-card h2,
        .summary-card span {
            color: #ffffff;
        }

        .summary-card p {
            margin: 0 0 5px;
            font-size: 14px;
            font-weight: 800;
            opacity: 0.95;
        }

        .summary-card h2 {
            margin: 0;
            font-size: 31px;
            font-weight: 900;
        }

        .summary-card span {
            display: block;
            margin-top: 6px;
            font-size: 13px;
            font-weight: 700;
            opacity: 0.95;
        }

        .filter-card {
            background: rgba(255, 255, 255, 0.97);
            border: 1px solid rgba(225, 225, 225, 0.9);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow);
            margin-bottom: 26px;
        }

        .filters {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 190px 160px 160px;
            gap: 14px;
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

        .vendors-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
        }

        .vendor-card {
            background: #ffffff;
            border: 1px solid rgba(225, 225, 225, 0.9);
            border-radius: 20px;
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: 0.25s ease;
        }

        .vendor-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .vendor-image-wrap {
            position: relative;
            height: 185px;
            overflow: hidden;
        }

        .vendor-image-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .vendor-badge {
            position: absolute;
            top: 16px;
            left: 16px;
            background: rgba(255, 255, 255, 0.92);
            color: var(--coral);
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 900;
        }

        .vendor-body {
            padding: 22px;
        }

        .vendor-heading {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 14px;
        }

        .vendor-icon {
            width: 46px;
            height: 46px;
            min-width: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 18px;
        }

        .vendor-icon.photo { background: #2563eb; }
        .vendor-icon.venue { background: var(--coral); }
        .vendor-icon.catering { background: #f59e0b; }
        .vendor-icon.decoration { background: #16a34a; }
        .vendor-icon.music { background: #8b5cf6; }
        .vendor-icon.attire { background: #7c3aed; }
        .vendor-icon.makeup { background: #ec4899; }
        .vendor-icon.invitation { background: #0ea5e9; }
        .vendor-icon.transportation { background: #64748b; }
        .vendor-icon.others { background: #6b7280; }

        .vendor-heading h3 {
            margin: 0 0 5px;
            color: var(--text);
            font-size: 18px;
            font-weight: 900;
            line-height: 1.2;
        }

        .vendor-heading p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
        }

        .vendor-heading p i {
            color: var(--coral);
            margin-right: 5px;
        }

        .vendor-desc {
            margin: 0 0 16px;
            color: #4b5563;
            font-size: 14px;
            line-height: 1.55;
        }

        .vendor-meta {
            display: grid;
            gap: 8px;
            margin-bottom: 18px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
        }

        .vendor-meta span {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
            word-break: break-word;
        }

        .vendor-meta i {
            color: var(--coral);
            width: 16px;
        }

        .vendor-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            padding-top: 16px;
            border-top: 1px solid #f0f0f0;
        }

        .vendor-footer strong {
            font-size: 18px;
            color: var(--text);
            font-weight: 900;
            white-space: nowrap;
        }

        .vendor-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .solid-btn,
        .outline-btn,
        .added-btn,
        .cancel-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 12px;
            font-family: inherit;
            font-weight: 800;
            cursor: pointer;
            transition: 0.2s ease;
            text-decoration: none;
        }

        .solid-btn {
            border: none;
            background: var(--coral);
            color: #ffffff;
            padding: 10px 14px;
            font-size: 13px;
            box-shadow: 0 12px 28px rgba(217, 95, 74, 0.18);
        }

        .solid-btn:hover {
            background: #c94f3d;
            transform: translateY(-1px);
        }

        .outline-btn {
            background: #ffffff;
            color: var(--coral);
            border: 1px solid #ff6b5f;
            padding: 10px 14px;
            font-size: 13px;
        }

        .outline-btn:hover {
            background: #ff6b5f;
            color: #ffffff;
        }

        .added-btn {
            border: none;
            background: var(--green-bg);
            color: var(--green);
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 900;
            cursor: not-allowed;
        }

        .solid-btn.large,
        .added-btn.large {
            padding: 12px 20px;
            font-size: 14px;
        }

        .page-footer {
            margin-top: 24px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .page-footer i {
            color: var(--coral);
        }

        .empty-vendors {
            grid-column: 1 / -1;
            background: #ffffff;
            border: 1px dashed #e5e7eb;
            border-radius: 20px;
            padding: 44px 24px;
            text-align: center;
            box-shadow: var(--shadow);
        }

        .empty-vendors i {
            font-size: 38px;
            color: var(--coral);
            margin-bottom: 12px;
        }

        .empty-vendors h3 {
            margin: 0 0 6px;
            font-size: 20px;
            font-weight: 900;
            color: var(--text);
        }

        .empty-vendors p {
            margin: 0;
            color: var(--muted);
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

        .vendor-modal {
            width: 100%;
            max-width: 760px;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
        }

        .modal-image {
            height: 260px;
            position: relative;
            overflow: hidden;
        }

        .modal-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .modal-close-btn {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 42px;
            height: 42px;
            border-radius: 14px;
            border: none;
            background: rgba(255, 255, 255, 0.94);
            color: var(--coral);
            cursor: pointer;
            font-size: 18px;
        }

        .modal-content {
            padding: 26px;
        }

        .modal-heading {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 18px;
        }

        .eyebrow {
            margin: 0 0 4px;
            color: var(--coral);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .modal-heading h2 {
            margin: 0 0 6px;
            font-size: 26px;
            font-weight: 900;
            color: var(--text);
        }

        .modal-heading span {
            color: var(--muted);
            font-size: 14px;
            font-weight: 700;
        }

        .modal-desc {
            color: #4b5563;
            font-size: 14px;
            line-height: 1.6;
            margin: 0 0 20px;
        }

        .modal-info-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 22px;
        }

        .info-box {
            background: #fafafa;
            border: 1px solid #eeeeee;
            border-radius: 16px;
            padding: 14px;
        }

        .info-box span {
            display: block;
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .info-box strong {
            display: block;
            color: var(--text);
            font-size: 14px;
            font-weight: 900;
            word-break: break-word;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            border-top: 1px solid #f0f0f0;
            padding-top: 18px;
        }

        .cancel-btn {
            border: none;
            background: #f3f4f6;
            color: #374151;
            padding: 12px 20px;
        }

        .cancel-btn:hover {
            background: #e5e7eb;
        }

        @media (max-width: 1250px) {
            .vendors-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .vendor-summary-grid {
                grid-template-columns: 1fr;
            }

            .filters {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 900px) {
            .vendors-page-wrapper {
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
            .vendors-grid,
            .filters,
            .nav-menu,
            .modal-info-grid {
                grid-template-columns: 1fr;
            }

            .header-right {
                width: 100%;
            }

            .vendor-footer,
            .modal-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .vendor-actions,
            .solid-btn,
            .outline-btn,
            .added-btn,
            .cancel-btn {
                width: 100%;
            }

            .modal-image {
                height: 200px;
            }
        }
        /* ===== Floating hover effect for Vendors page ===== */

.summary-card,
.filter-card,
.vendor-card {
    transition: 0.25s ease;
}

.summary-card:hover,
.filter-card:hover,
.vendor-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}

/* Make vendor cards feel clickable and smoother */
.vendor-card {
    cursor: pointer;
}

.vendor-card:hover .vendor-image-wrap img {
    transform: scale(1.04);
}

.vendor-image-wrap img {
    transition: 0.35s ease;
}

/* Buttons should not look like the whole card click */
.vendor-actions button {
    cursor: pointer;
}
    </style>
</div>
