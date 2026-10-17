<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'db.php';
$database = new Database();
$db = $database->getConnection();

$swal_script = "";

// จัดการเมื่อแอดมินกดยืนยันอนุมัติการจัดส่ง
if (isset($_POST['approve_order'])) {
    $order_id = $_POST['order_id'];
    $updateQuery = "UPDATE orders SET status = 'Approved (Shipping)' WHERE id = ?";
    $stmtUpdate = $db->prepare($updateQuery);
    $stmtUpdate->execute([$order_id]);
    
    $swal_script = "
        Swal.fire({
            icon: 'success',
            title: 'อนุมัติสำเร็จ!',
            text: 'อนุมัติคำสั่งซื้อเรียบร้อยแล้ว',
            confirmButtonColor: '#d81b60',
            confirmButtonText: 'ตกลง'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'admin_dashboard.php';
            }
        });
    ";
}

// จัดการเมื่อแอดมินกดปุ่มลบออร์เดอร์
if (isset($_POST['reject_order'])) {
    $order_id = $_POST['order_id'];
    
    $delItemQuery = "DELETE FROM order_items WHERE order_id = ?";
    $stmtDelItem = $db->prepare($delItemQuery);
    $stmtDelItem->execute([$order_id]);

    $delOrderQuery = "DELETE FROM orders WHERE id = ?";
    $stmtDelOrder = $db->prepare($delOrderQuery);
    $stmtDelOrder->execute([$order_id]);

    $swal_script = "
        Swal.fire({
            icon: 'success',
            title: 'ลบออร์เดอร์สำเร็จ!',
            text: 'ลบรายการคำสั่งซื้อออกจากระบบเรียบร้อยแล้ว',
            confirmButtonColor: '#d81b60',
            confirmButtonText: 'ตกลง'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'admin_dashboard.php';
            }
        });
    ";
}

// จัดการเพิ่มสินค้าใหม่
if (isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $image_url = trim($_POST['image_url']);
    $category = trim($_POST['category']);

    $insertProd = "INSERT INTO products (name, description, price, stock, image_url, category) VALUES (?, ?, ?, ?, ?, ?)";
    $stmtAdd = $db->prepare($insertProd);
    $stmtAdd->execute([$name, $description, $price, $stock, $image_url, $category]);

    $swal_script = "
        Swal.fire({
            icon: 'success',
            title: 'เพิ่มสินค้าสำเร็จ!',
            text: 'เพิ่มเครื่องสำอางรายการใหม่เข้าสู่ระบบเรียบร้อยแล้ว',
            confirmButtonColor: '#d81b60',
            confirmButtonText: 'ตกลง'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'admin_dashboard.php';
            }
        });
    ";
}

// จัดการแก้ไขสินค้า
if (isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $image_url = trim($_POST['image_url']);
    $category = trim($_POST['category']);

    $updateProd = "UPDATE products SET name = ?, description = ?, price = ?, stock = ?, image_url = ?, category = ? WHERE id = ?";
    $stmtEdit = $db->prepare($updateProd);
    $stmtEdit->execute([$name, $description, $price, $stock, $image_url, $category, $product_id]);

    $swal_script = "
        Swal.fire({
            icon: 'success',
            title: 'แก้ไขสินค้าสำเร็จ!',
            text: 'อัปเดตข้อมูลเครื่องสำอางเรียบร้อยแล้ว',
            confirmButtonColor: '#d81b60',
            confirmButtonText: 'ตกลง'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'admin_dashboard.php';
            }
        });
    ";
}

// จัดการลบสินค้า
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    $delProd = "DELETE FROM products WHERE id = ?";
    $stmtDelProd = $db->prepare($delProd);
    $stmtDelProd->execute([$product_id]);

    $swal_script = "
        Swal.fire({
            icon: 'success',
            title: 'ลบสินค้าสำเร็จ!',
            text: 'ลบเครื่องสำอางออกจากระบบเรียบร้อยแล้ว',
            confirmButtonColor: '#d81b60',
            confirmButtonText: 'ตกลง'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'admin_dashboard.php';
            }
        });
    ";
}

