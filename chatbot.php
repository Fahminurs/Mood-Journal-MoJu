<?php
// API Key Gemini
$apiKey = "AIzaSyD8Yc4hT0yBmCEP_0Km2qsRyG25Qpr2l_8";

// Cek jika ada permintaan POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil input pengguna
    $userInput = isset($_POST['message']) ? $_POST['message'] : '';

    // Validasi input
    if (empty($userInput)) {
        echo json_encode(['status' => 'error', 'message' => 'Pesan tidak boleh kosong']);
        exit;
    }

    // Siapkan data untuk permintaan API
    $postData = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $userInput]
                ]
            ]
        ]
    ];

    // Panggil API Gemini menggunakan CURL
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent?key=$apiKey");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

    // Eksekusi permintaan dan dapatkan respons
    $response = curl_exec($ch);

    // Tangani error curl
    if (curl_errno($ch)) {
        echo json_encode(['status' => 'error', 'message' => 'Kesalahan server: ' . curl_error($ch)]);
        curl_close($ch);
        exit;
    }

    curl_close($ch);

    // Decode respons
    $apiResponse = json_decode($response, true);

    // Ambil hasil
    $botReply = isset($apiResponse['contents'][0]['parts'][0]['text']) ? $apiResponse['contents'][0]['parts'][0]['text'] : 'Maaf, saya tidak dapat memahami pesan Anda.';

    // Kirim respons ke pengguna
    echo json_encode(['status' => 'success', 'reply' =>  $apiResponse]);
    exit;
}
?>
