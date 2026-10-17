<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

require_once 'db.php';
require_once 'Product.php';
require_once 'CartClass.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);
$cart = new Cart();

$cart_items = $cart->getCartItems();
$total_qty = array_sum($cart_items);

$success_popup = false;
if (isset($_POST['add_to_cart'])) {
    $cart->add($_POST['product_id'], 1);
    $success_popup = true;
    $cart_items = $cart->getCartItems();
    $total_qty = array_sum($cart_items);
}

// จัดการเมื่อลูกคือกดยืนยันว่าได้รับสินค้าแล้ว
if (isset($_POST['received_order'])) {
    $order_id = $_POST['order_id'];
    $updateStatus = "UPDATE orders SET status = 'Completed (Received)' WHERE id = ? AND customer_id = ?";
    $stmtStatus = $db->prepare($updateStatus);
    $stmtStatus->execute([$order_id, $_SESSION['user_id']]);
    echo "<script>window.location='index.php';</script>";
}

// รับค่าการค้นหาและหมวดหมู่
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

// สร้างคำสั่ง SQL ตามเงื่อนไขการค้นหา
$query = "SELECT * FROM products WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $query .= " AND category = ?";
    $params[] = $category;
}

$query .= " ORDER BY id DESC";
$stmt = $db->prepare($query);
$stmt->execute($params);

