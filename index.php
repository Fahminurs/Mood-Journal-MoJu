<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Sederhana</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .chat-container {
            max-width: 600px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .chat-box {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            max-height: 400px;
            overflow-y: auto;
        }
        .chat-input {
            display: flex;
            gap: 10px;
        }
        input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-box" id="chat-box"></div>
        <div class="chat-input">
            <input type="text" id="user-input" placeholder="Tulis pesan di sini..." />
            <button id="send-btn">Kirim</button>
        </div>
    </div>

    <script>
    const chatBox = document.getElementById('chat-box');
    const userInput = document.getElementById('user-input');
    const sendBtn = document.getElementById('send-btn');

    // Fungsi untuk menambahkan pesan ke chatBox
    function appendMessage(sender, message) {
        const messageDiv = document.createElement('div');
        messageDiv.textContent = `${sender}: ${message}`;
        chatBox.appendChild(messageDiv);
        chatBox.scrollTop = chatBox.scrollHeight; // Scroll ke bagian bawah chat
    }

    // Event listener untuk tombol kirim
    sendBtn.addEventListener('click', () => {
        const message = userInput.value.trim(); // Ambil nilai input pengguna
        if (!message) return; // Jika kosong, hentikan proses

        // Tambahkan pesan pengguna ke kotak obrolan
        appendMessage('You', message);

        // Kirim pesan ke server menggunakan fetch
        fetch('response_gemini.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `message=${encodeURIComponent(message)}` // Kirim pesan pengguna
        })
            .then(response => response.json()) // Parsing respons JSON dari server
            .then(data => {
                if (data.status === 'success') {
                    appendMessage('Bot', data.reply); // Tambahkan respons bot ke obrolan
                } else {
                    appendMessage('Bot', 'Terjadi kesalahan: ' + data.message);
                }
            })
            .catch(error => {
                appendMessage('Bot', 'Gagal menghubungi server.'); // Jika ada error jaringan
                console.error(error); // Tampilkan error di konsol
            });

        userInput.value = ''; // Kosongkan input setelah pesan dikirim
    });
</script>

</body>
</html>