// ดึงรายการคำสั่งซื้อทั้งหมด
$query = "SELECT o.*, u.username FROM orders o LEFT JOIN users u ON o.customer_id = u.id ORDER BY o.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงรายการสินค้าทั้งหมด
$prodQuery = "SELECT * FROM products ORDER BY id DESC";
$prodStmt = $db->prepare($prodQuery);
$prodStmt->execute();
$products = $prodStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - จัดการระบบร้านค้า</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body { background-color: #fff0f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .hero-banner { background: linear-gradient(135deg, #ff69b4, #ffb6c1); color: white; padding: 20px; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 5px 15px rgba(255,105,180,0.2); }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .slip-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 2px solid #ffb6c1; }
        .product-thumb { width: 45px; height: 45px; object-fit: cover; border-radius: 8px; }
        @media (min-width: 768px) {
            .hero-banner { padding: 30px; }
            .slip-thumb { width: 70px; height: 70px; }
        }
    </style>
</head>
<body class="py-3 py-md-5">
    
    <div class="container">
        <!-- Hero Banner -->
        <div class="hero-banner d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h4 class="fw-bold mb-1 fs-5 fs-md-4"><i class="fa-solid fa-user-shield me-2"></i>ระบบหลังบ้าน (Admin)</h4>
                <p class="mb-0 small text-white-50">ตรวจสอบคำสั่งซื้อ จัดการสลิป และสินค้าคงเหลือ</p>
            </div>
            <div>
                <a href="logout.php" class="btn btn-light text-danger fw-bold shadow-sm btn-sm px-3 py-2"><i class="fa-solid fa-right-from-bracket me-1"></i> ออกจากระบบ</a>
            </div>
        </div>

        <!-- ปุ่มเปิดฟอร์มเพิ่มสินค้า -->
        <div class="mb-4 text-end">
            <button type="button" class="btn btn-danger fw-bold shadow-sm rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="fa-solid fa-plus-circle me-2"></i>เพิ่มสินค้าใหม่
            </button>
        </div>

        <!-- Modal ฟอร์มเพิ่มสินค้า -->
        <div class="modal fade" id="addProductModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold"><i class="fa-solid fa-circle-plus me-2"></i>เพิ่มรายการเครื่องสำอางใหม่</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold">ชื่อสินค้า</label>
                                <input type="text" name="name" class="form-control" placeholder="เช่น ลิปสติกเนื้อแมท" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">หมวดหมู่</label>
                                <select name="category" class="form-select" required>
                                    <option value="ลิป">ลิปสติก</option>
                                    <option value="บรัชออน">บรัชออน</option>
                                    <option value="มาสคาร่า">มาสคาร่า</option>
                                    <option value="รองพื้น">รองพื้น / แป้ง</option>
                                    <option value="เซรั่ม">สกินแคร์</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">รายละเอียดสินค้า</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="รายละเอียด..." required></textarea>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold">ราคา (บาท)</label>
                                    <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold">จำนวนสต็อก</label>
                                    <input type="number" name="stock" class="form-control" placeholder="0" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">ลิงก์รูปภาพสินค้า (Image URL)</label>
                                <input type="text" name="image_url" class="form-control" placeholder="https://..." required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                            <button type="submit" name="add_product" class="btn btn-danger rounded-pill px-4 fw-bold">บันทึกสินค้า</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ส่วนจัดการรายการคำสั่งซื้อ -->
        <div class="card p-3 p-md-4 bg-white mb-5">
            <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-list-check me-2 text-danger"></i>รายการคำสั่งซื้อของลูกค้าทั้งหมด</h5>
            
            <?php if (empty($orders)): ?>
                <div class="text-center py-4 text-muted">ยังไม่มีคำสั่งซื้อเข้ามาในระบบ</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>ลูกค้า / ข้อมูลติดต่อ</th>
                                <th>รายการสินค้า</th>
                                <th>ยอดเงิน</th>
                                <th>สลิป</th>
                                <th>สถานะ</th>
                                <th class="text-end">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="fw-bold text-danger">#<?php echo $order['id']; ?></td>
                                <td>
                                    <strong><?php echo !empty($order['customer_name']) ? htmlspecialchars($order['customer_name']) : ('สมาชิก: ' . htmlspecialchars($order['username'])); ?></strong><br>
                                    <small class="text-muted"><i class="fa-solid fa-location-dot me-1"></i><?php echo !empty($order['address']) ? htmlspecialchars($order['address']) : '-'; ?></small><br>
                                    <small class="text-primary fw-bold"><i class="fa-solid fa-phone me-1"></i><?php echo !empty($order['phone']) ? htmlspecialchars($order['phone']) : '-'; ?></small>
                                </td>
                                <td style="white-space: normal; min-width: 200px;">
                                    <?php
                                        $itemQuery = "SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";
                                        $itemStmt = $db->prepare($itemQuery);
                                        $itemStmt->execute([$order['id']]);
                                        $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($items as $it) {
                                            echo "<span class='badge bg-light text-dark border mb-1'>• " . htmlspecialchars($it['name']) . " (" . $it['quantity'] . ")</span><br>";
                                        }
                                    ?>
                                </td>
                                <td class="text-danger fw-bold"><?php echo number_format($order['total_price'], 2); ?> ฿</td>
                                <td>
                                    <?php if (!empty($order['slip_image']) && file_exists("uploads/" . $order['slip_image'])): ?>
                                        <a href="uploads/<?php echo $order['slip_image']; ?>" target="_blank">
                                            <img src="uploads/<?php echo $order['slip_image']; ?>" class="slip-thumb shadow-sm" alt="Slip">
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">ไม่มีสลิป</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($order['status'] == 'Pending'): ?>
                                        <span class="badge bg-warning text-dark px-2 py-1">รอตรวจสอบ</span>
                                    <?php elseif ($order['status'] == 'Approved (Shipping)'): ?>
                                        <span class="badge bg-primary px-2 py-1">กำลังจัดส่ง</span>
                                    <?php else: ?>
                                        <span class="badge bg-success px-2 py-1">ได้รับของแล้ว</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($order['status'] == 'Pending'): ?>
                                        <div class="d-flex justify-content-end gap-1">
                                            <form method="POST">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <button type="submit" name="approve_order" class="btn btn-success btn-sm fw-bold shadow-sm" title="อนุมัติ"><i class="fa-solid fa-check"></i></button>
                                            </form>
                                            <button type="button" class="btn btn-danger btn-sm fw-bold shadow-sm" title="ลบ" onclick="confirmReject(<?php echo $order['id']; ?>, 'ต้องการปฏิเสธและลบคำสั่งซื้อนี้ใช่หรือไม่')"><i class="fa-solid fa-xmark"></i></button>
                                        </div>
                                    <?php elseif ($order['status'] == 'Completed (Received)'): ?>
                                        <button type="button" class="btn btn-danger btn-sm fw-bold shadow-sm rounded-pill px-2" onclick="confirmReject(<?php echo $order['id']; ?>, 'ลูกค้าได้รับของแล้ว ต้องการลบออร์เดอร์นี้ออกจากระบบใช่หรือไม่')">
                                            <i class="fa-solid fa-trash-can me-1"></i>ลบ
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted small">รอรับสินค้า</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- ส่วนจัดการรายการสินค้าในระบบ -->
        <div class="card p-3 p-md-4 bg-white mb-4">
            <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-boxes-stacked me-2 text-danger"></i>จัดการรายการสินค้าทั้งหมดในระบบ</h5>
            <div class="table-responsive">
                <table class="table align-middle text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th>รูปภาพ</th>
                            <th>ชื่อสินค้า</th>
                            <th>หมวดหมู่</th>
                            <th>ราคา</th>
                            <th>สต็อก</th>
                            <th class="text-end">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td>
                                <?php if (!empty($p['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($p['image_url']); ?>" class="product-thumb border" alt="Product">
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold text-truncate" style="max-width: 150px;"><?php echo htmlspecialchars($p['name']); ?></td>
                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($p['category'] ?? 'อื่นๆ'); ?></span></td>
                            <td class="text-danger fw-bold"><?php echo number_format($p['price'], 2); ?> ฿</td>
                            <td><span class="badge bg-light text-dark border"><?php echo $p['stock']; ?> ชิ้น</span></td>
                            <td class="text-end">
                                <button type="button" class="btn btn-warning btn-sm rounded-pill px-2 fw-bold text-white me-1" data-bs-toggle="modal" data-bs-target="#editProductModal<?php echo $p['id']; ?>">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-2 fw-bold" onclick="confirmDeleteProduct(<?php echo $p['id']; ?>)">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal ฟอร์มแก้ไขสินค้าแต่ละชิ้น -->
                        <div class="modal fade" id="editProductModal<?php echo $p['id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-warning text-dark">
                                        <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i>แก้ไขสินค้า</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body text-start">
                                            <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">ชื่อสินค้า</label>
                                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($p['name']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">หมวดหมู่</label>
                                                <select name="category" class="form-select" required>
                                                    <option value="ลิป" <?php echo ($p['category'] == 'ลิป') ? 'selected' : ''; ?>>ลิปสติก</option>
                                                    <option value="บรัชออน" <?php echo ($p['category'] == 'บรัชออน') ? 'selected' : ''; ?>>บรัชออน</option>
                                                    <option value="มาสคาร่า" <?php echo ($p['category'] == 'มาสคาร่า') ? 'selected' : ''; ?>>มาสคาร่า</option>
                                                    <option value="รองพื้น" <?php echo ($p['category'] == 'รองพื้น') ? 'selected' : ''; ?>>รองพื้น / แป้ง</option>
                                                    <option value="เซรั่ม" <?php echo ($p['category'] == 'เซรั่ม') ? 'selected' : ''; ?>>สกินแคร์</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">รายละเอียดสินค้า</label>
                                                <textarea name="description" class="form-control" rows="2" required><?php echo htmlspecialchars($p['description']); ?></textarea>
                                            </div>
                                            <div class="row">
                                                <div class="col-6 mb-3">
                                                    <label class="form-label fw-bold">ราคา (บาท)</label>
                                                    <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $p['price']; ?>" required>
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label class="form-label fw-bold">จำนวนสต็อก</label>
                                                    <input type="number" name="stock" class="form-control" value="<?php echo $p['stock']; ?>" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">ลิงก์รูปภาพสินค้า (Image URL)</label>
                                                <input type="text" name="image_url" class="form-control" value="<?php echo htmlspecialchars($p['image_url']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                                            <button type="submit" name="edit_product" class="btn btn-warning rounded-pill px-4 fw-bold text-dark">บันทึก</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function confirmReject(orderId, message) {
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ใช่, ดำเนินการ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'admin_dashboard.php';
                    
                    const inputId = document.createElement('input');
                    inputId.type = 'hidden';
                    inputId.name = 'order_id';
                    inputId.value = orderId;
                    
                    const inputAction = document.createElement('input');
                    inputAction.type = 'hidden';
                    inputAction.name = 'reject_order';
                    inputAction.value = '1';
                    
                    form.appendChild(inputId);
                    form.appendChild(inputAction);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function confirmDeleteProduct(productId) {
            Swal.fire({
                title: 'คุณต้องการลบสินค้านี้ใช่หรือไม่?',
                text: "การลบจะไม่สามารถกู้คืนได้!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ใช่, ลบสินค้า',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'admin_dashboard.php';
                    
                    const inputId = document.createElement('input');
                    inputId.type = 'hidden';
                    inputId.name = 'product_id';
                    inputId.value = productId;
                    
                    const inputAction = document.createElement('input');
                    inputAction.type = 'hidden';
                    inputAction.name = 'delete_product';
                    inputAction.value = '1';
                    
                    form.appendChild(inputId);
                    form.appendChild(inputAction);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>

    <?php if (!empty($swal_script)): ?>
    <script>
        <?php echo $swal_script; ?>
    </script>
    <?php endif; ?>
</body>
</html>