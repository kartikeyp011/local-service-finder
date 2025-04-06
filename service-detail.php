<?php
// Database config
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "local_service_finder"; // adjust if your DB name is different

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    header("Location: services.php");
    exit;
}
$service_id = (int)$_GET['id'];

if ($service_id > 0) {
    $sql = "SELECT s.*, u.name AS provider_name 
            FROM services s 
            LEFT JOIN users u ON s.provider_id = u.id 
            WHERE s.id = $service_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $service = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Detail</title>
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Adjust path if needed -->
    <style>
        .container { max-width: 900px; margin: 50px auto; padding: 20px; font-family: Arial, sans-serif; }
        .card { background: #fff; padding: 30px; box-shadow: 0 0 10px rgba(0,0,0,0.1); border-radius: 8px; }
        h2 { color: #333; }
        .detail { margin-bottom: 10px; }
        .label { font-weight: bold; color: #555; }
        .not-found { text-align: center; padding: 50px 20px; color: #777; background: #f8f8f8; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($service): ?>
            <div class="card">
                <h2><?php echo htmlspecialchars($service['title']); ?></h2>
                <div class="detail"><span class="label">Provider:</span> <?php echo htmlspecialchars($service['provider_name'] ?? 'Unknown'); ?></div>
                <div class="detail"><span class="label">Category:</span> <?php echo htmlspecialchars($service['category']); ?></div>
                <div class="detail"><span class="label">Location:</span> <?php echo htmlspecialchars($service['location']); ?></div>
                <div class="detail"><span class="label">Price:</span> ₹<?php echo htmlspecialchars($service['price']); ?></div>
                <div class="detail"><span class="label">Description:</span><br> <?php echo nl2br(htmlspecialchars($service['description'])); ?></div>
                <div class="detail"><span class="label">Posted on:</span> <?php echo htmlspecialchars(date("F j, Y, g:i a", strtotime($service['created_at']))); ?></div>
            </div>
        <?php elseif ($service_id > 0): ?>
            <div class="not-found">
                <h2>No service found.</h2>
                <p>The service you’re looking for might have been removed or doesn’t exist.</p>
            </div>
        <?php else: ?>
            <div class="not-found">
                <h2>No service selected.</h2>
                <p>Please select a service to view its details.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
