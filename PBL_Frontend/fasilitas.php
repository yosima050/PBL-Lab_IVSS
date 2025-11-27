<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Fasilitas 5 Kolom - Warna Kustom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Definisi Warna BARU, disesuaikan dengan anggota.php */
        :root {
            --color-dark-blue: #0047AB; /* Warna Bingkai dan Garis Pemisah (Tidak dipakai untuk border tabel) */
            --color-light-blue: #DFECFF; 
            
            /* Warna dari anggota.php */
            --color-table-header-border: #004C99; /* Biru Tua untuk Header dan Border */
            --color-table-striped: #F5F9FF; /* Biru Muda untuk Striping */
        }

        /* Gaya Kustom untuk Tampilan yang Persis dan Rapi */
        body {
            background-color: #f8f9fa; 
            padding: 20px;
        }
        
        /* Gaya Tombol Mahasiswa (Kuning) */
        .btn-mahasiswa-custom {
            background-color: #F9D723; /* Kuning (warning) */
            color: #212529; /* Teks gelap */
            font-weight: bold;
            border: none;
            padding: 8px 15px;
            border-radius: .25rem;
            margin-bottom: 20px;
        }

        /* Gaya Area Pencarian dan Filter */
        .search-filter-row {
            margin-top: 10px;
            margin-bottom: 20px;
        }
        
        /* Gaya Input Pencarian */
        .input-search-custom {
            /* Input Group di gambar baru tidak menggunakan border terpisah untuk ikon */
            border-right: none !important; 
            border-color: #ced4da; /* Border abu-abu standar */
            border-radius: .25rem 0 0 .25rem !important; 
            padding-left: 3rem !important; /* Ruang untuk ikon */
        }

        /* Gaya Tombol Filter */
        .btn-filter-custom {
            /* Meniru tampilan Filter di gambar baru */
            background-color: #F5F9FF; /* Latar belakang abu-abu muda */
            color: #000; /* Teks hitam/gelap */
            border: none;
            padding: .375rem 1rem;
            border-radius: 0 .25rem .25rem 0; /* Corner kanan melengkung */
        }

        /* Container Input dengan Ikon (agar ikon menyatu dengan border input) */
        .input-group-custom {
            position: relative;
        }
        .input-group-custom .fa-search {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            color: #6c757d;
        }


        /* --- GAYA TABEL BARU (DISESUAIKAN DENGAN anggota.php) --- */
        
        /* Hapus container border luar tebal yang lama */
        .table-custom-container {
            /* HANYA sebagai div pembungkus jika diperlukan */
            border: none; 
            border-radius: 0;
            overflow-x: auto; /* Tambahkan agar responsif di layar kecil */
        }

        .table-custom {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse; /* Penting untuk border 1px */
        }
        
        /* Header Tabel (Latar Belakang Biru Tua/Navy) */
        .table-custom thead th {
            background-color: var(--color-table-header-border); /* Warna Biru Tua #004C99 */
            color: #fff; /* Teks Putih */
            font-weight: bold;
            border: 1px solid var(--color-table-header-border); /* Border 1px Biru Tua */
            height: 38px; /* Disesuaikan dengan anggota.php */
            padding: 0.5rem;
            text-align: center; /* Sesuaikan dengan anggota.php */
        }


        /* Border Sel (Biru Tua 1px) */
        .table-custom th, 
        .table-custom td {
            border: 1px solid var(--color-table-header-border); /* Border 1px Biru Tua */
            height: 38px; /* Disesuaikan dengan anggota.php */
            padding: 0.5rem;
            width: 20%; 
            font-weight: normal; /* Hapus bold pada sel data */
            text-align: center; /* Tambahkan agar teks di tengah */
        }

        /* Striping Baris (Baris Ganjil) - Biru Muda Terang #E6F3FF */
        .table-custom tbody tr:nth-child(odd) {
            background-color: var(--color-table-striped); /* #E6F3FF */
        }

        /* Striping Baris (Baris Genap) - Putih */
        .table-custom tbody tr:nth-child(even) {
            background-color: #fff;
        }

        /* Hapus border kanan pada kolom terakhir dan bawah pada baris terakhir (sudah otomatis jika menggunakan border-collapse) */
        /* Biarkan ini di-override jika perlu penyesuaian spesifik */
        
    </style>
</head>
<body>

<div class="container">
    
    <button class="btn btn-mahasiswa-custom" type="button">Fasilitas dan Halaman Produk</button>

    <div class="row align-items-center search-filter-row">
        <div class="col-12 d-flex">
            <div class="input-group-custom flex-grow-1">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control input-search-custom" placeholder="Cari Kategori...">
            </div>
            
            <button class="btn btn-filter-custom" type="button">
                <i class="fas fa-filter"></i> Filter
            </button>
        </div>
    </div>
    
    <div class="table-custom-container">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width: 20%;">Kolom 1</th>
                    <th style="width: 20%;">Kolom 2</th>
                    <th style="width: 20%;">Kolom 3</th>
                    <th style="width: 20%;">Kolom 4</th>
                    <th style="width: 20%;">Kolom 5</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $jumlah_baris = 15;
                for ($i = 0; $i < $jumlah_baris; $i++): 
                ?>
                <tr>
                    <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> </tr>
                <?php endfor; ?>
                </tbody>
        </table>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>