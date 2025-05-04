<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'provider') {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "local_service_finder");
$provider_id = $_SESSION['user_id'];

// Quick stats
$total_services = $conn->query("SELECT COUNT(*) as total FROM services WHERE provider_id = $provider_id")->fetch_assoc()['total'];
$total_bookings = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE service_id IN (SELECT id FROM services WHERE provider_id = $provider_id)")->fetch_assoc()['total'];
$total_reviews = $conn->query("SELECT COUNT(*) as total FROM reviews WHERE provider_id = $provider_id")->fetch_assoc()['total'];

// Latest services
$services = $conn->query("SELECT * FROM services WHERE provider_id = $provider_id ORDER BY created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Provider Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            background: #343a40;
            color: #fff;
            position: fixed;
            width: 240px;
            top: 0;
            left: 0;
            padding-top: 2rem;
        }
        .sidebar a {
            color: #adb5bd;
            padding: 0.75rem 1.5rem;
            display: block;
            text-decoration: none;
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
        .topbar {
            background: #fff;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card i {
            font-size: 2rem;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<nav class="sidebar">
    <div class="text-center mb-4">
        <i class="fas fa-user-tie fa-3x"></i>
        <h5 class="mt-2"><?= htmlspecialchars($_SESSION['user_name']); ?></h5>
    </div>
    <a href="provider-dashboard.php" class="active"><i class="fas fa-chart-line me-2"></i>Dashboard</a>
    <a href="add-service.php"><i class="fas fa-plus me-2"></i>Add Service</a>
    <a href="booking-requests.php"><i class="fas fa-clipboard-check me-2"></i>Booking Requests</a>
    <a href="provider-reviews.php"><i class="fas fa-star me-2"></i>Reviews Received</a>
    <a href="edit-provider-profile.php"><i class="fas fa-user-cog me-2"></i>Edit Profile</a>
</nav>

<!-- Main content -->
<div class="main">
    <!-- Top bar -->
    <div class="topbar">
        <h4 class="mb-0">Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?> 👷</h4>
        <a href="logout.php" class="btn btn-outline-danger"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
    </div>

    <!-- Quick stats -->
    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm p-3">
                <i class="fas fa-tools text-primary"></i>
                <h5 class="mt-2">Total Services</h5>
                <p class="fs-4 fw-bold"><?= $total_services; ?></p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm p-3">
                <i class="fas fa-clipboard-list text-success"></i>
                <h5 class="mt-2">Booking Requests</h5>
                <p class="fs-4 fw-bold"><?= $total_bookings; ?></p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm p-3">
                <i class="fas fa-star text-warning"></i>
                <h5 class="mt-2">Reviews Received</h5>
                <p class="fs-4 fw-bold"><?= $total_reviews; ?></p>
            </div>
        </div>
    </div>

    <!-- Latest services -->
    <h4 class="mt-5">Service Listings</h4>
    <?php if ($services->num_rows > 0): ?>
        <table class="table table-bordered bg-white mt-3">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Price (₹)</th>
                    <th>Posted On</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($service = $services->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($service['title']); ?></td>
                        <td><?= htmlspecialchars($service['category']); ?></td>
                        <td><?= htmlspecialchars($service['location']); ?></td>
                        <td><?= number_format($service['price'], 2); ?></td>
                        <td><?= date("d M Y", strtotime($service['created_at'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="mt-3">You haven’t posted any services yet.</p>
    <?php endif; ?>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>