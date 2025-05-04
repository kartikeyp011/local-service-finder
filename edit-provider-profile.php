<?php
session_start();

// Ensure the user is logged in and is a provider
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'provider') {
    header('Location: login.php');
    exit;
}

$conn = new mysqli("localhost", "root", "", "local_service_finder");

// Fetch provider data
$provider_id = $_SESSION['user_id'];
$query = "SELECT name, email, password FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$provider = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle profile update
$errors = [];
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Profile Update
    if (isset($_POST['update_profile'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);

        if (empty($name) || empty($email)) {
            $errors[] = "Name and Email are required.";
        }

        if (empty($errors)) {
            $update_query = "UPDATE users SET name = ?, email = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ssi", $name, $email, $provider_id);

            if ($update_stmt->execute()) {
                $_SESSION['user_name'] = $name; // Update session with new name
                $success_msg = "Profile updated successfully!";
            } else {
                $errors[] = "Failed to update profile. Please try again.";
            }
        }
    }

    // Change Password
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $errors[] = "All password fields are required.";
        }

        if ($new_password !== $confirm_password) {
            $errors[] = "New password and confirm password do not match.";
        }

        // Check current password
        if (!password_verify($current_password, $provider['password'])) {
            $errors[] = "Current password is incorrect.";
        }

        if (empty($errors)) {
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_password_query = "UPDATE users SET password = ? WHERE id = ?";
            $update_password_stmt = $conn->prepare($update_password_query);
            $update_password_stmt->bind_param("si", $hashed_new_password, $provider_id);

            if ($update_password_stmt->execute()) {
                $success_msg = "Password changed successfully!";
            } else {
                $errors[] = "Failed to change password. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Provider Profile - Local Service Finder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            background: #343a40;
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            width: 240px;
            padding-top: 1.5rem;
        }
        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            display: block;
            padding: 0.75rem 1.5rem;
            transition: all 0.2s;
        }
        .sidebar a:hover,
        .sidebar a.active {
            background-color: #495057;
            color: #fff;
        }
        .main {
            margin-left: 240px;
            padding: 2rem;
        }
        .form-container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .form-container .form-group {
            margin-bottom: 1.5rem;
        }
        .form-container .btn-primary {
            background-color: #009579;
            border: none;
        }
        .form-container .btn-primary:hover {
            background-color: #00775a;
        }
        .form-container h2 {
            color: #333;
            font-weight: 600;
        }
        .form-container .alert {
            padding: 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<!-- Sidebar Navigation -->
<nav class="sidebar">
    <div class="text-center mb-4">
        <i class="fas fa-user-tie fa-3x"></i>
        <h5 class="mt-2"><?= htmlspecialchars($_SESSION['user_name']); ?></h5>
    </div>
    <a href="provider-dashboard.php" class="<?= $current_page == 'provider-dashboard.php' ? 'active' : '' ?>"><i class="fas fa-chart-line me-2"></i>Dashboard</a>
    <a href="add-service.php" class="<?= $current_page == 'add-service.php' ? 'active' : '' ?>"><i class="fas fa-plus me-2"></i>Add Service</a>
    <a href="booking-requests.php" class="<?= $current_page == 'booking-requests.php' ? 'active' : '' ?>"><i class="fas fa-clipboard-check me-2"></i>Booking Requests</a>
    <a href="provider-reviews.php" class="<?= $current_page == 'provider-reviews.php' ? 'active' : '' ?>"><i class="fas fa-star me-2"></i>Reviews Received</a>
    <a href="edit-provider-profile.php" class="<?= $current_page == 'edit-provider-profile.php' ? 'active' : '' ?>"><i class="fas fa-user-cog me-2"></i>Edit Profile</a>
</nav>

<!-- Main Content -->
<main class="main">
    <div class="form-container">
        <h2>Edit Profile</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (isset($success_msg)): ?>
            <div class="alert alert-success">
                <?= $success_msg ?>
            </div>
        <?php endif; ?>

        <!-- Profile Update Form -->
        <form method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($provider['name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($provider['email']) ?>" required>
            </div>

            <button type="submit" name="update_profile" class="btn btn-primary btn-block">Update Profile</button>
        </form>

        <hr>

        <!-- Change Password Form -->
        <h2>Change Password</h2>
        <form method="POST">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>

            <button type="submit" name="change_password" class="btn btn-primary btn-block">Change Password</button>
        </form>
    </div>
</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>