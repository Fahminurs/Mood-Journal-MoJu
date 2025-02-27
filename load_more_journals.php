<?php  
require_once 'koneksi.php';  
require_once __DIR__ . '/Auth/auth.php';  

// Pastikan hanya user yang login bisa mengakses  
AuthHelper::requireLogin();  

// Dapatkan user yang sedang login  
$user = AuthHelper::getCurrentUser();  

// Ambil parameter halaman  
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;  
$limit = 5; // Jumlah jurnal per halaman  
// Simulasi delay untuk infinite scroll  
$should_delay = true;  
if ($should_delay) {  
    // Delay acak antara 0.5 - 1.5 detik untuk efek loading yang natural  
    $delay = mt_rand(500, 1500) / 1000;  
    usleep($delay * 1000000);  
}  
// Hitung offset  
$offset = ($page - 1) * $limit;  

try {  
    // Query untuk mengambil jurnal dengan pagination  
    $journal_query = "SELECT   
        id_journal,   
        judul,   
        isi,   
        mood,   
        create_at  
    FROM journal   
    WHERE id_user = ?   
    ORDER BY COALESCE(update_at, create_at) DESC  
    LIMIT ? OFFSET ?";  

    // Siapkan parameter query  
    $journal_params = [  
        $user['user_id'],   
        $limit,  
        $offset  
    ];  

    $journals = selectQuery($journal_query, $journal_params, 'Get User Journals with Pagination');  
    
    // Mapping mood ke nama file emoji  
    $mood_emoji_map = [  
        'Berpikir' => 'Berpikir atau bingung.png',  
        'Bingung' => 'Berpikir atau bingung.png',  
        'Cemas' => 'Cemas.png',  
        'Cinta' => 'Cinta atau sayang.png',  
        'Sayang' => 'Cinta atau sayang.png',  
        'Lucu' => 'Lucu.png',  
        'Marah' => 'Marah.png',  
        'Ngantuk' => 'Ngantuk atau lelah.png',  
        'Lelah' => 'Ngantuk atau lelah.png',  
        'Percaya Diri' => 'Percaya Diri.png',  
        'Ragu' => 'Ragu atau curiga.png',  
        'Curiga' => 'Ragu atau curiga.png',  
        'Sedih' => 'Sedih.png',  
        'Senang' => 'Senang.png',  
        'Takut' => 'Takut.png',  
        'Terkejut' => 'Terkejut.png'  
    ];  

    // Fungsi untuk memotong teks  
    function truncateToAlphabets($text, $length = 10) {  
        $text = strip_tags($text);  
        $alphabetsOnly = preg_replace('/[^a-zA-Z]/', '', $text);  
        $truncated = mb_substr($alphabetsOnly, 0, $length);  
        $suffix = (mb_strlen($alphabetsOnly) > $length) ? '...' : '';  
        return $truncated . $suffix;  
    }  
    
    // Proses data jurnal  
    $processedJournals = array_map(function($journal) use ($mood_emoji_map) {  
        return [  
            'id_journal' => $journal['id_journal'],  
            'judul' => htmlspecialchars($journal['judul']),  
            'isi' => htmlspecialchars(truncateToAlphabets($journal['isi'])),  
            'create_at' => date('d M Y', strtotime($journal['create_at'])),  
            'mood_emoji' => $mood_emoji_map[$journal['mood']] ?? 'Senang.png'  
        ];  
    }, $journals);  
    
    // Hitung total jurnal untuk cek apakah masih ada data  
    $total_query = "SELECT COUNT(*) as total   
        FROM journal   
        WHERE id_user = ?";  
    
    $total_params = [$user['user_id']];  
    $total_result = selectQuery($total_query, $total_params, 'Count Total Journals');  
    $total_journals = $total_result[0]['total'] ?? 0;  
    
    // Kembalikan data dalam format JSON  
    header('Content-Type: application/json');  
    echo json_encode([  
        'success' => true,  
        'journals' => $processedJournals,  
        'hasMore' => ($page * $limit) < $total_journals,  // Cek apakah masih ada jurnal lain  
        'page' => $page,  
        'total' => $total_journals  
    ]);  
} catch (Exception $e) {  
    // Tangani error  
    header('Content-Type: application/json');  
    echo json_encode([  
        'success' => false,  
        'message' => $e->getMessage()  
    ]);  
}