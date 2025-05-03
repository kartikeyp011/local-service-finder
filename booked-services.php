<?php
session_start();
include('config.php');

// Ensure the user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: login.php');
    exit();
}

// Get the logged-in customer's ID
$customer_id = $_SESSION['user_id'];

// SQL to retrieve booked services for the customer, with service details
$sql = "
    SELECT b.id AS booking_id, s.title AS service_title, s.category, u.name AS provider_name, s.price, b.status, b.created_at
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    JOIN users u ON s.provider_id = u.id
    WHERE b.customer_id = ? 
    ORDER BY b.created_at DESC
";

// Prepare the query
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booked Services</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Sidebar and Navbar (assumed you have already done this) -->

<div class="container mt-4">
    <h2>Your Booked Services</h2>
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Service Title</th>
                    <th scope="col">Category</th>
                    <th scope="col">Provider</th>
                    <th scope="col">Price</th>
                    <th scope="col">Status</th>
                    <th scope="col">Booking Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['service_title']); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><?php echo htmlspecialchars($row['provider_name']); ?></td>
                        <td>₹<?php echo number_format($row['price'], 2); ?></td>
                        <td>
                            <span class="badge 
                                <?php echo $row['status'] === 'approved' ? 'badge-success' : 'badge-warning'; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date("d M Y", strtotime($row['created_at'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You have not booked any services yet.</p>
    <?php endif; ?>
</div>

<!-- Bootstrap JS (optional) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
// Close the database connection
$stmt->close();
$conn->close();
?>