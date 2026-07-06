<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jodoh Together | Budget</title>
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
            <a href="{{ url('budget') }}" class="nav-link active"><i class="fa-solid fa-dollar-sign"></i> Budget</a>
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
                <h1>Budget</h1>
                <p>Track your wedding expenses and manage your budget.</p>
            </div>
            <div class="header-right">
                <button class="add-btn" type="button"><i class="fa-solid fa-plus"></i> Add Expense</button>
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

        <!-- Top Cards -->
        <div class="budget-cards">
            <div class="budget-card">
                <div class="card-icon red"><i class="fa-solid fa-wallet"></i></div>
                <div class="budget-card-content">
                    <span class="budget-label">Total Budget</span>
                    <span class="budget-amount">RM 20,000</span>
                </div>
            </div>
            <div class="budget-card">
                <div class="card-icon orange"><i class="fa-solid fa-coins"></i></div>
                <div class="budget-card-content">
                    <span class="budget-label">Total Spent</span>
                    <span class="budget-amount">RM 10,000</span>
                    <span class="budget-subtext">50% of budget</span>
                </div>
            </div>
            <div class="budget-card">
                <div class="card-icon green"><i class="fa-solid fa-wallet"></i></div>
                <div class="budget-card-content">
                    <span class="budget-label">Remaining Budget</span>
                    <span class="budget-amount">RM 10,000</span>
                    <span class="budget-subtext">50% left</span>
                </div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="content-grid">
            
            <!-- Left Column: Table -->
            <div class="card-wrap" style="padding-bottom: 16px;">
                <!-- Filters -->
                <div class="filters">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Search expenses...">
                    </div>
                    <select class="filter-select">
                        <option>All Categories</option>
                    </select>
                    <select class="filter-select">
                        <option>All Status</option>
                    </select>
                    <button class="filter-btn"><i class="fa-regular fa-calendar"></i> Date Range</button>
                </div>

                <!-- Table -->
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Expense</th>
                                <th>Category</th>
                                <th>Date</th>
                                <th>Amount (RM)</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="expense-name">Venue Rental Deposit</div>
                                </td>
                                <td>
                                    <div class="category-cell">
                                        <div class="cat-icon-wrap venue"><i class="fa-solid fa-building-columns"></i></div>
                                        Venue
                                    </div>
                                </td>
                                <td>2023-11-10</td>
                                <td style="font-weight: 600;">RM 5,000</td>
                                <td>
                                    <div class="status-icon-badge paid">
                                        <i class="fa-solid fa-check"></i> Paid
                                    </div>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn dropdown">
                                            <i class="fa-regular fa-file-lines"></i>
                                            <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>
                                        </button>
                                        <button class="action-btn icon-only delete"><i class="fa-regular fa-trash-can"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="expense-name">Groom's Suit</div>
                                </td>
                                <td>
                                    <div class="category-cell">
                                        <div class="cat-icon-wrap attire"><i class="fa-solid fa-shirt"></i></div>
                                        Attire
                                    </div>
                                </td>
                                <td>2023-11-15</td>
                                <td style="font-weight: 600;">RM 1,200</td>
                                <td>
                                    <div class="status-icon-badge pending">
                                        <i class="fa-solid fa-xmark"></i> Pending
                                    </div>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn dropdown">
                                            <i class="fa-regular fa-file-lines"></i>
                                            <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>
                                        </button>
                                        <button class="action-btn icon-only delete"><i class="fa-regular fa-trash-can"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="expense-name">Wedding Cake</div>
                                </td>
                                <td>
                                    <div class="category-cell">
                                        <div class="cat-icon-wrap catering"><i class="fa-solid fa-bell-concierge"></i></div>
                                        Catering
                                    </div>
                                </td>
                                <td>2023-11-20</td>
                                <td style="font-weight: 600;">RM 1,800</td>
                                <td>
                                    <div class="status-icon-badge paid">
                                        <i class="fa-solid fa-check"></i> Paid
                                    </div>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn dropdown">
                                            <i class="fa-regular fa-file-lines"></i>
                                            <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>
                                        </button>
                                        <button class="action-btn icon-only delete"><i class="fa-regular fa-trash-can"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="expense-name">Decoration Deposit</div>
                                </td>
                                <td>
                                    <div class="category-cell">
                                        <div class="cat-icon-wrap decoration"><i class="fa-solid fa-tree"></i></div>
                                        Decoration
                                    </div>
                                </td>
                                <td>2023-11-25</td>
                                <td style="font-weight: 600;">RM 2,000</td>
                                <td>
                                    <div class="status-icon-badge pending">
                                        <i class="fa-solid fa-xmark"></i> Pending
                                    </div>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn dropdown">
                                            <i class="fa-regular fa-file-lines"></i>
                                            <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>
                                        </button>
                                        <button class="action-btn icon-only delete"><i class="fa-regular fa-trash-can"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="table-footer">
                    <i class="fa-solid fa-circle-info" style="color: #bbb;"></i> All amounts are in Malaysian Ringgit (RM)
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-col">
                <!-- Budget Overview -->
                <div class="card-wrap">
                    <div class="card-title">Budget Overview</div>
                    <div class="donut-chart-wrap">
                        <div class="donut-chart">
                            <div class="donut-inner">
                                <span class="donut-val">50%</span>
                                <span class="donut-label">Spent</span>
                            </div>
                        </div>
                    </div>
                    <div class="legend-list">
                        <div class="legend-item">
                            <div class="legend-left">
                                <div class="legend-dot" style="background: #4CAF50;"></div>
                                <span style="color: #555;">Spent</span>
                            </div>
                            <span class="legend-val">RM 10,000 (50%)</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-left">
                                <div class="legend-dot" style="background: #e0e0e0;"></div>
                                <span style="color: #555;">Remaining</span>
                            </div>
                            <span class="legend-val">RM 10,000 (50%)</span>
                        </div>
                    </div>
                    <div class="legend-divider"></div>
                    <div class="legend-total">
                        <span>Total Budget</span>
                        <span>RM 20,000</span>
                    </div>
                </div>

                <!-- By Category -->
                <div class="card-wrap">
                    <div class="card-title">By Category</div>
                    <div class="category-list">
                        <div class="category-item">
                            <div class="category-info">
                                <div class="cat-icon-wrap venue"><i class="fa-solid fa-building-columns"></i></div>
                                <div>
                                    <div class="category-name">Venue</div>
                                    <div class="category-desc">RM 5,000</div>
                                </div>
                            </div>
                            <div class="category-amount">RM 5,000</div>
                        </div>
                        <div class="category-item">
                            <div class="category-info">
                                <div class="cat-icon-wrap catering"><i class="fa-solid fa-bell-concierge"></i></div>
                                <div>
                                    <div class="category-name">Catering</div>
                                    <div class="category-desc">RM 0 (0%)</div>
                                </div>
                            </div>
                            <div class="category-amount">RM 1,800</div>
                        </div>
                        <div class="category-item">
                            <div class="category-info">
                                <div class="cat-icon-wrap attire"><i class="fa-solid fa-shirt"></i></div>
                                <div>
                                    <div class="category-name">Attire</div>
                                    <div class="category-desc">RM 0 (0%)</div>
                                </div>
                            </div>
                            <div class="category-amount">RM 1,200</div>
                        </div>
                        <div class="category-item">
                            <div class="category-info">
                                <div class="cat-icon-wrap photo"><i class="fa-solid fa-camera"></i></div>
                                <div>
                                    <div class="category-name">Photography</div>
                                    <div class="category-desc">RM 0 (0%)</div>
                                </div>
                            </div>
                            <div class="category-amount">RM 0</div>
                        </div>
                        <div class="category-item">
                            <div class="category-info">
                                <div class="cat-icon-wrap decoration"><i class="fa-solid fa-tree"></i></div>
                                <div>
                                    <div class="category-name">Decoration</div>
                                    <div class="category-desc">RM 0 (0%)</div>
                                </div>
                            </div>
                            <div class="category-amount">RM 2,000</div>
                        </div>
                        <div class="category-item">
                            <div class="category-info">
                                <div class="cat-icon-wrap others"><i class="fa-solid fa-ellipsis"></i></div>
                                <div>
                                    <div class="category-name">Others</div>
                                    <div class="category-desc">RM 0 (0%)</div>
                                </div>
                            </div>
                            <div class="category-amount">RM 0</div>
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
