<?php include "db.php"; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Form Antrian Dokter</title>
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
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
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
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1a73e8;
        }
        
        input[type="text"],
        input[type="date"],
        input[type="tel"],
        select,
        textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #c2e0ff;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="date"]:focus,
        input[type="tel"]:focus,
        select:focus,
        textarea:focus {
            border-color: #1a73e8;
            outline: none;
        }
        
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        button {
            background-color: #1a73e8;
            color: white;
            border: none;
            padding: 14px 25px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #0d5bba;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-col {
            flex: 1;
        }
        
        @media (max-width: 600px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
    <script>
    function loadSesi() {
        let tanggal = document.getElementById("tanggal").value;
        let sesiDropdown = document.getElementById("sesi");

        // reset isi dropdown
        sesiDropdown.innerHTML = "<option value=''>-- Memuat sesi... --</option>";

        if (tanggal) {
            let d = new Date(tanggal);
            let hari = d.getDay(); // 0=minggu, 1=senin, dst

            let xhr = new XMLHttpRequest();
            xhr.open("GET", "load_sesi.php?hari=" + hari + "&tanggal=" + tanggal, true);
            xhr.onload = function() {
                if (this.status == 200) {
                    sesiDropdown.innerHTML = this.responseText;
                }
            }
            xhr.send();
        } else {
            sesiDropdown.innerHTML = "<option value=''>-- Pilih Tanggal Dulu --</option>";
        }
    }
    </script>
</head>
<body>
    <div class="container">
        <h2>Form Antrian Dokter</h2>
        <form method="POST" action="proses.php">
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="tanggal">Tanggal Periksa:</label>
                        <input type="date" name="tanggal" id="tanggal" onchange="loadSesi()" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="sesi">Pilih Sesi:</label>
                        <select name="sesi_id" id="sesi" required>
                            <option value="">-- Pilih Tanggal Dulu --</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap:</label>
                        <input type="text" name="nama" id="nama" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="telp">No. Telepon:</label>
                        <input type="tel" name="telp" id="telp" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <textarea name="alamat" id="alamat" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="keluhan">Keluhan:</label>
                <textarea name="keluhan" id="keluhan" required></textarea>
            </div>
            
            <button type="submit">Daftar</button>
        </form>
    </div>
</body>
</html>