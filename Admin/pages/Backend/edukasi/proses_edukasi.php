<?php  
ob_start();  
header('Content-Type: application/json');  

require_once  __DIR__ . '/../../../../koneksi.php';
require_once __DIR__ . '/../../../../Auth/auth.php'; 

// Pastikan hanya admin edukasi dan admin yang bisa mengakses  
AuthHelper::requireRole(['admin', 'admin artikel']);  

try {  
    // Ambil data dari raw input  
    $rawInput = file_get_contents('php://input');  
    $data = json_decode($rawInput, true);  

    // Ekstrak data  
    $author = $data['author'] ?? '';  
    $judul = $data['judul'] ?? '';  
    $content = $data['konten'] ?? '';  

    // Validasi input  
    $errors = [];  
    if (empty($author)) $errors[] = "Penulis harus diisi";  
    if (empty($judul)) $errors[] = "Judul harus diisi";  
    if (empty($content)) $errors[] = "Konten harus diisi";  

    if (!empty($errors)) {  
        throw new Exception(implode(", ", $errors));  
    }  

    // Ambil ID user dari sesi  
    $id_user = $_SESSION['user_id'];  

    // Simpan edukasi ke database  
    $stmt = executeQuery(  
        "INSERT INTO artikel (author, judul, content, jenis, id_user, create_at) VALUES (?, ?, ?, 'edukasi', ?, NOW())",  
        [$author, $judul, $content, $id_user]  
    );  

    // Ambil ID edukasi yang baru saja dibuat  
    $edukasi_id = getConnection()->lastInsertId();  

    // Kirim respons sukses  
    echo json_encode([  
        'status' => 'success',  
        'message' => 'Edukasi berhasil disimpan',  
        'id_edukasi' => $edukasi_id  
    ]);  
} catch (Exception $e) {  
    // Kirim respons error  
    http_response_code(400);  
    echo json_encode([  
        'status' => 'error',  
        'message' => $e->getMessage()  
    ]);  
    exit;  
}  
ob_end_flush();