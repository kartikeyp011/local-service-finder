<?php
// Include config and database connection
include('config.php');

// Check if the user is logged in
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header('Location: login.php');
    exit;
}

// Get the customer id
$customer_id = $_SESSION['user_id'];

// Handle booking a service
$booked_service = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_service_id'])) {
    $service_id = $_POST['book_service_id'];

    // Check if the service is already booked by the customer
    $query_check = "SELECT * FROM bookings WHERE customer_id = ? AND service_id = ? AND status != 'canceled'";
    $stmt_check = $conn->prepare($query_check);
    $stmt_check->bind_param("ii", $customer_id, $service_id);
    $stmt_check->execute();
    $check_result = $stmt_check->get_result();

    if ($check_result->num_rows > 0) {
        $error_message = "You have already booked this service.";
    } else {
        // Book the service
        $query_book = "INSERT INTO bookings (customer_id, service_id, status) VALUES (?, ?, 'pending')";
        $stmt_book = $conn->prepare($query_book);
        $stmt_book->bind_param("ii", $customer_id, $service_id);
        if ($stmt_book->execute()) {
            // Fetch the details of the booked service
            $query_service = "SELECT * FROM services WHERE id = ?";
            $stmt_service = $conn->prepare($query_service);
            $stmt_service->bind_param("i", $service_id);
            $stmt_service->execute();
            $result_service = $stmt_service->get_result();
            $booked_service = $result_service->fetch_assoc();
            $success_message = "Service successfully booked!";
        } else {
            $error_message = "Error booking the service. Please try again.";
        }
        $stmt_book->close();
    }
    $stmt_check->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Services</title>
    <!-- Correct Bootstrap CSS Link -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
    </style>
</head>

<body>

<!-- Sidebar Navigation -->
<nav class="sidebar">
    <div class="text-center mb-4">
        <i class="fas fa-user-circle fa-3x"></i>
        <h5 class="mt-2"><?= htmlspecialchars($_SESSION['user_name']) ?></h5>
    </div>
    <a href="customer-dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
    <a href="booked-services.php"><i class="fas fa-clipboard-list me-2"></i>Booked Services</a>
    <a href="edit_profile.php"><i class="fas fa-user-edit me-2"></i>Edit Profile</a>
    <a href="service_history.php"><i class="fas fa-history me-2"></i>Service History</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
</nav>

<!-- Main Content -->
<main class="main">
    <div class="container">
        <h1 class="my-4">Booking Confirmation</h1>

        <!-- Display success or error messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php elseif (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Show the booked service details -->
        <?php if ($booked_service): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($booked_service['title']) ?></h5>
                    <p class="card-text"><?= htmlspecialchars($booked_service['description']) ?></p>
                    <p class="card-text"><strong>Price: </strong> ₹<?= number_format($booked_service['price'], 2) ?></p>
                    <p class="card-text"><strong>Location: </strong> <?= htmlspecialchars($booked_service['location']) ?></p>
                    <p class="card-text"><strong>Status: </strong> Pending</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Correct Bootstrap JS Link -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>