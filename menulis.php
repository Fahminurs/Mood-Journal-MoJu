<?php
require_once 'koneksi.php';
require_once __DIR__ . '/Auth/auth.php'; 
session_start();

// Cek login dan role admin  
AuthHelper::requireRole(['user']);   


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

function generateCustomUUID() {
    return sprintf(
        '%s-%s-%s-%s',
        bin2hex(random_bytes(4)),
        bin2hex(random_bytes(4)),
        bin2hex(random_bytes(4)),
        bin2hex(random_bytes(4))
    );
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Pengguna belum login']);
    exit;
}

$db = getConnection();

// Fetch the journal ID from the URL query string (e.g., menulis.php?id=<uuid>)
$id_journal = isset($_GET['id']) ? $_GET['id'] : null;
$jurnal = null;

// Fetch journal data if a valid ID is provided in the URL
if ($id_journal) {
    $stmt = $db->prepare("SELECT * FROM journal WHERE id_journal = ? AND id_user = ?");
    $stmt->execute([$id_journal, $_SESSION['user_id']]);
    $jurnal = $stmt->fetch();

    if (!$jurnal) {
        header("Location: ../home.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = isset($_POST['content']) ? filter_var($_POST['content'], FILTER_SANITIZE_STRING) : '';
    $judul = isset($_POST['judul']) ? filter_var($_POST['judul'], FILTER_SANITIZE_STRING) : '';
    $mood = isset($_POST['mood']) ? filter_var($_POST['mood'], FILTER_SANITIZE_STRING) : 'Tidak diketahui'; 

    // Debugging input POST
    // error_log("Judul diterima: $judul");
    // error_log("Isi diterima: $content");
    // error_log("Mood diterima: $mood");

    if (empty($content)) {
        echo json_encode(['status' => 'error', 'message' => 'Isi jurnal tidak boleh kosong']);
        exit;
    }

    try {
        if ($id_journal) {
            $stmt = $db->prepare("UPDATE journal SET judul=?, mood = ?, isi = ?, update_at = NOW() WHERE id_journal = ? AND id_user = ?");
            $stmt->execute([$judul, $mood, $content, $id_journal, $_SESSION['user_id']]);
            echo json_encode(['status' => 'success', 'message' => 'Jurnal berhasil diperbarui']);
        } else {
            $id_journal = generateCustomUUID();
            $stmt = $db->prepare("INSERT INTO journal (id_journal, id_user, isi, mood, judul) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id_journal, $_SESSION['user_id'], $content, $mood, $judul]);
            echo json_encode([
                'status' => 'success',
                'message' => 'Jurnal berhasil dibuat',
                'mood' => $mood
            ]);
        }
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
    }
    exit;
}
?>







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sankofa+Display&display=swap" rel="stylesheet">  
    <link rel="stylesheet" href="Assets/css/menulis.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css">  
</head>
<body>
<div class="note-container">
    <nav class="note-nav">
        <div class="nav-left">
            <h1 class="logo">MoJu</h1>
            <div class="nav-divider"></div>
            <span class="page-type">Notes</span>
        </div>
        <div class="nav-actions">
            <button class="btn-cancel" onclick="window.location.href='home.php'">
                <i class="fas fa-times"></i>
                <span>Cancel</span>
            </button>
            <button class="btn-save" id="save-journal">
                <i class="fas fa-check"></i>
                <span>Save</span>
            </button>
        </div>
    </nav>
    <div class="response-container">
    <p for="gemini-response" style="font-weight: 800;">AI Analisis:</p>
    <span id="gemini-response" style="background-color: lightyellow; padding: 10px; border-radius: 5px; display: block; margin-top: 10px; font-size:12px;">
        Hasil analisis akan muncul di sini...
    </span>
</div>
<div style="margin-bottom: 20px;"></div>

    <div class="note-content">
        <div class="title-wrapper">
            <div class="title-section">
                <input type="text" class="note-title" id="note-title"  name="judul"
                    value="<?php echo $jurnal ? htmlspecialchars($jurnal['judul']) : 'Untitled'; ?>" 
                    placeholder="Add title here..." oninput="updateWordCount()"> 
                <div class="meta-info">
                    <span class="last-edited">
                        <i class="far fa-clock"></i>
                        dibuat       :
                        <time id="last-edited"><?php echo $jurnal ? htmlspecialchars($jurnal['create_at']) : ''; ?></time>
    <br>
    <br>
                        <i class="far fa-clock"></i>
                        diperbaharui :
                        <time id="last-edited"><?php echo $jurnal ? htmlspecialchars($jurnal['update_at']) : ''; ?></time>
                        <br>
                        <br>
                        <i class="far fa-clock"></i> 
                        mood : <?php echo $jurnal ? htmlspecialchars($jurnal['mood']) : ''; ?>
                    </span>
                    <span class="word-count" id="word-count" style="background-color: bisque; border-radius:10px; padding:30px 10px; color:black ; display:flex; align-item:center;" >0 words</span>
                </div>
            </div>
            <div class="decoration-circle"></div>
        </div>

        <div class="editor-wrapper">
            <textarea class="note-editor" id="note-editor" name="isi"
                placeholder="Start writing your thoughts here..." 
                oninput="updateWordCount()"><?php echo $jurnal ? htmlspecialchars($jurnal['isi']) : ''; ?></textarea>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
    <script>
        // Word counter
        const editor = document.querySelector('.note-editor');
        const wordCount = document.querySelector('.word-count');

        editor.addEventListener('input', () => {
            const words = editor.value.trim().split(/\s+/).filter(word => word.length > 0);
            wordCount.textContent = `${words.length} words`;
        });

        // Auto-resize title
        const title = document.querySelector('.note-title');
        title.addEventListener('focus', () => {
            title.select();
        });

      
    </script>
<script>
    document.getElementById("save-journal").addEventListener("click", function () {
    const title = document.getElementById("note-title").value.trim();
    const content = document.getElementById("note-editor").value.trim();

    if (!content || !title) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Judul dan isi jurnal tidak boleh kosong',
        });
        return;
    }

    Swal.fire({
        title: 'Menunggu...',
        text: 'Konten sedang dianalisis...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const formData = new FormData();
    formData.append("judul", title); // Kirim judul
    formData.append("content", content); // Kirim konten

    fetch('response_gemini.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const mood = data.mood; // Ambil mood
                console.log("Mood diterima:", mood); // Debugging di konsol browser
                document.getElementById("gemini-response").textContent = `Mood: ${mood}`;

                const journalId = new URLSearchParams(window.location.search).get('id');
                const saveUrl = journalId ? `menulis.php?id=${journalId}` : "menulis.php";
                const saveData = new FormData();
                saveData.append("judul", title); // Kirim judul ke server
                saveData.append("content", content); // Kirim konten ke server
                saveData.append("mood", mood); // Kirim mood ke server

                return fetch(saveUrl, {
                    method: 'POST',
                    body: saveData,
                });
            } else {
                throw new Error(data.message);
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message,
                }).then(() => {
                    window.location.href = 'home.php';
                });
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: error.message || 'Tidak dapat menghubungi server',
            });
        });
});

</script>
</body>
</html>