<?php $current_page = 'index'; ?>
<?php include 'includes/header.php'; ?>

        <!-- Service Detail Start -->
        <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
            <div class="container">
                <div class="row gy-5 gx-4">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center mb-5">
                            <img class="flex-shrink-0 img-fluid border rounded" src="<?= $service['profile_image'] ?? 'img/default-profile.png' ?>" alt="" style="width: 80px; height: 80px;">
                            <div class="text-start ps-4">
                                <h3 class="mb-3"><?= htmlspecialchars($service['provider_name']) ?></h3>
                                <span class="text-truncate me-3">
                                    <i class="fa fa-map-marker-alt text-primary me-2"></i>
                                    <?= htmlspecialchars($service['location']) ?>
                                </span>
                            </div>
                        </div>

                        <div class="mb-5">
                            <h4 class="mb-3">Service Description</h4>
                            <p><?= nl2br(htmlspecialchars($service['description'])) ?></p>

                            <h4 class="mb-3">Skills / Responsibilities</h4>
                            <ul class="list-unstyled">
                                <?php foreach (explode("\n", $service['responsibilities']) as $item): ?>
                                    <li><i class="fa fa-angle-right text-primary me-2"></i><?= htmlspecialchars($item) ?></li>
                                <?php endforeach; ?>
                            </ul>

                            <h4 class="mb-3">Qualifications</h4>
                            <ul class="list-unstyled">
                                <?php foreach (explode("\n", $service['qualifications']) as $item): ?>
                                    <li><i class="fa fa-angle-right text-primary me-2"></i><?= htmlspecialchars($item) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="bg-light rounded p-5 mb-4 wow slideInUp" data-wow-delay="0.1s">
                            <h4 class="mb-4">Service Info</h4>
                            <p><i class="fa fa-angle-right text-primary me-2"></i>Posted On: <?= date("d M, Y", strtotime($service['created_at'])) ?></p>
                            <p><i class="fa fa-angle-right text-primary me-2"></i>Location: <?= htmlspecialchars($service['location']) ?></p>
                        </div>
                        <div class="bg-light rounded p-5 wow slideInUp" data-wow-delay="0.1s">
                            <h4 class="mb-4">About Provider</h4>
                            <p class="m-0"><?= nl2br(htmlspecialchars($service['about_provider'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Service Detail End -->

<?php include 'includes/footer.php'; ?>