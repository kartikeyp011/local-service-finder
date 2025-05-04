<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'provider') {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "local_service_finder");

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $category = trim($_POST["category"]);
    $description = trim($_POST["description"]);
    $location = trim($_POST["location"]);
    $price = $_POST["price"];
    $provider_id = $_SESSION["user_id"];

    if ($title && $category && $price) {
        $stmt = $conn->prepare("INSERT INTO services (provider_id, title, category, description, location, price) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssd", $provider_id, $title, $category, $description, $location, $price);

        if ($stmt->execute()) {
            $success = "Service added successfully!";
        } else {
            $error = "Something went wrong. Try again.";
        }

        $stmt->close();
    } else {
        $error = "Please fill all required fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Service</title>
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
    <a href="add-service.php" class="active"><i class="fas fa-plus me-2"></i>Add Service</a>
    <a href="booking-requests.php"><i class="fas fa-clipboard-check me-2"></i>Booking Requests</a>
    <a href="provider-reviews.php"><i class="fas fa-star me-2"></i>Reviews Received</a>
    <a href="edit-provider-profile.php"><i class="fas fa-user-cog me-2"></i>Edit Profile</a>
</nav>

<!-- Main content -->
<div class="main">
    <!-- Top bar -->
    <div class="topbar">
        <h4 class="mb-0">Add New Service</h4>
        <a href="logout.php" class="btn btn-outline-danger"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
    </div>

    <!-- Form -->
    <div class="mt-4">
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Service Title *</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Category *</label>
                <select name="category" class="form-control" required>
                    <option value="">Select a Category</option>
                    <option value="Plumbing">Plumbing</option>
                    <option value="Electrical">Electrical</option>
                    <option value="Carpentry">Carpentry</option>
                    <option value="Cleaning">Cleaning</option>
                    <option value="Painting">Painting</option>
                    <option value="Gardening">Gardening</option>
                    <option value="HVAC">HVAC (Heating, Ventilation, and Air Conditioning)</option>
                    <option value="Pest Control">Pest Control</option>
                    <option value="Moving">Moving</option>
                    <option value="Security Services">Security Services</option>
                    <option value="Personal Training">Personal Training</option>
                    <option value="IT Support">IT Support</option>
                    <option value="Web Development">Web Development</option>
                    <option value="Graphic Design">Graphic Design</option>
                    <option value="Photography">Photography</option>
                    <option value="Event Planning">Event Planning</option>
                    <option value="Appliance Repair">Appliance Repair</option>
                    <option value="Car Maintenance">Car Maintenance</option>
                    <option value="Home Renovation">Home Renovation</option>
                    <option value="Babysitting">Babysitting</option>
                    <option value="Pet Sitting">Pet Sitting</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Location *</label>
                <select name="location" class="form-control" required>
                    <option value="">Select a Location</option>
                    <option value="Delhi">Delhi</option>
                    <option value="Mumbai">Mumbai</option>
                    <option value="Kolkāta">Kolkāta</option>
                    <option value="Bangalore">Bangalore</option>
                    <option value="Chennai">Chennai</option>
                    <option value="Hyderābād">Hyderābād</option>
                    <option value="Pune">Pune</option>
                    <option value="Ahmedabad">Ahmedabad</option>
                    <option value="Sūrat">Sūrat</option>
                    <option value="Lucknow">Lucknow</option>
                    <option value="Jaipur">Jaipur</option>
                    <option value="Kanpur">Kanpur</option>
                    <option value="Mirzāpur">Mirzāpur</option>
                    <option value="Nāgpur">Nāgpur</option>
                    <option value="Ghāziābād">Ghāziābād</option>
                    <option value="Supaul">Supaul</option>
                    <option value="Vadodara">Vadodara</option>
                    <option value="Rājkot">Rājkot</option>
                    <option value="Vishākhapatnam">Vishākhapatnam</option>
                    <option value="Indore">Indore</option>
                    <option value="Thāne">Thāne</option>
                    <option value="Bhopāl">Bhopāl</option>
                    <option value="Pimpri-Chinchwad">Pimpri-Chinchwad</option>
                    <option value="Patna">Patna</option>
                    <option value="Bilāspur">Bilāspur</option>
                    <option value="Ludhiāna">Ludhiābna</option>
                    <option value="Āgra">Āgra</option>
                    <option value="Madurai">Madurai</option>
                    <option value="Jamshedpur">Jamshedpur</option>
                    <option value="Prayagraj">Prayagraj</option>
                    <option value="Nāsik">Nāsik</option>
                    <option value="Farīdābād">Farīdābād</option>
                    <option value="Meerut">Meerut</option>
                    <option value="Jabalpur">Jabalpur</option>
                    <option value="Kalyān">Kalyān</option>
                    <option value="Vasai-Virar">Vasai-Virar</option>
                    <option value="Najafgarh">Najafgarh</option>
                    <option value="Vārānasi">Vārānasi</option>
                    <option value="Srīnagar">Srīnagar</option>
                    <option value="Aurangābād">Aurangābād</option>
                    <option value="Dhanbād">Dhanbād</option>
                    <option value="Amritsar">Amritsar</option>
                    <option value="Alīgarh">Alīgarh</option>
                    <option value="Guwāhāti">Guwāhāti</option>
                    <option value="Hāora">Hāora</option>
                    <option value="Rānchi">Rānchi</option>
                    <option value="Gwalior">Gwalior</option>
                    <option value="Chandīgarh">Chandīgarh</option>
                    <option value="Haldwāni">Haldwāni</option>
                    <option value="Vijayavāda">Vijayavāda</option>
                    <option value="Jodhpur">Jodhpur</option>
                    <option value="Raipur">Raipur</option>
                    <option value="Kota">Kota</option>
                    <option value="Bhayandar">Bhayandar</option>
                    <option value="Loni">Loni</option>
                    <option value="Ambattūr">Ambattūr</option>
                    <option value="Salt Lake City">Salt Lake City</option>
                    <option value="Bhātpāra">Bhātpāra</option>
                    <option value="Kūkatpalli">Kūkatpalli</option>
                    <option value="Dāsarhalli">Dāsarhalli</option>
                    <option value="Muzaffarpur">Muzaffarpur</option>
                    <option value="Oulgaret">Oulgaret</option>
                    <option value="New Delhi">New Delhi</option>
                    <option value="Tiruvottiyūr">Tiruvottiyūr</option>
                    <option value="Puducherry">Puducherry</option>
                    <option value="Byatarayanpur">Byatarayanpur</option>
                    <option value="Pallāvaram">Pallāvaram</option>
                    <option value="Secunderābād">Secunderābād</option>
                    <option value="Shimla">Shimla</option>
                    <option value="Puri">Puri</option>
                    <option value="Murtazābād">Murtazābād</option>
                    <option value="Shrīrāmpur">Shrīrāmpur</option>
                    <option value="Chandannagar">Chandannagar</option>
                    <option value="Sultānpur Mazra">Sultānpur Mazra</option>
                    <option value="Krishnanagar">Krishnanagar</option>
                    <option value="Bārākpur">Bārākpur</option>
                    <option value="Bhālswa Jahangirpur">Bhālswa Jahangirpur</option>
                    <option value="Nāngloi Jāt">Nāngloi Jāt</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Price (₹)</label>
                <input type="number" step="0.01" name="price" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Add Service</button>
            <a href="provider-dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </form>

    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>