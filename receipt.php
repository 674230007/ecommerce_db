<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

require_once 'db.php';
$database = new Database();
$db = $database->getConnection();

$order_id = $_GET['order_id'] ?? 0;
$customer_id = $_SESSION['user_id'];

// ดึงข้อมูลคำสั่งซื้อ (อนุญาตให้ดูและพิมพ์ใบเสร็จได้ทั้งสถานะที่กำลังจัดส่ง และได้รับสินค้าแล้ว)
$query = "SELECT * FROM orders WHERE id = ? AND customer_id = ? AND status IN ('Approved (Shipping)', 'Completed (Received)')";
$stmt = $db->prepare($query);
$stmt->execute([$order_id, $customer_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<script>alert('ไม่พบข้อมูลใบเสร็จ หรือคำสั่งซื้อยังไม่ได้รับการอนุมัติจากแอดมินค่ะ'); window.location='index.php';</script>";
    exit();
}

// ดึงรายการสินค้าในออร์เดอร์
$itemQuery = "SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";
$itemStmt = $db->prepare($itemQuery);
$itemStmt->execute([$order_id]);
$items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเสร็จรับเงิน / ใบกำกับสินค้า - Order #<?php echo $order['id']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #fff0f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px 0; }
        .receipt-card { max-width: 800px; margin: auto; background: white; border-radius: 15px; padding: 40px; box-shadow: 0 5px 25px rgba(255,105,180,0.2); }
        .receipt-header { border-bottom: 2px solid #ffb6c1; padding-bottom: 20px; margin-bottom: 20px; }
        .total-box { background-color: #fff0f5; padding: 15px; border-radius: 10px; font-weight: bold; color: #d81b60; }
        @media print {
            body { background: white; padding: 0; }
            .no-print { display: none; }
            .receipt-card { box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="receipt-card">
            <!-- ส่วนหัวใบเสร็จ -->
            <div class="receipt-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold text-danger mb-1"><i class="fa-solid fa-wand-magic-sparkles me-2"></i>Beauty OOP Store</h3>
                    <p class="text-muted mb-0 small">ร้านเครื่องสำอางออนไลน์ครบวงจร โทร: 081-234-5678</p>
                </div>
                <div class="text-end">
                    <h4 class="fw-bold text-secondary">ใบเสร็จรับเงิน</h4>
                    <span class="badge bg-success">ทำรายการสำเร็จ (ได้รับสินค้าแล้ว)</span>
                </div>
            </div>

            <!-- ข้อมูลออร์เดอร์และลูกค้า -->
            <div class="row mb-4">
                <div class="col-sm-6">
                    <h6 class="fw-bold text-dark">ข้อมูลจัดส่งสินค้า:</h6>
                    <p class="mb-1"><strong>ชื่อผู้รับ:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                    <p class="mb-1"><strong>ที่อยู่:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                    <p class="mb-0"><strong>เบอร์โทร:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                </div>
                <div class="col-sm-6 text-sm-end">
                    <h6 class="fw-bold text-dark">รายละเอียดคำสั่งซื้อ:</h6>
                    <p class="mb-1"><strong>เลขที่ออร์เดอร์:</strong> #<?php echo $order['id']; ?></p>
                    <p class="mb-0"><strong>วันที่สั่งซื้อ:</strong> <?php echo $order['created_at']; ?></p>
                </div>
            </div>

            <!-- ตารางรายการสินค้า -->
            <table class="table align-middle mb-4">
                <thead class="table-light">
                    <tr>
                        <th>ลำดับ</th>
                        <th>ชื่อสินค้า</th>
                        <th class="text-center">จำนวน</th>
                        <th class="text-end">ราคาต่อหน่วย</th>
                        <th class="text-end">ราคารวม</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td class="fw-bold"><?php echo htmlspecialchars($item['name']); ?></td>
                        <td class="text-center"><?php echo $item['quantity']; ?> ชิ้น</td>
                        <td class="text-end"><?php echo number_format($item['price'], 2); ?> ฿</td>
                        <td class="text-end"><?php echo number_format($item['price'] * $item['quantity'], 2); ?> ฿</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- ยอดรวมสุทธิ -->
            <div class="row justify-content-end mb-5">
                <div class="col-sm-5">
                    <div class="total-box d-flex justify-content-between align-items-center">
                        <span>ยอดชำระสุทธิ:</span>
                        <span class="fs-4"><?php echo number_format($order['total_price'], 2); ?> บาท</span>
                    </div>
                </div>
            </div>

            <!-- ปุ่มสั่งพิมพ์ / กลับหน้าแรก -->
            <div class="d-flex justify-content-between no-print pt-3 border-top">
                <a href="index.php" class="btn btn-secondary px-4"><i class="fa-solid fa-arrow-left me-1"></i>กลับหน้าหลัก</a>
                <button onclick="window.print()" class="btn btn-danger px-4 fw-bold shadow-sm"><i class="fa-solid fa-print me-2"></i>พิมพ์ใบเสร็จ</button>
            </div>
        </div>
    </div>

</body>
</html>