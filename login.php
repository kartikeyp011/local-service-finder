<?php 
session_start(); 

$conn = new mysqli("localhost", "root", "", "local_service_finder");
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $errors[] = "Both fields are required.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                if ($user['role'] == 'provider') {
                    header("Location: provider-dashboard.php");
                } else {
                    header("Location: customer-dashboard.php");
                }
                exit;
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "No account found with that email.";
        }

        $stmt->close();
    }
}

$current_page = 'login'; 
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Local Service Finder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #009579;
            margin: 0;
        }

        .container {
            max-width: 430px;
            background: #fff;
            padding: 2rem;
            margin: 100px auto;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .form header {
            font-size: 2rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .form input {
            height: 50px;
            width: 100%;
            margin-bottom: 1.3rem;
            padding: 0 15px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
            outline: none;
        }

        .form input:focus {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .form input.button {
            background: #009579;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }

        .form input.button:hover {
            background: #00775a;
        }

        .signup {
            text-align: center;
            font-size: 15px;
            margin-top: 1rem;
        }

        .signup a {
            color: #009579;
            text-decoration: none;
        }

        .signup a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="index.php" class="navbar-brand d-flex align-items-center text-center py-0 px-4 px-lg-5">
            <h1 class="m-0 text-primary">LocalServiceFinder</h1>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index.php" class="nav-item nav-link <?= ($current_page == 'index') ? 'active' : '' ?>">Home</a>
                <a href="about.php" class="nav-item nav-link <?= ($current_page == 'about') ? 'active' : '' ?>">About</a>
                <a href="services.php" class="nav-item nav-link <?= ($current_page == 'services') ? 'active' : '' ?>">Services</a>
                <a href="contact.php" class="nav-item nav-link <?= ($current_page == 'contact') ? 'active' : '' ?>">Contact</a>
            </div>
            <a href="login.php" class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block"> Login / Signup<i class="fa fa-arrow-right ms-3"></i> </a>
        </div>
    </nav>

    <!-- Login Form -->
    <div class="container">
        <form method="POST" class="form">
            <header>Login</header>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $err) echo "<p>$err</p>"; ?>
                </div>
            <?php endif; ?>

            <input type="email" name="email" placeholder="Enter your email" required>
            <input type="password" name="password" placeholder="Enter your password" required>
            <input type="submit" class="button" value="Login">

            <div class="signup">
                Don't have an account? <a href="signup.php">Signup</a>
            </div>
        </form>
    </div>

    <!-- Optional JS -->
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>