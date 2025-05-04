<?php $current_page = 'services'; ?>
<?php include 'includes/header.php'; ?>

        <!-- Jobs Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <h1 class="text-center mb-5 wow fadeInUp" data-wow-delay="0.1s">Job Listing</h1>
                <div class="tab-class text-center wow fadeInUp" data-wow-delay="0.3s">
                    <ul class="nav nav-pills d-inline-flex justify-content-center border-bottom mb-5">
                        <li class="nav-item">
                            <a class="d-flex align-items-center text-start mx-3 ms-0 pb-3 active" data-bs-toggle="pill" href="#tab-1">
                                <h6 class="mt-n1 mb-0">Featured</h6>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="d-flex align-items-center text-start mx-3 pb-3" data-bs-toggle="pill" href="#tab-2">
                                <h6 class="mt-n1 mb-0">Full Time</h6>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="d-flex align-items-center text-start mx-3 me-0 pb-3" data-bs-toggle="pill" href="#tab-3">
                                <h6 class="mt-n1 mb-0">Part Time</h6>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab-1" class="tab-pane fade show p-0 active">
                        <div id="tab-1" class="tab-pane fade show p-0 active">
<?php
$conn = new mysqli("localhost", "root", "", "local_service_finder");
$query = "SELECT s.*, u.name AS provider_name FROM services s 
          JOIN users u ON s.provider_id = u.id 
          ORDER BY s.created_at DESC";
$result = $conn->query($query);

if ($result->num_rows > 0):
    while ($service = $result->fetch_assoc()):
?>
    <div class="job-item p-4 mb-4">
    <div class="row g-4">
        <div class="col-sm-12 col-md-8 d-flex align-items-center">
            <img class="flex-shrink-0 img-fluid border rounded" src="img/com-logo-1.jpg" alt="" style="width: 80px; height: 80px;">
            <div class="text-start ps-4">
                <h5 class="mb-3"><?= htmlspecialchars($service['title']); ?></h5>
                <span class="text-truncate me-3"><i class="fa fa-user text-primary me-2"></i><?= htmlspecialchars($service['provider_name']); ?></span>
                <span class="text-truncate me-3"><i class="fa fa-map-marker-alt text-primary me-2"></i><?= htmlspecialchars($service['location']); ?></span>
                <span class="text-truncate me-0"><i class="fa fa-rupee-sign text-primary me-2"></i><?= number_format($service['price'], 2); ?></span>
            </div>
        </div>
        <div class="col-sm-12 col-md-4 d-flex flex-column align-items-start align-items-md-end justify-content-center">
            <div class="d-flex mb-3">
                <a class="btn btn-light btn-square me-3" href="#"><i class="far fa-heart text-primary"></i></a>
                <a class="btn btn-primary me-2" href="service-detail.php?id=<?= $service['id'] ?>">View Details</a>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'customer'): ?>
                    <form method="POST" action="book-service.php" class="d-inline">
                        <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                        <button type="submit" class="btn btn-success">Book Now</button>
                    </form>
                <?php endif; ?>
            </div>
            <small class="text-truncate"><i class="far fa-calendar-alt text-primary me-2"></i>Posted: <?= date("d M, Y", strtotime($service['created_at'])); ?></small>
        </div>
    </div>
</div>

<?php
    endwhile;
else:
    echo "<p class='text-center'>No services available yet.</p>";
endif;
?>
</div>
</div>
                            <a class="btn btn-primary py-3 px-5" href="">Browse More Jobs</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Jobs End -->

<?php include 'includes/footer.php'; ?>