<?php
// Load FPDF
require __DIR__ . '/fpdf/fpdf.php';
// Load library PHP QR Code
require __DIR__ . '/phpqrcode/qrlib.php';
include "db.php";

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("ID tidak valid.");
}

// Query data pasien
$query = "SELECT p.*, s.hari, s.jam 
          FROM pasien p 
          JOIN sesi s ON p.sesi_id = s.id
          WHERE p.id = '$id'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Data pasien tidak ditemukan.");
}

$data = mysqli_fetch_assoc($result);

// --- Generate QR Code ---
$qrDir = __DIR__ . "/temp_qr/";
if (!file_exists($qrDir)) {
    mkdir($qrDir, 0777, true);
}

$qrFile = $qrDir . "qrcode_" . $data['id'] . ".png";

$qrText = "ID: ".$data['id']."\n".
          "No Antrian: ".$data['no_antrian']."\n".
          "Nama: ".$data['nama']."\n".
          "Tanggal: ".$data['tanggal']."\n".
          "Sesi: ".$data['hari']." (".$data['jam'].")";

QRcode::png($qrText, $qrFile, QR_ECLEVEL_L, 5);

// Buat instance PDF
$pdf = new FPDF();
$pdf->AddPage();

// Tambahkan garis tepi (border luar halaman)
$pdf->SetDrawColor(0, 0, 0); // Warna hitam
$pdf->SetLineWidth(1); // Ketebalan garis tepi
$pdf->Rect(5, 5, 200, 287); // x, y, width, height (ukuran A4)

// Logo drAlini (sebelah kiri)
if (file_exists(__DIR__ . "/drAlini.png")) {
    $pdf->Image('drAlini.png',10,8,40); // x,y,width
}

$pdf->SetFont('Arial','B',16);
$pdf->Cell(190,10,'Bukti Pendaftaran Antrian Online',0,1,'C');
$pdf->Ln(5);

// Kotak Nomor Antrian
$pdf->SetFillColor(0, 102, 204); // biru
$pdf->SetTextColor(255,255,255); // putih
$pdf->SetFont('Arial','B',20);
$pdf->Cell(190,20,'Nomor Antrian: '.$data['no_antrian'],0,1,'C',true);
$pdf->Ln(5);

// Reset warna font
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','B',12);

// Pertebal garis tabel
$pdf->SetLineWidth(0.6); // <--- INI DITAMBAHKAN

$pdf->Cell(60,10,'Data',1,0,'C',true);
$pdf->Cell(130,10,'Keterangan',1,1,'C',true);

// Isi tabel
$pdf->SetFont('Arial','',12);
$pdf->Cell(60,10,'Nama',1);            $pdf->Cell(130,10,$data['nama'],1,1);
$pdf->Cell(60,10,'No. Telepon',1);     $pdf->Cell(130,10,$data['telp'],1,1);
$pdf->Cell(60,10,'Alamat',1);          $pdf->Cell(130,10,$data['alamat'],1,1);
$pdf->Cell(60,10,'Keluhan',1);         $pdf->Cell(130,10,$data['keluhan'],1,1);
$pdf->Cell(60,10,'Tanggal Periksa',1); $pdf->Cell(130,10,$data['tanggal'],1,1);
$pdf->Cell(60,10,'Sesi',1);            $pdf->Cell(130,10,$data['hari'].' ('.$data['jam'].')',1,1);

// QR Code (pojok kanan bawah)
$pdf->Ln(10);
if (file_exists($qrFile)) {
    $pdf->Image($qrFile, 150, 160, 40, 40); // x,y,width,height
}

// Footer kecil
$pdf->Ln(10);
$pdf->SetFont('Arial','I',9);
$pdf->MultiCell(0,6,"Terimakasih sudah mendaftar di klinik kami. Admin akan menghubungi anda lewat Whatsapp.\nSimpan ini sebagai bukti resmi antrian anda.");

// Output ke browser
$filename = "Bukti_Antrian_".$data['id'].".pdf";
$pdf->Output('I', $filename);
