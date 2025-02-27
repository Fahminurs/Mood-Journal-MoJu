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

    // Ambil ID edukasi dari input  
    $edukasi_id = $inputData['id_edukasi'] ?? $_GET['id_edukasi'] ?? null;  

    // Validasi input  
    if (!$edukasi_id) {  
        throw new Exception("ID edukasi tidak valid");  
    }  

    // Query untuk mengambil detail edukasi dengan informasi tambahan  
    $query = "  
        SELECT   
            a.id_artikel AS id_edukasi,   
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
            a.id_artikel = ? AND a.jenis = 'edukasi'  
    ";  

    // Eksekusi query  
    $stmt = getConnection()->prepare($query);  
    $stmt->execute([$edukasi_id]);  
    $edukasi = $stmt->fetch(PDO::FETCH_ASSOC);  

    // Cek apakah edukasi ditemukan  
    if (!$edukasi) {  
        throw new Exception("Edukasi tidak ditemukan");  
    }  

    // Log aktivitas pengambilan detail  
    $logger->logActivity('EDUKASI_DETAIL', 'Mengambil detail edukasi', [  
        'id_edukasi' => $edukasi_id,  
        'judul' => $edukasi['judul']  
    ]);  

    // Siapkan data untuk dikirim  
    $responseData = [  
        'status' => 'success',  
        'data' => [  
            'id_edukasi' => $edukasi['id_edukasi'],  
            'author' => $edukasi['author'],  
            'judul' => $edukasi['judul'],  
            'content' => $edukasi['content'], // Kirim full content  
            'jenis' => $edukasi['jenis'],  
            'create_at' => date('d M Y H:i:s', strtotime($edukasi['create_at'])),  
            'update_at' => date('d M Y H:i:s', strtotime($edukasi['update_at'])),  
            'pembuat' => $edukasi['pembuat'],  
            'email_pembuat' => $edukasi['email_pembuat']  
        ]  
    ];  

    // Kirim respons  
    echo json_encode($responseData);  

} catch (Exception $e) {  
    // Log error  
    $logger->logError('EDUKASI_DETAIL_ERROR', 'Gagal mengambil detail edukasi', $e);  

    // Kirim respons error  
    http_response_code(400);  
    echo json_encode([  
        'status' => 'error',  
        'message' => $e->getMessage()  
    ]);  
}