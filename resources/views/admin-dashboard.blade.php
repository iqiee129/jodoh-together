<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jodoh Together | Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>

    <aside class="sidebar">
        <div class="logo"><i class="fa-solid fa-heart"></i><span>Jodoh Together</span></div>
        <nav class="nav-menu">
            <a href="{{ url('admin/dashboard') }}" class="nav-link active"><i class="fa-solid fa-chart-simple"></i>
                Overview</a>
            <a href="{{ url('admin/vendors') }}" class="nav-link"><i class="fa-solid fa-store"></i> Vendors</a>
            <a href="{{ url('admin/users') }}" class="nav-link"><i class="fa-regular fa-user"></i> Users</a>
        </nav>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link logout-link admin-logout" style="border: none; background: none; width: 100%; text-align: left; cursor: pointer;">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
            </button>
        </form>
    </aside>

    <main class="main-content admin-main">
        <header>
            <div class="page-title">
                <h1>Admin Dashboard</h1>
                <p>A minimal summary of users, vendors, categories, and recent activity.</p>
            </div>
            <div class="header-right">
                <div class="profile-wrap" id="profileWrap">
                    <button class="profile-btn" id="profileBtn" type="button" aria-haspopup="true"
                        aria-expanded="false">
                        <img src="https://i.pravatar.cc/150?img=32" alt="Admin">
                        <span>Admin</span>
                        <i class="fa-solid fa-chevron-down chevron"></i>
                    </button>
                    <div class="profile-dropdown">
                        <div class="profile-summary">
                            <strong>System Admin</strong>
                            <span id="adminEmailText">admin@example.com</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-link logout admin-logout" style="border: none; background: none; width: 100%; text-align: left; cursor: pointer;">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <section class="admin-stats">
            <div class="budget-card">
                <div class="card-icon red"><i class="fa-regular fa-user"></i></div>
                <div class="budget-card-content">
                    <span class="budget-label">Total Users</span>
                    <span class="budget-amount">4</span>
                </div>
            </div>
            <div class="budget-card">
                <div class="card-icon orange"><i class="fa-solid fa-store"></i></div>
                <div class="budget-card-content">
                    <span class="budget-label">Total Vendors</span>
                    <span class="budget-amount">3</span>
                </div>
            </div>
            <div class="budget-card">
                <div class="card-icon blue"><i class="fa-solid fa-layer-group"></i></div>
                <div class="budget-card-content">
                    <span class="budget-label">Total Categories</span>
                    <span class="budget-amount">3</span>
                </div>
            </div>
            <div class="budget-card">
                <div class="card-icon green"><i class="fa-regular fa-calendar-plus"></i></div>
                <div class="budget-card-content">
                    <span class="budget-label">Recent Signups</span>
                    <span class="budget-amount">2</span>
                    <span class="budget-subtext">Last 7 days</span>
                </div>
            </div>
        </section>

        <div class="admin-summary-grid">
            <section class="card-wrap">
                <div class="section-head">
                    <div>
                        <div class="card-title">Vendor Summary</div>
                        <p class="admin-muted">Current vendors grouped by category.</p>
                    </div>
                    <a class="filter-btn admin-link-btn" href="{{ url('admin/vendors') }}"><i class="fa-solid fa-store"></i>
                        Manage Vendors</a>
                </div>
                <div class="summary-list">
                    <div class="summary-row">
                        <span><i class="fa-solid fa-camera"></i> Photography</span>
                        <strong>1 vendor</strong>
                    </div>
                    <div class="summary-row">
                        <span><i class="fa-solid fa-building-columns"></i> Venue</span>
                        <strong>1 vendor</strong>
                    </div>
                    <div class="summary-row">
                        <span><i class="fa-solid fa-bell-concierge"></i> Catering</span>
                        <strong>1 vendor</strong>
                    </div>
                </div>
            </section>

            <section class="card-wrap">
                <div class="section-head">
                    <div>
                        <div class="card-title">User Summary</div>
                        <p class="admin-muted">Recently registered users in the system.</p>
                    </div>
                    <a class="filter-btn admin-link-btn" href="{{ url('admin/users') }}"><i class="fa-regular fa-user"></i>
                        View Users</a>
                </div>
                <div class="recent-list">
                    <div class="recent-item">
                        <div class="recent-avatar">I</div>
                        <div>
                            <div class="recent-name">Irfan Raziq</div>
                            <div class="admin-muted">Created 10 Jan 2026</div>
                        </div>
                    </div>
                    <div class="recent-item">
                        <div class="recent-avatar">A</div>
                        <div>
                            <div class="recent-name">Aisyah Rahman</div>
                            <div class="admin-muted">Created 11 Jan 2026</div>
                        </div>
                    </div>
                    <div class="recent-item">
                        <div class="recent-avatar">N</div>
                        <div>
                            <div class="recent-name">Nadia Zulkifli</div>
                            <div class="admin-muted">Created 02 Feb 2026</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="card-wrap admin-wide-card">
                <div class="card-title">Admin Notes</div>
                <div class="admin-note-grid">
                    <div class="admin-note-row">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Review inactive vendors monthly.</span>
                    </div>
                    <div class="admin-note-row">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Confirm vendor contact details before publishing.</span>
                    </div>
                    <div class="admin-note-row">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Monitor new registrations for duplicates.</span>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script>
        if (sessionStorage.getItem("jodohAdminLoggedIn") !== "true") {
            window.location.href = "{{ url('admin/login') }}";
        }

        document.getElementById("adminEmailText").textContent =
            sessionStorage.getItem("jodohAdminEmail") || "admin@example.com";

        document.querySelectorAll(".admin-logout").forEach((link) => {
            link.addEventListener("click", (event) => {
                sessionStorage.removeItem("jodohAdminLoggedIn");
                sessionStorage.removeItem("jodohAdminEmail");
            });
        });

        const profileWrap = document.getElementById("profileWrap");
        const profileBtn = document.getElementById("profileBtn");

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
    </script>
</body>

</html>
