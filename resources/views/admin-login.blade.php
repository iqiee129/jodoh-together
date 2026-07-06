<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jodoh Together | Admin Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Pacifico&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('login.css') }}">
</head>

<body>
    <main class="login-page">
        <h1 class="brand">Jodoh Together</h1>

        <section class="auth-card admin-auth-card" aria-label="Admin login panel">
            <div class="form-panel login-panel">
                <h2 class="login-title">Admin Login</h2>
                <p class="login-subtitle">Manage users, vendors, and dashboard activity.</p>

                <form class="login-form" id="adminLoginForm">
                    <input class="form-input" id="adminEmail" type="email" placeholder="email" aria-label="Email"
                        required>
                    <input class="form-input" id="adminPassword" type="password" placeholder="password"
                        aria-label="Password" required>
                    <button class="btn login-btn" type="submit">LOGIN</button>

                </form>
            </div>

            <div class="message-panel signup-panel">
                <h2 class="message-heading">ADMIN<br>DASHBOARD</h2>
                <a class="btn signup-btn admin-return-link" href="{{ url('login') }}">USER LOGIN</a>
            </div>
        </section>
    </main>

    <script>
        const adminLoginForm = document.getElementById("adminLoginForm");
        const adminEmail = document.getElementById("adminEmail");
        const adminPassword = document.getElementById("adminPassword");
        const loginMessage = document.getElementById("loginMessage");

        adminLoginForm.addEventListener("submit", (event) => {
            event.preventDefault();

            if (!adminEmail.value.trim() || !adminPassword.value.trim()) {
                loginMessage.textContent = "Please enter both email and password.";
                return;
            }

            sessionStorage.setItem("jodohAdminLoggedIn", "true");
            sessionStorage.setItem("jodohAdminEmail", adminEmail.value.trim());
            window.location.href = "{{ url('admin/dashboard') }}";
        });
    </script>
</body>

</html>