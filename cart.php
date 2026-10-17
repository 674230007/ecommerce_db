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
$cart = new Cart();
$cart_items = $cart->getCartItems();

$order_success = false;

if(isset($_POST['checkout']) && !empty($cart_items)) {
    $customer_id = $_SESSION['user_id'];
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    
    $total_price = 0;
    foreach($cart_items as $product_id => $qty) {
        $query = "SELECT price FROM products WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $total_price += $product['price'] * $qty;
        }
    }

    $slip_name = "";
    if (isset($_FILES['slip']) && $_FILES['slip']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES["slip"]["name"], PATHINFO_EXTENSION);
        $slip_name = "slip_" . time() . "_" . uniqid() . "." . $file_extension;
        move_uploaded_file($_FILES["slip"]["tmp_name"], $target_dir . $slip_name);
    }

    // บันทึกคำสั่งซื้อพร้อมข้อมูลลูกค้าและสลิป
    $query = "INSERT INTO orders (customer_id, total_price, status, customer_name, address, phone, slip_image) VALUES (?, ?, 'Pending', ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([$customer_id, $total_price, $name, $address, $phone, $slip_name]);
    $order_id = $db->lastInsertId();

    foreach($cart_items as $product_id => $qty) {
        $query = "SELECT price FROM products WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $queryItem = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmtItem = $db->prepare($queryItem);
            $stmtItem->execute([$order_id, $product_id, $qty, $product['price']]);

            $prodObj = new Product($db);
            $prodObj->updateStock($product_id, $qty);
        }
    }

    $cart->clear();
    $order_success = true;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระเงินและยืนยันคำสั่งซื้อ - Beauty OOP Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body { background-color: #fff0f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .hero-section { background: linear-gradient(135deg, #ff69b4, #ffb6c1); color: white; padding: 30px 0; border-radius: 0 0 20px 20px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(255,105,180,0.2); }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(255,105,180,0.1); }
        .total-price { color: #d81b60; font-weight: bold; }
        .qr-code { width: 180px; height: 180px; object-fit: cover; border-radius: 10px; border: 2px dashed #ff69b4; padding: 5px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-pink" href="index.php"><i class="fa-solid fa-wand-magic-sparkles me-2 text-warning"></i>Beauty OOP Store</a>
            <div class="d-flex">
                <a href="index.php" class="btn btn-outline-light">
                    <i class="fa-solid fa-store me-1"></i> หน้าร้านค้า
                </a>
            </div>
        </div>
    </nav>

    <div class="hero-section text-center">
        <div class="container">
            <h2 class="fw-bold"><i class="fa-solid fa-file-invoice-dollar me-2"></i>ยืนยันคำสั่งซื้อและชำระเงิน</h2>
            <p class="mb-0">กรอกข้อมูลจัดส่งและแนบหลักฐานการโอนเงิน</p>
        </div>
    </div>

    <div class="container mb-5">
        <?php if(empty($cart_items) && !$order_success): ?>
            <div class="card p-5 text-center bg-white">
                <i class="fa-solid fa-cart-shopping fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">ยังไม่มีสินค้าในตะกร้าของคุณ</h5>
                <a href="index.php" class="btn btn-danger mt-3 px-4 shadow-sm mx-auto" style="max-width: 250px;"><i class="fa-solid fa-arrow-left me-1"></i>กลับไปเลือกซื้อเครื่องสำอาง</a>
            </div>
        <?php else: ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-lg-7 mb-4">
                        <div class="card p-4 bg-white h-100">
                            <h4 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-basket-shopping me-2 text-danger"></i>รายการสินค้า</h4>
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ชื่อสินค้า</th>
                                        <th>จำนวน</th>
                                        <th>ราคารวม</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $grand_total = 0;
                                    foreach($cart_items as $product_id => $qty):
                                        $query = "SELECT * FROM products WHERE id = ?";
                                        $stmt = $db->prepare($query);
                                        $stmt->execute([$product_id]);
                                        $item = $stmt->fetch(PDO::FETCH_ASSOC);
                                        
                                        if($item):
                                            $subtotal = $item['price'] * $qty;
                                            $grand_total += $subtotal;
                                    ?>
                                    <tr>
                                        <td class="fw-bold text-dark"><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td><?php echo $qty; ?> ชิ้น</td>
                                        <td class="text-danger fw-bold"><?php echo number_format($subtotal, 2); ?> บาท</td>
                                    </tr>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </tbody>
                            </table>
                            <div class="text-end mt-auto pt-3 border-top">
                                <h5>ยอดชำระสุทธิทั้งสิ้น: <span class="total-price fs-3"><?php echo number_format($grand_total, 2); ?> บาท</span></h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5 mb-4">
                        <div class="card p-4 bg-white">
                            <h4 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-address-card me-2 text-danger"></i>ข้อมูลจัดส่งสินค้า</h4>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">ชื่อ-นามสกุล ผู้รับ</label>
                                <input type="text" name="name" class="form-control" placeholder="กรอกชื่อ-นามสกุลจริง" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">ที่อยู่จัดส่งสินค้า</label>
                                <textarea name="address" class="form-control" rows="3" placeholder="บ้านเลขที่, ถนน, ตำบล, อำเภอ, จังหวัด, รหัสไปรษณีย์" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">เบอร์โทรศัพท์ติดต่อ</label>
                                <input type="text" name="phone" class="form-control" placeholder="0812345678" required>
                            </div>

                            <hr class="my-4">

                            <h4 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-qrcode me-2 text-danger"></i>ชำระเงินผ่าน QR Code</h4>
                            <div class="text-center mb-3">
                                <img src="https://promptpay.io/0812345678/100.png" class="qr-code mb-2" alt="QR Code Payment">
                                <p class="text-muted small mb-0">สแกนเพื่อชำระเงินตามยอดซื้อสุทธิ</p>
                                <span class="badge bg-light text-dark border mt-1">ชื่อบัญชี: บิวตี้ สโตร์ (ไทยแลนด์)</span>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fa-solid fa-receipt me-1 text-danger"></i>อัปโหลดสลิปหลักฐานการโอนเงิน</label>
                                <input type="file" name="slip" class="form-control" accept="image/*" required>
                            </div>

                            <button type="submit" name="checkout" class="btn btn-danger btn-lg w-100 py-3 fw-bold shadow-sm"><i class="fa-solid fa-check-circle me-2"></i>ยืนยันการสั่งซื้อ</button>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if ($order_success): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'สั่งซื้อสำเร็จ!',
            text: 'สั่งซื้อเครื่องสำอางและแจ้งชำระเงินเรียบร้อยแล้วค่ะ',
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