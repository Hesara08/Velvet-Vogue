<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js" integrity="sha384-rOA1PnstxnOBLzCLMcre8ybwbTmemjzdNlILg8O7z1lUkLXozs4DHonlDtnE7fpc" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="assets/css/about_us.css">
    
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

                <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal">
                    <i class="fas fa-user icon1"></i>
                </a>
            </div>

        </div>
    </nav>

    <!-- about -->
        <section class="about-section">
        <div class="about-container">
            <div class="about-image">
                <img src="assets/imgs/team.png" alt="Our Team">
            </div>
            <div class="about-content">
                <h1>About Velvet Vogue</h1>
                <p>
                    Velvet Vogue is your destination for the latest trends in fashion. Founded in 2025, our mission is to empower individuals to express themselves through style. We curate collections for men and women that blend timeless elegance with modern flair.
                </p>
                <p>
                    Our team is passionate about quality, sustainability, and customer satisfaction. Whether you're shopping for everyday essentials or a statement piece, Velvet Vogue is here to help you look and feel your best.
                </p>
                <div class="about-highlights">
                    <div>
                        <h3>5000+</h3>
                        <span>Happy Customers</span>
                    </div>
                    <div>
                        <h3>100+</h3>
                        <span>Brands</span>
                    </div>
                    <div>
                        <h3>24/7</h3>
                        <span>Support</span>
                    </div>
                </div>
            </div>
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