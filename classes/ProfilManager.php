<?php
require_once __DIR__ . '/../config/database.php';

class ProfilManager {
    private $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    // Mengambil informasi umum organisasi
    public function getOrganisasiInfo() {
        $query = "SELECT * FROM organisasi_info LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Mengambil sejarah organisasi
    public function getSejarahOrganisasi() {
        $query = "SELECT paragraf FROM sejarah_organisasi ORDER BY urutan";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    // Mengambil misi organisasi
    public function getMisiOrganisasi() {
        $query = "SELECT misi_text FROM misi_organisasi ORDER BY urutan";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    // Mengambil nilai-nilai organisasi
    public function getNilaiOrganisasi() {
        $query = "SELECT icon, nama, deskripsi FROM nilai_organisasi ORDER BY id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mengambil struktur organisasi
    public function getStrukturOrganisasi() {
        $query = "SELECT level, nama, posisi, deskripsi, foto FROM struktur_organisasi ORDER BY level, id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mengambil tim pengajar dengan mata pelajaran
    public function getTimPengajar() {
        $query = "SELECT tp.nama, mp.nama as mata_pelajaran, mp.kode as mata_pelajaran_kode, 
                         tp.deskripsi, tp.foto 
                  FROM tim_pengajar tp 
                  JOIN mata_pelajaran mp ON tp.mata_pelajaran_id = mp.id 
                  WHERE tp.status = 'aktif'
                  ORDER BY mp.nama, tp.nama";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mengambil mata pelajaran untuk filter
    public function getMataPelajaranFilter() {
        $query = "SELECT kode, nama FROM mata_pelajaran ORDER BY nama";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Tambahkan opsi "Semua" di awal
        $filter = ['all' => 'Semua'];
        foreach ($result as $row) {
            $filter[$row['kode']] = $row['nama'];
        }
        return $filter;
    }
    
    // Mengambil kontak info
    public function getKontakInfo() {
        $query = "SELECT jenis, nilai FROM kontak_info WHERE status = 'aktif' ORDER BY jenis, urutan";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $kontak = ['alamat' => [], 'telepon' => [], 'email' => [], 'fax' => []];
        foreach ($result as $row) {
            $kontak[$row['jenis']][] = $row['nilai'];
        }
        return $kontak;
    }

    public function updateOrganisasiInfo($data) {
    $this->pdo->beginTransaction();
    try {
        // Update organisasi_info
        $query = "UPDATE organisasi_info SET visi = :visi, tahun_berdiri = :tahun_berdiri, jumlah_siswa_awal = :jumlah_siswa_awal";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':visi' => $data['visi'],
            ':tahun_berdiri' => $data['tahun_berdiri'],
            ':jumlah_siswa_awal' => $data['jumlah_siswa_awal']
        ]);

        // Update sejarah_organisasi
        $deleteStmt = $this->pdo->prepare("DELETE FROM sejarah_organisasi");
        $deleteStmt->execute();
        $sejarahQuery = "INSERT INTO sejarah_organisasi (paragraf, urutan) VALUES (:paragraf, :urutan)";
        $sejarahStmt = $this->pdo->prepare($sejarahQuery);
        $sejarah = explode(',', $data['sejarah']);
        foreach ($sejarah as $index => $paragraf) {
            $sejarahStmt->execute([
                ':paragraf' => trim($paragraf),
                ':urutan' => $index + 1
            ]);
        }

        // Update misi_organisasi
        $deleteStmt = $this->pdo->prepare("DELETE FROM misi_organisasi");
        $deleteStmt->execute();
        $misiQuery = "INSERT INTO misi_organisasi (misi_text, urutan) VALUES (:misi_text, :urutan)";
        $misiStmt = $this->pdo->prepare($misiQuery);
        $misi = explode(',', $data['misi']);
        foreach ($misi as $index => $misi_text) {
            $misiStmt->execute([
                ':misi_text' => trim($misi_text),
                ':urutan' => $index + 1
            ]);
        }

        // Update nilai_organisasi
        $deleteStmt = $this->pdo->prepare("DELETE FROM nilai_organisasi");
        $deleteStmt->execute();
        $nilaiQuery = "INSERT INTO nilai_organisasi (icon, nama, deskripsi) VALUES (:icon, :nama, :deskripsi)";
        $nilaiStmt = $this->pdo->prepare($nilaiQuery);
        $nilai_nilai = explode(',', $data['nilai_nilai']);
        foreach ($nilai_nilai as $nilai) {
            list($nama, $icon, $deskripsi) = array_pad(explode('|', trim($nilai)), 3, '');
            $nilaiStmt->execute([
                ':icon' => $icon,
                ':nama' => $nama,
                ':deskripsi' => $deskripsi
            ]);
        }

        // Update struktur_organisasi
        $deleteStmt = $this->pdo->prepare("DELETE FROM struktur_organisasi");
        $deleteStmt->execute();
        $strukturQuery = "INSERT INTO struktur_organisasi (level, nama, posisi, deskripsi, foto) VALUES (:level, :nama, :posisi, :deskripsi, :foto)";
        $strukturStmt = $this->pdo->prepare($strukturQuery);
        $struktur = explode(',', $data['struktur_organisasi']);
        foreach ($struktur as $staff) {
            list($nama, $posisi, $foto, $deskripsi, $level) = array_pad(explode('|', trim($staff)), 5, '');
            $strukturStmt->execute([
                ':level' => $level,
                ':nama' => $nama,
                ':posisi' => $posisi,
                ':deskripsi' => $deskripsi,
                ':foto' => $foto
            ]);
        }

        // Update tim_pengajar
        $deleteStmt = $this->pdo->prepare("DELETE FROM tim_pengajar");
        $deleteStmt->execute();
        $pengajarQuery = "INSERT INTO tim_pengajar (nama, mata_pelajaran_id, deskripsi, foto, status) 
                         SELECT :nama, mp.id, :deskripsi, :foto, 'aktif' 
                         FROM mata_pelajaran mp WHERE mp.kode = :mata_pelajaran_kode";
        $pengajarStmt = $this->pdo->prepare($pengajarQuery);
        $pengajar = explode(',', $data['tim_pengajar']);
        foreach ($pengajar as $teacher) {
            list($nama, $foto, $mata_pelajaran, $mata_pelajaran_kode, $deskripsi) = array_pad(explode('|', trim($teacher)), 5, '');
            $pengajarStmt->execute([
                ':nama' => $nama,
                ':mata_pelajaran_kode' => $mata_pelajaran_kode,
                ':deskripsi' => $deskripsi,
                ':foto' => $foto
            ]);
        }

        // Update mata_pelajaran_filter
        $deleteStmt = $this->pdo->prepare("DELETE FROM mata_pelajaran");
        $deleteStmt->execute();
        $filterQuery = "INSERT INTO mata_pelajaran (kode, nama) VALUES (:kode, :nama)";
        $filterStmt = $this->pdo->prepare($filterQuery);
        $filter = explode(',', $data['mata_pelajaran_filter']);
        foreach ($filter as $item) {
            list($kode, $nama) = array_pad(explode('|', trim($item)), 2, '');
            $filterStmt->execute([
                ':kode' => $kode,
                ':nama' => $nama
            ]);
        }

        // Update kontak_info
        $deleteStmt = $this->pdo->prepare("DELETE FROM kontak_info");
        $deleteStmt->execute();
        $kontakQuery = "INSERT INTO kontak_info (jenis, nilai, status, urutan) VALUES (:jenis, :nilai, 'aktif', :urutan)";
        $kontakStmt = $this->pdo->prepare($kontakQuery);
        $alamat = explode(',', $data['kontak_info']['alamat']);
        foreach ($alamat as $index => $nilai) {
            $kontakStmt->execute([
                ':jenis' => 'alamat',
                ':nilai' => trim($nilai),
                ':urutan' => $index + 1
            ]);
        }
        $telepon = explode(',', $data['kontak_info']['telepon']);
        foreach ($telepon as $index => $nilai) {
            $kontakStmt->execute([
                ':jenis' => 'telepon',
                ':nilai' => trim($nilai),
                ':urutan' => $index + 1
            ]);
        }

        $this->pdo->commit();
        return true;
    } catch (Exception $e) {
        $this->pdo->rollBack();
        error_log('Update Organisasi Info Error: ' . $e->getMessage());
        throw new Exception('Failed to update organisasi info: ' . $e->getMessage());
    }
}
}
?>