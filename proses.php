<?php
include "db.php";

$nama    = $_POST['nama'];
$telp    = $_POST['telp'];
$alamat  = $_POST['alamat'];
$keluhan = $_POST['keluhan'];
$tanggal = $_POST['tanggal'];
$sesi_id = $_POST['sesi_id'];

// --- Cari nomor antrian terakhir untuk tanggal + sesi ini ---
$qLast = mysqli_query($conn, "SELECT no_antrian 
                              FROM pasien 
                              WHERE tanggal='$tanggal' AND sesi_id='$sesi_id'
                              ORDER BY id DESC LIMIT 1");

if (mysqli_num_rows($qLast) > 0) {
    $row = mysqli_fetch_assoc($qLast);
    $lastNum = intval(substr($row['no_antrian'], 1)); // ambil angka setelah huruf A
    $newNum  = $lastNum + 1;
} else {
    $newNum = 1;
}

// Format nomor antrian, contoh A001
$no_antrian = "A" . str_pad($newNum, 3, "0", STR_PAD_LEFT);

// --- Kurangi kuota sesi ---
mysqli_query($conn, "UPDATE sesi 
                      SET kuota = kuota - 1 
                      WHERE id='$sesi_id' AND kuota > 0");

// --- Simpan data pasien ---
$sql = "INSERT INTO pasien (no_antrian, nama, telp, alamat, keluhan, tanggal, sesi_id) 
        VALUES ('$no_antrian','$nama','$telp','$alamat','$keluhan','$tanggal','$sesi_id')";
if (!mysqli_query($conn, $sql)) {
    die("Error simpan data: " . mysqli_error($conn));
}

// --- Redirect ke hasil.php ---
header("Location: hasil.php");
exit;
?>
