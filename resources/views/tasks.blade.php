<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jodoh Together | Tasks</title>
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
            <a href="{{ url('tasks') }}" class="nav-link active"><i class="fa-regular fa-square-check"></i> Tasks</a>
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
                <h1>Tasks</h1>
                <p>Manage your wedding tasks and keep track of your progress.</p>
            </div>
            <div class="header-right">
                <button class="add-task-btn" type="button"><i class="fa-solid fa-plus"></i> Add Task</button>
                <div class="notification" role="button" tabindex="0"><i class="fa-regular fa-bell"></i><span
                        class="badge">3</span></div>
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

        <!-- Stat Cards -->
        <div class="stat-cards">
            <div class="stat-card">
                <div class="stat-icon total"><i class="fa-solid fa-clipboard-list"></i></div>
                <div class="stat-info"><label>Total Tasks</label>
                    <div class="num">157</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon pending"><i class="fa-regular fa-clock"></i></div>
                <div class="stat-info"><label>Pending</label>
                    <div class="num orange">68</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon progress"><i class="fa-solid fa-arrows-spin"></i></div>
                <div class="stat-info"><label>In Progress</label>
                    <div class="num teal">42</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon completed"><i class="fa-solid fa-check"></i></div>
                <div class="stat-info"><label>Completed</label>
                    <div class="num green">47</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters">
            <div class="search-box"><i class="fa-solid fa-magnifying-glass"></i><input type="text"
                    placeholder="Search tasks..."></div>
            <select class="filter-select">
                <option>All Status</option>
                <option>Completed</option>
                <option>In Progress</option>
                <option>Completed</option>
            </select>
            <select class="filter-select">
                <option>All Categories</option>
                <option>Venue</option>
                <option>Invitations</option>
                <option>Catering</option>
                <option>Attire</option>
                <option>Photography</option>
                <option>Decoration</option>
            </select>
            <select class="filter-select">
                <option>All Priorities</option>
                <option>High</option>
                <option>Medium</option>
                <option>Low</option>
            </select>
            <select class="filter-select" style="min-width:210px">
                <option>Sort by: Deadline (Soonest)</option>
                <option>Sort by: Deadline (Latest)</option>
                <option>Sort by: Priority</option>
            </select>
        </div>

        <!-- Task Table -->
        <div class="task-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:40px"></th>
                        <th>Task</th>
                        <th>Category</th>
                        <th>Deadline</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="task-check"></div>
                        </td>
                        <td>
                            <div class="task-cell">
                                <div>
                                    <div class="task-name">Visit wedding venue at Glass House</div>
                                    <div class="task-desc">Visit and confirm the venue details, packages and
                                        availability.</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="cat-cell"><i class="fa-solid fa-location-dot"></i> Venue</div>
                        </td>
                        <td>
                            <div class="deadline-date">25 Apr 2030</div>
                            <div class="deadline-days">in 2 days</div>
                        </td>
                        <td><span class="priority-badge high">High</span></td>
                        <td><span class="status-badge upcoming">Completed</span></td>
                        <td>
                            <div class="action-btns"><button class="action-btn" title="Edit"><i
                                        class="fa-solid fa-pen-to-square"></i></button><button class="action-btn delete"
                                    title="Delete"><i class="fa-regular fa-trash-can"></i></button></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="task-check"></div>
                        </td>
                        <td>
                            <div class="task-cell">
                                <div>
                                    <div class="task-name">Prepare guest list and invitation card</div>
                                    <div class="task-desc">Finalize guest list and design invitation card.</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="cat-cell"><i class="fa-solid fa-envelope-open-text"></i> Invitations</div>
                        </td>
                        <td>
                            <div class="deadline-date">27 Apr 2030</div>
                            <div class="deadline-days">in 4 days</div>
                        </td>
                        <td><span class="priority-badge high">High</span></td>
                        <td><span class="status-badge upcoming">Completed</span></td>
                        <td>
                            <div class="action-btns"><button class="action-btn" title="Edit"><i
                                        class="fa-solid fa-pen-to-square"></i></button><button class="action-btn delete"
                                    title="Delete"><i class="fa-regular fa-trash-can"></i></button></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="task-check"></div>
                        </td>
                        <td>
                            <div class="task-cell">
                                <div>
                                    <div class="task-name">Confirm catering menu</div>
                                    <div class="task-desc">Schedule tasting and confirm the final menu.</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="cat-cell"><i class="fa-solid fa-utensils"></i> Catering</div>
                        </td>
                        <td>
                            <div class="deadline-date">30 Apr 2030</div>
                            <div class="deadline-days">in 7 days</div>
                        </td>
                        <td><span class="priority-badge medium">Medium</span></td>
                        <td><span class="status-badge in-progress">In Progress</span></td>
                        <td>
                            <div class="action-btns"><button class="action-btn" title="Edit"><i
                                        class="fa-solid fa-pen-to-square"></i></button><button class="action-btn delete"
                                    title="Delete"><i class="fa-regular fa-trash-can"></i></button></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="task-check"></div>
                        </td>
                        <td>
                            <div class="task-cell">
                                <div>
                                    <div class="task-name">Final fitting for wedding attire</div>
                                    <div class="task-desc">Final fitting for both bride and groom.</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="cat-cell"><i class="fa-solid fa-shirt"></i> Attire</div>
                        </td>
                        <td>
                            <div class="deadline-date">15 May 2030</div>
                            <div class="deadline-days">in 22 days</div>
                        </td>
                        <td><span class="priority-badge medium">Medium</span></td>
                        <td><span class="status-badge in-progress">In Progress</span></td>
                        <td>
                            <div class="action-btns"><button class="action-btn" title="Edit"><i
                                        class="fa-solid fa-pen-to-square"></i></button><button class="action-btn delete"
                                    title="Delete"><i class="fa-regular fa-trash-can"></i></button></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="task-check"></div>
                        </td>
                        <td>
                            <div class="task-cell">
                                <div>
                                    <div class="task-name">Book photography &amp; videography</div>
                                    <div class="task-desc">Confirm photographer and videographer.</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="cat-cell"><i class="fa-solid fa-camera"></i> Photography</div>
                        </td>
                        <td>
                            <div class="deadline-date">20 May 2030</div>
                            <div class="deadline-days">in 27 days</div>
                        </td>
                        <td><span class="priority-badge low">Low</span></td>
                        <td><span class="status-badge upcoming">Completed</span></td>
                        <td>
                            <div class="action-btns"><button class="action-btn" title="Edit"><i
                                        class="fa-solid fa-pen-to-square"></i></button><button class="action-btn delete"
                                    title="Delete"><i class="fa-regular fa-trash-can"></i></button></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="task-check done"><i class="fa-solid fa-check" style="font-size:13px"></i></div>
                        </td>
                        <td>
                            <div class="task-cell completed">
                                <div>
                                    <div class="task-name">Send save the date to guests</div>
                                    <div class="task-desc">Send save the date via WhatsApp and email.</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="cat-cell"><i class="fa-solid fa-envelope-open-text"></i> Invitations</div>
                        </td>
                        <td>
                            <div class="deadline-date">10 Mar 2030</div>
                        </td>
                        <td><span class="priority-badge low">Low</span></td>
                        <td><span class="status-badge completed-s">Completed</span></td>
                        <td>
                            <div class="action-btns"><button class="action-btn" title="Edit"><i
                                        class="fa-solid fa-pen-to-square"></i></button><button class="action-btn delete"
                                    title="Delete"><i class="fa-regular fa-trash-can"></i></button></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="task-check done"><i class="fa-solid fa-check" style="font-size:13px"></i></div>
                        </td>
                        <td>
                            <div class="task-cell completed">
                                <div>
                                    <div class="task-name">Choose wedding theme and color</div>
                                    <div class="task-desc">Decide the overall theme and color palette.</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="cat-cell"><i class="fa-solid fa-palette"></i> Decoration</div>
                        </td>
                        <td>
                            <div class="deadline-date">28 Feb 2030</div>
                        </td>
                        <td><span class="priority-badge low">Low</span></td>
                        <td><span class="status-badge completed-s">Completed</span></td>
                        <td>
                            <div class="action-btns"><button class="action-btn" title="Edit"><i
                                        class="fa-solid fa-pen-to-square"></i></button><button class="action-btn delete"
                                    title="Delete"><i class="fa-regular fa-trash-can"></i></button></div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="table-footer">
                <span class="showing">Showing 1 to 7 of 157 tasks</span>
                <div class="pagination">
                    <button class="page-btn" title="Previous"><i class="fa-solid fa-chevron-left"
                            style="font-size:11px"></i></button>
                    <button class="page-btn active">1</button>
                    <button class="page-btn">2</button>
                    <button class="page-btn">3</button>
                    <span class="page-dots">…</span>
                    <button class="page-btn">16</button>
                    <button class="page-btn" title="Next"><i class="fa-solid fa-chevron-right"
                            style="font-size:11px"></i></button>
                </div>
            </div>
        </div>
    </main>

    <script>
        const profileWrap = document.getElementById("profileWrap");
        const profileBtn = document.getElementById("profileBtn");
        profileBtn.addEventListener("click", (e) => { e.stopPropagation(); const o = profileWrap.classList.toggle("open"); profileBtn.setAttribute("aria-expanded", o); });
        document.addEventListener("click", (e) => { if (!profileWrap.contains(e.target)) { profileWrap.classList.remove("open"); profileBtn.setAttribute("aria-expanded", "false"); } });
        document.addEventListener("keydown", (e) => { if (e.key === "Escape") { profileWrap.classList.remove("open"); profileBtn.setAttribute("aria-expanded", "false"); } });
    </script>
</body>

</html>
