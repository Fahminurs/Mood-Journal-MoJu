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
    if (empty($inputData['id_edukasi'])) $errors[] = "ID edukasi tidak valid";  
    if (empty($inputData['author'])) $errors[] = "Penulis harus diisi";  
    if (empty($inputData['judul'])) $errors[] = "Judul harus diisi";  
    if (empty($inputData['konten'])) $errors[] = "Konten edukasi harus diisi";  

    // Jika ada error, kembalikan response error  
    if (!empty($errors)) {  
        http_response_code(400);  
        echo json_encode([  
            'status' => 'error',  
            'message' => implode(', ', $errors)  
        ]);  
        exit;  
    }  

    // Dapatkan ID user yang sedang login  
    $user = AuthHelper::getCurrentUser();  
    $id_user = $user['user_id'] ?? null;  

    // Cek apakah edukasi ada  
    $checkQuery = "SELECT * FROM artikel WHERE id_artikel = ? AND jenis = 'edukasi'";  
    $existingEdukasi = selectQuery($checkQuery, [$inputData['id_edukasi']], 'Check Artikel Edukasi Exists');  

    if (empty($existingEdukasi)) {  
        http_response_code(404);  
        echo json_encode([  
            'status' => 'error',  
            'message' => 'Artikel edukasi tidak ditemukan'  
        ]);  
        exit;  
    }  

    // Siapkan query update  
    $updateQuery = "UPDATE artikel SET   
                    author = ?,   
                    judul = ?,   
                    content = ?,   
                    id_user = ?,   
                    update_at = NOW()   
                    WHERE id_artikel = ?";  

    try {  
        // Eksekusi update dengan parameter terikat  
        executeQuery(  
            $updateQuery,   
            [  
                $inputData['author'],   
                $inputData['judul'],   
                $inputData['konten'],   
                $id_user,   
                $inputData['id_edukasi']  
            ],   
            'Update Artikel Edukasi'  
        );  

        // Log aktivitas  
        $logger->logActivity('UPDATE_ARTIKEL_EDUKASI', 'Artikel edukasi berhasil diperbarui', [  
            'id_artikel' => $inputData['id_edukasi'],  
            'author' => $inputData['author'],  
            'judul' => $inputData['judul'],  
            'user_id' => $id_user  
        ]);  

        // Kembalikan response sukses  
        echo json_encode([  
            'status' => 'success',  
            'message' => 'Artikel edukasi berhasil diperbarui',  
            'data' => [  
                'id_edukasi' => $inputData['id_edukasi'],  
                'author' => $inputData['author'],  
                'judul' => $inputData['judul']  
            ]  
        ]);  

    } catch (PDOException $e) {  
        // Error database  
        http_response_code(500);  
        $logger->logError('UPDATE_ARTIKEL_EDUKASI_ERROR', 'Gagal memperbarui artikel edukasi', $e);  

        echo json_encode([  
            'status' => 'error',  
            'message' => 'Gagal memperbarui artikel edukasi: ' . $e->getMessage()  
        ]);  
    }  

} catch (Exception $e) {  
    // Error umum  
    http_response_code(500);  
    $logger->logError('GENERAL_ERROR_UPDATE_ARTIKEL_EDUKASI', 'Kesalahan umum saat update artikel edukasi', $e);  

    echo json_encode([  
        'status' => 'error',  
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()  
    ]);  
}