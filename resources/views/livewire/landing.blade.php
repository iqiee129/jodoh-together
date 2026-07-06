<?php

use function Livewire\Volt\{layout};

layout('layouts.app');

?>

<div class="landing-page">
    <div class="landing-bg-layer"></div>
    <div class="landing-dark-layer"></div>

    <nav class="landing-nav">
        <a href="{{ route('landing') }}" class="brand-wrap" wire:navigate>
            <div class="brand-icon">
                <i class="fa-solid fa-heart"></i>
            </div>

            <div>
                <h1>Jodoh Together</h1>
                <p>Wedding Planner System</p>
            </div>
        </a>

        <div class="nav-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="nav-link dashboard-link" wire:navigate>
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="nav-link" wire:navigate>
                    Login
                </a>

                <a href="{{ route('register') }}" class="nav-btn" wire:navigate>
                    Get Started
                </a>
            @endauth
        </div>
    </nav>

    <main class="landing-shell">
        <section class="hero-section">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fa-solid fa-ring"></i>
                    Smart wedding planning for couples
                </div>

                <p class="eyebrow">Plan Your Dream Wedding</p>

                <h2>
                    Your wedding, your budget, your tasks — beautifully organized.
                </h2>

                <p class="hero-text">
                    Jodoh Together helps couples manage wedding details, checklist tasks, budgets,
                    vendors, and calendar planning in one simple dashboard built for your wedding journey.
                </p>

                <div class="hero-actions">
                    @auth
                        <a href="{{ route('dashboard') }}" class="primary-btn" wire:navigate>
                            Open Dashboard
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="primary-btn" wire:navigate>
                            Start Planning
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                        <a href="{{ route('login') }}" class="secondary-btn" wire:navigate>
                            Login
                        </a>
                    @endauth
                </div>

                <div class="hero-mini">
                    <div>
                        <i class="fa-solid fa-wallet"></i>
                        <strong>Budget</strong>
                        <span>Track paid and pending expenses</span>
                    </div>

                    <div>
                        <i class="fa-solid fa-list-check"></i>
                        <strong>Tasks</strong>
                        <span>Follow your wedding checklist</span>
                    </div>

                    <div>
                        <i class="fa-regular fa-calendar-check"></i>
                        <strong>Calendar</strong>
                        <span>Sync important wedding dates</span>
                    </div>
                </div>
            </div>

            <div class="hero-preview-area">
                <div class="preview-glow"></div>

                <div class="preview-card main-preview">
                    <div class="preview-header">
                        <div>
                            <p>Wedding Progress</p>
                            <h3>68%</h3>
                        </div>

                        <div class="preview-icon">
                            <i class="fa-solid fa-ring"></i>
                        </div>
                    </div>

                    <div class="progress-bar">
                        <span></span>
                    </div>

                    <div class="preview-list">
                        <div>
                            <i class="fa-solid fa-check"></i>
                            <span>Venue booked</span>
                        </div>

                        <div>
                            <i class="fa-solid fa-check"></i>
                            <span>Budget planned</span>
                        </div>

                        <div>
                            <i class="fa-solid fa-clock"></i>
                            <span>Photographer pending</span>
                        </div>
                    </div>
                </div>

                <div class="preview-card float-card vendor-card">
                    <div class="float-icon">
                        <i class="fa-solid fa-store"></i>
                    </div>

                    <div>
                        <strong>Vendors</strong>
                        <span>Browse by category and state</span>
                    </div>
                </div>

                <div class="preview-card float-card budget-card">
                    <div class="float-icon">
                        <i class="fa-solid fa-wallet"></i>
                    </div>

                    <div>
                        <strong>RM 17,500</strong>
                        <span>Total wedding budget</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="features-section refined-section">
    <div class="section-heading refined-heading">
        <span class="section-pill">
            <i class="fa-solid fa-sparkles"></i>
            Jodoh Together Features
        </span>

        <h3>Everything your wedding plan needs</h3>

        <p>
            From wedding details to budget control, Jodoh Together keeps your planning flow clear,
            beautiful, and easy to manage.
        </p>
    </div>

    <div class="features-grid refined-features-grid">
        <div class="feature-card refined-feature-card">
            <div class="feature-topline"></div>

            <div class="feature-icon coral">
                <i class="fa-solid fa-heart"></i>
            </div>

            <span class="feature-number">01</span>

            <h4>My Wedding</h4>

            <p>
                Keep your wedding date, venue, theme, partner name, guest count, and wedding photo in one place.
            </p>
        </div>

        <div class="feature-card refined-feature-card">
            <div class="feature-topline orange-line"></div>

            <div class="feature-icon orange">
                <i class="fa-solid fa-list-check"></i>
            </div>

            <span class="feature-number">02</span>

            <h4>Task Checklist</h4>

            <p>
                Create wedding tasks, set deadlines, track pending items, and complete your checklist step by step.
            </p>
        </div>

        <div class="feature-card refined-feature-card">
            <div class="feature-topline green-line"></div>

            <div class="feature-icon green">
                <i class="fa-solid fa-wallet"></i>
            </div>

            <span class="feature-number">03</span>

            <h4>Budget Tracker</h4>

            <p>
                Monitor your total budget, spending, pending expenses, and remaining balance by category.
            </p>
        </div>

        <div class="feature-card refined-feature-card">
            <div class="feature-topline blue-line"></div>

            <div class="feature-icon blue">
                <i class="fa-solid fa-store"></i>
            </div>

            <span class="feature-number">04</span>

            <h4>Vendor Directory</h4>

            <p>
                Browse vendors for venue, catering, attire, photography, decoration, invitations, and more.
            </p>
        </div>
    </div>
