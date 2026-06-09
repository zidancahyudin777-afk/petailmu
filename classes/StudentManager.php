<?php
require_once __DIR__ . '/../config/database.php';

class StudentManager {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    /**
     * Autentikasi siswa berdasarkan username dan password.
     * @return array|false Data siswa jika berhasil, false jika gagal.
     */
    public function authenticateStudent($username, $password) {
        try {
            $query = "SELECT id, nama, email, password, jenjang, kelas FROM students WHERE username = :username";
            $stmt  = $this->pdo->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($student && password_verify($password, $student['password'])) {
                return $student;
            }
            return false;
        } catch (PDOException $e) {
            error_log('Student Authentication Error: ' . $e->getMessage());
            throw new Exception('Gagal melakukan autentikasi: ' . $e->getMessage());
        }
    }

    /**
     * Ambil data siswa berdasarkan ID.
     */
    public function getStudentById($id) {
        try {
            $query = "SELECT id, nama, email, jenjang, kelas, created_at FROM students WHERE id = :id";
            $stmt  = $this->pdo->prepare($query);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Get Student Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Ambil data pendaftaran milik siswa berdasarkan student_id.
     */
    public function getRegistrationsByStudentId($studentId) {
        try {
            $query = "SELECT p.id, p.jenjang, p.kelas, p.package_type, p.durasi,
                             p.total_price, p.status, p.created_at
                      FROM pendaftaran p
                      WHERE p.student_id = :student_id
                      ORDER BY p.created_at DESC";
            $stmt  = $this->pdo->prepare($query);
            $stmt->execute([':student_id' => $studentId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Get Registrations Error: ' . $e->getMessage());
            return [];
        }
    }
}
?>
