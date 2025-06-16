<?php
$conn = new mysqli("localhost", "root", "", "vv");
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
if (!$product) {
    echo "<h2>Product not found.</h2>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> | Velvet Vogue</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js" integrity="sha384-rOA1PnstxnOBLzCLMcre8ybwbTmemjzdNlILg8O7z1lUkLXozs4DHonlDtnE7fpc" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="assets/css/product.css">
</head>
<body>

    <!-- navbar -->   
    <nav class="navbar navbar-expand-lg navbar-light bg-light py-3 fixed-top">
        <div class="container">
            <img src="assets/images/logo.png" alt="Logo" class="logo">

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse nav-buttons" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="men.php">Men</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="women.php">Women</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about_us.html">About Us</a>
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


    <!-- Product Details -->
    <div class="container my-5">
        <div class="row justify-content-center align-items-start g-5">
            <div class="col-md-6">
                <div class="card shadow-sm p-4 border-0" style="border-radius:22px;">
                    <div class="text-center mb-3">
                        <img id="mainProductImg"
                            src="<?php echo htmlspecialchars($product['photo']); ?>"
                            class="img-fluid mb-3 main-product-img"
                            alt="<?php echo htmlspecialchars($product['name']); ?>"
                            style="max-height:340px; border-radius:18px; box-shadow:0 4px 24px rgba(32,4,238,0.08); object-fit:cover;">
                    </div>
                    <?php
                    $sub_imgs = json_decode($product['sub_images'] ?? '[]', true);
                    if ($sub_imgs && is_array($sub_imgs)):
                    ?>
                    <div class="mb-3 d-flex flex-wrap gap-2 justify-content-center">
                        <?php foreach ($sub_imgs as $img): ?>
                            <img src="<?php echo htmlspecialchars($img); ?>"
                                alt="Sub Image"
                                class="sub-img-thumb border"
                                style="width:65px;height:65px;object-fit:cover;border-radius:10px;cursor:pointer;transition:box-shadow 0.2s;"
                                onmouseover="this.style.boxShadow='0 0 0 3px #b18cff';"
                                onmouseout="this.style.boxShadow='none';">
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm p-4 border-0" style="border-radius:22px; position:relative;">
                    
                    <h2 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h2>
                    <h4 class="product-price">$<?php echo htmlspecialchars($product['price']); ?></h4>
                    <form method="post" action="shopping_cart.php">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                        <!-- Size Selection -->
                        <div class="mb-4">
                            <label class="form-label d-block mb-2 fw-semibold">Size:</label>
                            <div class="btn-group" role="group" aria-label="Size">
                                <?php
                                $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
                                foreach ($sizes as $i => $size):
                                ?>
                                    <input type="radio" class="btn-check" name="size" id="size<?php echo $size; ?>" value="<?php echo $size; ?>" autocomplete="off" <?php echo $i === 0 ? 'required' : ''; ?>>
                                    <label class="btn btn-outline-dark px-4" for="size<?php echo $size; ?>"><?php echo $size; ?></label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Color Selection -->
                        <div class="mb-4">
                            <label class="form-label d-block mb-2 fw-semibold">Color:</label>
                            <div class="d-flex gap-3">
                                <input type="radio" class="btn-check" name="color" id="colorRed" value="Red" autocomplete="off" required>
                                <label class="color-swatch" for="colorRed" style="background: #e53935;"></label>

                                <input type="radio" class="btn-check" name="color" id="colorBlack" value="Black" autocomplete="off">
                                <label class="color-swatch" for="colorBlack" style="background: #000;"></label>

                                <input type="radio" class="btn-check" name="color" id="colorBeige" value="Beige" autocomplete="off">
                                <label class="color-swatch" for="colorBeige" style="background:rgb(222, 217, 207);"></label>

                                <input type="radio" class="btn-check" name="color" id="colorBlueGray" value="BlueGray" autocomplete="off">
                                <label class="color-swatch" for="colorBlueGray" style="background:rgb(17, 234, 242);"></label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="qty" class="form-label fw-semibold">Quantity</label>
                            <input type="number" name="qty" id="qty" class="form-control qty-input" value="1" min="1" style="max-width:120px;" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold" style="font-size:1.1rem;letter-spacing:1px;border-radius:12px;">
                            <i class="fas fa-shopping-cart me-2"></i> ADD TO CART
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
    

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const mainImg = document.getElementById('mainProductImg');
        document.querySelectorAll('.sub-img-thumb').forEach(function(thumb) {
            thumb.addEventListener('click', function() {
                mainImg.src = this.src;
            });
        });
    });
    </script>
</body>
</html>
