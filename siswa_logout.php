<?php
session_start();
// Hapus hanya session siswa, biarkan session admin tidak terganggu
unset($_SESSION['student_id']);
unset($_SESSION['student_nama']);
unset($_SESSION['student_email']);
unset($_SESSION['student_jenjang']);
unset($_SESSION['student_kelas']);
session_regenerate_id(true);
header('Location: siswa_login.php');
exit;
?>
