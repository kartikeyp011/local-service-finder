<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Welcome, <?= $_SESSION['user_name']; ?> 🧑‍💼 (Customer)</h2>
    <p>This will be your customer dashboard.</p>
    <a href="logout.php" class="btn btn-danger">Logout</a>
</div>
</body>
</html>
