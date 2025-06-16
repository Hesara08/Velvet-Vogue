<?php
$conn = new mysqli("localhost", "root", "", "vv");
$category = 'women';
$where = "category='$category'";
if (isset($_GET['min_price']) && is_numeric($_GET['min_price'])) {
    $min = (float)$_GET['min_price'];
    $where .= " AND price >= $min";
}
if (isset($_GET['max_price']) && is_numeric($_GET['max_price'])) {
    $max = (float)$_GET['max_price'];
    $where .= " AND price <= $max";
}
$products = $conn->query("SELECT * FROM products WHERE $where");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Women's Clothes</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js" integrity="sha384-rOA1PnstxnOBLzCLMcre8ybwbTmemjzdNlILg8O7z1lUkLXozs4DHonlDtnE7fpc" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="assets/css/women.css">
    <link rel="stylesheet" href="assets/css/home.css">

</head>
<body>
    
  <!-- navbar -->   
    <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
    <nav class="navbar navbar-expand-lg navbar-light bg-light py-3 fixed-top">
        <div class="container">
            <img src="assets/images/logo.png" alt="Logo" class="logo">

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse nav-buttons" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?php if($current_page == 'home.php') echo 'active'; ?>" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if($current_page == 'men.php') echo 'active'; ?>" href="men.php">Men</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if($current_page == 'women.php') echo 'active'; ?>" href="women.php">Women</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if($current_page == 'about_us.php' || $current_page == 'about_us.html') echo 'active'; ?>" href="about_us.php">About Us</a>
                    </li>
                </ul>
            </div>

            <div class="icon">
                <a href="shopping_cart.php">
                    <i class="fas fa-shopping-cart icon1"></i>
                </a>

                <a href="register.php">
                    <i class="fas fa-user icon1"></i>
                </a>
            </div>

        </div>
    </nav>

     <!--women home img-->
    <img src="assets/imgs/women.jpg" alt="home_img" class="home_img1">

    <section id="home">
        <div class="container">
            <h5>Welcome to Our Store</h5>
            <h1><span>New season, </span>New style.</h1>
            <p>Your one-stop shop for all your fashion needs.</p>
            <button>Shop Now</button>
        </div>
    </section>

    <!-- Filter -->
<div class="container d-flex justify-content-center my-4">
    <div class="card shadow" style="border-radius: 18px; min-width: 350px; max-width: 500px; width: 100%;">
        <div class="card-body">
            <form method="get" class="d-flex flex-column flex-md-row align-items-center gap-3">
                <div class="d-flex flex-column flex-md-row align-items-center gap-2 flex-grow-1">
                    <label for="min_price" class="form-label mb-0 fw-semibold" style="color:#b18cff;">Min Price</label>
                    <input type="number" name="min_price" id="min_price" class="form-control" style="width:120px;" min="0" value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">

                    <span class="fw-bold" style="color:#888;">-</span>

                    <label for="max_price" class="form-label mb-0 fw-semibold" style="color:#b18cff;">Max Price</label>
                    <input type="number" name="max_price" id="max_price" class="form-control" style="width:120px;" min="0" value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
                </div>
                <button type="submit" class="btn px-4 py-2 fw-bold" style="background:#b18cff;color:#fff;border-radius:8px;">Search</button>
            </form>
        </div>
    </div>
</div>

    <!-- Women Display -->
       <section id="women-products" class="container my-5">
        <h2 class="text-center mb-4">Women's Collection</h2>
        <div class="row justify-content-center">
            <?php while($row = $products->fetch_assoc()): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if($row['photo']): ?>
                            <img src="<?php echo htmlspecialchars($row['photo']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <?php else: ?>
                            <img src="assets/imgs/no-image.png" class="card-img-top" alt="No Image">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <p class="card-text mb-2"><strong>Price:</strong> $<?php echo htmlspecialchars($row['price']); ?></p>
                            <a href="product.php?id=<?php echo $row['id']; ?>" class="btn btn-primary mt-auto">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>



    <!--footer-->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-col">
                <img src="assets/images/logo.png" alt="Velvet Vogue Logo" class="footer-logo">
                <p>Velvet Vogue &copy; 2025<br>Your style destination.</p>
            </div>

            <div class="footer-col">
                <h5>Quick Links</h5>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="men.php">Men</a></li>
                    <li><a href="women.php">Women</a></li>
                    <li><a href="about_us.php">About Us</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5>Contact</h5>
                <ul>
                    <li><i class="fas fa-envelope"></i> velvetvogue@gmail.com</li>
                    <li><i class="fas fa-phone"></i> +94 775678901</li>
                    <li><i class="fas fa-map-marker-alt"></i> 123/B, Gampaha, Sri Lanka</li>
                </ul>
            </div>

            <div class="footer-col social">
                <h5>Follow Us</h5>
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>



</body>
</html>