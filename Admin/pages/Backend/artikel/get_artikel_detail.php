<?php  
require_once  __DIR__ . '/../../../../koneksi.php';
require_once __DIR__ . '/../../../../Auth/auth.php';

// Set header untuk JSON  
header('Content-Type: application/json');  

// Logger  
$logger = Logger::getInstance();  

try {  
    // Pastikan hanya admin yang bisa mengakses  
    AuthHelper::requireRole(['admin', 'admin artikel']);  

    // Ambil data dari raw input atau GET  
    $rawInput = file_get_contents('php://input');  
    $inputData = json_decode($rawInput, true);  

    // Ambil ID artikel dari input  
    $artikelId = $inputData['id_artikel'] ?? $_GET['id_artikel'] ?? null;  

    // Validasi input  
    if (!$artikelId) {  
        throw new Exception("ID artikel tidak valid");  
    }  

    // Query untuk mengambil detail artikel dengan informasi tambahan  
    $query = "  
        SELECT   
            a.id_artikel,   
            a.author,   
            a.judul,   
            a.content,   
            a.jenis,   
            a.create_at,   
            a.update_at,  
            u.username AS pembuat,  
            u.email AS email_pembuat  
        FROM   
            artikel a  
        LEFT JOIN   
            user u ON a.id_user = u.id_user  
        WHERE   
            a.id_artikel = ?  
    ";  

    // Eksekusi query  
    $stmt = getConnection()->prepare($query);  
    $stmt->execute([$artikelId]);  
    $artikel = $stmt->fetch(PDO::FETCH_ASSOC);  

    // Cek apakah artikel ditemukan  
    if (!$artikel) {  
        throw new Exception("Artikel tidak ditemukan");  
    }  

    // Log aktivitas pengambilan detail  
    $logger->logActivity('ARTIKEL_DETAIL', 'Mengambil detail artikel', [  
        'id_artikel' => $artikelId,  
        'judul' => $artikel['judul']  
    ]);  

    // Siapkan data untuk dikirim  
    $responseData = [  
        'status' => 'success',  
        'data' => [  
            'id_artikel' => $artikel['id_artikel'],  
            'author' => $artikel['author'],  
            'judul' => $artikel['judul'],  
            'content' => $artikel['content'], // Kirim full content  
            'jenis' => $artikel['jenis'],  
            'create_at' => date('d M Y H:i:s', strtotime($artikel['create_at'])),  
            'update_at' => date('d M Y H:i:s', strtotime($artikel['update_at'])),  
            'pembuat' => $artikel['pembuat'],  
            'email_pembuat' => $artikel['email_pembuat']  
        ]  
    ];  

    // Kirim respons  
    echo json_encode($responseData);  

} catch (Exception $e) {  
    // Log error  
    $logger->logError('ARTIKEL_DETAIL_ERROR', 'Gagal mengambil detail artikel', $e); 
    Logger::getInstance()->logError('ARTIKEL_DETAIL_ERROR', 'Gagal mengambil detail artikel', $e); 

    // Kirim respons error  
    http_response_code(400);  
    echo json_encode([  
        'status' => 'error',  
        'message' => $e->getMessage()  
    ]);  
}