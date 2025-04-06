<?php
$conn = new mysqli("localhost", "root", "", "local_service_finder");

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';

$sql = "SELECT s.*, u.name AS provider_name FROM services s 
        JOIN users u ON s.provider_id = u.id 
        WHERE 1";

if (!empty($keyword)) {
    $keyword_escaped = $conn->real_escape_string($keyword);
    $sql .= " AND (s.title LIKE '%$keyword_escaped%' OR s.description LIKE '%$keyword_escaped%')";
}
if (!empty($category)) {
    $category_escaped = $conn->real_escape_string($category);
    $sql .= " AND s.category = '$category_escaped'";
}
if (!empty($location)) {
    $location_escaped = $conn->real_escape_string($location);
    $sql .= " AND s.location = '$location_escaped'";
}

$sql .= " ORDER BY s.created_at DESC";
$result = $conn->query($sql);
?>

<?php $current_page = 'search-results'; ?>
<?php include 'includes/header.php'; ?>

    <!-- Results -->
    <div class="container-xxl py-5">
        <div class="container">
            <h1 class="text-center mb-5 wow fadeInUp" data-wow-delay="0.1s">Available Services</h1>
            <div class="tab-class text-center wow fadeInUp" data-wow-delay="0.3s">
                <div class="tab-content">
                    <div class="tab-pane fade show p-0 active">
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($service = $result->fetch_assoc()): ?>
                                <div class="job-item p-4 mb-4">
                                    <div class="row g-4">
                                        <div class="col-sm-12 col-md-8 d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid border rounded" src="img/com-logo-1.jpg" alt="" style="width: 80px; height: 80px;">
                                            <div class="text-start ps-4">
                                                <h5 class="mb-3"><?= htmlspecialchars($service['title']); ?></h5>
                                                <p><?= htmlspecialchars($service['description']); ?></p>
                                                <span class="text-truncate me-3"><i class="fa fa-map-marker-alt text-primary me-2"></i><?= htmlspecialchars($service['location']); ?></span>
                                                <span class="text-truncate me-0"><i class="fa fa-rupee-sign text-primary me-2"></i><?= number_format($service['price'], 2); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-4 d-flex flex-column align-items-start align-items-md-end justify-content-center">
                                            <a class="btn btn-primary" href="#">View Details</a>
                                            <small class="text-truncate mt-2"><i class="far fa-calendar-alt text-primary me-2"></i>Posted: <?= date("d M, Y", strtotime($service['created_at'])); ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-center">No matching services found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>