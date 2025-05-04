<?php 
session_start(); 

// Check if user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "local_service_finder");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch bookings of the logged-in customer
$stmt = $conn->prepare("SELECT b.id, b.service_id, b.message, b.status, b.created_at, s.title, s.provider_id 
                        FROM bookings b 
                        JOIN services s ON b.service_id = s.id 
                        WHERE b.customer_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Check for errors
if (!$result) {
    die("Query failed: " . $conn->error);
}

$current_page = 'booked-services';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booked Services - Local Service Finder</title>
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
            transition: width 0.3s;
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
        .sidebar .user-info {
            text-align: center;
            margin-bottom: 2rem;
        }
        .sidebar .user-info img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-bottom: 1rem;
        }
        .main {
            margin-left: 240px;
            padding: 2rem;
        }
        .status {
            font-weight: bold;
        }
        .pending {
            background-color: #ffc107; /* yellow */
            color: black;
            padding: 0.25rem 0.5rem;
            border-radius: 5px;
        }
        .approved {
            background-color: #28a745; /* green */
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 5px;
        }
        .rejected {
            background-color: #dc3545; /* red */
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 5px;
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
    <div class="user-info">
        <img src="https://via.placeholder.com/50" alt="User Photo">
        <h5><?= htmlspecialchars($_SESSION['user_name']) ?></h5>
    </div>
    <a href="customer-dashboard.php" class="<?= ($current_page == 'customer-dashboard') ? 'active' : '' ?>"><i class="fas fa-home me-2"></i>Dashboard</a>
    <a href="booked-services.php" class="<?= ($current_page == 'booked-services') ? 'active' : '' ?>"><i class="fas fa-clipboard-list me-2"></i>Booked Services</a>
    <a href="edit_profile.php" class="<?= ($current_page == 'edit-profile') ? 'active' : '' ?>"><i class="fas fa-user-edit me-2"></i>Edit Profile</a>
    <a href="service_history.php" class="<?= ($current_page == 'service-history') ? 'active' : '' ?>"><i class="fas fa-history me-2"></i>Service History</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
</nav>

<!-- Main Content -->
<main class="main">
    <h2 class="fw-bold">My Booked Services</h2>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($booking = $result->fetch_assoc()): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><?= htmlspecialchars($booking['title']) ?></h5>
                </div>
                <div class="card-body">
                    <p><strong>Message:</strong> <?= htmlspecialchars($booking['message']) ?></p>
                    <p><strong>Status:</strong> 
                        <span class="status 
                            <?php 
                                // Assign class based on status
                                if ($booking['status'] == 'pending') {
                                    echo 'pending';
                                } elseif ($booking['status'] == 'completed') {
                                    echo 'approved'; // Green for completed
                                } elseif ($booking['status'] == 'canceled') {
                                    echo 'rejected'; // Red for canceled
                                }
                            ?>">
                            <?= ucfirst($booking['status']) ?>
                        </span>
                    </p>

                    <p><strong>Booked On:</strong> <?= date("F j, Y, g:i a", strtotime($booking['created_at'])) ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>You have no booked services yet.</p>
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
