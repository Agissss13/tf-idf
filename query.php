<?php
include 'koneksi.php';
include 'preprocessing.php';

// Inisialisasi variabel
$query_asli = isset($_GET['q']) ? $_GET['q'] : '';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian & Kategorisasi Otomatis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
           --primary-gradient: linear-gradient(135deg, #07DD11FF 0%, #0A95F1FF 100%);
            --secondary-gradient: linear-gradient(135degrgb(4, 5, 6)8e 0%, #38ef7d 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
            color: #1a202c; 
        }

        /* Navbar Modern */
        .nav-pills .nav-link {
            color: #4a5568;
            font-weight: 500;
            transition: all 0.3s ease;
            border-radius: 10px;
            padding: 10px 20px;
        }

        .nav-pills .nav-link.active {
            background: var(--primary-gradient);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        /* Card Styling */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            background: var(--glass-bg);
            transition: transform 0.3s ease;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #edf2f7;
            padding: 20px;
        }

        /* Modern Table */
        .table {
            border-radius: 12px;
            overflow: hidden;
        }

        .table thead {
            background: #f8fafc;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.75rem;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f5f9;
            transition: background 0.2s;
        }

        /* Hero Section */
        .hero-section {
            background: var(--primary-gradient);
            color: white;
            padding: 60px 0;
            border-radius: 0 0 50px 50px;
            margin-bottom: -50px;
        }

        /* Custom Button */
        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: var(--secondary-gradient);
            border: none;
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4);
        }

        /* Badge & Label */
        .badge-custom {
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-light">

    <div class="hero-section text-center">
        <div class="container animate__animated animate__fadeIn">
            <h1 class="display-5 fw-bold mb-2">Text Processing TF-IDF</h1>
            <p class="lead opacity-75">Mendikti Ungkap Arahan Prabowo Soal Pengembangan SDM Untuk Dukung Program MBG</p>
        </div>
    </div>

    <div class="container nav-container">
        <div class="card card-nav p-2 mb-5 animate__animated animate__fadeInDown">
            <ul class="nav nav-pills nav-justified">
                <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-cloud-upload me-2"></i>Input Data</a></li>
                <li class="nav-item"><a class="nav-link" href="proses_tfidf.php"><i class="bi bi-grid-3x3 me-2"></i>Matriks TF-IDF</a></li>
                <li class="nav-item"><a class="nav-link active" href="query.php"><i class="bi bi-search me-2"></i>Pencarian</a></li>
            </ul>
        </div>

        <div class="card card-modern p-4 mb-5 border-0">
            <form action="" method="GET">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control border-start-0" placeholder="Ketik kata kunci pencarian..." value="<?= htmlspecialchars($query_asli) ?>">
                    <button class="btn btn-primary px-5" type="submit">Cari</button>
                </div>
            </form>
        </div>

    </div>
    <?php
    if ($query_asli != '') {
        // 1. Preprocessing Query
        $query_clean = preprocessing($query_asli);
        $query_terms = explode(" ", $query_clean);

        // Ambil semua data kalimat dari database untuk dihitung
        $res_kalimat = mysqli_query($conn, "SELECT k.*, d.judul FROM kalimat k JOIN documents d ON k.doc_id = d.id");
        $total_docs = mysqli_num_rows($res_kalimat);

        if ($total_docs > 0) {
            $docs_data = [];
            $scores = [];

            // Muat data ke array memory
            while ($row = mysqli_fetch_assoc($res_kalimat)) {
                $docs_data[$row['id']] = $row;
                $scores[$row['id']] = 0; // Inisialisasi skor awal
            }

            // 2. Perhitungan TF-IDF Query
            foreach ($query_terms as $term) {
                if (empty($term)) continue;

                // a. Hitung DF (Document Frequency) untuk term query ini
                $df = 0;
                foreach ($docs_data as $data) {
                    $words = explode(" ", $data['kalimat_clean']);
                    if (in_array($term, $words)) {
                        $df++;
                    }
                }

                // b. Hitung IDF
                $idf = ($df > 0) ? log10($total_docs / $df) : 0;

                // c. Hitung TF dan akumulasi Skor WDT (Weight)
                foreach ($docs_data as $id => $data) {
                    $words = explode(" ", $data['kalimat_clean']);
                    $tf = 0;
                    foreach ($words as $w) {
                        if ($w == $term) $tf++;
                    }

                    // Skor akhir adalah jumlahan TF*IDF dari setiap term query
                    $scores[$id] += ($tf * $idf);
                }
            }

            // Urutkan skor dari yang tertinggi
            arsort($scores);

            // Cek apakah ada hasil yang cocok (skor > 0)
            if (max($scores) > 0) {
                // Ambil ID pemenang (Rank 1)
                reset($scores);
                $top_id = key($scores);
                $top_score = current($scores);
                $top_data = $docs_data[$top_id];

                // 3. Kategorisasi Otomatis menggunakan Lexicon (di functions.php)
                $kategori_otomatis = getAutomaticCategory($top_data['kalimat_clean']);
    ?>

                <div class="card highlight-top shadow mb-5">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-trophy-fill"></i> Hasil Relevansi Tertinggi (Peringkat #1)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-2">
                                    <span class="badge bg-dark">ID Kalimat: <?= $top_data['id'] ?></span>
                                    <span class="badge bg-primary">Dokumen: <?= $top_data['judul'] ?></span>
                                </div>

                                <span class="badge bg-outline-primary mb-2 text-primary border border-primary text-uppercase">Kategori Terdeteksi</span>
                                <h2 class="display-6 fw-bold text-success mb-3"><?= $kategori_otomatis ?></h2>

                                <p class="lead mb-3">"<?= $top_data['kalimat_asli'] ?>"</p>
                                <p class="text-muted small italic">Kalimat ini memiliki bobot tertinggi terhadap query "<strong><?= htmlspecialchars($query_asli) ?></strong>"</p>
                            </div>
                            <div class="col-md-4 text-center border-start d-flex flex-column justify-content-center">
                                <p class="text-muted mb-0 text-uppercase small fw-bold">Skor Akhir TF-IDF</p>
                                <div class="display-4 fw-bold text-success"><?= number_format($top_score, 4) ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="mb-3"><i class="bi bi-list-ol"></i> Peringkat Semua Kalimat</h4>
                <div class="table-responsive bg-white p-3 shadow-sm rounded">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Skor</th>
                                <th>Kalimat</th>
                                <th width="15%">Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($scores as $id => $score) {
                                if ($score > 0) {
                                    $bg_class = ($no == 1) ? 'table-success' : '';
                                    echo "<tr class='$bg_class'>";
                                    echo "<td>$no</td>";
                                    echo "<td class='fw-bold text-primary'>" . number_format($score, 4) . "</td>";
                                    echo "<td>" . $docs_data[$id]['kalimat_asli'] . "</td>";
                                    echo "<td>" . $docs_data[$id]['judul'] . "</td>";
                                    echo "</tr>";
                                    $no++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            <?php } else { ?>
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle"></i> Tidak ada kalimat yang cocok dengan kata kunci <strong>"<?= htmlspecialchars($query_asli) ?>"</strong>.
                </div>
            <?php }
        } else { ?>
            <div class="alert alert-danger text-center">
                Database kosong. Silakan <a href="index.php">upload dokumen</a> terlebih dahulu.
            </div>
    <?php }
    }
    ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>