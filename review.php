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

$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
$customer_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Check if this booking is valid and completed
$stmt = $conn->prepare("SELECT b.id, b.service_id, s.provider_id, s.title 
                        FROM bookings b 
                        JOIN services s ON b.service_id = s.id 
                        WHERE b.id = ? AND b.customer_id = ? AND b.status = 'completed'");
$stmt->bind_param("ii", $booking_id, $customer_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    $error = "Invalid or unauthorized booking.";
} else {
    // Check if review already exists
    $check = $conn->prepare("SELECT id FROM reviews WHERE booking_id = ?");
    $check->bind_param("i", $booking_id);
    $check->execute();
    $check_result = $check->get_result();
    if ($check_result->num_rows > 0) {
        $error = "You have already submitted a review for this service.";
    }
}

// Handle review submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error)) {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    if ($rating < 1 || $rating > 5) {
        $error = "Rating must be between 1 and 5.";
    } else {
        $stmt = $conn->prepare("INSERT INTO reviews (booking_id, customer_id, provider_id, rating, comment) 
                                VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $booking_id, $customer_id, $booking['provider_id'], $rating, $comment);
        if ($stmt->execute()) {
            $success = "Your review has been submitted successfully.";
        } else {
            $error = "Failed to submit review.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave a Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Leave a Review</h4>
        </div>
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php elseif (!empty($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
                <a href="service_history.php" class="btn btn-secondary mt-3">Back to History</a>
            <?php elseif ($booking): ?>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Service</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($booking['title']) ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating (1–5)</label>
                        <select name="rating" id="rating" class="form-select" required>
                            <option value="">--Select--</option>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea name="comment" id="comment" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Submit Review</button>
                    <a href="service_history.php" class="btn btn-secondary">Cancel</a>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>