// ดึงประวัติคำสั่งซื้อของฉัน
$myOrdersQuery = "SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC";
$myOrdersStmt = $db->prepare($myOrdersQuery);
$myOrdersStmt->execute([$_SESSION['user_id']]);
$myOrders = $myOrdersStmt->fetchAll(PDO::FETCH_ASSOC);
$myOrderCount = count($myOrders);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cosmetics Shop - ร้านเครื่องสำอางออนไลน์</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            background-color: #fff0f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Modern Navbar Styling */
        .navbar-custom {
            background-color: #ffffff;
            box-shadow: 0 4px 20px rgba(255, 105, 180, 0.15);
            padding: 10px 0;
        }

        .navbar-brand {
            font-size: 1.3rem;
            color: #d81b60 !important;
            letter-spacing: 0.5px;
        }

        /* Hero Section Styling */
        .hero-section {
            background: linear-gradient(135deg, #ff69b4, #ffb6c1);
            color: white;
            padding: 35px 15px;
            border-radius: 0 0 25px 25px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(255, 105, 180, 0.25);
        }

        .hero-title {
            font-weight: 800;
            font-size: 1.8rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Card Styling */
        .card {
            border: none;
            border-radius: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
            overflow: hidden;
            background: #fff;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(255, 105, 180, 0.25);
        }

        .product-img {
            height: 160px;
            object-fit: cover;
        }

        .price {
            color: #d81b60;
            font-weight: bold;
            font-size: 1.1rem;
        }

        /* Category Filter Buttons */
        .category-btn {
            border-radius: 50rem;
            padding: 6px 15px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        @media (min-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-section {
                padding: 45px 20px;
            }

            .product-img {
                height: 200px;
            }

            .navbar-brand {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>

    <!-- Modern Navbar -->
    <nav class="navbar navbar-expand navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-truncate" href="index.php">
                <i class="fa-solid fa-wand-magic-sparkles me-1 text-danger"></i>Beauty & Cosmetics
            </a>
            <div class="d-flex align-items-center gap-1 gap-md-2">
                <!-- ปุ่มเปิด Pop-up ประวัติการสั่งซื้อ -->
                <button type="button" class="btn btn-light position-relative px-2 px-md-3 py-1.5 text-secondary fw-bold border shadow-sm rounded-pill font-sm" data-bs-toggle="modal" data-bs-target="#orderHistoryModal">
                    <i class="fa-solid fa-clock-rotate-left text-danger"></i> <span class="d-none d-md-inline">ประวัติสั่งซื้อ</span>
                    <?php if ($myOrderCount > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo $myOrderCount; ?>
                        </span>
                    <?php endif; ?>
                </button>

                <a href="cart.php" class="btn btn-light position-relative px-2 px-md-3 py-1.5 text-danger fw-bold border shadow-sm rounded-pill">
                    <i class="fa-solid fa-bag-shopping"></i> <span class="d-none d-md-inline">ตะกร้า</span>
                    <?php if ($total_qty > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo $total_qty; ?>
                        </span>
                    <?php endif; ?>
                </a>
                <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-2 px-md-3 py-1.5" title="ออกจากระบบ">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Modal Pop-up ประวัติการสั่งซื้อและสถานะสินค้า -->
    <div class="modal fade" id="orderHistoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-clock-rotate-left me-2"></i>ประวัติการสั่งซื้อและสถานะสินค้าของฉัน</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3 p-md-4">
                    <?php if (empty($myOrders)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fa-solid fa-box-open fa-3x mb-3 text-danger"></i>
                            <h5>ยังไม่มีประวัติการสั่งซื้อสินค้า</h5>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>ยอดเงิน</th>
                                        <th>สถานะ</th>
                                        <th class="text-end">การจัดการ / ใบเสร็จ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($myOrders as $mo): ?>
                                        <tr>
                                            <td class="fw-bold">#<?php echo $mo['id']; ?></td>
                                            <td class="text-danger fw-bold"><?php echo number_format($mo['total_price'], 2); ?> ฿</td>
                                            <td>
                                                <?php if ($mo['status'] == 'Pending'): ?>
                                                    <span class="badge bg-warning text-dark px-2 py-1">รอตรวจสอบ</span>
                                                <?php elseif ($mo['status'] == 'Approved (Shipping)'): ?>
                                                    <span class="badge bg-primary px-2 py-1">กำลังจัดส่ง</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success px-2 py-1">ได้รับสินค้าแล้ว</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <?php if ($mo['status'] == 'Approved (Shipping)'): ?>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="order_id" value="<?php echo $mo['id']; ?>">
                                                        <button type="submit" name="received_order" class="btn btn-sm btn-success fw-bold shadow-sm rounded-pill px-2 py-1 me-1"><i class="fa-solid fa-box-open me-1"></i>ได้รับของแล้ว</button>
                                                    </form>
                                                    <a href="receipt.php?order_id=<?php echo $mo['id']; ?>" target="_blank" class="btn btn-sm btn-danger fw-bold shadow-sm rounded-pill px-2 py-1"><i class="fa-solid fa-print me-1"></i>ใบเสร็จ</a>
                                                <?php elseif ($mo['status'] == 'Completed (Received)'): ?>
                                                    <span class="text-success small fw-bold me-1"><i class="fa-solid fa-circle-check"></i> เสร็จสิ้น</span>
                                                    <a href="receipt.php?order_id=<?php echo $mo['id']; ?>" target="_blank" class="btn btn-sm btn-danger fw-bold shadow-sm rounded-pill px-2 py-1"><i class="fa-solid fa-print me-1"></i>ใบเสร็จ</a>
                                                <?php else: ?>
                                                    <span class="text-muted small">รออนุมัติ</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Hero Header -->
    <div class="hero-section text-center">
        <div class="container">
            <h1 class="hero-title mb-2">✨ แหล่งรวมบิวตี้ไอเทมสุดฮิต ✨</h1>
            <p class="lead mb-3 text-white-50 fs-6 fs-md-5">เติมเต็มความสวยของคุณด้วย ลิปสติก บรัชออน และสกินแคร์เกรดพรีเมียมครบวงจร</p>

            <!-- ช่องค้นหา (Search Box) -->
            <form method="GET" class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="input-group shadow-sm">
                        <input type="text" name="search" class="form-control border-0 py-2" placeholder="ค้นหาชื่อเครื่องสำอาง..." value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-dark px-3 px-md-4" type="submit"><i class="fa-solid fa-search"></i> ค้นหา</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mb-5">

        <!-- ปุ่มแบ่งหมวดหมู่ (Category Filters) แบบ Sticky ค้างอยู่ด้านบนเวลาเลื่อนหน้าจอ -->
        <div class="d-flex flex-wrap justify-content-center gap-1 gap-md-2 mb-4 sticky-top py-3" style="top: 70px; background-color: #fff0f5; z-index: 1020;">
            <a href="index.php" class="btn <?php echo empty($category) ? 'btn-danger' : 'btn-outline-danger'; ?> category-btn shadow-sm">
                <i class="fa-solid fa-border-all me-1"></i> ทั้งหมด
            </a>
            <a href="index.php?category=ลิป" class="btn <?php echo $category == 'ลิป' ? 'btn-danger' : 'btn-outline-danger'; ?> category-btn shadow-sm">
                <i class="fa-solid fa-heart me-1"></i> ลิปสติก
            </a>
            <a href="index.php?category=บรัชออน" class="btn <?php echo $category == 'บรัชออน' ? 'btn-danger' : 'btn-outline-danger'; ?> category-btn shadow-sm">
                <i class="fa-solid fa-palette me-1"></i> บรัชออน
            </a>
            <a href="index.php?category=มาสคาร่า" class="btn <?php echo $category == 'มาสคาร่า' ? 'btn-danger' : 'btn-outline-danger'; ?> category-btn shadow-sm">
                <i class="fa-solid fa-eye me-1"></i> มาสคาร่า
            </a>
            <a href="index.php?category=รองพื้น" class="btn <?php echo $category == 'รองพื้น' ? 'btn-danger' : 'btn-outline-danger'; ?> category-btn shadow-sm">
                <i class="fa-solid fa-wand-magic me-1"></i> รองพื้น / แป้ง
            </a>
            <a href="index.php?category=เซรั่ม" class="btn <?php echo $category == 'เซรั่ม' ? 'btn-danger' : 'btn-outline-danger'; ?> category-btn shadow-sm">
                <i class="fa-solid fa-spa me-1"></i> สกินแคร์
            </a>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-secondary fs-5 fs-md-4"><i class="fa-solid fa-heart me-2 text-danger"></i>รายการเครื่องสำอางทั้งหมด</h4>
            <span class="badge bg-white text-danger border px-2 px-md-3 py-2 shadow-sm fs-7 rounded-pill">สินค้าพร้อมส่ง</span>
        </div>

        <div class="row">
            <?php if ($stmt->rowCount() == 0): ?>
                <div class="col-12 text-center py-5">
                    <i class="fa-solid fa-face-sad-tear fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">ไม่พบสินค้าที่คุณค้นหา</h5>
                    <a href="index.php" class="btn btn-danger mt-2 rounded-pill px-4">ดูสินค้าทั้งหมด</a>
                </div>
            <?php else: ?>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
                    <!-- Responsive Grid: มือถือแสดง 2 คอลัมน์ (col-6), คอมพิวเตอร์แสดง 4 คอลัมน์ (col-md-3) -->
                    <div class="col-6 col-md-3 mb-4">
                        <div class="card h-100 shadow-sm">
                            <?php if (!empty($row['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($row['image_url']); ?>" class="card-img-top product-img" alt="Product Image">
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column p-2.5 p-md-3">
                                <h6 class="card-title fw-bold text-dark text-truncate"><?php echo htmlspecialchars($row['name']); ?></h6>
                                <p class="card-text text-muted small flex-grow-1 d-none d-md-block"><?php echo htmlspecialchars($row['description']); ?></p>
                                <div class="mb-2 mb-md-3">
                                    <span class="price"><?php echo number_format($row['price'], 2); ?> ฿</span><br>
                                    <span class="badge bg-light text-secondary border mt-1 font-xs">คงเหลือ: <?php echo $row['stock']; ?> ชิ้น</span>
                                </div>
                                <form method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                    <?php if ($row['stock'] > 0): ?>
                                        <button type="submit" name="add_to_cart" class="btn btn-danger w-100 rounded-pill fw-bold shadow-sm btn-sm py-md-2"><i class="fa-solid fa-cart-plus me-1"></i> เพิ่มใส่ตะกร้า</button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-secondary w-100 rounded-pill btn-sm" disabled>สินค้าหมด</button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>


    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if ($success_popup): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: 'เพิ่มเครื่องสำอางลงตะกร้าเรียบร้อยแล้วค่ะ',
                confirmButtonColor: '#d81b60',
                confirmButtonText: 'ตกลง'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location = 'index.php';
                }
            });
        </script>
    <?php endif; ?>
</body>

</html>