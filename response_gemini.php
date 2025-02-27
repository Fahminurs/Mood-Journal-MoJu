<?php
require 'vendor/autoload.php'; // Pastikan sudah terinstall dependency GeminiAPI

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

// Membaca input dari POST
$title = isset($_POST['judul']) ? trim($_POST['judul']) : null;
$content = isset($_POST['content']) ? trim($_POST['content']) : null;

if (!$title || !$content) {
    echo json_encode(['status' => 'error', 'message' => 'Judul dan isi konten tidak boleh kosong.']);
    exit;
}

try {
    // Tambahkan instruksi ke konten yang akan dianalisis
    $instructions = "Instruksi:\nTeks berikut akan dianalisis untuk menentukan suasana hati dominan penulis. Pilih satu kata yang paling menggambarkan suasana hati dominan penulis dari daftar berikut: 'Berpikir', 'Bingung', 'Cemas', 'Cinta', 'Sayang', 'Lucu', 'Marah', 'Menjijikan', 'Ngantuk', 'Lelah', 'Percaya Diri', 'Ragu', 'Curiga', 'Sedih', 'Senang', 'Takut', 'Terkejut'.\nJawab hanya dengan satu kata sebagai hasil analisis suasana hati, tanpa tambahan, penjelasan, atau konteks lain. Fokuskan pada suasana hati dominan yang muncul dalam teks, meskipun ada campuran emosi atau ambiguitas. Pilih kata yang paling sesuai dengan nuansa umum teks, bahkan jika terdapat perasaan yang saling bertentangan atau tidak langsung diungkapkan. Ingat, meskipun teks terasa ambigu atau sulit untuk diinterpretasikan, selalu pilih satu kata yang paling tepat menggambarkan perasaan utama penulis.";

    $fullContent = $instructions ."\nTeks:". "\njudul: $title\isi: $content";
error_log($fullContent);
    // Inisialisasi client Gemini dengan API key Anda
    $client = new Client("AIzaSyD8Yc4hT0yBmCEP_0Km2qsRyG25Qpr2l_8"); // Ganti dengan API Key Anda

    // Analisis konten dengan API Gemini
    $response = $client->geminiPro()->generateContent(new TextPart($fullContent));
    // Ambil respons mood dari Gemini
    $mood = trim($response->Text()); // Pastikan hasil mood diambil dengan benar
    error_log($mood );

    // Kembalikan hasil analisis sebagai JSON
    echo json_encode(['status' => 'success', 'mood' => $mood]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
