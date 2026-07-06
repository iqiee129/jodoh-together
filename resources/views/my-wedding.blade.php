<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jodoh Together | My Wedding</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>

    <aside class="sidebar">
        <div class="logo">
            <i class="fa-solid fa-heart"></i>
            <span>Jodoh Together</span>
        </div>
        <nav class="nav-menu">
            <a href="{{ url('dashboard') }}" class="nav-link"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a href="{{ url('my/wedding') }}" class="nav-link active"><i class="fa-regular fa-calendar-check"></i> My
                Wedding</a>
            <a href="{{ url('tasks') }}" class="nav-link"><i class="fa-regular fa-square-check"></i> Tasks</a>
            <a href="{{ url('budget') }}" class="nav-link"><i class="fa-solid fa-dollar-sign"></i> Budget</a>
            <a href="{{ url('vendors') }}" class="nav-link"><i class="fa-solid fa-store"></i> Vendors</a>
            <a href="{{ url('calendar') }}" class="nav-link"><i class="fa-regular fa-calendar"></i> Calendar</a>
        </nav>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link logout-link" style="border: none; background: none; width: 100%; text-align: left; cursor: pointer;">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
            </button>
        </form>
    </aside>

    <main class="main-content">

        <header>
            <div class="page-title">
                <h1>My Wedding</h1>
                <p>Manage your wedding details, tasks and important information.</p>
            </div>

            <div class="user-controls">
                <div class="notification" role="button" tabindex="0"
                    onclick="alert('You have 3 upcoming wedding reminders.')">
                    <i class="fa-regular fa-bell"></i>
                    <span class="badge">3</span>
                </div>

                <div class="profile-wrap" id="profileWrap">
                    <button class="profile-btn" id="profileBtn" type="button" aria-haspopup="true"
                        aria-expanded="false">
                        <img src="https://i.pravatar.cc/150?img=11" alt="Irfan Raziq">
                        <span>Irfan Raziq</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="profile-dropdown" id="profileMenu">
                        <div class="profile-summary">
                            <strong>Irfan Raziq</strong>
                            <span>irfan@example.com</span>
                        </div>
                        <a href="{{ url('profile') }}" class="dropdown-link"><i class="fa-regular fa-user"></i> My Profile</a>
                        <a href="{{ url('settings') }}" class="dropdown-link"><i class="fa-solid fa-gear"></i> Settings</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-link logout" style="border: none; background: none; width: 100%; text-align: left; cursor: pointer;">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <section class="layout">
            <!-- ===== Left Column ===== -->
            <div class="left-stack">

                <!-- Wedding Summary Card -->
                <article class="card wedding-card">
                    <div class="couple-photo" role="img" aria-label="Wedding couple photo"></div>

                    <div class="wedding-details">
                        <div class="title-row">
                            <h2>Irfan &amp; Aisyah</h2>
                            <span class="status-badge teal">Upcoming</span>
                        </div>

                        <div class="info-list">
                            <div class="info-row">
                                <i class="fa-regular fa-calendar"></i>
                                <span class="label">Wedding Date</span>
                                <span class="value">31 April 2030 (Wednesday)</span>
                            </div>
                            <div class="info-row">
                                <i class="fa-solid fa-location-dot"></i>
                                <span class="label">Venue</span>
                                <span class="value">Glass House, Penang</span>
                            </div>
                            <div class="info-row">
                                <i class="fa-solid fa-users"></i>
                                <span class="label">Guests</span>
                                <span class="value">-</span>
                            </div>
                            <div class="info-row">
                                <i class="fa-regular fa-file-lines"></i>
                                <span class="label">Wedding Theme</span>
                                <span class="value">Minimalist Elegance</span>
                            </div>
                        </div>

                        <button class="edit-btn" type="button">
                            <i class="fa-solid fa-pen"></i>
                            Edit Details
                        </button>
                    </div>
                </article>

                <!-- Upcoming Milestones Card -->
                <article class="card milestone-card">
                    <div class="card-header">
                        <h2>Upcoming Tasks</h2>
                        <button class="outline-btn" type="button">View All Tasks</button>
                    </div>

                    <div class="timeline">
                        <!-- Milestone 1 -->
                        <div class="date-pill">25 APR 2030</div>
                        <span class="node"></span>
                        <div>
                            <p class="milestone-title">Visit wedding venue at Glass House</p>
                            <p class="milestone-time">10:00 AM</p>
                        </div>
                        <span class="status-badge teal">Upcoming</span>

                        <!-- Milestone 2 -->
                        <div class="date-pill">27 APR 2030</div>
                        <span class="node"></span>
                        <div>
                            <p class="milestone-title">Prepare guest list and invitation card</p>
                            <p class="milestone-time">2:00 PM</p>
                        </div>
                        <span class="status-badge teal">Upcoming</span>

                        <!-- Milestone 3 -->
                        <div class="date-pill">30 APR 2030</div>
                        <span class="node"></span>
                        <div>
                            <p class="milestone-title">Confirm catering menu</p>
                            <p class="milestone-time">11:00 AM</p>
                        </div>
                        <span class="status-badge teal">Upcoming</span>

                        <!-- Milestone 4 -->
                        <div class="date-pill">15 MAY 2030</div>
                        <span class="node"></span>
                        <div>
                            <p class="milestone-title">Final fitting for wedding attire</p>
                            <p class="milestone-time">3:00 PM</p>
                        </div>
                        <span class="status-badge teal">Upcoming</span>

                        <!-- Milestone 5 - Important -->
                        <div class="date-pill">31 APR 2030</div>
                        <span class="node important"></span>
                        <div>
                            <p class="milestone-title">Wedding Day</p>
                            <p class="milestone-time">All Day</p>
                        </div>
                        <span class="status-badge red">Important</span>
                    </div>
                </article>

            </div>

            <!-- ===== Right Column ===== -->
            <div class="right-stack">

                <!-- Wedding Countdown Card -->
                <article class="card countdown-card">
                    <h2>Wedding Countdown</h2>
                    <div class="countdown-main">
                        <strong>121</strong>
                        <span>days to go</span>
                    </div>
                    <div class="countdown-date">
                        <i class="fa-regular fa-calendar"></i>
                        <span>24/04/31</span>
                    </div>

                    <!-- Decorative hearts -->
                    <div class="hearts-decoration" aria-hidden="true">
                        <i class="fa-solid fa-heart"></i>
                        <i class="fa-solid fa-heart"></i>
                        <i class="fa-solid fa-heart"></i>
                        <i class="fa-solid fa-heart"></i>
                    </div>

                    <!-- Decorative floral -->
                    <div class="floral-decoration" aria-hidden="true">
                        <i class="fa-solid fa-seedling"></i>
                    </div>
                </article>

                <!-- Wedding Information Card -->
                <article class="card information-card">
                    <h2>Wedding Information</h2>

                    <div class="details-list">
                        <div class="detail-item">
                            <i class="fa-regular fa-calendar"></i>
                            <div>
                                <p>Wedding Date</p>
                                <strong>31 April 2030 (Wednesday)</strong>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fa-solid fa-location-dot"></i>
                            <div>
                                <p>Venue</p>
                                <strong>Glass House, Penang</strong>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fa-solid fa-palette"></i>
                            <div>
                                <p>Theme</p>
                                <strong>Minimalist Elegance</strong>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fa-solid fa-users"></i>
                            <div>
                                <p>Estimated Guests</p>
                                <strong>-</strong>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fa-solid fa-dollar-sign"></i>
                            <div>
                                <p>Budget</p>
                                <strong>RM 20,000</strong>
                            </div>
                        </div>
                    </div>

                    <button class="edit-btn" type="button">
                        <i class="fa-solid fa-pen"></i>
                        Edit Information
                    </button>
                </article>

            </div>
        </section>

    </main>

    <script>
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

        document.addEventListener("keydown", (event) => {
            if (event.key === "Escape") {
                profileWrap.classList.remove("open");
                profileBtn.setAttribute("aria-expanded", "false");
            }
        });
    </script>

</body>

</html>
