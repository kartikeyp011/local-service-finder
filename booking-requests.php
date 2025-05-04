<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'provider') {
    header('Location: login.php');
    exit;
}

$conn = new mysqli("localhost", "root", "", "local_service_finder");

$provider_id = $_SESSION['user_id'];
$query = "
    SELECT b.id, u.name AS customer_name, s.title, b.message, b.status, b.created_at 
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    JOIN users u ON b.customer_id = u.id
    WHERE b.provider_id = ?
    ORDER BY b.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Requests - Local Service Finder</title>
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
            width: 240px;
            top: 0;
            left: 0;
            padding-top: 1.5rem;
        }
        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            display: block;
            padding: 0.75rem 1.5rem;
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
        .table th {
            background-color: #009579;
            color: white;
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
    <a href="provider-dashboard.php"><i class="fas fa-chart-line me-2"></i>Dashboard</a>
    <a href="add-service.php"><i class="fas fa-plus me-2"></i>Add Service</a>
    <a href="booking-requests.php" class="active"><i class="fas fa-clipboard-check me-2"></i>Booking Requests</a>
    <a href="provider-reviews.php"><i class="fas fa-star me-2"></i>Reviews Received</a>
    <a href="edit-provider-profile.php"><i class="fas fa-user-cog me-2"></i>Edit Profile</a>
</nav>

<!-- Main -->
<main class="main">
    <h2 class="mb-4">Booking Requests</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Service Title</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Requested At</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['customer_name']) ?></td>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                            <td><span class="badge bg-secondary"><?= ucfirst($row['status']) ?></span></td>
                            <td><?= date("d M Y, h:i A", strtotime($row['created_at'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">No booking requests found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>