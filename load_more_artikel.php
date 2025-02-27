<?php  
require_once 'koneksi.php';  

// Fungsi untuk mensimulasikan delay server  
function simulateServerDelay($minSeconds = 1, $maxSeconds = 3) {  
    $delay = rand($minSeconds * 1000000, $maxSeconds * 1000000);  
    usleep($delay); // Delay dalam mikrodetik  
}  

// Panggil fungsi delay sebelum proses utama  
simulateServerDelay(1, 1); // Delay antara 1-3 detik  

// Ambil parameter halaman  
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;  
$limit = 5; // Jumlah artikel per halaman  
$offset = ($page - 1) * $limit;  

// Hitung total artikel untuk pagination  
$total_query = "SELECT COUNT(*) as total FROM artikel WHERE jenis = ?";  
$total_params = ['artikel'];  

try {  
    // Ambil total artikel  
    $total_result = selectQuery($total_query, $total_params, 'Count Total Artikel');  
    $total_artikels = $total_result[0]['total'];  
    $total_pages = ceil($total_artikels / $limit);  

    // Query untuk mengambil artikel dengan pagination  
    $artikel_query = "SELECT * FROM artikel   
                      WHERE jenis = ?   
                      ORDER BY COALESCE(update_at, create_at) DESC   
                      LIMIT ? OFFSET ?";  
    $artikel_params = ['artikel', $limit, $offset];  

    $artikels = selectQuery($artikel_query, $artikel_params, 'Get Artikel with Pagination');  

    // Kembalikan data dalam format JSON  
    header('Content-Type: application/json');  
    echo json_encode([  
        'success' => true,  
        'artikels' => $artikels,  
        'pagination' => [  
            'current_page' => $page,  
            'total_pages' => $total_pages,  
            'total_artikels' => $total_artikels,  
            'per_page' => $limit  
        ]  
    ]);  
} catch (Exception $e) {  
    // Tangani error  
    header('Content-Type: application/json');  
    http_response_code(500); // Set status code server error  
    echo json_encode([  
        'success' => false,  
        'message' => $e->getMessage(),  
        'error_code' => $e->getCode()  
    ]);  
} 