<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jodoh Together | Profile</title>
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
                <h1>Profile Setting</h1>
                <p>Update and manage your basic wedding profile information.</p>
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

        <!-- Profile Card -->
        <div class="profile-card">
            <div class="profile-card-header">
                Personal Information
            </div>
            <div class="profile-card-body">
                <div class="profile-picture-section">
                    <img src="https://i.pravatar.cc/150?img=11" alt="Profile Picture" class="profile-pic-large">
                    <button class="btn-solid"><i class="fa-solid fa-plus"></i> Change Picture</button>
                </div>

                <div class="profile-form-grid">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <div class="form-input-wrap">
                            <input type="text" class="form-input" value="Irfan Raziq">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div class="form-input-wrap">
                            <input type="email" class="form-input" value="irfan.raziq@example.com">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="form-input-wrap">
                            <input type="password" class="form-input" value="........">
                            <button type="button" class="input-link">Change Password</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Wedding Date</label>
                        <div class="form-input-wrap">
                            <input type="text" id="weddingDateDisplay" class="form-input" value="25/11/2026" readonly style="background: #fafafa;">
                            <!-- Hidden date input for native picker -->
                            <input type="date" id="weddingDateHidden" style="position: absolute; width: 0; height: 0; border: none; padding: 0; margin: 0; opacity: 0; right: 40px;">
                            <i class="fa-regular fa-calendar input-icon" id="calendarIcon"></i>
                        </div>
                    </div>
                </div>

                <div class="save-btn-wrap">
                    <button class="btn-solid btn-large">Save Changes</button>
                </div>
            </div>
        </div>

        <!-- Footer Area -->
        <div class="page-footer-split">
            <div class="footer-left">
                <div class="info-row">
                    <i class="fa-solid fa-circle-info"></i> All amounts are in Malaysian Ringgit (RM)
                </div>
                <div style="font-weight: 600; color: #11151a;">
                    Please ensure all vendor appointments and deadlines are confirmed with the vendors.
                </div>
            </div>
            <div class="footer-right">
                Passwords must be at least 12 characters and include numbers and special characters.
            </div>
        </div>
    </main>

    <script>
        // Profile Dropdown
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

        // Wedding Date Picker Logic
        const calendarIcon = document.getElementById('calendarIcon');
        const weddingDateHidden = document.getElementById('weddingDateHidden');
        const weddingDateDisplay = document.getElementById('weddingDateDisplay');

        // When icon is clicked, open the native date picker
        calendarIcon.addEventListener('click', () => {
            if(typeof weddingDateHidden.showPicker === 'function') {
                weddingDateHidden.showPicker();
            } else {
                weddingDateHidden.focus(); // Fallback for older browsers
            }
        });

        // Update the display input when a date is selected
        weddingDateHidden.addEventListener('change', function() {
            if(this.value) {
                const parts = this.value.split('-');
                if (parts.length === 3) {
                    // Format: DD/MM/YYYY
                    weddingDateDisplay.value = `${parts[2]}/${parts[1]}/${parts[0]}`;
                }
            }
        });
    </script>
</body>

</html>
