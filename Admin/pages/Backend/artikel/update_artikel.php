<?php  
require_once  __DIR__ . '/../../../../koneksi.php';
require_once __DIR__ . '/../../../../Auth/auth.php';

// Set header untuk JSON  
header('Content-Type: application/json');  

// Logger  
$logger = Logger::getInstance();  

try {  
    // Pastikan hanya admin yang bisa update artikel  
    AuthHelper::requireRole(['admin', 'admin artikel']);  

    // Ambil data dari raw input  
    $rawInput = file_get_contents('php://input');  
    $inputData = json_decode($rawInput, true);  

    // Validasi input  
    $errors = [];  
    if (empty($inputData['id_artikel'])) $errors[] = "ID artikel tidak valid";  
    if (empty($inputData['author'])) $errors[] = "Penulis harus diisi";  
    if (empty($inputData['judul'])) $errors[] = "Judul harus diisi";  
    if (empty($inputData['konten'])) $errors[] = "Konten artikel harus diisi";  

    if (!empty($errors)) {  
        throw new Exception(implode(", ", $errors));  
    }  

    // Query untuk update artikel  
    $query = "  
        UPDATE artikel   
        SET   
            author = ?,   
            judul = ?,   
            content = ?,   
            update_at = NOW()   
        WHERE   
            id_artikel = ?  
    ";  

    // Eksekusi query  
    $stmt = getConnection()->prepare($query);  
    $updateResult = $stmt->execute([  
        $inputData['author'],  
        $inputData['judul'],  
        $inputData['konten'],  
        $inputData['id_artikel']  
    ]);  

    // Cek apakah update berhasil  
    if (!$updateResult) {  
        throw new Exception("Gagal memperbarui artikel");  
    }  

    // Log aktivitas  
    $logger->logActivity('ARTIKEL_UPDATE', 'Artikel berhasil diperbarui', [  
        'id_artikel' => $inputData['id_artikel'],  
        'judul' => $inputData['judul'],  
        'author' => $inputData['author']  
    ]);  

    // Kirim respons sukses  
    echo json_encode([  
        'status' => 'success',  
        'message' => 'Artikel berhasil diperbarui',  
        'id_artikel' => $inputData['id_artikel']  
    ]);  

} catch (Exception $e) {  
    // Log error  
    $logger->logError('ARTIKEL_UPDATE_ERROR', 'Gagal memperbarui artikel', $e);  

    // Kirim respons error  
    http_response_code(400);  
    echo json_encode([  
        'status' => 'error',  
        'message' => $e->getMessage()  
    ]);  
}