<?php
session_start();
$conn = new mysqli("localhost", "root", "", "vv");
$products = $conn->query("SELECT * FROM products WHERE category='new'");

//  login modal
$show_login_modal = !isset($_SESSION['customer_id']);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $conn->real_escape_string($_POST['loginEmail']);
    $password = $_POST['loginPassword'];

    
    $admin_result = $conn->query("SELECT id, password FROM admins WHERE email='$email'");
    if ($admin_result && $admin = $admin_result->fetch_assoc()) {
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            header("Location: admin.php");
            exit;
        }
    }

   
    $result = $conn->query("SELECT id, password FROM customers WHERE email='$email'");
    if ($result && $row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['customer_id'] = $row['id'];
            $show_login_modal = false;
           
            header("Location: new_profile.php");
            exit;
        } else {
            $login_error = "Invalid email or password!";
            $show_login_modal = true;
        }
    } else {
        $login_error = "Invalid email or password!";
        $show_login_modal = true;
    }
}

//  registration 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = $conn->real_escape_string($_POST['registerName']);
    $email = $conn->real_escape_string($_POST['registerEmail']);
    $phone = $conn->real_escape_string($_POST['registerPhone']);
    $address = $conn->real_escape_string($_POST['registerAddress']);
    $password = password_hash($_POST['registerPassword'], PASSWORD_DEFAULT);

   
    $exists = $conn->query("SELECT id FROM customers WHERE email='$email'")->num_rows;
    if ($exists) {
        $register_error = "Email already registered!";
    } else {
        $conn->query("INSERT INTO customers (name, email, phone, address, password) VALUES ('$name', '$email', '$phone', '$address', '$password')");
        $_SESSION['customer_id'] = $conn->insert_id;
        header("Location: new_profile.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Velvet Vogue</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js" integrity="sha384-rOA1PnstxnOBLzCLMcre8ybwbTmemjzdNlILg8O7z1lUkLXozs4DHonlDtnE7fpc" crossorigin="anonymous"></script>

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

            <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal">
                <i class="fas fa-user icon1"></i>
            </a>
        </div>

    </div>
</nav>

    
     <!--home-->
    <img src="assets/imgs/home.jpeg" alt="home_img" class="home_img1">

    <section id="home">
        <div class="container">
            <h5>Welcome to Our Store</h5>
            <h1><span>New season, </span>New style.</h1>
            <p>Your one-stop shop for all your fashion needs.</p>
            <button>Shop Now</button>
        </div>
    </section>


    <!-- New Arrivals -->
    <section id="new-arrivals" class="container my-5">
        <h2 class="text-center mb-4">New Arrivals</h2>
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


    <!--brands-->
    <section id="brands" class="container">
        <div class="row">
            <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/imgs/brand1.png">
            <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/imgs/brand2.png">
            <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/imgs/brand3.png">
            <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/imgs/brand4.png">
            <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/imgs/brand5.png">
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


    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content p-0" style="border-radius:28px; overflow:hidden;">
        <div class="row g-0">
            <div class="col-md-6 bg-white p-5 d-flex flex-column justify-content-center">
            <h2 class="fw-bold mb-1" style="color:#181818;">Log In</h2>
            <p class="mb-4" style="color:#888;">Welcome back! Please enter your details</p>
            <form method="post" action="">
                <div class="mb-3">
                    <label for="loginEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="loginEmail" name="loginEmail" required>
                </div>
                <div class="mb-3">
                    <label for="loginPassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="loginPassword" name="loginPassword" required>
                </div>
                <?php if (isset($login_error)): ?>
                    <div class="alert alert-danger py-2"><?php echo $login_error; ?></div>
                <?php endif; ?>
                <div class="mb-3">
                <a href="#" class="text-decoration-none" style="color:#b18cff;font-size:0.97rem;">forgot password ?</a>
                </div>
                <button type="submit" name="login" class="btn w-100 mb-3" style="background:#b18cff;color:#fff;font-weight:600;font-size:1.1rem;border-radius:10px;">Log in</button>
                <div class="divider text-center my-3" style="color:#aaa;position:relative;">
                <span style="background:#fff;position:relative;top:-0.8em;padding:0 1em;">Or Continue With</span>
                </div>
                <div class="d-flex gap-2 mb-3">
                <button type="button" class="btn btn-light flex-fill border" style="border-radius:10px;">
                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/google/google-original.svg" width="22" class="me-2">Google
                </button>
                <button type="button" class="btn btn-light flex-fill border" style="border-radius:10px;">
                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/facebook/facebook-original.svg" width="22" class="me-2">Facebook
                </button>
                </div>
                <div class="text-center mt-2" style="color:#888;">
                Don't have account? <a href="#" style="color:#b18cff;font-weight:600;text-decoration:none;" data-bs-toggle="modal" data-bs-target="#registerModal">Sign up</a>
                </div>
            </form>
            </div>
            <div class="col-md-6 d-none d-md-block" style="background:linear-gradient(120deg,#b18cff 0%,#ffb6ea 100%);">
            <img src="assets/imgs/login.png" alt="Login Visual" style="width:100%;height:100%;object-fit:cover;">
            </div>
        </div>
        </div>
    </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content p-0" style="border-radius:28px; overflow:hidden;">
          <div class="row g-0">
            <div class="col-md-6 bg-white p-5 d-flex flex-column justify-content-center">
              
              <h2 class="fw-bold mb-1" style="color:#181818;">Sign Up</h2>
              <p class="mb-4" style="color:#888;">Create your account to get started</p>
              <form method="post" action="">
                <div class="mb-3">
                  <label for="registerName" class="form-label">Full Name</label>
                  <input type="text" class="form-control" id="registerName" name="registerName" required>
                </div>
                <div class="mb-3">
                  <label for="registerEmail" class="form-label">Email</label>
                  <input type="email" class="form-control" id="registerEmail" name="registerEmail" required>
                </div>
                <div class="mb-3">
                  <label for="registerPhone" class="form-label">Phone Number</label>
                  <input type="tel" class="form-control" id="registerPhone" name="registerPhone" required>
                </div>
                <div class="mb-3">
                  <label for="registerAddress" class="form-label">Address</label>
                  <textarea class="form-control" id="registerAddress" name="registerAddress" rows="2" required></textarea>
                </div>
                <div class="mb-3">
                  <label for="registerPassword" class="form-label">Password</label>
                  <input type="password" class="form-control" id="registerPassword" name="registerPassword" required>
                </div>
                <div class="mb-3">
                  <label for="registerConfirmPassword" class="form-label">Confirm Password</label>
                  <input type="password" class="form-control" id="registerConfirmPassword" name="registerConfirmPassword" required>
                </div>
                <?php if (isset($register_error)): ?>
                  <div class="alert alert-danger"><?php echo $register_error; ?></div>
                <?php elseif (isset($register_success)): ?>
                  <div class="alert alert-success">Registration successful!</div>
                <?php endif; ?>
                <button type="submit" name="register" class="btn w-100 mb-3" style="background:#b18cff;color:#fff;font-weight:600;font-size:1.1rem;border-radius:10px;">Sign up</button>
                <div class="divider text-center my-3" style="color:#aaa;position:relative;">
                  <span style="background:#fff;position:relative;top:-0.8em;padding:0 1em;">Or Continue With</span>
                </div>
                <div class="d-flex gap-2 mb-3">
                  <button type="button" class="btn btn-light flex-fill border" style="border-radius:10px;">
                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/google/google-original.svg" width="22" class="me-2">Google
                  </button>
                  <button type="button" class="btn btn-light flex-fill border" style="border-radius:10px;">
                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/facebook/facebook-original.svg" width="22" class="me-2">Facebook
                  </button>
                </div>
                <div class="text-center mt-2" style="color:#888;">
                  Already have an account? 
                  <a href="#" style="color:#b18cff;font-weight:600;text-decoration:none;" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Log in</a>
                </div>
              </form>
            </div>
            <div class="col-md-6 d-none d-md-block" style="background:linear-gradient(120deg,#b18cff 0%,#ffb6ea 100%);">
              <img src="assets/imgs/reg.jpeg" alt="Register Visual" style="width:100%;height:100%;object-fit:cover;">
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php if ($show_login_modal): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    loginModal.show();
});
</script>
<?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
        var signUpLinks = document.querySelectorAll('[data-bs-target="#registerModal"]');
        signUpLinks.forEach(function(link) {
            link.addEventListener('click', function () {
                var loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
                if (loginModal) {
                    loginModal.hide();
                }
            });
        });
    });
    </script>

</body>
</html>