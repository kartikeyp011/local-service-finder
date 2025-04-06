<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'provider') {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "local_service_finder");

$provider_id = $_SESSION['user_id'];

$result = $conn->query("SELECT * FROM services WHERE provider_id = $provider_id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Provider Dashboard</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Welcome, <?= $_SESSION['user_name']; ?> 👷</h2>

    <a href="add-service.php" class="btn btn-success mb-3">+ Add New Service</a>
    <a href="logout.php" class="btn btn-danger mb-3 float-end">Logout</a>

    <h4>Your Service Listings</h4>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered bg-white mt-3">
            <thead class="table-light">
                <tr>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Price (₹)</th>
                    <th>Posted On</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($service = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($service['category']); ?></td>
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
</body>
</html>