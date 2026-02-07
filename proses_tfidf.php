<?php
include 'koneksi.php';
include 'preprocessing.php';

// Ambil semua data dokumen untuk header tabel
$res_docs = mysqli_query($conn, "SELECT id, judul FROM documents ORDER BY id ASC");
$docs = [];
$doc_map = []; // Untuk mapping ID database ke nomor urut (Doc 1, Doc 2, dst)
$i = 1;
while ($row = mysqli_fetch_assoc($res_docs)) {
    $docs[$row['id']] = $row['judul'];
    $doc_map[$row['id']] = $i++;
}

// Ambil semua data kalimat
$res_kalimat = mysqli_query($conn, "SELECT doc_id, kalimat_clean FROM kalimat ORDER BY id ASC");
$data_kalimat = [];
$all_terms = [];

while ($row = mysqli_fetch_assoc($res_kalimat)) {
    $terms = explode(" ", $row['kalimat_clean']);
    $data_kalimat[] = $terms;
    foreach ($terms as $t) {
        if (!empty($t)) $all_terms[] = $t;
    }
}

// Ambil term unik dan urutkan secara abjad
$unique_terms = array_unique($all_terms);
sort($unique_terms);
$total_docs = count($data_kalimat);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matriks TF-IDF - Detail Perhitungan</title>
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
            color: #0004F8FF;
        }

        /* Navbar Modern */
        .nav-pills .nav-link {
            color: #075DF3FF;
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

<body>

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
                <li class="nav-item"><a class="nav-link active" href="proses_tfidf.php"><i class="bi bi-grid-3x3 me-2"></i>Matriks TF-IDF</a></li>
                <li class="nav-item"><a class="nav-link" href="query.php"><i class="bi bi-search me-2"></i>Pencarian</a></li>
            </ul>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark mb-0">Matriks Bobot TF-IDF</h3>
            <div>
                <a href="export_excel.php" class="btn btn-outline-primary btn-sm me-2 rounded-pill px-3">
                    <i class="bi bi-file-earmark-excel"></i> Ekspor Excel
                </a>
                <a href="proses_tfidf.php" class="btn btn-success btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-clockwise"></i> Hitung Ulang
                </a>
            </div>
        </div>

    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-dark sticky-top">
                    <tr>
                        <th rowspan="2" class="sticky-col">No</th>
                        <th rowspan="2" class="sticky-col">Term (Kata Dasar)</th>
                        <th colspan="<?= $total_docs ?>">Term Frequency (TF) Per Kalimat</th>
                        <th rowspan="2">DF</th>
                        <th rowspan="2">IDF</th>
                        <th colspan="<?= $total_docs ?>">Bobot (W = TF * IDF)</th>
                    </tr>
                    <tr>
                        <?php for ($n = 1; $n <= $total_docs; $n++) echo "<th>K$n</th>"; ?>
                        <?php for ($n = 1; $n <= $total_docs; $n++) echo "<th>K$n</th>"; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($unique_terms as $term) {
                        echo "<tr>";
                        echo "<td class='text-center sticky-col bg-light'>" . $no++ . "</td>";
                        echo "<td class='fw-bold sticky-col'>$term</td>";

                        $df = 0;
                        $tf_list = [];

                        // Hitung TF dan DF
                        foreach ($data_kalimat as $doc_index => $words) {
                            $tf = 0;
                            foreach ($words as $w) {
                                if ($w == $term) $tf++;
                            }
                            $tf_list[] = $tf;
                            if ($tf > 0) $df++;
                            echo "<td class='text-center'>$tf</td>";
                        }

                        // Hitung IDF
                        $idf = ($df > 0) ? log10($total_docs / $df) : 0;

                        echo "<td class='bg-warning bg-opacity-10 text-center fw-bold'>$df</td>";
                        echo "<td class='bg-info bg-opacity-10 text-center'>" . number_format($idf, 4) . "</td>";

                        // Hitung Bobot W
                        foreach ($tf_list as $tf_val) {
                            $w = $tf_val * $idf;
                            $class = ($w > 0) ? 'table-success fw-bold' : 'text-muted opacity-50';
                            echo "<td class='text-center $class'>" . number_format($w, 4) . "</td>";
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-light small text-muted">
        * K1, K2, dst = Urutan Kalimat | TF = Kemunculan Kata | DF = Jumlah Kalimat yang mengandung kata | IDF = log10(Total Kalimat / DF)
    </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>