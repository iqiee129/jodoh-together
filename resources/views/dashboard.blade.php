<div class="dashboard-wrapper" style="display: flex;">
    <aside class="sidebar">
        <div class="logo">
            <i class="fa-solid fa-heart"></i>
            <span>Jodoh Together</span>
        </div>
        <nav class="nav-menu">
            <a href="{{ url('dashboard') }}" class="nav-link active"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a href="{{ url('my/wedding') }}" class="nav-link"><i class="fa-regular fa-calendar-check"></i> My Wedding</a>
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

    <main class="main-content" style="flex-grow: 1;">
        <header>
            <div class="page-title">
                <h1>Hello, {{ auth()->user()->name ?? 'Guest' }}!</h1>
                <p>Excited for your wedding?</p>
            </div>
            <div class="user-controls">
                <div class="notification" onclick="alert('You have 3 upcoming wedding reminders.')" role="button" tabindex="0">
                    <i class="fa-regular fa-bell"></i>
                    <span class="badge">3</span>
                </div>
                <div class="profile-wrap" id="profileWrap">
                    <button class="profile" id="profileButton" type="button" aria-haspopup="true" aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'G') }}&background=random" alt="{{ auth()->user()->name ?? 'User' }}">
                        <span>{{ auth()->user()->name ?? 'Guest' }}</span>
                        <i class="fa-solid fa-chevron-down" style="font-size: 12px; margin-left: 5px;"></i>
                    </button>
                    <div class="profile-dropdown" id="profileDropdown">
                        <div class="profile-summary">
                            <strong>{{ auth()->user()->name ?? 'Guest' }}</strong>
                            <span>{{ auth()->user()->email ?? 'No email' }}</span>
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

        <div class="dashboard-grid">
            <div class="top-cards">
                <div class="red-card" onclick="window.location.href='my-wedding.html'">
                    <div style="display: flex; justify-content: space-between;">
                        <h3>Wedding Day</h3>
                        <span style="font-size: 14px;">24/04/31</span>
                    </div>
                    <p class="subtitle">{{ auth()->user()->name ?? 'Groom' }} & Bride</p>
                    <div class="days-count">
                        <span class="number">121</span>
                        <span class="text">days to go</span>
                    </div>
                    <i class="fa-regular fa-calendar-plus card-icon-large"></i>
                </div>

                <div class="red-card" onclick="window.location.href='budget.html'">
                    <h3>Budget (RM)</h3>
                    <div style="height: 25px;"></div>
                    <div class="stat-value">RM 0 <span class="small">/ RM 20,000</span></div>
                    <div class="progress-container">
                        <span class="progress-text">0% spent</span>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 0%;"></div>
                        </div>
                    </div>
                </div>

                <div class="red-card" onclick="window.location.href='tasks.html'">
                    <h3>Tasks</h3>
                    <div style="height: 25px;"></div>
                    <div class="stat-value">0 <span class="small">/ 157 completed</span></div>
                    <div class="progress-container">
                        <span class="progress-text">0% completed</span>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 0%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="middle-section">
                <div class="white-card">
                    <h2>Calendar</h2>
                    <div class="calendar-header">
                        <i class="fa-solid fa-chevron-left"></i>
                        <span>April 2030</span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>
                    <div class="calendar-days">
                        <div class="day-col"><div class="day-name">Mon</div><div class="day-number">21</div></div>
                        <div class="day-col"><div class="day-name">Tue</div><div class="day-number">22</div></div>
                        <div class="day-col"><div class="day-name">Wed</div><div class="day-number">23</div></div>
                        <div class="day-col"><div class="day-name">Thu</div><div class="day-number">24</div></div>
                        <div class="day-col"><div class="day-name">Fri</div><div class="day-number active">25<div class="day-dot teal"></div></div></div>
                        <div class="day-col"><div class="day-name">Sat</div><div class="day-number">26</div></div>
                        <div class="day-col"><div class="day-name">Sun</div><div class="day-number">27<div class="day-dot red"></div></div></div>
                    </div>
                    <button class="outline-btn" onclick="window.location.href='calendar.html'">View full calendar</button>
                </div>

                <div class="white-card">
                    <h2>Upcoming Tasks</h2>
                    <div class="task-list">
                        <div class="task-item">
                            <div class="task-icon teal"><i class="fa-regular fa-calendar"></i></div>
                            <div class="task-info">
                                <div class="task-title">Visit wedding venue at Glass House</div>
                                <div class="task-meta">Friday, 25 April 2030 • 10:00 AM</div>
                            </div>
                            <span class="badge-status upcoming-teal">Upcoming</span>
                        </div>
                        <div class="task-item">
                            <div class="task-icon red"><i class="fa-regular fa-calendar-check"></i></div>
                            <div class="task-info">
                                <div class="task-title">Prepare guest list and invitation card</div>
                                <div class="task-meta">Sunday, 27 April 2030 • 2:00 PM</div>
                            </div>
                            <span class="badge-status upcoming-red">Important</span>
                        </div>
                    </div>
                    <button class="outline-btn" onclick="window.location.href='tasks.html'">View all tasks</button>
                </div>
            </div>

            <div class="bottom-cards">
                <div class="bottom-card" onclick="window.location.href='calendar.html'">
                    <div class="icon-circle"><i class="fa-regular fa-calendar"></i></div>
                    <div class="bottom-card-info">
                        <p>Wedding Date</p>
                        <h3>31 April 2030</h3>
                    </div>
                </div>
                <div class="bottom-card" onclick="window.location.href='vendors.html'">
                    <div class="icon-circle"><i class="fa-solid fa-store"></i></div>
                    <div class="bottom-card-info">
                        <p>Vendors Booked</p>
                        <h3>0 / 0</h3>
                    </div>
                </div>
                <div class="bottom-card" onclick="window.location.href='budget.html'">
                    <div class="icon-circle"><i class="fa-solid fa-dollar-sign"></i></div>
                    <div class="bottom-card-info">
                        <p>Budget Remaining</p>
                        <h3>RM 20,000</h3>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const profileWrap = document.getElementById("profileWrap");
        const profileButton = document.getElementById("profileButton");

        profileButton.addEventListener("click", (event) => {
            event.stopPropagation();
            const isOpen = profileWrap.classList.toggle("open");
            profileButton.setAttribute("aria-expanded", isOpen);
        });

        document.addEventListener("click", (event) => {
            if (!profileWrap.contains(event.target)) {
                profileWrap.classList.remove("open");
                profileButton.setAttribute("aria-expanded", "false");
            }
        });

        document.addEventListener("keydown", (event) => {
            if (event.key === "Escape") {
                profileWrap.classList.remove("open");
                profileButton.setAttribute("aria-expanded", "false");
            }
        });
    </script>
</div>