</section>

<section class="planning-section refined-section">
    <div class="planning-card refined-planning-card">
        <div class="planning-copy">
            <span class="section-pill">
                <i class="fa-solid fa-route"></i>
                How It Works
            </span>

            <h3>Plan your wedding journey in 3 simple steps</h3>

            <p>
                Jodoh Together is designed to guide couples from first setup to final preparation without feeling messy.
            </p>

            @auth
                <a href="{{ route('dashboard') }}" class="planning-btn" wire:navigate>
                    Open Dashboard
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            @else
                <a href="{{ route('register') }}" class="planning-btn" wire:navigate>
                    Start Planning Now
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            @endauth
        </div>

        <div class="steps-grid refined-steps-grid">
            <div class="step-item refined-step-item">
                <span>1</span>

                <div>
                    <small>Account Setup</small>
                    <h4>Create your account</h4>
                    <p>Register with email or continue with Google to begin your planning journey.</p>
                </div>
            </div>

            <div class="step-item refined-step-item">
                <span>2</span>

                <div>
                    <small>Wedding Setup</small>
                    <h4>Add wedding details</h4>
                    <p>Enter your date, venue, theme, guest count, and total budget.</p>
                </div>
            </div>

            <div class="step-item refined-step-item">
                <span>3</span>

                <div>
                    <small>Planning Dashboard</small>
                    <h4>Manage everything</h4>
                    <p>Track tasks, expenses, vendors, and calendar reminders from one dashboard.</p>
                </div>
            </div>
        </div>
    </div>
