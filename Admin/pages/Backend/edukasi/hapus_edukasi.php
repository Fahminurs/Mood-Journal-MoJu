<?php  
require_once  __DIR__ . '/../../../../koneksi.php';
require_once __DIR__ . '/../../../../Auth/auth.php';

// Pastikan hanya admin edukasi dan admin yang bisa mengakses  
AuthHelper::requireRole(['admin', 'admin artikel']);  

// Set header untuk JSON  
header('Content-Type: application/json');  

// Logger  
$logger = Logger::getInstance();  

try {  
    // Ambil data dari raw input  
    $rawInput = file_get_contents('php://input');  
    $inputData = json_decode($rawInput, true);  

    // Validasi input  
    $edukasi_id = $inputData['id_edukasi'] ?? null;  

    if (!$edukasi_id) {  
        throw new Exception("ID edukasi tidak valid");  
    }  

    // Mulai transaksi untuk keamanan  
    $conn = getConnection();  
    $conn->beginTransaction();  

    // Cek apakah edukasi ada dan sesuai jenis  
    $checkStmt = $conn->prepare("SELECT * FROM artikel WHERE id_artikel = ? AND jenis = 'edukasi'");  
    $checkStmt->execute([$edukasi_id]);  
    $edukasi = $checkStmt->fetch(PDO::FETCH_ASSOC);  

    if (!$edukasi) {  
        throw new Exception("Edukasi tidak ditemukan");  
    }  

    // Hapus edukasi  
    $deleteStmt = $conn->prepare("DELETE FROM artikel WHERE id_artikel = ?");  
    $deleteResult = $deleteStmt->execute([$edukasi_id]);  

    // Commit transaksi  
    $conn->commit();  

    // Log aktivitas penghapusan  
    $logger->logActivity('EDUKASI_HAPUS', 'Edukasi berhasil dihapus', [  
        'id_edukasi' => $edukasi_id,  
        'judul' => $edukasi['judul']  
    ]);  

    // Kirim respons sukses  
    echo json_encode([  
        'status' => 'success',  
        'message' => 'Edukasi berhasil dihapus',  
        'id_edukasi' => $edukasi_id  
    ]);  

} catch (Exception $e) {  
    // Rollback transaksi jika terjadi kesalahan  
    if ($conn && $conn->inTransaction()) {  
        $conn->rollBack();  
    }  

    // Log error  
    $logger->logError('EDUKASI_HAPUS_ERROR', 'Gagal menghapus edukasi', $e);  

    // Kirim respons error  
    http_response_code(400);  
    echo json_encode([  
        'status' => 'error',  
        'message' => $e->getMessage()  
    ]);  
}