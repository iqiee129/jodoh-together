<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jodoh Together | Calendar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>

    <aside class="sidebar">
        <div class="logo"><i class="fa-solid fa-heart"></i><span>Jodoh Together</span></div>
        <nav class="nav-menu">
            <a href="{{ url('dashboard') }}" class="nav-link"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a href="{{ url('my/wedding') }}" class="nav-link"><i class="fa-regular fa-calendar-check"></i> My Wedding</a>
            <a href="{{ url('tasks') }}" class="nav-link"><i class="fa-regular fa-square-check"></i> Tasks</a>
            <a href="{{ url('budget') }}" class="nav-link"><i class="fa-solid fa-dollar-sign"></i> Budget</a>
            <a href="{{ url('vendors') }}" class="nav-link"><i class="fa-solid fa-store"></i> Vendors</a>
            <a href="{{ url('calendar') }}" class="nav-link active"><i class="fa-regular fa-calendar"></i> Calendar</a>
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
                <h1>Calendar</h1>
                <p>Manage your wedding appointments, vendor schedules, and key dates.</p>
            </div>
            <div class="header-right">
                <div class="notification" role="button" tabindex="0">
                    <i class="fa-regular fa-bell"></i>
                    <span class="badge">3</span>
                </div>
                <div class="profile-wrap" id="profileWrap">
                    <button class="profile-btn" id="profileBtn" type="button" aria-haspopup="true"
                        aria-expanded="false">
                        <img src="https://i.pravatar.cc/150?img=11" alt="Irfan Raziq">
                        <span>Irfan Raziq</span>
                        <i class="fa-solid fa-chevron-down chevron"></i>
                    </button>
                    <div class="profile-dropdown">
                        <div class="profile-summary"><strong>Irfan Raziq</strong><span>irfan@example.com</span></div>
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

        <!-- Calendar Controls -->
        <div class="calendar-controls">
            <div class="calendar-control-left">
                <div class="btn-group">
                    <button class="btn-group-item active">Month</button>
                    <button class="btn-group-item">Week</button>
                    <button class="btn-group-item">Day</button>
                    <button class="btn-group-item">List</button>
                </div>
                <div class="icon-btn-group">
                    <button class="icon-btn"><i class="fa-solid fa-chevron-left"></i></button>
                    <button class="icon-btn"><i class="fa-solid fa-chevron-right"></i></button>
                    <button class="icon-btn" style="padding: 10px 18px; font-weight: 600;">Today</button>
                </div>
            </div>
            <!-- Removed the Add Event button per user instruction -->
        </div>

        <!-- Main Layout Grid -->
        <div class="content-grid">
            
            <!-- Calendar Main View -->
            <div class="calendar-card">
                <div class="calendar-header">
                    <div class="calendar-month-title">May 2026</div>
                    <div class="calendar-month-right">May 2026</div>
                </div>

                <div class="calendar-grid">
                    <!-- Day Headers -->
                    <div class="calendar-day-header">Sun</div>
                    <div class="calendar-day-header">Mon</div>
                    <div class="calendar-day-header">Tue</div>
                    <div class="calendar-day-header">Wed</div>
                    <div class="calendar-day-header">Thu</div>
                    <div class="calendar-day-header">Fri</div>
                    <div class="calendar-day-header">Sat</div>

                    <!-- Week 1 -->
                    <div class="calendar-cell"><div class="calendar-date other-month">26</div></div>
                    <div class="calendar-cell"><div class="calendar-date other-month">27</div></div>
                    <div class="calendar-cell"><div class="calendar-date other-month">28</div></div>
                    <div class="calendar-cell"><div class="calendar-date other-month">29</div></div>
                    <div class="calendar-cell"><div class="calendar-date other-month">30</div></div>
                    <div class="calendar-cell"><div class="calendar-date">1</div></div>
                    <div class="calendar-cell"><div class="calendar-date">2</div></div>

                    <!-- Week 2 -->
                    <div class="calendar-cell"><div class="calendar-date">3</div></div>
                    <div class="calendar-cell"><div class="calendar-date">4</div></div>
                    <div class="calendar-cell"><div class="calendar-date">5</div></div>
                    <div class="calendar-cell"><div class="calendar-date">6</div></div>
                    <div class="calendar-cell"><div class="calendar-date">7</div></div>
                    <div class="calendar-cell"><div class="calendar-date">8</div></div>
                    <div class="calendar-cell"><div class="calendar-date">9</div></div>

                    <!-- Week 3 -->
                    <div class="calendar-cell"><div class="calendar-date">10</div></div>
                    <div class="calendar-cell"><div class="calendar-date">11</div></div>
                    <div class="calendar-cell"><div class="calendar-date today">12</div></div>
                    <div class="calendar-cell"><div class="calendar-date">13</div>
                        <div class="calendar-event light">
                            <span><i class="fa-solid fa-shirt"></i> Groom's Suit: Initial Fitting</span>
                            <span>11:00 AM</span>
                        </div>
                    </div>
                    <div class="calendar-cell"><div class="calendar-date">14</div>
                        <div class="calendar-event light">
                            <span><i class="fa-solid fa-shirt"></i> Groom's Suit: Initial Fitting</span>
                            <span>11:00 AM</span>
                        </div>
                    </div>
                    <div class="calendar-cell"><div class="calendar-date">15</div>
                        <div class="calendar-event blue">
                            <span><i class="fa-solid fa-camera"></i> Penang Pearl Photography: Shot List Review</span>
                            <span>2:00 PM</span>
                        </div>
                    </div>
                    <div class="calendar-cell"><div class="calendar-date">16</div></div>

                    <!-- Week 4 -->
                    <div class="calendar-cell"><div class="calendar-date">17</div></div>
                    <div class="calendar-cell"><div class="calendar-date">18</div></div>
                    <div class="calendar-cell"><div class="calendar-date">19</div></div>
                    <div class="calendar-cell"><div class="calendar-date">20</div>
                        <div class="calendar-event green">
                            <span><i class="fa-solid fa-building"></i> Glass House Venue: Site Access Walkthrough</span>
                            <span>10:00 AM</span>
                        </div>
                    </div>
                    <div class="calendar-cell"><div class="calendar-date">21</div></div>
                    <div class="calendar-cell"><div class="calendar-date">22</div>
                        <div class="calendar-event orange">
                            <span><i class="fa-solid fa-bell-concierge"></i> Perak Palace Catering: Menu Finalization & Tasting</span>
                            <span>6:00 PM</span>
                        </div>
                    </div>
                    <div class="calendar-cell"><div class="calendar-date">23</div>
                        <div class="calendar-event orange">
                            <span><i class="fa-solid fa-bell-concierge"></i> Perak Palace Catering: Menu Finalization & Tasting</span>
                            <span>6:00 PM</span>
                        </div>
                    </div>

                    <!-- Week 5 -->
                    <div class="calendar-cell"><div class="calendar-date">24</div></div>
                    <div class="calendar-cell"><div class="calendar-date">25</div></div>
                    <div class="calendar-cell"><div class="calendar-date">26</div></div>
                    <div class="calendar-cell"><div class="calendar-date">27</div>
                        <div class="calendar-event purple">
                            <span><i class="fa-solid fa-music"></i> Taiping Timeless Tunes: Playlist & Cue Review</span>
                            <span>7:00 PM</span>
                        </div>
                    </div>
                    <div class="calendar-cell"><div class="calendar-date">28</div>
                        <div class="calendar-event red">
                            <span><i class="fa-solid fa-seedling"></i> Kangar Floral Fantasies: Deco Centerpiece Final Review</span>
                            <span>2:00 PM</span>
                        </div>
                    </div>
                    <div class="calendar-cell"><div class="calendar-date">29</div></div>
                    <div class="calendar-cell"><div class="calendar-date">30</div></div>

                    <!-- Week 6 (Remaining) -->
                    <div class="calendar-cell"><div class="calendar-date">31</div>
                        <div class="calendar-event light">
                            <span><i class="fa-regular fa-calendar-check"></i> Photographer Deposit Payment Deadline</span>
                            <span>All Day</span>
                        </div>
                    </div>
                    <div class="calendar-cell"><div class="calendar-date other-month">1</div></div>
                    <div class="calendar-cell"><div class="calendar-date other-month">2</div></div>
                    <div class="calendar-cell"><div class="calendar-date other-month">3</div></div>
                    <div class="calendar-cell"><div class="calendar-date other-month">4</div></div>
                    <div class="calendar-cell"><div class="calendar-date other-month">5</div></div>
                    <div class="calendar-cell"><div class="calendar-date other-month">6</div></div>
                </div>
                
                <div class="page-footer">
                    <i class="fa-solid fa-circle-info"></i> All amounts are in Malaysian Ringgit (RM). Please ensure all vendor appointments and deadlines are confirmed with the vendors.
                </div>
            </div>

            <!-- Right Sidebar Panel -->
            <div class="sidebar-card">
                <div class="sidebar-title">Upcoming at a Glance</div>
                
                <div class="agenda-section">
                    <div class="agenda-section-title">Next Appointment</div>
                    <div class="agenda-item">
                        <div class="agenda-date">
                            <span>May</span>
                            <span>13</span>
                        </div>
                        <div class="agenda-content">
                            <div class="agenda-title">Groom's Suit: Initial Fitting</div>
                            <div class="agenda-time">11:00 AM</div>
                        </div>
                    </div>
                </div>

                <div class="agenda-section">
                    <div class="agenda-section-title">Vendor Meetings</div>
                    
                    <div class="agenda-item">
                        <div class="agenda-date blue">
                            <span>May</span>
                            <span>15</span>
                        </div>
                        <div class="agenda-content">
                            <div class="agenda-title">Penang Pearl Photography: Shot List Review</div>
                        </div>
                        <div class="agenda-time-right">
                            <span>2:00 PM</span>
                            <span>5 PM</span>
                        </div>
                    </div>

                    <div class="agenda-item">
                        <div class="agenda-date green">
                            <span>May</span>
                            <span>20</span>
                        </div>
                        <div class="agenda-content">
                            <div class="agenda-title">Glass House Venue: Site Access Walkthrough</div>
                        </div>
                        <div class="agenda-time-right">
                            <span>10:00 AM</span>
                            <span>3 PM</span>
                        </div>
                    </div>

                    <div class="agenda-item">
                        <div class="agenda-date orange">
                            <span>May</span>
                            <span>22</span>
                        </div>
                        <div class="agenda-content">
                            <div class="agenda-title">Perak Palace Catering: Menu Finalization</div>
                        </div>
                        <div class="agenda-time-right">
                            <span>7:00 PM</span>
                            <span>6 PM</span>
                        </div>
                    </div>
                </div>

                <div class="agenda-section">
                    <div class="agenda-section-title">Key Deadlines</div>
                    
                    <div class="agenda-item">
                        <div class="agenda-date red">
                            <span>May</span>
                            <span>30</span>
                        </div>
                        <div class="agenda-content">
                            <div class="agenda-title">Budget deadline<br><span style="font-weight:500; color:#666;">Processing</span></div>
                        </div>
                        <div class="agenda-time-right">
                            <span>All Day</span>
                            <span>7 PM</span>
                        </div>
                    </div>

                    <div class="agenda-item">
                        <div class="agenda-date blue">
                            <span>May</span>
                            <span>30</span>
                        </div>
                        <div class="agenda-content">
                            <div class="agenda-title">Shot list review<br><span style="font-weight:500; color:#666;">All Day</span></div>
                        </div>
                        <div class="agenda-time-right">
                            <span>All Day</span>
                            <span>2 PM</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>
        const profileWrap = document.getElementById("profileWrap");
        const profileBtn = document.getElementById("profileBtn");

        if (profileBtn) {
            profileBtn.addEventListener("click", (event) => {
                event.stopPropagation();
                const isOpen = profileWrap.classList.toggle("open");
                profileBtn.setAttribute("aria-expanded", isOpen);
            });
        }

        document.addEventListener("click", (event) => {
            if (profileWrap && !profileWrap.contains(event.target)) {
                profileWrap.classList.remove("open");
                if (profileBtn) profileBtn.setAttribute("aria-expanded", "false");
            }
        });

        document.addEventListener("keydown", (event) => {
            if (event.key === "Escape" && profileWrap) {
                profileWrap.classList.remove("open");
                if (profileBtn) profileBtn.setAttribute("aria-expanded", "false");
            }
        });
    </script>
</body>

</html>
