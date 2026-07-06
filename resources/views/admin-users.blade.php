<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jodoh Together | Admin Users</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>

    <aside class="sidebar">
        <div class="logo"><i class="fa-solid fa-heart"></i><span>Jodoh Together</span></div>
        <nav class="nav-menu">
            <a href="{{ url('admin/dashboard') }}" class="nav-link"><i class="fa-solid fa-chart-simple"></i> Overview</a>
            <a href="{{ url('admin/vendors') }}" class="nav-link"><i class="fa-solid fa-store"></i> Vendors</a>
            <a href="{{ url('admin/users') }}" class="nav-link active"><i class="fa-regular fa-user"></i> Users</a>
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
                <h1>User Management</h1>
                <p>View registered users and search accounts by name or email.</p>
            </div>
        </header>

        <section class="admin-stats">
            <div class="budget-card">
                <div class="card-icon red"><i class="fa-regular fa-user"></i></div>
                <div class="budget-card-content">
                    <span class="budget-label">Total Users</span>
                    <span class="budget-amount" id="totalUsers">4</span>
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

        <section class="card-wrap">
            <div class="section-head">
                <div>
                    <div class="card-title">Registered Users</div>
                    <p class="admin-muted">Monitor full name, email, wedding date, and account creation date.</p>
                </div>
            </div>
            <div class="filters admin-filters">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="userSearch" placeholder="Search users...">
                </div>
            </div>
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Wedding Date</th>
                            <th>Account Created</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody"></tbody>
                </table>
            </div>
        </section>
    </main>

    <script>
        if (sessionStorage.getItem("jodohAdminLoggedIn") !== "true") {
            window.location.href = "{{ url('admin/login') }}";
        }

        const users = [
            { name: "Irfan Raziq", email: "irfan@example.com", weddingDate: "31 April 2030", created: "10 Jan 2026" },
            { name: "Aisyah Rahman", email: "aisyah@example.com", weddingDate: "31 April 2030", created: "11 Jan 2026" },
            { name: "Nadia Zulkifli", email: "nadia@example.com", weddingDate: "18 June 2030", created: "02 Feb 2026" },
            { name: "Farhan Hakim", email: "farhan@example.com", weddingDate: "05 August 2030", created: "08 Feb 2026" }
        ];

        const userSearch = document.getElementById("userSearch");
        const userTableBody = document.getElementById("userTableBody");
        const totalUsers = document.getElementById("totalUsers");

        function renderUsers() {
            const searchTerm = userSearch.value.toLowerCase();
            const filtered = users.filter((user) => `${user.name} ${user.email}`.toLowerCase().includes(searchTerm));

            userTableBody.innerHTML = filtered.map((user) => `
                <tr>
                    <td><div class="expense-name">${user.name}</div></td>
                    <td>${user.email}</td>
                    <td>${user.weddingDate}</td>
                    <td>${user.created}</td>
                </tr>
            `).join("");

            if (!filtered.length) {
                userTableBody.innerHTML = `<tr><td colspan="4" class="empty-state">No users found.</td></tr>`;
            }

            totalUsers.textContent = users.length;
        }

        userSearch.addEventListener("input", renderUsers);

        document.querySelectorAll(".admin-logout").forEach((link) => {
            link.addEventListener("click", (event) => {
                sessionStorage.removeItem("jodohAdminLoggedIn");
                sessionStorage.removeItem("jodohAdminEmail");
            });
        });

        renderUsers();
    </script>
</body>

</html>
