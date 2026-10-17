<?php
session_start();
require_once 'db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password == $user['password']) { // แนะนำให้ใช้ password_verify หากเข้ารหัส
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - Beauty & Cosmetics</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-pink: #ff69b4;
            --accent-rose: #d81b60;
        }

        body { 
            background: linear-gradient(135deg, #fff5f8 0%, #ffb6c1 100%); 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            padding: 15px;
        }

        /* Brand Header Styling */
        .brand-header {
            text-align: center;
            margin-bottom: 25px;
        }
        .brand-title {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #d81b60, #ff69b4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 4px 15px rgba(216, 27, 96, 0.15);
            letter-spacing: 0.5px;
        }
        .brand-subtitle {
            color: #6c757d;
            font-size: 0.95rem;
            margin-top: 5px;
        }

        /* Card Glassmorphic Design */
        .card-login { 
            border: none; 
            border-radius: 24px; 
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(216, 27, 96, 0.15);
            overflow: hidden;
            padding: 35px;
        }

        .form-control {
            border-radius: 50rem;
            padding: 12px 20px;
            border: 1px solid #ffb6c1;
            background-color: #fdf8fa;
        }
        .form-control:focus {
            border-color: var(--accent-rose);
            box-shadow: 0 0 0 0.25rem rgba(216, 27, 96, 0.15);
            background-color: #fff;
        }

        .form-label {
            font-weight: 600;
            color: #4a4a4a;
            margin-left: 10px;
            font-size: 0.9rem;
        }

        .btn-custom {
            background: linear-gradient(135deg, #ff69b4, #d81b60);
            border: none;
            color: white;
            border-radius: 50rem;
            padding: 12px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(216, 27, 96, 0.3);
        }
        .btn-custom:hover {
            background: linear-gradient(135deg, #d81b60, #ad1457);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(216, 27, 96, 0.4);
        }
    </style>
</head>
<body>

    <div class="login-container">
        <!-- ชื่อร้านเด่นชัดเจน -->
        <div class="brand-header">
            <h1 class="brand-title">
                <i class="fa-solid fa-wand-magic-sparkles me-2"></i>Beauty & Cosmetics
            </h1>
            <p class="brand-subtitle">ระบบร้านค้าเครื่องสำอางและสกินแคร์ออนไลน์</p>
        </div>

        <div class="card card-login">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-light text-danger rounded-circle shadow-sm mb-2" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-lock fa-lg"></i>
                </div>
                <h4 class="fw-bold text-dark mb-0">เข้าสู่ระบบ</h4>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 text-center rounded-pill small" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-user me-1 text-danger"></i> ชื่อผู้ใช้งาน (Username)</label>
                    <input type="text" name="username" class="form-control" placeholder="กรอกชื่อผู้ใช้ของคุณ" required>
                </div>
                <div class="mb-4">
                    <label class="form-label"><i class="fa-solid fa-key me-1 text-danger"></i> รหัสผ่าน (Password)</label>
                    <input type="password" name="password" class="form-control" placeholder="กรอกรหัสผ่านของคุณ" required>
                </div>
                <button type="submit" class="btn btn-custom w-100 shadow-sm">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> เข้าสู่ระบบ
                </button>
            </form>
            
            <div class="text-center mt-4">
                <small class="text-muted">ยังไม่มีบัญชีผู้ใช้งาน? <a href="register.php" class="text-danger fw-bold text-decoration-none">สมัครสมาชิก</a></small>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>