<?php
include "db.php";

$hariMap = [
    1 => "Senin",
    3 => "Rabu",
    5 => "Jumat"
];

if (!isset($_GET['hari']) || !array_key_exists($_GET['hari'], $hariMap)) {
    echo "<option value=''>Tidak ada sesi tersedia</option>";
    exit;
}

$hari = $hariMap[$_GET['hari']];
$result = mysqli_query($conn, "SELECT * FROM sesi WHERE hari='$hari'");

while ($row = mysqli_fetch_assoc($result)) {
    if ($row['kuota'] > 0) {
        echo "<option value='".$row['id']."'>".$row['hari']." (".$row['jam'].") - sisa kuota ".$row['kuota']."</option>";
    }
}
?>
