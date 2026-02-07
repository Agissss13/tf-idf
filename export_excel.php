<?php
include 'koneksi.php';
include 'preprocessing.php';

// Memberitahu browser untuk mengunduh file sebagai Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Matriks_TFIDF.xls");

// Ambil data dokumen
$res_docs = mysqli_query($conn, "SELECT id, judul FROM documents ORDER BY id ASC");
$total_docs = mysqli_num_rows($res_docs);

// Ambil data kalimat
$res_kalimat = mysqli_query($conn, "SELECT kalimat_clean FROM kalimat ORDER BY id ASC");
$data_kalimat = [];
$all_terms = [];

while ($row = mysqli_fetch_assoc($res_kalimat)) {
    $terms = explode(" ", $row['kalimat_clean']);
    $data_kalimat[] = $terms;
    foreach ($terms as $t) {
        if (!empty($t)) $all_terms[] = $t;
    }
}

$unique_terms = array_unique($all_terms);
sort($unique_terms);
$jml_kalimat = count($data_kalimat);
?>

<table border="1">
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Term (Kata Dasar)</th>
            <th colspan="<?= $jml_kalimat ?>">Term Frequency (TF)</th>
            <th rowspan="2">DF</th>
            <th rowspan="2">IDF</th>
            <th colspan="<?= $jml_kalimat ?>">Bobot (W)</th>
        </tr>
        <tr>
            <?php for($n=1; $n<=$jml_kalimat; $n++) echo "<th>K$n</th>"; ?>
            <?php for($n=1; $n<=$jml_kalimat; $n++) echo "<th>K$n</th>"; ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($unique_terms as $term) {
            echo "<tr>";
            echo "<td>".$no++."</td>";
            echo "<td>$term</td>";

            $df = 0;
            $tf_list = [];

            foreach ($data_kalimat as $words) {
                $tf = 0;
                foreach ($words as $w) {
                    if ($w == $term) $tf++;
                }
                $tf_list[] = $tf;
                if ($tf > 0) $df++;
                echo "<td>$tf</td>";
            }

            $idf = ($df > 0) ? log10($jml_kalimat / $df) : 0;
            echo "<td>$df</td>";
            echo "<td>".str_replace('.', ',', round($idf, 4))."</td>";

            foreach ($tf_list as $tf_val) {
                $w = $tf_val * $idf;
                echo "<td>".str_replace('.', ',', round($w, 4))."</td>";
            }
            echo "</tr>";
        }
        ?>
    </tbody>
</table>