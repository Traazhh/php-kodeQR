<?php
include "db.php";

// Ambil data pasien terakhir yang baru daftar
$result = mysqli_query($conn, "SELECT p.*, s.hari, s.jam 
                               FROM pasien p 
                               JOIN sesi s ON p.sesi_id=s.id
                               ORDER BY p.id DESC LIMIT 1");
$data = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Hasil Pendaftaran</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f0f8ff;
            margin: 0;
            padding: 20px;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }
        
        .container {
            max-width: 700px;
            width: 100%;
            margin: 20px auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 100, 200, 0.15);
            padding: 30px;
        }
        
        h2 {
            color: #1a73e8;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
        }
        
        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        
        .result-table td {
            padding: 12px 15px;
            border: 1px solid #c2e0ff;
        }
        
        .result-table tr td:first-child {
            font-weight: 600;
            background-color: #e8f1ff;
            color: #1a73e8;
            width: 30%;
        }
        
        .no-antrian {
            font-size: 24px;
            color: #1a73e8;
            font-weight: bold;
        }
        
        .print-link {
            display: inline-block;
            background-color: #1a73e8;
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 6px;
            font-weight: 600;
            transition: background-color 0.3s;
            text-align: center;
        }
        
        .print-link:hover {
            background-color: #0d5bba;
        }
        
        .success-icon {
            text-align: center;
            color: #1a73e8;
            font-size: 50px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✓</div>
        <h2>Hasil Pendaftaran Antrian</h2>
        
        <table class="result-table">
            <tr>
                <td>No. Antrian</td>
                <td><span class="no-antrian"><?= $data['no_antrian'] ?></span></td>
            </tr>
            <tr>
                <td>Tanggal Periksa</td>
                <td><?= $data['tanggal'] ?></td>
            </tr>
            <tr>
                <td>Sesi</td>
                <td><?= $data['hari']." (".$data['jam'].")" ?></td>
            </tr>
            <tr>
                <td>Nama Lengkap</td>
                <td><?= $data['nama'] ?></td>
            </tr>
            <tr>
                <td>No Telp</td>
                <td><?= $data['telp'] ?></td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td><?= $data['alamat'] ?></td>
            </tr>
            <tr>
                <td>Keluhan</td>
                <td><?= $data['keluhan'] ?></td>
            </tr>
        </table>

        <div style="text-align: center;">
            <a href="cetak.php?id=<?= $data['id'] ?>" target="_blank" class="print-link">Cetak PDF</a>
        </div>
    </div>
</body>
</html>