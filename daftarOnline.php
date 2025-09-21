<?php
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

$conn = new mysqli("localhost", "root", "", "antrian");

$tanggal = $_POST['tanggal'] ?? '';
$sesi_id = $_POST['sesi'] ?? '';
$nama = $_POST['nama'] ?? '';
$telepon = $_POST['telepon'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$keluhan = $_POST['keluhan'] ?? '';
$message = '';

function getHari($tanggal)
{
    if (!$tanggal) return '';
    $hari = date('N', strtotime($tanggal));
    if ($hari == 1) return 'senin';
    if ($hari == 3) return 'rabu';
    if ($hari == 5) return 'jumat';
    return '';
}

$hari = getHari($tanggal);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $sesi_id) {
    $cek = $conn->query("SELECT * FROM jadwal WHERE id=$sesi_id AND kuota > 0");
    if ($cek->num_rows > 0) {
        $conn->query("UPDATE jadwal SET kuota = kuota - 1 WHERE id=$sesi_id");
        $conn->query("INSERT INTO pendaftaran (tanggal, sesi_id, nama, telepon, alamat, keluhan) 
                      VALUES ('$tanggal', '$sesi_id', '$nama', '$telepon', '$alamat', '$keluhan')");

        $sesiData = $cek->fetch_assoc();

        $html = "
        <div style='text-align:center;'>
            <img src='https://dokteralini.id/public/website_dralini/uploads/favicon/dr-01-1.png' height='60'><br>
            <h2 style='margin:10px 0;'>Bukti Pendaftaran Antrian</h2>
            <hr>
        </div>
        <table border='1' cellpadding='8' cellspacing='0' width='100%' style='border-collapse:collapse; font-family:Arial;'>
            <tr><td><b>Nama</b></td><td>$nama</td></tr>
            <tr><td><b>No. Telepon</b></td><td>$telepon</td></tr>
            <tr><td><b>Alamat</b></td><td>$alamat</td></tr>
            <tr><td><b>Tanggal Periksa</b></td><td>$tanggal</td></tr>
            <tr><td><b>Hari</b></td><td>$hari</td></tr>
            <tr><td><b>Sesi</b></td><td>" . substr($sesiData['jam'], 0, 5) . "</td></tr>
            <tr><td><b>Keluhan</b></td><td>$keluhan</td></tr>
        </table>
        <p style='text-align:center; margin-top:20px; font-size:12px;'>Harap datang 15 menit sebelum jadwal periksa.</p>
        ";

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("bukti-antrian-$nama.pdf", ["Attachment" => false]);
        exit;
    } else {
        $message = '<div class="alert alert-danger">⚠️ Kuota habis atau sesi tidak valid!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Antrian Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="https://dokteralini.id/public/website_dralini/uploads/favicon/dr-01-1.png" type="image/x-icon">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd, #1a73e8);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            width: 100%;
            max-width: 500px;
            border-radius: 15px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <div class="card p-4">
        <h3 class="text-center mb-3">Daftar Antrian Online</h3>
        <?= $message ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Tanggal Periksa</label>
                <input type="date" id="tanggal" name="tanggal" class="form-control"
                    value="<?= htmlspecialchars($tanggal) ?>" required onchange="this.form.submit()">
                <small class="text-muted" id="info-hari">Pilih hanya Senin, Rabu & Jum'at.</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Pilih Sesi</label>
                <select name="sesi" id="sesi" class="form-select" <?= !$hari ? 'disabled' : '' ?>>
                    <option value="">-- Pilih Sesi --</option>
                    <?php
                    if ($hari) {
                        $sesi = $conn->query("SELECT * FROM jadwal WHERE hari='$hari'");
                        while ($row = $sesi->fetch_assoc()) {
                            $selected = ($sesi_id == $row['id']) ? 'selected' : '';
                            echo "<option value='{$row['id']}' $selected>
                        Jam " . substr($row['jam'], 0, 5) . " (sisa kuota: {$row['kuota']})
                      </option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($nama) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">No. Telepon/Whatsapp</label>
                <input type="text" name="telepon" class="form-control" value="<?= htmlspecialchars($telepon) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="2"><?= htmlspecialchars($alamat) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Keluhan</label>
                <textarea name="keluhan" class="form-control" rows="2"><?= htmlspecialchars($keluhan) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100">Daftar Sekarang</button>
        </form>
    </div>

    <script>
        document.getElementById('tanggal').addEventListener('change', function() {
            let tgl = new Date(this.value);
            let day = tgl.getDay();
            let sesi = document.getElementById('sesi');
            let info = document.getElementById('info-hari');

            if (day === 1 || day === 3 || day === 5) {
                sesi.removeAttribute('disabled');
                info.textContent = "✅ Hari valid, silakan pilih sesi.";
                info.classList.add("text-success");
                info.classList.remove("text-danger");
            } else {
                sesi.setAttribute('disabled', 'disabled');
                sesi.value = "";
                info.textContent = "❌ Hari tidak valid. Pilih Senin, Rabu, atau Jum'at.";
                info.classList.add("text-danger");
                info.classList.remove("text-success");
            }
        });
    </script>
</body>

</html>