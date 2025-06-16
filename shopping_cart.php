<?php
session_start();
$conn = new mysqli("localhost", "root", "", "vv");

// Add to cart 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $id = intval($_POST['product_id']);
    $size = $_POST['size'] ?? '';
    $color = $_POST['color'] ?? '';
    $qty = max(1, intval($_POST['qty'] ?? 1));
    $key = $id . '_' . $size . '_' . $color;

    // Add or update cart
    $_SESSION['cart'][$key] = [
        'id' => $id,
        'size' => $size,
        'color' => $color,
        'qty' => $qty
    ];
    header("Location: shopping_cart.php");
    exit;
}

// Remove from cart
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    header("Location: shopping_cart.php");
    exit;
}

// Update quantity
if (isset($_POST['update_qty'], $_POST['cart_key'])) {
    $cart_key = $_POST['cart_key'];
    $qty = max(1, intval($_POST['qty']));
    if (isset($_SESSION['cart'][$cart_key])) {
        $_SESSION['cart'][$cart_key]['qty'] = $qty;
    }
    header("Location: shopping_cart.php");
    exit;
}


$cart_items = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $key => $item) {
        $product = $conn->query("SELECT * FROM products WHERE id=" . intval($item['id']))->fetch_assoc();
        if ($product) {
            $product['size'] = $item['size'];
            $product['color'] = $item['color'];
            $product['qty'] = $item['qty'];
            $product['cart_key'] = $key;
            $cart_items[] = $product;
            $total += $product['price'] * $item['qty'];
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    if (!isset($_SESSION['customer_id'])) {
        
        header("Location: home.php"); 
        exit;
    }

    
    $customer_id = intval($_SESSION['customer_id']); 
    $order_total = $total;
    $order_date = date('Y-m-d H:i:s');
    $conn->query("INSERT INTO orders (customer_id, total, order_date) VALUES ($customer_id, $order_total, '$order_date')");
    $order_id = $conn->insert_id;

    
    foreach ($cart_items as $item) {
        $pid = intval($item['id']);
        $size = $conn->real_escape_string($item['size']);
        $color = $conn->real_escape_string($item['color']);
        $qty = intval($item['qty']);
        $price = floatval($item['price']);
        $conn->query("INSERT INTO order_items (order_id, product_id, size, color, qty, price) VALUES ($order_id, $pid, '$size', '$color', $qty, $price)");
    }

    
    $_SESSION['cart'] = [];
    $_SESSION['order_success'] = true;
    header("Location: shopping_cart.php");
    exit;
}

$order_success = false;
if (!empty($_SESSION['order_success'])) {
    $order_success = true;
    unset($_SESSION['order_success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart | Velvet Vogue</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"></script>
    <link rel="stylesheet" href="assets/css/shopping_cart.css">
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
                <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="men.php">Men</a></li>
                <li class="nav-item"><a class="nav-link" href="women.php">Women</a></li>
                <li class="nav-item"><a class="nav-link" href="about_us.html">About Us</a></li>
            </ul>
        </div>
        <div class="icon">
            <a href="shopping_cart.php"><i class="fas fa-shopping-cart icon1"></i></a>
            <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal">
                <i class="fas fa-user icon1"></i>
            </a>
        </div>
    </div>
</nav>

<!--cart section-->
<div class="container cart-section" style="margin-top:120px;">
    <h2 class="mb-4 fw-bold"><i class="fas fa-shopping-cart me-2"></i>Your Shopping Cart</h2>
    <?php if (empty($cart_items)): ?>
        <div class="cart-empty">
            <i class="fas fa-shopping-cart fa-3x mb-3 text-muted"></i>
            <div class="alert alert-info d-inline-block">Your cart is empty.</div>
        </div>
    <?php else: ?>
    <div class="card cart-card border-0">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Size</th>
                            <th>Color</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($item['photo']); ?>" alt="" class="cart-img me-2">
                                <span class="fw-bold"><?php echo htmlspecialchars($item['name']); ?></span>
                            </td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($item['size']); ?></span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($item['color']); ?></span>
                            </td>
                            <td>
                                <form method="post" action="shopping_cart.php" class="d-flex align-items-center">
                                    <input type="hidden" name="cart_key" value="<?php echo htmlspecialchars($item['cart_key']); ?>">
                                    <input type="number" name="qty" value="<?php echo $item['qty']; ?>" min="1" class="form-control form-control-sm me-2" style="width:70px;">
                                    <button type="submit" name="update_qty" class="btn btn-sm btn-outline-primary" title="Update"><i class="fas fa-sync"></i></button>
                                </form>
                            </td>
                            <td class="fw-bold">$<?php echo number_format($item['price'] * $item['qty'], 2); ?></td>
                            <td>
                                <a href="shopping_cart.php?remove=<?php echo urlencode($item['cart_key']); ?>" class="btn btn-sm btn-outline-danger" title="Remove"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-4">
                <h4>Total: <span class="text-primary">$<?php echo number_format($total, 2); ?></span></h4>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <?php if (isset($_SESSION['customer_id'])): ?>
                   
                    <button type="button" class="btn btn-success btn-lg px-4" data-bs-toggle="modal" data-bs-target="#checkoutModal">
                        <i class="fas fa-credit-card me-2"></i>Checkout
                    </button>
                <?php else: ?>
                    
                    <button type="button" class="btn btn-primary btn-lg px-4 me-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="fas fa-user me-2"></i>Login
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-lg px-4" data-bs-toggle="modal" data-bs-target="#registerModal">
                        <i class="fas fa-user-plus me-2"></i>Sign Up
                    </button>
                    <div class="text-danger mt-2 fw-bold">Please sign up to place an order.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius: 22px; box-shadow: 0 8px 32px rgba(37,99,235,0.10);">
      <div class="modal-header" style="background: #f5f8ff; border-top-left-radius: 22px; border-top-right-radius: 22px;">
        <h5 class="modal-title fw-bold" id="checkoutModalLabel" style="color:#2563eb;">
          <i class="fas fa-credit-card me-2"></i>Checkout Summary
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body px-4 py-4">
        <?php if (!empty($order_success)): ?>
          <div class="alert alert-success text-center fw-bold fs-5 border-0 py-4" id="orderSuccessMsg" style="background:linear-gradient(90deg,#e0f7fa 0,#f5f8ff 100%);border-radius:18px;">
            <span style="font-size:2.5rem;display:inline-block;vertical-align:middle;">
                <i class="fas fa-check-circle" style="color:#22c55e;animation:pop 0.6s;"></i>
            </span>
            <span class="d-block mt-2" style="font-size:1.3rem;color:#2563eb;">
                Your order placed successfully!
            </span>
            <div style="font-size:1rem;color:#333;opacity:0.7;">Thank you for shopping with Velvet Vogue.</div>
          </div>
        <?php else: ?>
        <div class="row">
          <div class="col-md-8">
            <table class="table align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Product</th>
                  <th>Size</th>
                  <th>Color</th>
                  <th>Qty</th>
                  <th>Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($cart_items as $item): ?>
                <tr>
                  <td>
                    <img src="<?php echo htmlspecialchars($item['photo']); ?>" alt="" style="width:40px;height:40px;object-fit:cover;border-radius:8px;margin-right:8px;">
                    <span class="fw-semibold"><?php echo htmlspecialchars($item['name']); ?></span>
                  </td>
                  <td><span class="badge bg-primary bg-opacity-10 text-primary"><?php echo htmlspecialchars($item['size']); ?></span></td>
                  <td>
                    <span class="badge" style="background:<?php echo htmlspecialchars($item['color']) === 'Black' ? '#222' : (htmlspecialchars($item['color']) === 'Red' ? '#e53935' : '#e0e7ef'); ?>;color:#fff;">
                      <?php echo htmlspecialchars($item['color']); ?>
                    </span>
                  </td>
                  <td><?php echo $item['qty']; ?></td>
                  <td class="fw-bold">$<?php echo number_format($item['price'] * $item['qty'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="col-md-4">
            <div class="checkout-summary-card p-4 rounded-4 shadow-sm" style="background:#f5f8ff;">
              <h6 class="fw-bold mb-3" style="color:#2563eb;">Order Summary</h6>
              <div class="d-flex justify-content-between mb-2">
                <span>Items:</span>
                <span><?php echo count($cart_items); ?></span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span>Subtotal:</span>
                <span>$<?php echo number_format($total, 2); ?></span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span>Shipping:</span>
                <span class="text-success">FREE</span>
              </div>
              <hr>
              <div class="d-flex justify-content-between fw-bold fs-5">
                <span>Total:</span>
                <span class="text-primary">$<?php echo number_format($total, 2); ?></span>
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>
      <div class="modal-footer" style="background: #f5f8ff; border-bottom-left-radius: 22px; border-bottom-right-radius: 22px;">
        <?php if (empty($order_success)): ?>
        <form method="post" action="shopping_cart.php" class="me-auto">
          <button type="submit" name="place_order" class="btn btn-success px-5 py-2 fw-bold" style="border-radius:10px;font-size:1.1rem;">
            <i class="fas fa-check-circle me-2"></i>Confirm Order
          </button>
        </form>
        <?php endif; ?>
        <button type="button" class="btn btn-outline-secondary px-4" style="border-radius:10px;" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- footer -->
<footer class="footer mt-5">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<?php if (!empty($order_success)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var checkoutModal = new bootstrap.Modal(document.getElementById('checkoutModal'));
    checkoutModal.show();
});
</script>
<?php endif; ?>
</body>
</html>