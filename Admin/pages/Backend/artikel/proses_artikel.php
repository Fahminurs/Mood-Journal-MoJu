<?php  
ob_start();  // Tambahkan ini di awal file  
header('Content-Type: application/json');  // Tambahkan header JSON  

require_once  __DIR__ . '/../../../../koneksi.php';
require_once __DIR__ . '/../../../../Auth/auth.php';

// Pastikan hanya admin artikel dan admin yang bisa mengakses  
AuthHelper::requireRole(['admin', 'admin artikel']);  

try {  
    // Ambil data dari raw input (karena menggunakan JSON)  
    $rawInput = file_get_contents('php://input');  
    $data = json_decode($rawInput, true);  

    // Ekstrak data  
    $author = $data['author'] ?? '';  
    $judul = $data['judul'] ?? '';  
    $content = $data['konten'] ?? '';  
    $jenis = $data['jenis'] ?? 'artikel';    

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

    // Simpan artikel ke database  
    $stmt = executeQuery(  
        "INSERT INTO artikel (author, judul, content, jenis, id_user, create_at) VALUES (?, ?, ?, ?, ?, NOW())",  
        [$author, $judul, $content, $jenis, $id_user]  
    );  

    // Ambil ID artikel yang baru saja dibuat  
    $artikel_id = getConnection()->lastInsertId();  

    // Kirim respons sukses  
    echo json_encode([  
        'status' => 'success',  
        'message' => 'Artikel berhasil disimpan'  
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
ob_end_flush();  // Tambahkan di akhir file