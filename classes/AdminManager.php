<?php
require_once __DIR__ . '/../config/database.php';

class AdminManager {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function authenticateAdmin($username, $password) {
        try {
            $query = "SELECT id, username, password FROM admins WHERE username = :username";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['password'])) {
                return $admin;
            }
            return false;
        } catch (PDOException $e) {
            error_log('Admin Authentication Error: ' . $e->getMessage());
            throw new Exception('Failed to authenticate admin: ' . $e->getMessage());
        }
    }
}
?>