</section>

        

        <section class="cta-section">
            <div>
                <p class="eyebrow">Ready to begin?</p>
                <h3>Plan your wedding with confidence.</h3>
                <p>
                    Start organizing your wedding details, budget, vendors, and timeline today.
                </p>
            </div>

            @auth
                <a href="{{ route('dashboard') }}" class="primary-btn" wire:navigate>
                    Go to Dashboard
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            @else
                <a href="{{ route('register') }}" class="primary-btn" wire:navigate>
                    Create Free Account
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            @endauth
        </section>
    </main>

    <style>
        :root {
            --bg: #f7f3ef;
            --dark: #1b1c22;
            --text: #111827;
            --muted: #6b7280;
            --coral: #d95f4a;
            --coral-dark: #b94e3e;
            --coral-light: #fff1ee;
            --white: #ffffff;
            --shadow: 0 24px 70px rgba(31, 41, 55, 0.18);
            --shadow-hover: 0 30px 90px rgba(31, 41, 55, 0.24);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
        }

        .landing-page {
            min-height: 100vh;
            width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
            position: relative;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            color: var(--text);
            background: var(--bg);
        }

        .landing-bg-layer {
            position: fixed;
            inset: 0;
            background:
                url('{{ asset('images/auth-bg.jpg') }}') center / cover no-repeat;
            z-index: 0;
        }

        .landing-dark-layer {
            position: fixed;
            inset: 0;
            z-index: 1;
            background:
                linear-gradient(
                    90deg,
                    rgba(14, 16, 22, 0.88) 0%,
                    rgba(24, 20, 22, 0.82) 38%,
                    rgba(40, 32, 31, 0.55) 66%,
                    rgba(247, 243, 239, 0.2) 100%
                ),
                linear-gradient(
                    to bottom,
                    rgba(8, 10, 16, 0.52) 0%,
                    rgba(8, 10, 16, 0.45) 48%,
                    rgba(247, 243, 239, 0.96) 84%,
                    rgba(247, 243, 239, 1) 100%
                ),
                radial-gradient(circle at 16% 18%, rgba(217, 95, 74, 0.34), transparent 34%),
                radial-gradient(circle at 76% 20%, rgba(255, 255, 255, 0.12), transparent 34%);
            pointer-events: none;
        }

        .landing-nav {
            position: relative;
            z-index: 2;
            width: min(1180px, calc(100% - 48px));
            margin: 0 auto;
            padding: 28px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
            color: #ffffff;
            text-decoration: none;
            text-shadow: 0 8px 24px rgba(0, 0, 0, 0.45);
        }

        .brand-icon {
            width: 58px;
            height: 58px;
            border-radius: 19px;
            background: linear-gradient(135deg, var(--coral), var(--coral-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 16px 38px rgba(217, 95, 74, 0.38);
        }

        .brand-wrap h1 {
            margin: 0;
            font-size: 30px;
            font-weight: 950;
            letter-spacing: -0.05em;
            line-height: 1;
        }

        .brand-wrap p {
            margin: 6px 0 0;
            font-size: 13px;
            font-weight: 850;
            opacity: 0.95;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-link,
        .nav-btn {
            height: 46px;
            padding: 0 22px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 14px;
            font-weight: 950;
            transition: 0.25s ease;
        }

        .nav-link {
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.42);
            background: rgba(17, 24, 39, 0.18);
            backdrop-filter: blur(10px);
        }

        .nav-link:hover {
            background: #ffffff;
            color: var(--coral);
            transform: translateY(-3px);
        }

        .nav-btn {
            background: var(--coral);
            color: #ffffff;
            box-shadow: 0 16px 34px rgba(217, 95, 74, 0.34);
        }

        .nav-btn:hover {
            background: var(--coral-dark);
            transform: translateY(-3px);
        }

        .landing-shell {
            position: relative;
            z-index: 2;
            width: min(1180px, calc(100% - 48px));
            margin: 0 auto;
            padding: 28px 0 76px;
        }

        .hero-section {
            min-height: 660px;
            display: grid;
            grid-template-columns: 1.04fr 0.96fr;
            gap: 54px;
            align-items: center;
        }

        .hero-content {
            color: #ffffff;
            max-width: 690px;
        }

        .hero-badge {
            width: fit-content;
            margin-bottom: 18px;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.13);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: rgba(255, 255, 255, 0.94);
            display: inline-flex;
            align-items: center;
            gap: 9px;
            font-size: 13px;
            font-weight: 900;
            backdrop-filter: blur(12px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.18);
        }

        .hero-badge i {
            color: #ffd8cf;
        }

        .eyebrow {
            margin: 0 0 12px;
            color: #ffd8cf;
            font-size: 13px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: 0.16em;
        }

        .eyebrow.dark {
            color: var(--coral);
        }

        .hero-content h2 {
            margin: 0;
            font-size: clamp(42px, 5.4vw, 74px);
            line-height: 0.96;
            font-weight: 950;
            letter-spacing: -0.075em;
            color: #ffffff;
            text-shadow:
                0 5px 0 rgba(0, 0, 0, 0.1),
                0 18px 48px rgba(0, 0, 0, 0.65);
        }

        .hero-text {
            max-width: 610px;
            margin: 24px 0 0;
            color: rgba(255, 255, 255, 0.92);
            font-size: 17px;
            font-weight: 760;
            line-height: 1.75;
            text-shadow: 0 10px 28px rgba(0, 0, 0, 0.55);
        }

        .hero-actions {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            margin-top: 34px;
        }

        .primary-btn,
        .secondary-btn {
            height: 56px;
            padding: 0 28px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            font-size: 15px;
            font-weight: 950;
            transition: 0.25s ease;
        }

        .primary-btn {
            background: var(--coral);
            color: #ffffff;
            box-shadow: 0 20px 42px rgba(217, 95, 74, 0.38);
        }

        .primary-btn:hover {
            background: var(--coral-dark);
            transform: translateY(-4px);
            box-shadow: 0 24px 54px rgba(217, 95, 74, 0.46);
        }

        .secondary-btn {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.38);
            backdrop-filter: blur(10px);
        }

        .secondary-btn:hover {
            background: #ffffff;
            color: var(--coral);
            transform: translateY(-4px);
        }

        .hero-mini {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 13px;
            max-width: 650px;
            margin-top: 36px;
        }

        .hero-mini div {
            padding: 17px;
            border-radius: 19px;
            background: rgba(17, 24, 39, 0.34);
            border: 1px solid rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(14px);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.18);
        }

        .hero-mini i {
            color: #ffd8cf;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .hero-mini strong,
        .hero-mini span {
            display: block;
        }

        .hero-mini strong {
            color: #ffffff;
            font-size: 15px;
            font-weight: 950;
        }

        .hero-mini span {
            margin-top: 6px;
            color: rgba(255, 255, 255, 0.78);
            font-size: 12px;
            font-weight: 800;
            line-height: 1.45;
        }

        .hero-preview-area {
            position: relative;
            min-height: 520px;
        }

        .preview-glow {
            position: absolute;
            inset: 18% 4% auto auto;
            width: 380px;
            height: 380px;
            border-radius: 999px;
            background: rgba(217, 95, 74, 0.22);
            filter: blur(45px);
        }

        .preview-card {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.72);
            box-shadow: var(--shadow);
            backdrop-filter: blur(16px);
            transition: 0.25s ease;
        }

        .preview-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-hover);
        }

        .main-preview {
            width: min(430px, 100%);
            padding: 30px;
            border-radius: 32px;
            position: absolute;
            top: 72px;
            right: 22px;
        }

        .preview-header {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            align-items: center;
        }

        .preview-header p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            font-weight: 950;
        }

        .preview-header h3 {
            margin: 8px 0 0;
            font-size: 56px;
            font-weight: 950;
            letter-spacing: -0.06em;
            color: var(--text);
        }

        .preview-icon {
            width: 72px;
            height: 72px;
            border-radius: 24px;
            background: var(--coral-light);
            color: var(--coral);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .progress-bar {
            height: 13px;
            border-radius: 999px;
            background: #f3f4f6;
            margin: 23px 0;
            overflow: hidden;
        }

        .progress-bar span {
            display: block;
            width: 68%;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, var(--coral), #f59e0b);
        }

        .preview-list {
            display: grid;
            gap: 13px;
        }

        .preview-list div {
            padding: 14px 15px;
            border-radius: 16px;
            background: #fafafa;
            color: #374151;
            font-size: 13px;
            font-weight: 950;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .preview-list i {
            color: var(--coral);
        }

        .float-card {
            position: absolute;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px 20px;
            border-radius: 24px;
            min-width: 250px;
        }

        .float-icon {
            width: 50px;
            height: 50px;
            border-radius: 16px;
            background: var(--coral);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .float-card strong,
        .float-card span {
            display: block;
        }

        .float-card strong {
            color: var(--text);
            font-size: 15px;
            font-weight: 950;
        }

        .float-card span {
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
        }

        .vendor-card {
            left: 8px;
            top: 300px;
        }

        .budget-card {
            right: 58px;
            bottom: 48px;
        }

        .features-section,
        .planning-section,
        .cta-section {
            margin-top: 42px;
        }

        .features-section,
        .planning-section {
            position: relative;
        }

        .section-heading {
            text-align: center;
            margin-bottom: 24px;
        }

        .section-heading h3,
        .planning-copy h3,
        .cta-section h3 {
            margin: 0;
            color: var(--text);
            font-size: clamp(30px, 3.2vw, 44px);
            font-weight: 950;
            letter-spacing: -0.055em;
        }

        .section-heading p:not(.eyebrow),
        .planning-copy p:not(.eyebrow) {
            margin: 12px auto 0;
            max-width: 640px;
            color: var(--muted);
            font-size: 15px;
            font-weight: 720;
            line-height: 1.7;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
        }

        .feature-card {
            background: #ffffff;
            border: 1px solid rgba(229, 231, 235, 0.9);
            border-radius: 26px;
            padding: 25px;
            box-shadow: 0 18px 46px rgba(31, 41, 55, 0.08);
            transition: 0.25s ease;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 19px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 22px;
            margin-bottom: 18px;
        }

        .feature-icon.coral {
            background: var(--coral);
        }

        .feature-icon.orange {
            background: #f59e0b;
        }

        .feature-icon.green {
            background: #16a34a;
        }

        .feature-icon.blue {
            background: #2563eb;
        }

        .feature-card h4 {
            margin: 0;
            color: var(--text);
            font-size: 18px;
            font-weight: 950;
        }

        .feature-card p {
            margin: 10px 0 0;
            color: var(--muted);
            font-size: 14px;
            font-weight: 720;
            line-height: 1.65;
        }

        .planning-card {
            background: #ffffff;
            border-radius: 32px;
            padding: 32px;
            display: grid;
            grid-template-columns: 0.86fr 1.14fr;
            gap: 28px;
            align-items: center;
            box-shadow: var(--shadow);
        }

        .steps-grid {
            display: grid;
            gap: 14px;
        }

        .step-item {
            display: flex;
            gap: 15px;
            align-items: flex-start;
            padding: 17px;
            border-radius: 21px;
            background: #fafafa;
            border: 1px solid #f0f0f0;
        }

        .step-item span {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            background: var(--coral);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 950;
            flex: 0 0 auto;
        }

        .step-item h4 {
            margin: 0;
            color: var(--text);
            font-size: 16px;
            font-weight: 950;
        }

        .step-item p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 13px;
            font-weight: 720;
            line-height: 1.5;
        }

        .cta-section {
            border-radius: 34px;
            background:
                radial-gradient(circle at top right, rgba(217, 95, 74, 0.38), transparent 34%),
                linear-gradient(135deg, #6f3d2a, #4a271b);
            color: #ffffff;
            padding: 46px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }

        .cta-section .eyebrow {
            margin-bottom: 8px;
        }

        .cta-section h3 {
            color: #ffffff;
        }

        .cta-section p {
            margin: 12px 0 0;
            max-width: 570px;
            color: rgba(255, 255, 255, 0.82);
            font-size: 15px;
            font-weight: 720;
            line-height: 1.6;
        }

        @media (max-width: 980px) {
            .landing-dark-layer {
                background:
                    linear-gradient(
                        to bottom,
                        rgba(10, 12, 18, 0.86) 0%,
                        rgba(10, 12, 18, 0.74) 52%,
                        rgba(247, 243, 239, 0.96) 86%,
                        rgba(247, 243, 239, 1) 100%
                    ),
                    radial-gradient(circle at 18% 18%, rgba(217, 95, 74, 0.32), transparent 36%);
            }

            .hero-section {
                grid-template-columns: 1fr;
                min-height: auto;
                padding: 54px 0 24px;
            }

            .hero-preview-area {
                min-height: 450px;
            }

            .main-preview {
                right: 0;
                left: 0;
                margin: 0 auto;
            }

            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .planning-card {
                grid-template-columns: 1fr;
            }

            .cta-section {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 700px) {
            .landing-nav {
                width: min(100% - 28px, 1180px);
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-actions {
                width: 100%;
            }

            .nav-link,
            .nav-btn {
                flex: 1;
            }

            .landing-shell {
                width: min(100% - 28px, 1180px);
                padding-top: 8px;
            }

            .brand-wrap h1 {
                font-size: 24px;
            }

            .brand-wrap p {
                font-size: 12px;
            }

            .brand-icon {
                width: 52px;
                height: 52px;
            }

            .hero-content h2 {
                font-size: 43px;
            }

            .hero-text {
                font-size: 15px;
            }

            .hero-mini {
                grid-template-columns: 1fr;
            }

            .hero-preview-area {
                min-height: auto;
                display: grid;
                gap: 14px;
            }

            .preview-glow {
                display: none;
            }

            .main-preview,
            .float-card {
                position: relative;
                inset: auto;
                width: 100%;
                min-width: 0;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .planning-card,
            .cta-section {
                padding: 25px;
                border-radius: 26px;
            }
        }
        .refined-section {
    position: relative;
    z-index: 3;
}

.features-section.refined-section {
    margin-top: 70px;
    padding: 42px;
    border-radius: 36px;
    background:
        radial-gradient(circle at top left, rgba(217, 95, 74, 0.10), transparent 34%),
        linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(255, 250, 247, 0.96));
    border: 1px solid rgba(255, 255, 255, 0.86);
    box-shadow: 0 28px 90px rgba(31, 41, 55, 0.16);
}

.refined-heading {
    max-width: 720px;
    margin: 0 auto 30px;
}

.section-pill {
    width: fit-content;
    margin: 0 auto 14px;
    padding: 10px 15px;
    border-radius: 999px;
    background: var(--coral-light);
    color: var(--coral);
    font-size: 12px;
    font-weight: 950;
    letter-spacing: 0.09em;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.refined-heading h3 {
    color: var(--text);
    text-shadow: none;
}

.refined-heading p {
    color: #6b7280;
    font-size: 15px;
    font-weight: 750;
    line-height: 1.7;
}

.refined-features-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
}

.refined-feature-card {
    position: relative;
    min-height: 310px;
    padding: 28px;
    border-radius: 28px;
    background:
        linear-gradient(180deg, #ffffff 0%, #fffaf8 100%);
    border: 1px solid rgba(229, 231, 235, 0.85);
    box-shadow: 0 16px 45px rgba(31, 41, 55, 0.08);
    overflow: hidden;
    transition: 0.28s ease;
}

.refined-feature-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 30px 80px rgba(31, 41, 55, 0.18);
    border-color: rgba(217, 95, 74, 0.25);
}

.feature-topline {
    position: absolute;
    inset: 0 0 auto 0;
    height: 6px;
    background: var(--coral);
}

.feature-topline.orange-line {
    background: #f59e0b;
}

.feature-topline.green-line {
    background: #16a34a;
}

.feature-topline.blue-line {
    background: #2563eb;
}

.refined-feature-card .feature-icon {
    width: 62px;
    height: 62px;
    border-radius: 22px;
    margin-bottom: 26px;
    box-shadow: 0 16px 35px rgba(31, 41, 55, 0.12);
}

.refined-feature-card .feature-number {
    position: absolute;
    top: 28px;
    right: 28px;
    color: rgba(17, 24, 39, 0.08);
    font-size: 42px;
    font-weight: 950;
    letter-spacing: -0.06em;
}

.refined-feature-card h4 {
    margin: 0;
    color: var(--text);
    font-size: 21px;
    font-weight: 950;
    letter-spacing: -0.035em;
}

.refined-feature-card p {
    margin: 12px 0 0;
    color: #5f6878;
    font-size: 14px;
    font-weight: 760;
    line-height: 1.7;
}

.planning-section.refined-section {
    margin-top: 34px;
}

.refined-planning-card {
    position: relative;
    padding: 42px;
    border-radius: 36px;
    background:
        radial-gradient(circle at 12% 10%, rgba(217, 95, 74, 0.12), transparent 32%),
        linear-gradient(135deg, #ffffff 0%, #fff8f4 100%);
    border: 1px solid rgba(255, 255, 255, 0.9);
    box-shadow: 0 28px 90px rgba(31, 41, 55, 0.16);
    display: grid;
    grid-template-columns: 0.9fr 1.1fr;
    gap: 38px;
    align-items: center;
    overflow: hidden;
}

.refined-planning-card::before {
    content: "";
    position: absolute;
    width: 260px;
    height: 260px;
    border-radius: 999px;
    background: rgba(217, 95, 74, 0.10);
    right: -90px;
    bottom: -110px;
}

.refined-planning-card .planning-copy {
    position: relative;
    z-index: 2;
}

.refined-planning-card .section-pill {
    margin: 0 0 16px;
}

.refined-planning-card h3 {
    margin: 0;
    max-width: 460px;
    color: var(--text);
    font-size: clamp(34px, 3.4vw, 52px);
    line-height: 1.02;
    font-weight: 950;
    letter-spacing: -0.065em;
}

.refined-planning-card .planning-copy p {
    max-width: 430px;
    margin: 18px 0 0;
    color: #6b7280;
    font-size: 15px;
    font-weight: 750;
    line-height: 1.75;
}

.planning-btn {
    margin-top: 26px;
    height: 52px;
    padding: 0 22px;
    border-radius: 16px;
    background: var(--coral);
    color: #ffffff;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-size: 14px;
    font-weight: 950;
    box-shadow: 0 16px 34px rgba(217, 95, 74, 0.28);
    transition: 0.25s ease;
}

.planning-btn:hover {
    background: var(--coral-dark);
    transform: translateY(-4px);
}

.refined-steps-grid {
    position: relative;
    z-index: 2;
    display: grid;
    gap: 16px;
}

.refined-step-item {
    position: relative;
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 16px;
    align-items: flex-start;
    padding: 20px;
    border-radius: 24px;
    background: rgba(255, 255, 255, 0.84);
    border: 1px solid rgba(229, 231, 235, 0.9);
    box-shadow: 0 14px 36px rgba(31, 41, 55, 0.06);
    transition: 0.25s ease;
}

.refined-step-item:hover {
    transform: translateX(8px);
    border-color: rgba(217, 95, 74, 0.28);
    box-shadow: 0 22px 55px rgba(31, 41, 55, 0.12);
}

.refined-step-item span {
    width: 48px;
    height: 48px;
    border-radius: 18px;
    background: linear-gradient(135deg, var(--coral), var(--coral-dark));
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    font-weight: 950;
    box-shadow: 0 12px 28px rgba(217, 95, 74, 0.24);
}

.refined-step-item small {
    display: block;
    margin-bottom: 5px;
    color: var(--coral);
    font-size: 11px;
    font-weight: 950;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

.refined-step-item h4 {
    margin: 0;
    color: var(--text);
    font-size: 18px;
    font-weight: 950;
    letter-spacing: -0.025em;
}

.refined-step-item p {
    margin: 7px 0 0;
    color: #6b7280;
    font-size: 13px;
    font-weight: 760;
    line-height: 1.55;
}

@media (max-width: 980px) {
    .refined-features-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .refined-planning-card {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 700px) {
    .features-section.refined-section,
    .refined-planning-card {
        padding: 24px;
        border-radius: 28px;
    }

    .refined-features-grid {
        grid-template-columns: 1fr;
    }

    .refined-feature-card {
        min-height: auto;
    }

    .refined-step-item:hover {
        transform: translateY(-4px);
    }
}
    </style>
</div>