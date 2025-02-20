<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Test</title>

    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
    <link rel="manifest" href="assets/favicon/site.webmanifest">
    <link rel="shortcut icon" href="assets/favicon/favicon.ico">

    <link rel="stylesheet" href="assets/style.css">

    <script>
        async function handleAuth(endpoint, formId) {
            let form = document.getElementById(formId);
            let formData = new FormData(form);
            let data = Object.fromEntries(formData.entries());

            try {
                // POST request to backend (login or register)
                let response = await fetch(`../auth/${endpoint}.php`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(data)
                });

                // response check
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                let text = await response.text();
                console.log("Raw Response:", text);


                let result;
                try {
                    result = JSON.parse(text);
                } catch (e) {
                    console.error("Failed to parse response as JSON:", e);
                    alert("An error occurred while processing the response.");
                    return;
                }


                if (result.message) {
                    alert(result.message);
                } else if (result.error) {
                    alert(result.error);
                }


                if (endpoint === "login" && result.token) {
                    localStorage.setItem("token", result.token);
                    checkAuth();
                }

            } catch (error) {

                console.error("Error:", error);
                alert("An error occurred. Please try again.");
            }
        }


        function checkAuth() {
            let token = localStorage.getItem("token");
            document.getElementById("auth-forms").style.display = token ? "none" : "block";
            document.getElementById("user-section").style.display = token ? "block" : "none";
            fetchUser(); // Fetch user info if token exists
        }


        async function fetchUser() {
            let token = localStorage.getItem("token");
            if (!token) {
                document.getElementById("user-info").innerText = "Not logged in";
                return;
            }

            console.log("Sending token: " + token);


            let response = await fetch("../auth/user.php", {
                method: 'GET',
                headers: {
                    "Authorization": "Bearer " + token,
                    "Content-Type": "application/json"
                }
            });

            let data = await response.json();
            console.log(data);

            if (data.error) {
                alert("Error: " + data.error);
                return;
            }

            document.getElementById("user-info").innerText = data.name ? `Welcome, ${data.name}` : "Invalid token";
        }


        // 
        async function logout() {
            let token = localStorage.getItem("token");
            if (!token) return;

            await fetch("../auth/logout.php", {
                method: "POST",
                headers: {
                    "Authorization": `Bearer ${token}`
                }
            });

            localStorage.removeItem("token");
            alert("Logged out successfully");
            checkAuth();
        }

        window.onload = checkAuth;
    </script>
</head>

<body>
    <h1>Login Test</h1>

    <div id="auth-forms">
        <h2>Register</h2>
        <form id="register-form" onsubmit="event.preventDefault(); handleAuth('register', 'register-form');">
            <input type="text" name="name" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>

        <h2>Login</h2>
        <form id="login-form" onsubmit="event.preventDefault(); handleAuth('login', 'login-form');">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>

    <div id="user-section" style="display:none;">
        <p id="user-info"></p>
        <button onclick="logout()">Logout</button>
    </div>
</body>

</html>