<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Logbook</title>
    <!-- Tailwind untuk styling dasar -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Library html2pdf untuk download PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        /* Pengaturan Kertas A4 Landscape agar muat banyak kolom */
        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
            background-color: #f3f4f6;
        }

        /* Navigasi (Hilang saat didownload) */
        .no-print {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background: #ffffff;
            padding: 15px;
            border-radius: 8px;
            font-family: sans-serif;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            max-width: 280mm; /* Lebar Landscape */
            margin-left: auto;
            margin-right: auto;
            margin-top: 20px;
        }

        .btn-group {
            display: flex;
            gap: 10px;
        }

        .btn {
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            color: white;
        }

        .btn-back { background: #4b5563; }
        .btn-back:hover { background: #374151; }

        .btn-pdf { background: #dc2626; } /* Merah */
        .btn-pdf:hover { background: #b91c1c; }

        .btn-word { background: #2563eb; } /* Biru */
        .btn-word:hover { background: #1d4ed8; }

        /* Area Kertas A4 Landscape */
        .container {
            width: 280mm; /* Lebar A4 Landscape */
            min-height: 210mm;
            margin: 0 auto 40px auto;
            background: white;
            padding: 15mm;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        /* --- KOP SURAT --- */
        .header-kop {
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
            text-align: center;
            position: relative;
        }

        .logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 75px;
            height: auto;
        }

        .kop-text {
            text-align: center;
            flex-grow: 1;
            padding: 0 80px;
        }

        .kop-text h3 { margin: 0; font-size: 14pt; text-transform: uppercase; font-weight: normal; }
        .kop-text h1 { margin: 2px 0; font-size: 16pt; font-weight: bold; text-transform: uppercase; }
        .kop-text p { margin: 0; font-size: 10pt; font-style: italic; }

        /* Judul Laporan */
        .report-title {
            text-align: center;
            margin: 20px 0 25px 0;
        }
        .report-title h2 {
            margin: 0;
            text-transform: uppercase;
            text-decoration: underline;
            font-size: 12pt;
            font-weight: bold;
        }
        .report-title p {
            margin: 5px 0 0 0;
            font-size: 11pt;
        }

        /* --- TABEL DATA --- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt; /* Ukuran font tabel */
        }

        th, td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: top;
        }

        th {
            background-color: #e5e7eb;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9pt;
        }

        /* --- TANDA TANGAN --- */
        .signature-wrapper {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 40%;
            text-align: center;
        }

        .sign-space {
            height: 70px;
        }
    </style>
</head>
<body>

    <!-- Navigasi Pilihan Download (Tidak akan ikut ter-download) -->
    <div class="no-print">
        <div>
            <strong class="text-gray-800 text-lg">Download Laporan</strong><br>
            <small class="text-gray-500">Pilih format file yang diinginkan:</small>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.monitoring') }}" class="btn btn-back">
                <span>&larr;</span> Kembali
            </a>

            <!-- Tombol Download Word -->
            <button onclick="exportToWord()" class="btn btn-word">
                <span>📄</span> Download Word
            </button>

            <!-- Tombol Download PDF -->
            <button onclick="exportToPDF()" class="btn btn-pdf">
                <span>⬇️</span> Download PDF
            </button>
        </div>
    </div>

    <!-- Area yang akan dicetak/didownload -->
    <div id="print-area" class="container">

        <!-- KOP SURAT RESMI -->
        <div class="header-kop">
            <!-- Logo Tut Wuri Handayani -->
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9c/Logo_of_Ministry_of_Education_and_Culture_of_Republic_of_Indonesia.svg/400px-Logo_of_Ministry_of_Education_and_Culture_of_Republic_of_Indonesia.svg.png" alt="Logo" class="logo" crossorigin="anonymous">

            <div class="kop-text">
                <h3>Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi</h3>
                <h1>Balai Penjaminan Mutu Pendidikan (BPMP)</h1>
                <p>Jalan Raya Pendidikan No. 1, Kota Kendari, Sulawesi Tenggara</p>
            </div>
        </div>

        <div class="report-title">
            <h2>Laporan Kinerja Harian Pegawai</h2>
            <p>Periode Cetak: {{ now()->isoFormat('D MMMM Y') }}</p>
        </div>

        <!-- Tabel Data Logbook -->
        <table>
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 15%;">Nama Pegawai</th>
                    <th style="width: 10%;">Waktu & Tanggal</th> <!-- Digabung -->
                    <th style="width: 15%;">Lokasi</th>
                    <th style="width: 18%;">Sasaran SKP</th>
                    <th style="width: 20%;">Uraian Kegiatan</th>
                    <th style="width: 10%;">Output</th>
                    <th style="width: 8%;">Bukti</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $index => $log)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>

                    <!-- Kolom Nama -->
                    <td>
                        <strong>{{ $log->user->name }}</strong><br>
                        <span style="font-size: 8pt; color: #555;">{{ $log->user->email }}</span>
                    </td>

                    <!-- Kolom Waktu & Tanggal -->
                    <td style="text-align: center;">
                        <span style="font-weight: bold;">{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}</span><br>
                        <span style="font-size: 9pt;">
                            {{ \Carbon\Carbon::parse($log->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($log->jam_selesai)->format('H:i') }}
                        </span>
                    </td>

                    <!-- Kolom Lokasi -->
                    <td>
                        {{ $log->lokasi }}
                    </td>

                    <!-- Kolom Sasaran SKP -->
                    <td>
                        {{ $log->sasaran_pekerjaan }}
                    </td>

                    <!-- Kolom Kegiatan -->
                    <td>
                        <div style="text-align: justify;">{{ $log->kegiatan }}</div>
                    </td>

                    <!-- Kolom Output -->
                    <td>
                        {{ $log->output }}
                    </td>

                    <!-- Kolom Bukti -->
                    <td style="text-align: center;">
                        @if($log->bukti_foto)
                            <!-- Menggunakan base64 atau path langsung agar terbaca oleh html2pdf -->
                            <img src="{{ asset('storage/' . $log->bukti_foto) }}" style="width: 100%; max-width: 60px; height: auto; border: 1px solid #ccc;" crossorigin="anonymous">
                        @else
                            <span style="font-size: 9pt;">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">Tidak ada data logbook yang tersedia.</td>
                </tr>
                @endforelse
            </tbody>
        </table>


    </div>

    <!-- Script Export ke Word & PDF -->
    <script>
        // 1. Fungsi Export PDF (Menggunakan html2pdf)
        function exportToPDF() {
            // Ambil elemen yang akan di-convert
            var element = document.getElementById('print-area');

            // Konfigurasi PDF - Ubah ke Landscape agar muat semua kolom
            var opt = {
                margin:       [10, 10, 10, 10], // Margin (mm)
                filename:     'Laporan_Logbook_BPMP.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' } // Ubah ke Landscape
            };

            // Eksekusi
            html2pdf().set(opt).from(element).save();
        }

        // 2. Fungsi Export Word (Original)
        function exportToWord() {
            var header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' "+
                "xmlns:w='urn:schemas-microsoft-com:office:word' "+
                "xmlns='http://www.w3.org/TR/REC-html40'>"+
                "<head><meta charset='utf-8'><title>Export HTML to Word Document with JavaScript</title>"+
                "<style>body{font-family:'Times New Roman', serif; font-size:11pt;} table{border-collapse:collapse;width:100%;font-size:10pt;} td, th{border:1px solid black; padding:5px;} .signature-wrapper{display:flex;justify-content:space-between; margin-top:30px;} .signature-box{text-align:center; width:45%;}</style>"+
                "</head><body>";

            var footer = "</body></html>";
            var sourceHTML = header + document.getElementById("print-area").innerHTML + footer;
            var source = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(sourceHTML);
            var fileDownload = document.createElement("a");
            document.body.appendChild(fileDownload);
            fileDownload.href = source;
            fileDownload.download = 'Laporan_Logbook_BPMP.doc';
            fileDownload.click();
            document.body.removeChild(fileDownload);
        }
    </script>
</body>
</html>
