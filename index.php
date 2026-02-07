<?php
include 'koneksi.php';
include 'preprocessing.php';

// Logika Reset Data
if (isset($_POST['reset_data'])) {
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");
    mysqli_query($conn, "TRUNCATE TABLE kalimat");
    mysqli_query($conn, "TRUNCATE TABLE documents");
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");
    echo "<script>alert('Semua data telah dihapus!'); window.location='index.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TF-IDF - Upload & Preprocessing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {--primary-gradient: linear-gradient(135deg, #07DD11FF 0%, #0A95F1FF 100%);
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
            color: #05B1F5FF;
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
            border-bottom: 1px solid #440BE0FF;
            padding: 20px;
        }

        /* Modern Table */
        .table {
            border-radius: 12px;
            overflow: hidden;
        }

        .table thead {
            background: #0A73DBFF;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.75rem;
        }

        .table-hover tbody tr:hover {
            background-color: #197BDDFF;
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
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4);
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
        <div class="container">
            <h1 class="display-4 fw-bold">Text Processing TF-IDF</h1>
            <p class="lead opacity-75"> Mendikti Ungkap Arahan Prabowo Soal Pengembangan SDM Untuk Dukung Program MBG
        </div>
    </div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="card p-2 mb-4 shadow-sm border-0">
            <ul class="nav nav-pills nav-justified">
                <li class="nav-item"><a class="nav-link active" href="index.php"><i class="bi bi-cloud-upload me-2"></i>Input Data</a></li>
                <li class="nav-item"><a class="nav-link" href="proses_tfidf.php"><i class="bi bi-grid-3x3 me-2"></i>Matriks TF-IDF</a></li>
                <li class="nav-item"><a class="nav-link" href="query.php"><i class="bi bi-search me-2"></i>Pencarian</a></li>
            </ul>
        </div>

    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card shadow-sm p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Upload Dokumen</h4>
                    <form action="" method="POST" onsubmit="return confirm('Hapus semua data di database?')">
                        <button type="submit" name="reset_data" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash"></i> Reset Data
                        </button>
                    </form>
                </div>
                <hr>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Judul Dokumen</label>
                        <input type="text" name="judul" class="form-control" placeholder="Masukkan judul..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Input Manual (Teks)</label>
                        <textarea name="isi_manual" class="form-control" rows="6" placeholder="Ketik atau paste teks di sini..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Atau Upload File (.txt)</label>
                        <input type="file" name="file_txt" class="form-control" accept=".txt">
                        <div class="form-text">File harus berformat .txt</div>
                    </div>

                    <button type="submit" name="proses" class="btn btn-primary w-100 py-2 fw-bold">
                        <i class="bi bi-gear-fill"></i> Proses & Simpan
                    </button>
                </form>

                <?php
                if (isset($_POST['proses'])) {
                    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
                    $isi = "";

                    // --- TAMBAHKAN LOGIKA RESET OTOMATIS DI SINI ---
                    // Ini akan menghapus data lama setiap kali tombol "Proses" ditekan
                    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");
                    mysqli_query($conn, "TRUNCATE TABLE kalimat");
                    mysqli_query($conn, "TRUNCATE TABLE documents");
                    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");
                    // ----------------------------------------------

                    if (!empty($_FILES['file_txt']['tmp_name'])) {
                        $isi = file_get_contents($_FILES['file_txt']['tmp_name']);
                    } else {
                        $isi = $_POST['isi_manual'];
                    }

                    if (!empty(trim($isi))) {
                        // Simpan Dokumen Baru (ID akan kembali jadi 1 karena sudah di-TRUNCATE)
                        $isi_db = mysqli_real_escape_string($conn, $isi);
                        mysqli_query($conn, "INSERT INTO documents (judul, isi_dokumen) VALUES ('$judul', '$isi_db')");
                        $doc_id = mysqli_insert_id($conn);

                        $sentences = preg_split('/(?<=[.?!])\s+/', $isi, -1, PREG_SPLIT_NO_EMPTY);
                        $count = 0;
                        foreach ($sentences as $s) {
                            $s = trim($s);
                            if (!empty($s)) {
                                $s_clean = preprocessing($s);
                                $s_asli_db = mysqli_real_escape_string($conn, $s);
                                $s_clean_db = mysqli_real_escape_string($conn, $s_clean);
                                mysqli_query($conn, "INSERT INTO kalimat (doc_id, kalimat_asli, kalimat_clean) VALUES ('$doc_id', '$s_asli_db', '$s_clean_db')");
                                $count++;
                            }
                        }
                        echo "<div class='alert alert-success mt-3'>Berhasil! Data lama dibersihkan dan memproses <strong>$count</strong> kalimat baru.</div>";
                    }
                }
                ?>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm p-4">
                <h4 class="mb-3">Data Kalimat Terproses</h4>
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-striped table-hover small">
                        <thead class="table-primary sticky-top">
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Kalimat Asli</th>
                                <th>Hasil Preprocessing</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Ambil data semua dokumen yang sudah diunggah
                            $q = mysqli_query($conn, "SELECT k.*, d.judul FROM kalimat k JOIN documents d ON k.doc_id = d.id ORDER BY k.id ASC");

                            $no = 1; // RESET nomor urut ke 1 setiap kali halaman dimuat

                            while ($row = mysqli_fetch_array($q)) {
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>"; // Nomor akan selalu urut 1, 2, 3...
                                echo "<td><span class='badge bg-secondary'>" . $row['judul'] . "</span></td>";
                                echo "<td><small>" . $row['kalimat_asli'] . "</small></td>";
                                echo "<td class='text-success fw-bold'>" . $row['kalimat_clean'] . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>