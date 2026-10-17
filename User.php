<?php
class User {
    protected $conn;
    public $id;
    public $username;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($username, $password) {
        $query = "SELECT * FROM users WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        
        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // เช็คแบบเทียบข้อความตรงๆ เพื่อความชัวร์ในตอนทดสอบ
            if($password === $row['password']) {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['membership_level'] = $row['membership_level'];
                return true;
            }
        }
        return false;
    }
}

class Customer extends User {
    public function getDiscountRate() {
        if ($this->role == 'customer') {
            return 0.10;
        }
        return 0.0;
    }
}

class Admin extends User {
    public function manageSystem() {
        return "Admin privileges granted.";
    }
}
?>