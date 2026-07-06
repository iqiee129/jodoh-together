<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jodoh Together | Admin Vendors</title>
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
            <a href="{{ url('admin/vendors') }}" class="nav-link active"><i class="fa-solid fa-store"></i> Vendors</a>
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
                <h1>Vendor Management</h1>
                <p>Add, edit, delete, and view vendors stored in the system.</p>
            </div>
            <div class="header-right">
                <button class="add-btn" type="button" id="openVendorForm"><i class="fa-solid fa-plus"></i> Add
                    Vendor</button>
            </div>
        </header>

        <section class="card-wrap">
            <div class="section-head">
                <div>
                    <div class="card-title">Vendor List</div>
                    <p class="admin-muted">Maintain vendor name, category, location, price, contact, and description.
                    </p>
                </div>
            </div>

            <form class="admin-form" id="vendorForm">
                <input type="hidden" id="vendorId">
                <div class="form-grid">
                    <label>Vendor name<input id="vendorName" type="text" required></label>
                    <label>Category<input id="vendorCategory" type="text" required></label>
                    <label>State<input id="vendorState" type="text" required></label>
                    <label>City<input id="vendorCity" type="text" required></label>
                    <label>Price range<input id="vendorPrice" type="text" placeholder="RM 1,000 - RM 3,000"
                            required></label>
                    <label>Contact number<input id="vendorPhone" type="tel" required></label>
                    <label>Email<input id="vendorEmail" type="email" required></label>
                </div>
                <label>Description<textarea id="vendorDescription" rows="3" required></textarea></label>
                <div class="form-actions">
                    <button class="filter-btn" type="button" id="cancelVendorForm">Cancel</button>
                    <button class="add-btn" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Vendor</button>
                </div>
            </form>

            <div class="filters admin-filters">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="vendorSearch" placeholder="Search vendors...">
                </div>
                <select class="filter-select" id="vendorCategoryFilter">
                    <option value="">All Categories</option>
                </select>
            </div>

            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Vendor</th>
                            <th>Category</th>
                            <th>Location</th>
                            <th>Price Range</th>
                            <th>Contact</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="vendorTableBody"></tbody>
                </table>
            </div>
        </section>
    </main>

    <script>
        if (sessionStorage.getItem("jodohAdminLoggedIn") !== "true") {
            window.location.href = "{{ url('admin/login') }}";
        }

        const vendors = [
            {
                id: 1,
                name: "Penang Pearl Photography",
                category: "Photography",
                state: "Penang",
                city: "George Town",
                price: "RM 2,500 - RM 5,500",
                phone: "012-3456789",
                email: "penangpearl@mail.com",
                description: "Candid beach and garden wedding photography packages."
            },
            {
                id: 2,
                name: "Kedah Garden Venues",
                category: "Venue",
                state: "Kedah",
                city: "Alor Setar",
                price: "RM 6,000 - RM 12,000",
                phone: "013-4567890",
                email: "gardenvenues@mail.com",
                description: "Outdoor garden venue with custom decoration options."
            },
            {
                id: 3,
                name: "Perak Palace Catering",
                category: "Catering",
                state: "Perak",
                city: "Ipoh",
                price: "RM 18 - RM 45 / pax",
                phone: "014-5678901",
                email: "perakpalace@mail.com",
                description: "Traditional Malay wedding catering and buffet setup."
            }
        ];

        const vendorForm = document.getElementById("vendorForm");
        const vendorTableBody = document.getElementById("vendorTableBody");
        const vendorSearch = document.getElementById("vendorSearch");
        const vendorCategoryFilter = document.getElementById("vendorCategoryFilter");

        function renderVendors() {
            const searchTerm = vendorSearch.value.toLowerCase();
            const categoryFilter = vendorCategoryFilter.value;
            const filtered = vendors.filter((vendor) => {
                const matchesSearch = [vendor.name, vendor.category, vendor.state, vendor.city, vendor.email]
                    .join(" ")
                    .toLowerCase()
                    .includes(searchTerm);
                const matchesCategory = !categoryFilter || vendor.category === categoryFilter;
                return matchesSearch && matchesCategory;
            });

            vendorTableBody.innerHTML = filtered.map((vendor) => `
                <tr>
                    <td>
                        <div class="expense-name">${vendor.name}</div>
                        <div class="admin-muted">${vendor.description}</div>
                    </td>
                    <td><span class="status-badge upcoming">${vendor.category}</span></td>
                    <td>${vendor.city}, ${vendor.state}</td>
                    <td><strong>${vendor.price}</strong></td>
                    <td>
                        <div>${vendor.phone}</div>
                        <div class="admin-muted">${vendor.email}</div>
                    </td>
                    <td>
                        <div class="action-btns">
                            <button class="action-btn icon-only" type="button" aria-label="Edit ${vendor.name}" onclick="editVendor(${vendor.id})"><i class="fa-regular fa-pen-to-square"></i></button>
                            <button class="action-btn icon-only delete" type="button" aria-label="Delete ${vendor.name}" onclick="deleteVendor(${vendor.id})"><i class="fa-regular fa-trash-can"></i></button>
                        </div>
                    </td>
                </tr>
            `).join("");

            if (!filtered.length) {
                vendorTableBody.innerHTML = `<tr><td colspan="6" class="empty-state">No vendors found.</td></tr>`;
            }

            const categories = [...new Set(vendors.map((vendor) => vendor.category))].sort();
            vendorCategoryFilter.innerHTML = `<option value="">All Categories</option>` + categories.map((category) => {
                const selected = category === categoryFilter ? "selected" : "";
                return `<option value="${category}" ${selected}>${category}</option>`;
            }).join("");
        }

        function resetVendorForm() {
            vendorForm.reset();
            document.getElementById("vendorId").value = "";
            vendorForm.classList.remove("open");
        }

        function editVendor(id) {
            const vendor = vendors.find((item) => item.id === id);
            if (!vendor) return;

            document.getElementById("vendorId").value = vendor.id;
            document.getElementById("vendorName").value = vendor.name;
            document.getElementById("vendorCategory").value = vendor.category;
            document.getElementById("vendorState").value = vendor.state;
            document.getElementById("vendorCity").value = vendor.city;
            document.getElementById("vendorPrice").value = vendor.price;
            document.getElementById("vendorPhone").value = vendor.phone;
            document.getElementById("vendorEmail").value = vendor.email;
            document.getElementById("vendorDescription").value = vendor.description;
            vendorForm.classList.add("open");
            vendorForm.scrollIntoView({ behavior: "smooth", block: "center" });
        }

        function deleteVendor(id) {
            const index = vendors.findIndex((vendor) => vendor.id === id);
            if (index === -1) return;
            vendors.splice(index, 1);
            renderVendors();
        }

        vendorForm.addEventListener("submit", (event) => {
            event.preventDefault();

            const vendorData = {
                id: Number(document.getElementById("vendorId").value) || Date.now(),
                name: document.getElementById("vendorName").value.trim(),
                category: document.getElementById("vendorCategory").value.trim(),
                state: document.getElementById("vendorState").value.trim(),
                city: document.getElementById("vendorCity").value.trim(),
                price: document.getElementById("vendorPrice").value.trim(),
                phone: document.getElementById("vendorPhone").value.trim(),
                email: document.getElementById("vendorEmail").value.trim(),
                description: document.getElementById("vendorDescription").value.trim()
            };

            const existingIndex = vendors.findIndex((vendor) => vendor.id === vendorData.id);
            if (existingIndex >= 0) {
                vendors[existingIndex] = vendorData;
            } else {
                vendors.unshift(vendorData);
            }

            resetVendorForm();
            renderVendors();
        });

        document.getElementById("openVendorForm").addEventListener("click", () => vendorForm.classList.add("open"));
        document.getElementById("cancelVendorForm").addEventListener("click", resetVendorForm);
        vendorSearch.addEventListener("input", renderVendors);
        vendorCategoryFilter.addEventListener("change", renderVendors);

        document.querySelectorAll(".admin-logout").forEach((link) => {
            link.addEventListener("click", (event) => {
                sessionStorage.removeItem("jodohAdminLoggedIn");
                sessionStorage.removeItem("jodohAdminEmail");
            });
        });

        renderVendors();
    </script>
</body>

</html>
