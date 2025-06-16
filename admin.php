<?php
$conn = new mysqli("localhost", "root", "", "vv");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ADD PRODUCT
if (isset($_POST['add_product'])) {
    $name = $conn->real_escape_string($_POST['product_name']);
    $price = floatval($_POST['product_price']);
    $category = $conn->real_escape_string($_POST['product_category']);

    // photo upload
    $photo = "";
    if (!empty($_FILES['product_photo']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $photo = $target_dir . uniqid() . '_' . basename($_FILES["product_photo"]["name"]);
        move_uploaded_file($_FILES["product_photo"]["tmp_name"], $photo);
    }

    // sub images
    $sub_images = [];
    if (!empty($_FILES['product_sub_images']['name'][0])) {
        $target_dir = "uploads/";
        foreach ($_FILES['product_sub_images']['tmp_name'] as $key => $tmp_name) {
            if (!empty($_FILES['product_sub_images']['name'][$key])) {
                $sub_image_name = uniqid() . '_' . basename($_FILES['product_sub_images']['name'][$key]);
                $sub_image_path = $target_dir . $sub_image_name;
                if (move_uploaded_file($tmp_name, $sub_image_path)) {
                    $sub_images[] = $sub_image_path;
                }
            }
        }
    }
    $sub_images_json = json_encode($sub_images);

    $conn->query("INSERT INTO products (name, price, category, photo, sub_images) VALUES ('$name', $price, '$category', '$photo', '$sub_images_json')");
    echo '<div class="alert alert-success mb-3" id="addProductAlert">Product added successfully.</div>';
}

// UPDATE PRODUCT
if (isset($_POST['update_product'])) {
    $id = intval($_POST['edit_id']);
    $name = $conn->real_escape_string($_POST['edit_name']);
    $price = floatval($_POST['edit_price']);
    $category = $conn->real_escape_string($_POST['edit_category']);
    $old_photo = $conn->real_escape_string($_POST['edit_old_photo']);

    
    $photo = $old_photo;
    if (!empty($_FILES['edit_photo']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $photo = $target_dir . uniqid() . '_' . basename($_FILES["edit_photo"]["name"]);
        move_uploaded_file($_FILES["edit_photo"]["tmp_name"], $photo);
    }

    
    $old_sub_images = [];
    $result = $conn->query("SELECT sub_images FROM products WHERE id=$id");
    if ($result && $row = $result->fetch_assoc()) {
        $old_sub_images = json_decode($row['sub_images'], true) ?: [];
    }
    $new_sub_images = [];
    if (!empty($_FILES['edit_sub_images']['name'][0])) {
        $target_dir = "uploads/";
        foreach ($_FILES['edit_sub_images']['tmp_name'] as $key => $tmp_name) {
            if (!empty($_FILES['edit_sub_images']['name'][$key])) {
                $sub_image_name = uniqid() . '_' . basename($_FILES['edit_sub_images']['name'][$key]);
                $sub_image_path = $target_dir . $sub_image_name;
                if (move_uploaded_file($tmp_name, $sub_image_path)) {
                    $new_sub_images[] = $sub_image_path;
                }
            }
        }
    }
    $all_sub_images = array_merge($old_sub_images, $new_sub_images);
    $sub_images_json = json_encode($all_sub_images);

    $conn->query("UPDATE products SET name='$name', price=$price, category='$category', photo='$photo', sub_images='$sub_images_json' WHERE id=$id");
    echo '<div class="alert alert-success mb-3" id="updateProductAlert">Product updated successfully.</div>';
}

// DELETE PRODUCT
if (isset($_GET['delete_product'])) {
    $id = intval($_GET['delete_product']);
    $conn->query("DELETE FROM products WHERE id=$id");
    echo '<div class="alert alert-success mb-3" id="deleteProductAlert">Product deleted successfully.</div>';
}


$products = $conn->query("SELECT * FROM products ORDER BY id DESC");


$customers = $conn->query("SELECT * FROM customers ORDER BY id DESC");


$active_menu = 'products';
if (isset($_GET['page'])) {
    $active_menu = $_GET['page'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Velvet Vogue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/new_admin.css">
    <script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"></script>
    <style>
        .admin-dashboard {
            min-height: 100vh;
            display: flex;
        }

        .admin-sidebar {
            height: 100vh;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 300px;
            min-width: 300px;
            max-width: 300px;
            background: #181818 !important;
            color: #fff;
            box-shadow: 2px 0 12px rgba(32,4,238,0.06);
            z-index: 100;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }
        .admin-main {
            margin-left: 300px;
            width: calc(100% - 300px);
        }

        
        #editModal .modal-content {
            border-radius: 22px;
            box-shadow: 0 8px 32px rgba(37,99,235,0.12);
            background: #fff;
        }
        #editModal .modal-header {
            background: #f5f8ff;
            border-top-left-radius: 22px;
            border-top-right-radius: 22px;
        }
        #editModal .modal-title {
            color: #2563eb;
        }
        #editModal .form-label {
            color: #2563eb;
        }
        #editModal .btn-primary {
            background: #2563eb;
            border: none;
        }
        .order-modal .modal-content {
            border-radius: 22px;
            box-shadow: 0 8px 32px rgba(37,99,235,0.12);
            background: #fff;
            border: none;
        }
        .order-modal .modal-header {
            background: #f5f8ff;
            border-top-left-radius: 22px;
            border-top-right-radius: 22px;
            border-bottom: none;
        }
        .order-modal .modal-title {
            color: #2563eb;
            font-weight: bold;
        }
        .order-modal .modal-footer {
            background: #f5f8ff;
            border-bottom-left-radius: 22px;
            border-bottom-right-radius: 22px;
            border-top: none;
        }
        .order-modal .table th,
        .order-modal .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="admin-dashboard d-flex">
        <!-- Sidebar -->
        <nav class="admin-sidebar bg-dark text-white p-4" style="width:300px;min-width:300px;max-width:300px;">
            <div class="sidebar-header mb-4 text-center">
                <img src="assets/images/logo.png" alt="Logo" class="sidebar-logo mb-2">
                <h4>Velvet Vogue</h4>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a class="nav-link text-white <?php echo $active_menu == 'products' ? 'active bg-primary' : ''; ?>" href="admin.php?page=products">
                        <i class="fas fa-box me-2"></i>Products
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white <?php echo $active_menu == 'users' ? 'active bg-primary' : ''; ?>" href="admin.php?page=users">
                        <i class="fas fa-users me-2"></i>Customers
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white <?php echo $active_menu == 'orders' ? 'active bg-primary' : ''; ?>" href="admin.php?page=orders">
                        <i class="fas fa-receipt me-2"></i>Check Orders
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="home.php" target="_blank">
                        <i class="fas fa-store me-2"></i>Go to Shop
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white <?php echo $active_menu == 'settings' ? 'active bg-primary' : ''; ?>" href="admin.php?page=settings">
                        <i class="fas fa-cog me-2"></i>Settings
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <a class="nav-link text-danger" href="logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="admin-main flex-grow-1 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <?php if ($active_menu == 'users'): ?>
                    <h2 class="fw-bold">Customer Details</h2>
                    <span class="badge bg-primary fs-6 py-2 px-3">Admin Panel</span>
                <?php elseif ($active_menu == 'orders'): ?>
                    <h2 class="fw-bold">Order Management</h2>
                    <span class="badge bg-primary fs-6 py-2 px-3">Admin Panel</span>
                <?php else: ?>
                    <h2 class="fw-bold">Product Management</h2>
                    <span class="badge bg-primary fs-6 py-2 px-3">Admin Panel</span>
                <?php endif; ?>
            </div>

            <?php if ($active_menu == 'users'): ?>
                <!-- Customer Table -->
                <div class="card shadow-lg border-0 mb-4" style="border-radius:18px;">
                    <div class="card-body">
                        
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="border-radius:12px;overflow:hidden;">
                                <thead class="table-primary">
                                    <tr>
                                        <th scope="col"><i class="fas fa-user"></i> Name</th>
                                        <th scope="col"><i class="fas fa-envelope"></i> Email</th>
                                        <th scope="col"><i class="fas fa-phone"></i> Phone</th>
                                        <th scope="col"><i class="fas fa-map-marker-alt"></i> Address</th>
                                        <th scope="col"><i class="fas fa-birthday-cake"></i> Birthday</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $customers->fetch_assoc()): ?>
                                    <tr>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-2 py-1"><?php echo htmlspecialchars($row['email']); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge text-dark border px-2 py-1"><?php echo htmlspecialchars($row['phone'] ?? '-'); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge text-dark border px-2 py-1"><?php echo htmlspecialchars($row['address'] ?? '-'); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge text-dark border px-2 py-1"><?php echo htmlspecialchars($row['birthday'] ?? '-'); ?></span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php elseif ($active_menu == 'orders'): ?>
                <!-- Orders Table -->
                <?php
                $orders = $conn->query("
                    SELECT o.id AS order_id, o.order_date, o.total, 
                           c.name AS customer_name, c.phone, c.address
                    FROM orders o
                    JOIN customers c ON o.customer_id = c.id
                    ORDER BY o.order_date DESC
                ");
                $order_items = [];
                $res_items = $conn->query("SELECT * FROM order_items");
                while ($item = $res_items->fetch_assoc()) {
                    $order_items[$item['order_id']][] = $item;
                }
                ?>
                <div class="card shadow-lg border-0 mb-4" style="border-radius:18px;">
                    <div class="card-body">
                        <h4 class="mb-4 fw-bold" style="color:#2563eb;">
                            <i class="fas fa-receipt me-2"></i>Order List
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="border-radius:12px;overflow:hidden;">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Order Date</th>
                                        <th>Total</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $order_rows = []; 
                                    while($order = $orders->fetch_assoc()): 
                                        $order_rows[] = $order; 
                                    ?>
                                    <tr>
                                        <td>#<?php echo $order['order_id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($order['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($order['address']); ?></td>
                                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                        <td>$<?php echo htmlspecialchars($order['total']); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#orderModal<?php echo $order['order_id']; ?>">
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <?php if (!empty($order_rows)): ?>
                    <?php foreach ($order_rows as $order): ?>
                    <div class="modal fade order-modal" id="orderModal<?php echo $order['order_id']; ?>" tabindex="-1" aria-labelledby="orderModalLabel<?php echo $order['order_id']; ?>" aria-hidden="true">
                      <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="orderModalLabel<?php echo $order['order_id']; ?>">
                              <i class="fas fa-receipt me-2"></i>Order #<?php echo $order['order_id']; ?> Details
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <div class="mb-2"><strong><i class="fas fa-user me-1"></i>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></div>
                            <div class="mb-2"><strong><i class="fas fa-phone me-1"></i>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></div>
                            <div class="mb-2"><strong><i class="fas fa-map-marker-alt me-1"></i>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></div>
                            <div class="mb-2"><strong><i class="fas fa-calendar-alt me-1"></i>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></div>
                            <div class="table-responsive mt-3">
                              <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                  <tr>
                                    <th>Product</th>
                                    <th>Size</th>
                                    <th>Color</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php if (!empty($order_items[$order['order_id']])): ?>
                                    <?php foreach ($order_items[$order['order_id']] as $item): ?>
                                      <tr>
                                        <td><?php echo htmlspecialchars($item['product'] ?? $item['product_id']); ?></td>
                                        <td><?php echo htmlspecialchars($item['size']); ?></td>
                                        <td><?php echo htmlspecialchars($item['color']); ?></td>
                                        <td><?php echo htmlspecialchars($item['qty'] ?? $item['quantity']); ?></td>
                                        <td>$<?php echo htmlspecialchars($item['sub_total'] ?? $item['price'] * $item['qty']); ?></td>
                                      </tr>
                                    <?php endforeach; ?>
                                  <?php else: ?>
                                    <tr><td colspan="5">No items found.</td></tr>
                                  <?php endif; ?>
                                </tbody>
                              </table>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                              <i class="fas fa-times me-1"></i>Close
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php elseif ($active_menu == 'settings'): ?>
    <div class="container mb-4">
        <div class="row justify-content-center">
            <!-- Add Admin -->
            <div class="col-md-6">
                <div class="card shadow-lg border-0 mb-4" style="border-radius:18px;">
                    <div class="card-body">
                        <h4 class="mb-4 fw-bold" style="color:#2563eb;">
                            <i class="fas fa-user-plus me-2"></i>Add Admin
                        </h4>
                        <?php
                        //  Add Admin
                        if (isset($_POST['add_admin'])) {
                            $email = $conn->real_escape_string($_POST['admin_email']);
                            $password = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);
                            $conn->query("INSERT INTO admins (email, password) VALUES ('$email', '$password')");
                            echo '<div class="alert alert-success mb-3" id="addAdminAlert">Admin added successfully.</div>';
                        }
                        ?>
                        <form method="post" class="mb-4">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="admin_email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="admin_password" class="form-control" required>
                            </div>
                            <button type="submit" name="add_admin" class="btn btn-success w-100"><i class="fas fa-plus me-2"></i>Add Admin</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Edit Admin -->
            <div class="col-md-6">
                <div class="card shadow-lg border-0 mb-4" style="border-radius:18px;">
                    <div class="card-body">
                        <h4 class="mb-4 fw-bold" style="color:#2563eb;">
                            <i class="fas fa-user-edit me-2"></i>Edit Admin
                        </h4>
                        <?php
                        //  Update Admin
                        if (isset($_POST['update_admin'])) {
                            $admin_id = intval($_POST['admin_id']);
                            $email = $conn->real_escape_string($_POST['admin_email']);
                            $password = $_POST['admin_password'];
                            if (!empty($password)) {
                                $hashed = password_hash($password, PASSWORD_DEFAULT);
                                $conn->query("UPDATE admins SET email='$email', password='$hashed' WHERE id=$admin_id");
                            } else {
                                $conn->query("UPDATE admins SET email='$email' WHERE id=$admin_id");
                            }
                            echo '<div class="alert alert-success mb-3" id="updateAdminAlert">Admin updated successfully.</div>';
                        }

                        
                        $edit_admin_id = 1; 
                        $edit_admin = $conn->query("SELECT * FROM admins WHERE id=$edit_admin_id")->fetch_assoc();
                        ?>
                        <form method="post" class="mb-4">
                            <input type="hidden" name="admin_id" value="<?php echo $edit_admin_id; ?>">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="admin_email" class="form-control" value="<?php echo $edit_admin ? htmlspecialchars($edit_admin['email']) : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="admin_password" class="form-control" placeholder="Leave blank to keep current password">
                            </div>
                            <button type="submit" name="update_admin" class="btn btn-primary w-100"><i class="fas fa-save me-2"></i>Update Admin</button>
                        </form>
                        <?php if (!$edit_admin): ?>
                            <div class="alert alert-warning mt-2">No admin found with ID <?php echo $edit_admin_id; ?>.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Product Management -->
    <div class="card shadow-lg border-0 mb-4" style="border-radius:18px;">
        <div class="card-body">
            <h4 class="mb-4 fw-bold" style="color:#2563eb;">
                <i class="fas fa-box me-2"></i>Product Management
            </h4>
            <!-- Add Product -->
            <form method="post" enctype="multipart/form-data" class="mb-4">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <input type="text" name="product_name" class="form-control" placeholder="Product Name" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <input type="number" name="product_price" class="form-control" placeholder="Price" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <select name="product_category" class="form-control" required>
                            <option value="">Select Category</option>
                            <option value="men">Men</option>
                            <option value="women">Women</option>
                            <option value="new">New Arrivals</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <input type="file" name="product_photo" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-2 mb-3">
                        <input type="file" name="product_sub_images[]" class="form-control" accept="image/*" multiple>
                        <small class="text-muted">Sub Images</small>
                    </div>
                    <div class="col-md-1 mb-3">
                        <button type="submit" name="add_product" class="btn btn-success w-100"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </form>
            <!-- Product Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border-radius:12px;overflow:hidden;">
                    <thead class="table-primary">
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Photo</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $products->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td>$<?php echo htmlspecialchars($row['price']); ?></td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td>
                                <?php if ($row['photo']): ?>
                                    <img src="<?php echo htmlspecialchars($row['photo']); ?>" alt="" style="width:40px;height:40px;object-fit:cover;">
                                <?php else: ?>
                                    <span class="text-muted">No Image</span>
                                <?php endif; ?>
                                <?php
                                $sub_images = json_decode($row['sub_images'], true) ?: [];
                                foreach ($sub_images as $img):
                                ?>
                                    <img src="<?php echo htmlspecialchars($img); ?>" alt="" style="width:24px;height:24px;object-fit:cover;margin-left:2px;">
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <!-- Edit Button  -->
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="admin.php?delete_product=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                      <h5 class="modal-title" id="editModalLabel<?php echo $row['id']; ?>">Edit Product</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="edit_old_photo" value="<?php echo htmlspecialchars($row['photo']); ?>">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="edit_name" class="form-control" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" name="edit_price" class="form-control" value="<?php echo htmlspecialchars($row['price']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="edit_category" class="form-control" required>
                                <option value="men" <?php if($row['category']=='men') echo 'selected'; ?>>Men</option>
                                <option value="women" <?php if($row['category']=='women') echo 'selected'; ?>>Women</option>
                                <option value="new" <?php if($row['category']=='new') echo 'selected'; ?>>New Arrivals</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Photo</label>
                            <input type="file" name="edit_photo" class="form-control" accept="image/*">
                            <?php if ($row['photo']): ?>
                                <img src="<?php echo htmlspecialchars($row['photo']); ?>" alt="" style="width:60px;height:60px;object-fit:cover;margin-top:8px;">
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sub Images</label>
                            <input type="file" name="edit_sub_images[]" class="form-control" accept="image/*" multiple>
                            <div class="mt-2">
                                <?php
                                $sub_images = json_decode($row['sub_images'], true) ?: [];
                                foreach ($sub_images as $img):
                                ?>
                                    <img src="<?php echo htmlspecialchars($img); ?>" alt="" style="width:32px;height:32px;object-fit:cover;margin-right:4px;">
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="submit" name="update_product" class="btn btn-primary">Save changes</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editProduct(id, name, price, category, photo) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_category').value = category;
            document.getElementById('edit_old_photo').value = photo;
            document.getElementById('editModal').style.display = 'block';
        }
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        window.onclick = function(event) {
            var modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
    var addAlert = document.getElementById('addAdminAlert');
    if(addAlert) {
        setTimeout(function() {
            addAlert.style.display = 'none';
        }, 3000);
    }
    var updateAlert = document.getElementById('updateAdminAlert');
    if(updateAlert) {
        setTimeout(function() {
            updateAlert.style.display = 'none';
        }, 3000);
    }
});
document.addEventListener('DOMContentLoaded', function() {
    ['addProductAlert', 'updateProductAlert', 'deleteProductAlert'].forEach(function(id) {
        var alert = document.getElementById(id);
        if(alert) setTimeout(function() { alert.style.display = 'none'; }, 3000);
    });
});
function removeSubImage(btn) {
   
    var imgContainer = btn.closest('.position-relative');
    imgContainer.parentNode.removeChild(imgContainer);
}
    </script>
</body>
</html>


