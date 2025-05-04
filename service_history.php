<?php 
session_start(); 

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "local_service_finder");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch service history with potential reviews
$stmt = $conn->prepare("SELECT 
                            b.id AS booking_id, 
                            b.service_id, 
                            b.message, 
                            b.status, 
                            b.created_at, 
                            s.title, 
                            u.name AS provider_name,
                            r.rating, 
                            r.comment 
                        FROM bookings b 
                        JOIN services s ON b.service_id = s.id 
                        JOIN users u ON s.provider_id = u.id 
                        LEFT JOIN reviews r ON b.id = r.booking_id
                        WHERE b.customer_id = ? 
                          AND b.status IN ('completed', 'canceled', 'rescheduled') 
                        ORDER BY b.created_at DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

$current_page = 'service-history';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service History - Local Service Finder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap & FontAwesome -->
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
        .badge-status {
            padding: 0.35em 0.7em;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            text-transform: capitalize;
        }
        .completed {
            background-color: #28a745;
            color: white;
        }
        .canceled {
            background-color: #dc3545;
            color: white;
        }
        .rescheduled {
            background-color: #ffc107;
            color: black;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 2rem rgba(0,0,0,0.1);
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
    <a href="customer-dashboard.php" class="<?= ($current_page == 'customer-dashboard') ? 'active' : '' ?>"><i class="fas fa-home me-2"></i>Dashboard</a>
    <a href="booked-services.php"><i class="fas fa-clipboard-list me-2"></i>Booked Services</a>
    <a href="edit_profile.php"><i class="fas fa-user-edit me-2"></i>Edit Profile</a>
    <a href="service_history.php" class="active"><i class="fas fa-history me-2"></i>Service History</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
</nav>

<!-- Main Content -->
<main class="main">
    <h2 class="fw-bold">Service History</h2>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?= htmlspecialchars($row['title']) ?></h5>
                    <span class="badge-status <?= strtolower($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span>
                </div>
                <div class="card-body">
                    <p><strong>Message:</strong> <?= htmlspecialchars($row['message']) ?></p>
                    <p><strong>Provider:</strong> <?= htmlspecialchars($row['provider_name']) ?></p>
                    <p><strong>Date:</strong> <?= date("F j, Y, g:i a", strtotime($row['created_at'])) ?></p>

                    <?php if (strtolower($row['status']) == 'completed'): ?>
                        <?php if (is_null($row['rating'])): ?>
                            <a href="review.php?booking_id=<?= $row['booking_id'] ?>" class="btn btn-outline-primary mt-2">
                                <i class="fas fa-star me-1"></i>Leave a Review
                            </a>
                        <?php else: ?>
                            <div class="mt-3">
                                <h6><i class="fas fa-star text-warning me-1"></i>Your Review</h6>
                                <p><strong>Rating:</strong> <?= str_repeat('⭐', $row['rating']) ?> (<?= $row['rating'] ?>/5)</p>
                                <p><strong>Comment:</strong><br><?= nl2br(htmlspecialchars($row['comment'])) ?></p>
                                <p class="text-muted"><small>Reviewed on <?= date("F j, Y", strtotime($row['created_at'])) ?></small></p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>You have no service history to show.</p>
    <?php endif; ?>
</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php 
$stmt->close();
$conn->close();
?>