<?php
session_start();
// Access control: ensure user is logged in as a customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header('Location: login.php');
    exit;
}

// Database connection
require_once 'config.php';

// Get user name or default
$username = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Valued Customer';
$customer_id = $_SESSION['user_id'];

// Fetch total bookings, completed, pending, and rejected
$query = "SELECT COUNT(*) AS total_bookings, 
                 SUM(status = 'approved') AS completed,
                 SUM(status = 'pending') AS pending,
                 SUM(status = 'rejected') AS rejected
          FROM bookings
          WHERE customer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_assoc();

// Fetch recent bookings (last 5)
$query_recent = "SELECT s.title, b.created_at, b.status 
                 FROM bookings b
                 JOIN services s ON b.service_id = s.id
                 WHERE b.customer_id = ? 
                 ORDER BY b.created_at DESC 
                 LIMIT 5";
$stmt_recent = $conn->prepare($query_recent);
$stmt_recent->bind_param("i", $customer_id);
$stmt_recent->execute();
$result_recent = $stmt_recent->get_result();

// Convert status to a readable format
$recent_bookings = [];
while ($row = $result_recent->fetch_assoc()) {
    $recent_bookings[] = [
        'title' => htmlspecialchars($row['title']),
        'date' => date('F j, Y', strtotime($row['created_at'])),
        'status' => ucfirst($row['status']),
    ];
}

$stmt->close();
$stmt_recent->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 2rem rgba(0,0,0,0.1);
        }
        .card i {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>

<!-- Sidebar Navigation -->
<nav class="sidebar">
    <div class="text-center mb-4">
        <i class="fas fa-user-circle fa-3x"></i>
        <h5 class="mt-2"><?= htmlspecialchars($username) ?></h5>
    </div>
    <a href="customer-dashboard.php" class="active"><i class="fas fa-home me-2"></i>Dashboard</a>
    <a href="booked-services.php"><i class="fas fa-clipboard-list me-2"></i>Booked Services</a>
    <a href="edit_profile.php"><i class="fas fa-user-edit me-2"></i>Edit Profile</a>
    <a href="service_history.php"><i class="fas fa-history me-2"></i>Service History</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
</nav>

<!-- Main Content -->
<main class="main">
    <!-- Welcome Header -->
    <div class="mb-4">
        <h2 class="fw-bold">Welcome, <?= htmlspecialchars($username) ?>!</h2>
        <p class="text-muted">Here’s a snapshot of your service activity.</p>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Bookings</h5>
                    <p class="fs-3"><?= $stats['total_bookings'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Completed Services</h5>
                    <p class="fs-3"><?= $stats['completed'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Upcoming Appointments</h5>
                    <p class="fs-3"><?= $stats['pending'] ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Panel with Action Tips -->
    <div class="alert alert-info shadow-sm">
        <h5 class="alert-heading">Need help getting started?</h5>
        <ul class="mb-0">
            <li>Check your <strong>Booked Services</strong> to see what’s upcoming.</li>
            <li>Use <strong>Edit Profile</strong> to update your contact info.</li>
            <li>Review <strong>Service History</strong> for completed work.</li>
        </ul>
    </div>

    <!-- Recent Bookings Table -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Recent Bookings</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_bookings as $booking): ?>
                        <tr>
                            <td><?= $booking['title'] ?></td>
                            <td><?= $booking['date'] ?></td>
                            <td><span class="badge bg-secondary"><?= $booking['status'] ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recent_bookings)): ?>
                        <tr><td colspan="3" class="text-center text-muted">No recent bookings found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>