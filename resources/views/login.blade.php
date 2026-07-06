<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jodoh Together - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Pacifico&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('login.css') }}">
</head>
<body>
    <main class="login-page">
        <h1 class="brand">Jodoh Together</h1>

        <section class="auth-card" id="authCard" aria-label="Login and sign up panel">
            <div class="form-panel login-panel">
                <h2 class="login-title">Login</h2>
                <p class="login-subtitle">Sign in to continue</p>

                <form class="login-form">
                    <input class="form-input" type="text" placeholder="username" aria-label="Username">
                    <input class="form-input" type="password" placeholder="password" aria-label="Password">
                    <button class="btn login-btn" type="button" id="goDashboard">LOGIN</button>
                    <a class="admin-link" href="{{ url('admin/login') }}">Admin login</a>
                </form>
            </div>

            <div class="message-panel signup-panel">
                <h2 class="message-heading">DON'T HAVE<br>AN ACCOUNT?</h2>
                <button class="btn signup-btn" type="button" id="showRegister">SIGN UP</button>
            </div>

            <div class="message-panel return-panel">
                <h2 class="message-heading">ALREADY HAVE<br>AN ACCOUNT?</h2>
                <button class="btn return-btn" type="button" id="showLogin">LOGIN</button>
            </div>

            <div class="form-panel register-panel">
                <h2 class="register-title">Create a new<br>account</h2>

                <form class="register-form">
                    <input class="form-input" type="text" placeholder="full name" aria-label="Full name">
                    <input class="form-input" type="email" placeholder="email" aria-label="Email">
                    <input class="form-input" type="password" placeholder="password" aria-label="Password">
                    <input class="form-input" type="password" placeholder="confirm password" aria-label="Confirm password">
                    <div class="date-field">
                        <input class="form-input" id="weddingDateText" type="text" placeholder="wedding date" aria-label="Wedding date" readonly>
                        <button class="date-button" id="openDatePicker" type="button" aria-label="Choose wedding date">
                            <span class="date-icon" aria-hidden="true"></span>
                        </button>
                        <input class="date-input-native" id="weddingDatePicker" type="date" aria-hidden="true" tabindex="-1">
                    </div>
                    <button class="btn register-btn" type="button">SIGN UP</button>
                </form>
            </div>
        </section>
    </main>

    <script>
        const authCard = document.getElementById("authCard");
        const showRegister = document.getElementById("showRegister");
        const showLogin = document.getElementById("showLogin");
        const goDashboard = document.getElementById("goDashboard");
        const openDatePicker = document.getElementById("openDatePicker");
        const weddingDatePicker = document.getElementById("weddingDatePicker");
        const weddingDateText = document.getElementById("weddingDateText");

        showRegister.addEventListener("click", () => {
            authCard.classList.add("register-mode");
        });

        showLogin.addEventListener("click", () => {
            authCard.classList.remove("register-mode");
        });

        goDashboard.addEventListener("click", () => {
            window.location.href = "{{ url('dashboard') }}";
        });

        openDatePicker.addEventListener("click", () => {
            if (weddingDatePicker.showPicker) {
                weddingDatePicker.showPicker();
            } else {
                weddingDatePicker.focus();
                weddingDatePicker.click();
            }
        });

        weddingDatePicker.addEventListener("change", () => {
            if (!weddingDatePicker.value) return;

            const date = new Date(`${weddingDatePicker.value}T00:00:00`);
            weddingDateText.value = date.toLocaleDateString("en-GB", {
                day: "2-digit",
                month: "2-digit",
                year: "numeric"
            });
        });
    </script>
</body>
</html>
