<?php
// Jalankan perintah ipconfig
$output = shell_exec('ipconfig');

// Cari baris dengan "IPv4 Address"
$lines = explode("\n", $output);
$ipv4Address = null;

foreach ($lines as $line) {
    if (strpos($line, 'IPv4 Address') !== false) {
        // Ambil alamat IP setelah tanda ":"
        $parts = explode(':', $line);
        if (isset($parts[1])) {
            $ipv4Address = trim($parts[1]);
        }
        break; // Keluar dari loop setelah menemukan
    }
}

// Tampilkan hasilnya
if ($ipv4Address) {
    echo "IPv4 Address Anda adalah: " . $ipv4Address . ":3000";;
} else {
    echo "IPv4 Address tidak ditemukan.";
}
?>
