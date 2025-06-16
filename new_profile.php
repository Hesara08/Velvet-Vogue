<?php
session_start();
$conn = new mysqli("localhost", "root", "", "vv");

if (!isset($_SESSION['customer_id'])) {
    header("Location: home.php");
    exit;
}

$id = $_SESSION['customer_id'];
$result = $conn->query("SELECT * FROM customers WHERE id=$id");
$customer = $result->fetch_assoc();

//  profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);
    $birthday = $conn->real_escape_string($_POST['birthday']);
    $id = $customer['id'];

    $conn->query("UPDATE customers SET name='$name', email='$email', phone='$phone', address='$address', birthday='$birthday' WHERE id=$id");
    
    $customer = $conn->query("SELECT * FROM customers WHERE id=$id")->fetch_assoc();
    $update_success = true;
}

//  password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    //  password  DB
    $user = $conn->query("SELECT password FROM customers WHERE id={$customer['id']}")->fetch_assoc();
    if (!password_verify($current, $user['password'])) {
        $password_error = "Current password is incorrect!";
    } elseif ($new !== $confirm) {
        $password_error = "New passwords do not match!";
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $conn->query("UPDATE customers SET password='$hash' WHERE id={$customer['id']}");
        $password_success = true;
    }
}


$active_menu = 'profile'; 

if (isset($_GET['section'])) {
    $active_menu = $_GET['section'];
} elseif (isset($_POST['change_password'])) {
    $active_menu = 'settings';
}


