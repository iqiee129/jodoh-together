<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jodoh Together | Vendors</title>
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
            <a href="{{ url('vendors') }}" class="nav-link active"><i class="fa-solid fa-store"></i> Vendors</a>
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
                <h1>Vendors</h1>
                <p>Discover and browse wedding vendors in the Northern Region of Malaysia.</p>
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

        <!-- Filters -->
        <div class="filters">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Search vendors...">
            </div>
            <select class="filter-select">
                <option>Filter by Category</option>
            </select>
            <select class="filter-select">
                <option>Filter by State</option>
            </select>
            <select class="filter-select">
                <option>Sort by Price</option>
            </select>
        </div>

        <!-- Vendors Grid -->
        <div class="vendors-grid">
            
            <!-- Vendor 1 -->
            <div class="vendor-card">
                <div class="vendor-top">
                    <div class="vendor-header-left">
                        <div class="vendor-icon blue"><i class="fa-solid fa-camera"></i></div>
                        <div class="vendor-name">Penang Pearl<br>Photography</div>
                    </div>
                    <img src="https://images.unsplash.com/photo-1519741497674-611481863552?w=150&h=150&fit=crop" alt="Vendor Image" class="vendor-image">
                </div>
                <div class="vendor-location"><i class="fa-solid fa-location-dot"></i> George Town, Penang</div>
                <div class="vendor-cat-price">
                    <span class="vendor-category">Photography</span>
                    <span class="vendor-price">$$$$</span>
                </div>
                <div class="vendor-desc">
                    Experienced team specializing in candid beach and garden weddings. Custom packages available.
                </div>
                <div class="vendor-contact">
                    <div class="contact-info">
                        <div class="contact-item"><i class="fa-solid fa-phone"></i> 012-3456789</div>
                        <div class="contact-item"><i class="fa-regular fa-envelope"></i> penangpearl@m...</div>
                    </div>
                    <button class="vendor-btn btn-solid">Add to Budget</button>
                </div>
            </div>

            <!-- Vendor 2 -->
            <div class="vendor-card">
                <div class="vendor-top">
                    <div class="vendor-header-left">
                        <div class="vendor-icon blue"><i class="fa-solid fa-building-columns"></i></div>
                        <div class="vendor-name">Kedah Garden<br>Venues</div>
                    </div>
                    <img src="https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=150&h=150&fit=crop" alt="Vendor Image" class="vendor-image">
                </div>
                <div class="vendor-location"><i class="fa-solid fa-location-dot"></i> Alor Setar, Kedah</div>
                <div class="vendor-cat-price">
                    <span class="vendor-category">Venue</span>
                    <span class="vendor-price">$$$</span>
                </div>
                <div class="vendor-desc">
                    Experienced team specializing in candid beach and garden weddings. Custom packages available.
                </div>
                <div class="vendor-contact">
                    <div class="contact-info">
                        <div class="contact-item"><i class="fa-solid fa-phone"></i> 012-3456789</div>
                        <div class="contact-item"><i class="fa-regular fa-envelope"></i> penangpearl@n...</div>
                    </div>
                    <button class="vendor-btn btn-solid">Add to Budget</button>
                </div>
            </div>

            <!-- Vendor 3 -->
            <div class="vendor-card">
                <div class="vendor-top">
                    <div class="vendor-header-left">
                        <div class="vendor-icon orange"><i class="fa-solid fa-bell-concierge"></i></div>
                        <div class="vendor-name">Perak Palace<br>Catering</div>
                    </div>
                    <img src="https://images.unsplash.com/photo-1555244162-803834f70033?w=150&h=150&fit=crop" alt="Vendor Image" class="vendor-image">
                </div>
                <div class="vendor-location"><i class="fa-solid fa-location-dot"></i> Ipoh, Perak</div>
                <div class="vendor-cat-price">
                    <span class="vendor-category">Catering</span>
                    <span class="vendor-price">$$$$</span>
                </div>
                <div class="vendor-desc">
                    Experienced team specializing in candid beach and garden weddings. Custom packages available.
                </div>
                <div class="vendor-contact">
                    <div class="contact-info">
                        <div class="contact-item"><i class="fa-solid fa-phone"></i> 012-3456789</div>
                        <div class="contact-item"><i class="fa-regular fa-envelope"></i> penangpearl@n...</div>
                    </div>
                    <button class="vendor-btn btn-solid">Add to Budget</button>
                </div>
            </div>

            <!-- Vendor 4 -->
            <div class="vendor-card">
                <div class="vendor-top">
                    <div class="vendor-header-left">
                        <div class="vendor-icon orange"><i class="fa-solid fa-bell-concierge"></i></div>
                        <div class="vendor-name">Perak Palace<br>Catering</div>
                    </div>
                    <img src="https://images.unsplash.com/photo-1577047055728-662eb7488f50?w=150&h=150&fit=crop" alt="Vendor Image" class="vendor-image">
                </div>
                <div class="vendor-location"><i class="fa-solid fa-location-dot"></i> Ipoh, Perak</div>
                <div class="vendor-cat-price">
                    <span class="vendor-category">Catering</span>
                    <span class="vendor-price">$$$$</span>
                </div>
                <div class="vendor-desc">
                    Experienced team specializing in candid beach and garden weddings. Custom packages available.
                </div>
                <div class="vendor-contact">
                    <div class="contact-info">
                        <div class="contact-item"><i class="fa-solid fa-phone"></i> 012-3456789</div>
                        <div class="contact-item"><i class="fa-regular fa-envelope"></i> penangpearl@...</div>
                    </div>
                    <button class="vendor-btn btn-outline">View Details</button>
                </div>
            </div>

            <!-- Vendor 5 -->
            <div class="vendor-card">
                <div class="vendor-top">
                    <div class="vendor-header-left">
                        <div class="vendor-icon purple"><i class="fa-solid fa-music"></i></div>
                        <div class="vendor-name">Taiping Timeless<br>Tunes</div>
                    </div>
                    <img src="https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=150&h=150&fit=crop" alt="Vendor Image" class="vendor-image">
                </div>
                <div class="vendor-location"><i class="fa-solid fa-location-dot"></i> Taiping, Perak</div>
                <div class="vendor-cat-price">
                    <span class="vendor-category">Music/DJ</span>
                    <span class="vendor-price">$$</span>
                </div>
                <div class="vendor-desc" style="border-bottom: none; padding-bottom: 0;">
                    Experienced team specializing in candid beach and garden weddings. Custom packages available.
                </div>
            </div>

            <!-- Vendor 6 -->
            <div class="vendor-card">
                <div class="vendor-top">
                    <div class="vendor-header-left">
                        <div class="vendor-icon pink"><i class="fa-brands fa-pagelines"></i></div>
                        <div class="vendor-name">Kangar Floral<br>Fantasies</div>
                    </div>
                    <img src="https://images.unsplash.com/photo-1522067710321-72f5cb3e3ee8?w=150&h=150&fit=crop" alt="Vendor Image" class="vendor-image">
                </div>
                <div class="vendor-location"><i class="fa-solid fa-location-dot"></i> Kangar, Perlis</div>
                <div class="vendor-cat-price">
                    <span class="vendor-category">Decoration</span>
                    <span class="vendor-price">$$$</span>
                </div>
                <div class="vendor-desc" style="border-bottom: none; padding-bottom: 0;">
                    Experienced team specializing in candid beach and garden weddings. Custom packages available.
                </div>
            </div>

        </div>
        
        <div class="page-footer">
            <i class="fa-solid fa-circle-info"></i> All amounts are in Malaysian Ringgit (RM)
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
