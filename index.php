<?php
include 'config.php';

// Fetch distinct categories
$category_query = "SELECT DISTINCT category FROM services";
$category_result = mysqli_query($conn, $category_query);

// Fetch distinct locations
$location_query = "SELECT DISTINCT location FROM services";
$location_result = mysqli_query($conn, $location_query);

// Handle service filtering
$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$location = isset($_GET['location']) ? mysqli_real_escape_string($conn, $_GET['location']) : '';

// Build dynamic query
$sql = "SELECT * FROM services WHERE 1";

if (!empty($keyword)) {
    $sql .= " AND (title LIKE '%$keyword%' OR description LIKE '%$keyword%')";
}
if (!empty($category)) {
    $sql .= " AND category = '$category'";
}
if (!empty($location)) {
    $sql .= " AND location = '$location'";
}

$sql .= " ORDER BY created_at DESC";
$services_result = mysqli_query($conn, $sql);
?>

<?php $current_page = 'index'; ?>
<?php include 'includes/header.php'; ?>

        <!-- Carousel Start -->
        <div class="container-fluid p-0">
            <div class="owl-carousel header-carousel position-relative">
                <div class="owl-carousel-item position-relative">
                    <img class="img-fluid" src="img/carousel-1.jpg" alt="">
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: rgba(43, 57, 64, .5);">
                        <div class="container">
                            <div class="row justify-content-start">
                                <div class="col-10 col-lg-8">
                                    <h1 class="display-3 text-white animated slideInDown mb-4">Find Trusted Local Professionals Near You</h1>
                                    <p class="fs-5 fw-medium text-white mb-4 pb-2">Browse electricians, plumbers, cleaners, and more. We help you connect with verified professionals quickly and reliably.”</p>
                                    <a href="#searchstart" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Search A Job</a>
                                    <a href="services.php" class="btn btn-secondary py-md-3 px-md-5 animated slideInRight">Find A Talent</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="owl-carousel-item position-relative">
                    <img class="img-fluid" src="img/carousel-2.jpg" alt="">
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: rgba(43, 57, 64, .5);">
                        <div class="container">
                            <div class="row justify-content-start">
                                <div class="col-10 col-lg-8">
                                    <h1 class="display-3 text-white animated slideInDown mb-4">Book Skilled Experts for Your Everyday Needs</h1>
                                    <p class="fs-5 fw-medium text-white mb-4 pb-2">Browse electricians, plumbers, cleaners, and more. We help you connect with verified professionals quickly and reliably.”</p>
                                    <a href="#searchstart" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Search Services</a>
                                    <a href="signup.php" class="btn btn-secondary py-md-3 px-md-5 animated slideInRight">Become a Provider</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Carousel End -->

        <!-- Search Start -->
        <div class="container-fluid bg-primary mb-5 wow fadeIn" id="searchstart" data-wow-delay="0.1s" style="padding: 35px;">
            <div class="container">
            <form action="search-results.php" method="GET">
                    <div class="row g-2">
                        <div class="col-md-10">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" name="keyword" class="form-control border-0" placeholder="Search by skill (e.g., door fiz, whitewashing, etc.)" value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>" />
                                </div>
                                <div class="col-md-4">
                                    <select name="category" class="form-select border-0">
                                        <option value="">All Categories</option>
                                        <?php while ($row = mysqli_fetch_assoc($category_result)) : ?>
                                            <option value="<?= htmlspecialchars($row['category']) ?>" <?= (isset($_GET['category']) && $_GET['category'] == $row['category']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($row['category']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select name="location" class="form-select border-0">
                                        <option value="">All Locations</option>
                                        <?php while ($row = mysqli_fetch_assoc($location_result)) : ?>
                                            <option value="<?= htmlspecialchars($row['location']) ?>" <?= (isset($_GET['location']) && $_GET['location'] == $row['location']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($row['location']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-dark border-0 w-100">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Search End -->


        <!-- Category Start -->
        <?php
        require_once 'config.php';

        // Fetch categories and count services per category
        $category_sql = "SELECT category, COUNT(*) as count FROM services GROUP BY category";
        $category_result = $conn->query($category_sql);
        ?>

        <div class="container-xxl py-5">
            <div class="container">
                <h1 class="text-center mb-5 wow fadeInUp" data-wow-delay="0.1s">Explore By Category</h1>
                <div class="row g-4">
                    <?php if ($category_result && $category_result->num_rows > 0): ?>
                        <?php
                        $icons = [
                            "Electrician" => "fa-bolt",
                            "Painter" => "fa-paint-roller",
                            "Plumber" => "fa-wrench",
                            "Carpenter" => "fa-hammer",
                            "Cleaner" => "fa-broom"
                            // Add more icons as needed
                        ];
                        $i = 0;
                        while ($row = $category_result->fetch_assoc()):
                            $delay = 0.1 + ($i % 4) * 0.2;
                            $icon = isset($icons[$row['category']]) ? $icons[$row['category']] : 'fa-tools';
                            ?>
                            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="<?= $delay ?>s">
                                <a class="cat-item rounded p-4" href="index.php?category=<?= urlencode($row['category']) ?>">
                                    <i class="fa fa-3x <?= $icon ?> text-primary mb-4"></i>
                                    <h6 class="mb-3"><?= htmlspecialchars($row['category']) ?></h6>
                                    <p class="mb-0"><?= $row['count'] ?> Service<?= $row['count'] > 1 ? 's' : '' ?></p>
                                </a>
                            </div>
                            <?php $i++; endwhile; ?>
                    <?php else: ?>
                        <p class="text-center">No categories available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Category End -->

        <!-- About Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                        <div class="row g-0 about-bg rounded overflow-hidden">
                            <div class="col-6 text-start">
                                <img class="img-fluid w-100" src="img/about-1.jpg">
                            </div>
                            <div class="col-6 text-start">
                                <img class="img-fluid" src="img/about-2.jpg" style="width: 85%; margin-top: 15%;">
                            </div>
                            <div class="col-6 text-end">
                                <img class="img-fluid" src="img/about-3.jpg" style="width: 85%;">
                            </div>
                            <div class="col-6 text-end">
                                <img class="img-fluid w-100" src="img/about-4.jpg">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                        <h1 class="mb-4">Connecting You With Trusted Local Experts</h1>
                        <p class="mb-4">Our platform helps you book skilled local professionals for home repairs, cleaning, maintenance, and more.</p>
                        <p><i class="fa fa-check text-primary me-3"></i>Verified Local Professionals</p>
                        <p><i class="fa fa-check text-primary me-3"></i>Easy Booking & Communication</p>
                        <p><i class="fa fa-check text-primary me-3"></i>Trusted by Hundreds of Users</p>
                        <a class="btn btn-primary py-3 px-5 mt-3" href="about.php">Read More</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- About End -->

<?php include 'includes/footer.php'; ?>