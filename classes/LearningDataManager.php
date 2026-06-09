<?php
require_once __DIR__ . '/../config/database.php';

class LearningDataManager {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function addLearningData($student_id, $mata_pelajaran, $nilai, $tingkat_kesulitan, $gaya_belajar, $catatan) {
        $query = "INSERT INTO learning_data 
                  (student_id, mata_pelajaran, nilai, tingkat_kesulitan, gaya_belajar, catatan) 
                  VALUES 
                  (:student_id, :mata_pelajaran, :nilai, :tingkat_kesulitan, :gaya_belajar, :catatan)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':mata_pelajaran', $mata_pelajaran);
        $stmt->bindParam(':nilai', $nilai);
        $stmt->bindParam(':tingkat_kesulitan', $tingkat_kesulitan);
        $stmt->bindParam(':gaya_belajar', $gaya_belajar);
        $stmt->bindParam(':catatan', $catatan);

        return $stmt->execute();
    }

    public function getLearningDataByStudent($student_id) {
        $query = "SELECT * FROM learning_data 
                  WHERE student_id = :student_id 
                  ORDER BY tanggal_input DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>