$customer_orders = [];
$order_items_map = [];
if ($active_menu == 'orders') {
    $orders_res = $conn->query("SELECT * FROM orders WHERE customer_id = $id ORDER BY order_date DESC");
    while ($order = $orders_res->fetch_assoc()) {
        $customer_orders[] = $order;
    }
    
    if (!empty($customer_orders)) {
        $order_ids = array_column($customer_orders, 'id');
        $ids_str = implode(',', $order_ids);
        $items_res = $conn->query("SELECT * FROM order_items WHERE order_id IN ($ids_str)");
        while ($item = $items_res->fetch_assoc()) {
            $order_items_map[$item['order_id']][] = $item;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Velvet Vogue</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js" integrity="sha384-rOA1PnstxnOBLzCLMcre8ybwbTmemjzdNlILg8O7z1lUkLXozs4DHonlDtnE7fpc" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="assets/css/new_profile.css"> 
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

                <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal">
                    <i class="fas fa-user icon1"></i>
                </a>
            </div>

        </div>
    </nav>

    <!-- Profile Section -->
      <div class="container py-5" style="margin-top:100px;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="profile-wrapper d-flex bg-white shadow-lg rounded-4 overflow-hidden">
                    
                    <div class="profile-sidebar p-3 d-flex flex-column align-items-center" style="min-width:220px; background:#f5f8ff;">
                        <img src="assets/imgs/profile.png" alt="Profile" class="rounded-circle mb-3" style="width:70px;height:70px;object-fit:cover;border:3px solid #2563eb;">
                        <div class="fw-bold fs-5 mb-1"><?php echo htmlspecialchars($customer['name']); ?></div>
                        <div class="text-muted mb-4" style="font-size:0.97rem;"><?php echo htmlspecialchars($customer['email']); ?></div>
                        <ul class="nav flex-column w-100">
                            <li class="nav-item mb-0">
                                <a class="nav-link <?php echo $active_menu == 'profile' ? 'active' : ''; ?>" href="new_profile.php?section=profile" style="<?php echo $active_menu == 'profile' ? 'background:#2563eb;color:#fff;font-weight:600;border-radius:8px;' : 'color:#2563eb;font-weight:500;'; ?>">
                                    <i class="fas fa-user me-2"></i>Profile
                                </a>
                            </li>
                            <li class="nav-item mb-0">
                                <a class="nav-link <?php echo $active_menu == 'orders' ? 'active' : ''; ?>" href="new_profile.php?section=orders" style="color:#2563eb;font-weight:500;">
                                    <i class="fas fa-box me-2"></i>Orders
                                </a>
                            </li>
                            <li class="nav-item mb-0">
                                <a class="nav-link <?php echo $active_menu == 'settings' ? 'active' : ''; ?>" href="new_profile.php?section=settings" style="color:#2563eb;font-weight:500;">
                                    <i class="fas fa-cog me-2"></i>Settings
                                </a>
                            </li>
                            <li class="nav-item mt-0">
                                <a class="nav-link" href="logout.php" style="color:#ef4444;font-weight:600;">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- Main Content -->
                    <?php
                    $section = $active_menu;
                    ?>

                    <div class="flex-grow-1 p-3">
                        <?php if ($section == 'profile'): ?>
                            <h3 class="fw-bold mb-4" style="color:#181818;">Your Information</h3>
                            <?php if (!empty($update_success)): ?>
                                <div class="alert alert-success">Profile updated successfully!</div>
                            <?php endif; ?>
                            <form method="post" class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Full Name</label>
                                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phone</label>
                                    <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Birthday</label>
                                    <input type="date" class="form-control" name="birthday" value="<?php echo htmlspecialchars($customer['birthday'] ?? ''); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Address</label>
                                    <textarea class="form-control" name="address" rows="2" required><?php echo htmlspecialchars($customer['address']); ?></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="update_profile" class="btn btn-primary px-5 py-2 fw-bold" style="background:#2563eb;border-radius:8px;border:none;font-size:1.1rem;">Save Changes</button>
                                </div>
                            </form>
                        <?php elseif ($section == 'settings'): ?>
                            <h4 class="fw-bold mb-3" style="color:#181818;">Change Password</h4>
                            <!-- Password change  -->
                            <?php if (!empty($password_success)): ?>
                                <div class="alert alert-success">Password Changed Successfully!</div>
                            <?php endif; ?>
                            <?php if (!empty($password_error)): ?>
                                <div class="alert alert-danger"><?php echo $password_error; ?></div>
                            <?php endif; ?>
                            <form method="post" class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Current Password</label>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">New Password</label>
                                    <input type="password" class="form-control" name="new_password" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Confirm New Password</label>
                                    <input type="password" class="form-control" name="confirm_password" required>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="change_password" class="btn btn-primary px-5 py-2 fw-bold" style="background:#2563eb;border-radius:8px;border:none;font-size:1.1rem;">Change Password</button>
                                </div>
                            </form>
                        <?php elseif ($section == 'orders'): ?>
                            <h4 class="fw-bold mb-4" style="color:#181818;">Your Orders</h4>
                            <?php if (empty($customer_orders)): ?>
                                <div class="alert alert-info">You have no orders yet.</div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Date</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Items</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($customer_orders as $order): ?>
                                            <tr>
                                                <td>#<?php echo $order['id']; ?></td>
                                                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                                <td>$<?php echo htmlspecialchars($order['total']); ?></td>
                                                <td><?php echo htmlspecialchars($order['status'] ?? ''); ?></td>
                                                <td>
                                                    <?php if (!empty($order_items_map[$order['id']])): ?>
                                                        <ul class="mb-0">
                                                            <?php foreach ($order_items_map[$order['id']] as $item): ?>
                                                                <li>
                                                                    <?php
                                                                        echo htmlspecialchars($item['product'] ?? $item['product_id']);
                                                                        echo " x" . htmlspecialchars($item['qty'] ?? $item['quantity']);
                                                                        if (!empty($item['size'])) echo " (Size: " . htmlspecialchars($item['size']) . ")";
                                                                        if (!empty($item['color'])) echo " (Color: " . htmlspecialchars($item['color']) . ")";
                                                                    ?>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php else: ?>
                                                        <span class="text-muted">No items</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-info">This section is under construction.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

   
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var alert = document.querySelector('.alert-success');
        if(alert) {
            setTimeout(function() {
                alert.style.display = 'none';
            }, 3000);
        }

        // Show password 
        var settingsLink = document.querySelector('.nav-link[href="#"][style*="fa-cog"]') ||
                           Array.from(document.querySelectorAll('.nav-link')).find(link => link.innerHTML.includes('fa-cog'));
        var profileForm = document.querySelector('form[method="post"].row.g-4');
        var passwordForm = document.getElementById('passwordChangeForm');

        if(settingsLink && passwordForm && profileForm) {
            settingsLink.addEventListener('click', function(e) {
                e.preventDefault();
                profileForm.style.display = 'none';
                passwordForm.style.display = 'block';
            });
        }
    });
    </script>


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


</body>
</html>