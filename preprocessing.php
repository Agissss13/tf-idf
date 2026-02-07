<?php
// Memanggil library Sastrawi yang sudah diinstall via Composer
require_once __DIR__ . '/vendor/autoload.php';

use Sastrawi\Stemmer\StemmerFactory;
use Sastrawi\StopWordRemover\StopWordRemoverFactory;

function preprocessing($text) {
    // 1. Case Folding (Huruf kecil semua)
    $text = strtolower($text);
    
    // 2. Cleaning (Hapus angka dan tanda baca, sisakan huruf dan spasi)
    $text = preg_replace('/[^a-z ]/', ' ', $text);
    
    // 3. Stopword Removal (Menggunakan Sastrawi)
    // Membuat factory stopword remover
    $stopWordFactory = new StopWordRemoverFactory();
    $stopwordRemover = $stopWordFactory->createStopWordRemover();
    
    // Hapus stopword
    $text = $stopwordRemover->remove($text);

    // 4. Stemming (Menggunakan Sastrawi)
    // Membuat factory stemmer
    $stemmerFactory = new StemmerFactory();
    $stemmer = $stemmerFactory->createStemmer();

    // Lakukan stemming (mengubah ke kata dasar)
    $text = $stemmer->stem($text);
    
    return $text;
}

function getAutomaticCategory($clean_text) {
    // Definisi Kamus Kata Kunci (Gunakan kata dasar karena teks sudah di-stemming)
    $dictionary = [
        'Hukum' => [
            'hukum', 'undang', 'pasal', 'pidana', 'perdata', 'hakim', 'jaksa', 'polisi', 
            'sidang', 'gugat', 'penjara', 'delik', 'aturan', 'saksi', 'adil'
        ],
        'Teknologi' => [
            'teknologi','inovasi', 'komputer', 'internet', 'software', 'hardware', 'aplikasi', 
            'data', 'jaringan', 'digital', 'kecerdasan', 'ai', 'robot', 'sistem', 
            'web', 'mobile', 'gadget', 'program', 'coding'
        ],
        'Politik' => [
            'politik', 'presiden', 'menteri', 'dpr', 'pemilu', 'partai', 'demokrasi', 
            'kampanye', 'pemerintah', 'rakyat', 'negara', 'koalisi', 'suara', 'kebijakan'
        ],
        'Kesehatan' => [
            'sehat', 'sakit', 'dokter', 'obat', 'rumah', 'sakit', 'virus', 'bakteri', 
            'medis', 'vaksin', 'imun', 'diagnosis', 'terapi', 'pasien', 'klinik'
        ],
        'Ekonomi' => [
            'ekonomi', 'pasar', 'saham', 'investasi', 'uang', 'bank', 'inflasi', 
            'bisnis', 'dagang', 'harga', 'komoditas', 'laba', 'rugi', 'keuangan'
        ]
    ];

    $words = explode(" ", $clean_text);
    $scores = [];

    // Hitung kecocokan kata untuk setiap kategori
    foreach ($dictionary as $category => $keywords) {
        $scores[$category] = 0;
        foreach ($words as $word) {
            if (in_array($word, $keywords)) {
                $scores[$category]++;
            }
        }
    }

    // Cari kategori dengan skor tertinggi
    arsort($scores);
    $topCategory = key($scores);
    $topScore = current($scores);

    // Jika tidak ada kata yang cocok sama sekali
    if ($topScore == 0) {
        return "Umum / Tidak Teridentifikasi";
    }

    return $topCategory;
}
?>