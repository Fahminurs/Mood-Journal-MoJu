<?php  
require_once  __DIR__ . '/../../../../koneksi.php';
require_once __DIR__ . '/../../../../Auth/auth.php';

// Pastikan hanya admin artikel dan admin yang bisa mengakses  
AuthHelper::requireRole(['admin', 'admin artikel']);  

// Set header untuk JSON  
header('Content-Type: application/json');  

// Logger  
$logger = Logger::getInstance();  

try {  
    // Pastikan hanya admin yang bisa menghapus  
    AuthHelper::requireRole(['admin', 'admin artikel']);  

    // Ambil data dari raw input (untuk metode DELETE)  
    $rawInput = file_get_contents('php://input');  
    $inputData = json_decode($rawInput, true);  

    // Atau dari POST jika menggunakan POST  
    $artikelId = $inputData['id_artikel'] ?? $_POST['id_artikel'] ?? null;  

    // Validasi input  
    if (!$artikelId) {  
        throw new Exception("ID artikel tidak valid");  
    }  

    // Mulai transaksi untuk keamanan  
    $conn = getConnection();  
    $conn->beginTransaction();  

    // Cek apakah artikel ada  
    $checkStmt = $conn->prepare("SELECT * FROM artikel WHERE id_artikel = ?");  
    $checkStmt->execute([$artikelId]);  
    $artikel = $checkStmt->fetch(PDO::FETCH_ASSOC);  

    if (!$artikel) {  
        throw new Exception("Artikel tidak ditemukan");  
    }  

    // Hapus artikel  
    $deleteStmt = $conn->prepare("DELETE FROM artikel WHERE id_artikel = ?");  
    $deleteResult = $deleteStmt->execute([$artikelId]);  

    // Commit transaksi  
    $conn->commit();  

    // Log aktivitas penghapusan  
    $logger->logActivity('ARTIKEL_HAPUS', 'Artikel berhasil dihapus', [  
        'id_artikel' => $artikelId,  
        'judul' => $artikel['judul']  
    ]);  

    // Kirim respons sukses  
    echo json_encode([  
        'status' => 'success',  
        'message' => 'Artikel berhasil dihapus',  
        'id_artikel' => $artikelId  
    ]);  

} catch (Exception $e) {  
    // Rollback transaksi jika terjadi kesalahan  
    if ($conn && $conn->inTransaction()) {  
        $conn->rollBack();  
    }  

    // Log error  
    $logger->logError('ARTIKEL_HAPUS_ERROR', 'Gagal menghapus artikel', $e);  

    // Kirim respons error  
    http_response_code(400);  
    echo json_encode([  
        'status' => 'error',  
        'message' => $e->getMessage()  
    ]);  
}