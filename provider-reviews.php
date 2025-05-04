<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'provider') {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "local_service_finder");
$provider_id = $_SESSION['user_id'];

$query = "
    SELECT r.rating, r.comment, r.created_at, u.name AS customer_name
    FROM reviews r
    JOIN users u ON r.customer_id = u.id
    WHERE r.provider_id = ?
    ORDER BY r.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$result = $stmt->get_result();
$reviews = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Provider Reviews</title>
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
        .rating {
            color: #f1c40f;
        }
    </style>
</head>
<body>

<nav class="sidebar">
    <div class="text-center mb-4">
        <i class="fas fa-user-tie fa-3x"></i>
        <h5 class="mt-2"><?= htmlspecialchars($_SESSION['user_name']) ?></h5>
    </div>
    <a href="provider-dashboard.php"><i class="fas fa-chart-line me-2"></i>Dashboard</a>
    <a href="add-service.php"><i class="fas fa-plus me-2"></i>Add Service</a>
    <a href="booking-requests.php"><i class="fas fa-clipboard-check me-2"></i>Booking Requests</a>
    <a href="provider-reviews.php" class="active"><i class="fas fa-star me-2"></i>Reviews Received</a>
    <a href="edit-provider-profile.php"><i class="fas fa-user-cog me-2"></i>Edit Profile</a>
</nav>

<main class="main">
    <h2>Reviews Received</h2>

    <?php if (count($reviews) === 0): ?>
        <div class="alert alert-info">You have not received any reviews yet.</div>
    <?php else: ?>
        <?php foreach ($reviews as $review): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($review['customer_name']) ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?= date('F j, Y', strtotime($review['created_at'])) ?></h6>
                    <p class="card-text"><?= htmlspecialchars($review['comment']) ?></p>
                    <p class="card-text rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fa<?= $i <= $review['rating'] ? 's' : 'r' ?> fa-star"></i>
                        <?php endfor; ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>