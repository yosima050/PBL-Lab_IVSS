<?php
session_start();
require_once __DIR__ . '/db.php';

// Cek Login & Role
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin_sistem', 'ketua_lab'])) {
    header("Location: login.php"); exit;
}

// ==================================================================================
// FUNGSI UNTUK HANDLE UPLOAD FILE (KE DUA FOLDER)
// ==================================================================================
function handleFileUpload($fieldName, $allowedExts, $maxSize = 5242880) {
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] == UPLOAD_ERR_NO_FILE) {
        return null;
    }
    
    $file = $_FILES[$fieldName];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // 1. Validasi
    if (!in_array($ext, $allowedExts)) {
        throw new Exception("Ekstensi file tidak valid: $fieldName");
    }
    if ($file['size'] > $maxSize) {
        throw new Exception("Ukuran file terlalu besar.");
    }
    

    $dir1 = '../uploads/proyek/'; 
    
    $dir2 = 'uploads/proyek/'; 
    
    // Buat folder jika belum ada
    if (!is_dir($dir1)) mkdir($dir1, 0777, true);
    if (!is_dir($dir2)) mkdir($dir2, 0777, true);
    
    // --- PERUBAHAN DI SINI (Gunakan Nama Asli) ---
    $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
    // Ganti spasi & karakter aneh dengan underscore (_)
    $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);
    // Format akhir: Judul_Laporan_170321123.pdf
    $fileName = $safeName . '_' . time() . '.' . $ext;
    // ----------------------------------------------
    
    $filePath = $uploadDir . $fileName;
    
    // 4. Upload ke Folder 1 (Utama)
    if (move_uploaded_file($file['tmp_name'], $dir1 . $fileName)) {
        
        // 5. Salin ke Folder 2 (Dashboard)
        // copy(sumber, tujuan)
        if (!copy($dir1 . $fileName, $dir2 . $fileName)) {
            // Opsional: Error handling jika gagal copy, tapi biasanya kita biarkan saja
            // asalkan file utama tersimpan.
        }
        
        return $fileName;
    } else {
        throw new Exception("Gagal mengupload file.");
    }
}


// ==================================================================================
// 1. INSERT PROYEK DOSEN (+ FILE & FOTO)
// ==================================================================================
if (isset($_POST['create_dosen'])) {
    try {
        $pdo->beginTransaction();

        $dosen_ids = $_POST['id_dosen'] ?? [];
        if (count($dosen_ids) === 0) throw new Exception("Wajib memilih minimal satu dosen.");
        
        $ketua_id = $dosen_ids[0];
        $id_asisten = !empty($_POST['mahasiswa_asisten']) ? $_POST['mahasiswa_asisten'] : []; // Array Asisten

        // --- PROSES UPLOAD ---
        $foto = handleFileUpload('foto_proyek', ['jpg', 'jpeg', 'png', 'webp'], 2097152); // Max 2MB
        $file = handleFileUpload('file_proyek', ['pdf', 'doc', 'docx', 'xls', 'xlsx'], 5242880); // Max 5MB

        // 1. Insert Proyek Utama via function fn_insert_proyek_dosen
        $stmt = $pdo->prepare("
            SELECT public.fn_insert_proyek_dosen(
                :judul, :deskripsi, :tahun, :tipe, :id_dosen,
                :tgl_mulai, :tgl_selesai, :penulis, :kategori, :lokasi,
                :foto, :file
            )
        ");
        $stmt->execute([
            ':judul' => $_POST['judul'],
            ':deskripsi' => $_POST['deskripsi'],
            ':tahun' => $_POST['tahun'],
            ':tipe' => $_POST['tipe'],
            ':id_dosen' => $ketua_id,
            ':tgl_mulai' => $_POST['tgl_mulai'],
            ':tgl_selesai' => $_POST['tgl_selesai'],
            ':penulis' => $_POST['nama_penulis'],
            ':kategori' => $_POST['kategori'],
            ':lokasi' => $_POST['lokasi'],
            ':foto' => $foto,
            ':file' => $file
        ]);
        $newId = (int)$stmt->fetchColumn();

        // 2. Insert Tim Dosen (tambahan anggota selain ketua)
        if (count($dosen_ids) > 1) {
            $stmtDosen = $pdo->prepare("INSERT INTO detail_proyek_dosen (id_dosen, id_proyek, tanggal_mulai_proyek_dosen, tanggal_selesai_proyek_dosen, nama_penulis_proyek_dosen, kategori_proyek_dosen, lokasi_proyek_dosen) VALUES (:id_dosen, :id_proyek, :mulai, :selesai, :penulis, :kategori, :lokasi)");
            foreach (array_slice($dosen_ids, 1) as $dosen_id) {
                $stmtDosen->execute([
                    ':id_dosen' => $dosen_id,
                    ':id_proyek' => $newId,
                    ':mulai' => $_POST['tgl_mulai'],
                    ':selesai' => $_POST['tgl_selesai'],
                    ':penulis' => $_POST['nama_penulis'],
                    ':kategori' => $_POST['kategori'],
                    ':lokasi' => $_POST['lokasi']
                ]);
            }
        }

        // 3. Insert Asisten Mahasiswa (Jika ada)
        if (!empty($id_asisten)) {
            $stmtAsisten = $pdo->prepare("INSERT INTO detail_proyek_mahasiswa (id_mahasiswa, id_proyek, tanggal_mulai_proyek_mahasiswa, tanggal_selesai_proyek_mahasiswa, nama_penulis_proyek_mahasiswa, kategori_proyek_mahasiswa, lokasi_proyek_mahasiswa) VALUES (:id_mhs, :id_proyek, :mulai, :selesai, 'Asisten', 'Asisten', :lokasi)");
            foreach ($id_asisten as $mhs_id) {
                $stmtAsisten->execute([
                    ':id_mhs' => $mhs_id,
                    ':id_proyek' => $newId,
                    ':mulai' => $_POST['tgl_mulai'],
                    ':selesai' => $_POST['tgl_selesai'],
                    ':lokasi' => $_POST['lokasi']
                ]);
            }
        }

        $pdo->commit();
        $_SESSION['flash'] = "Proyek Dosen berhasil ditambahkan!";
        header("Location: proyek.php"); exit;
    } catch (Exception $e) {
        $pdo->rollBack(); die("Error Insert Dosen: " . $e->getMessage());
    }
}

// ==================================================================================
// 2. UPDATE PROYEK DOSEN (+ FILE & FOTO)
// ==================================================================================
if (isset($_POST['update_dosen'])) {
    try {
        $pdo->beginTransaction();
        $id_proyek = $_POST['edit_id_proyek'];
        
        $dosen_ids = $_POST['edit_id_dosen'] ?? [];
        if (count($dosen_ids) === 0) throw new Exception("Tim tidak boleh kosong.");
        $ketua_id = $dosen_ids[0];

        // --- PROSES UPLOAD UPDATE ---
        $fotoBaru = handleFileUpload('foto_proyek', ['jpg', 'jpeg', 'png', 'webp'], 2097152);
        $fileBaru = handleFileUpload('file_proyek', ['pdf', 'doc', 'docx', 'xls', 'xlsx'], 5242880);

        // Ambil file lama jika tidak ada upload baru
        $stmtOld = $pdo->prepare("SELECT foto_proyek, file_proyek FROM proyek WHERE id_proyek = :id");
        $stmtOld->execute([':id' => $id_proyek]);
        $oldData = $stmtOld->fetch(PDO::FETCH_ASSOC);

        $finalFoto = $fotoBaru ? $fotoBaru : $oldData['foto_proyek'];
        $finalFile = $fileBaru ? $fileBaru : $oldData['file_proyek'];

        // 1. Update Proyek Utama
        $stmt = $pdo->prepare("UPDATE proyek SET judul_proyek=:judul, deskripsi_proyek=:deskripsi, tahun_proyek=:tahun, tipe_proyek=:tipe, id_dosen=:id_dosen, foto_proyek=:foto, file_proyek=:file WHERE id_proyek=:id");
        $stmt->execute([
            ':judul' => $_POST['edit_judul'],
            ':deskripsi' => $_POST['edit_deskripsi'],
            ':tahun' => $_POST['edit_tahun'],
            ':tipe' => $_POST['edit_tipe'],
            ':id_dosen' => $ketua_id,
            ':foto' => $finalFoto,
            ':file' => $finalFile,
            ':id' => $id_proyek
        ]);

        // 2. Reset & Re-Insert Tim Dosen
        $pdo->prepare("DELETE FROM detail_proyek_dosen WHERE id_proyek = :id")->execute([':id' => $id_proyek]);
        $stmtDosen = $pdo->prepare("INSERT INTO detail_proyek_dosen (id_dosen, id_proyek, tanggal_mulai_proyek_dosen, tanggal_selesai_proyek_dosen, nama_penulis_proyek_dosen, kategori_proyek_dosen, lokasi_proyek_dosen) VALUES (:id_dosen, :id_proyek, :mulai, :selesai, :penulis, :kategori, :lokasi)");
        foreach ($dosen_ids as $dosen_id) {
            $stmtDosen->execute([
                ':id_dosen' => $dosen_id,
                ':id_proyek' => $id_proyek,
                ':mulai' => $_POST['edit_tgl_mulai'],
                ':selesai' => $_POST['edit_tgl_selesai'],
                ':penulis' => $_POST['edit_nama_penulis'],
                ':kategori' => $_POST['edit_kategori'],
                ':lokasi' => $_POST['edit_lokasi']
            ]);
        }

        // 3. Reset & Re-Insert Asisten
        $pdo->prepare("DELETE FROM detail_proyek_mahasiswa WHERE id_proyek = :id")->execute([':id' => $id_proyek]);
        $asisten_ids = $_POST['edit_mahasiswa_asisten'] ?? [];
        if (!empty($asisten_ids)) {
            $stmtAsisten = $pdo->prepare("INSERT INTO detail_proyek_mahasiswa (id_mahasiswa, id_proyek, tanggal_mulai_proyek_mahasiswa, tanggal_selesai_proyek_mahasiswa, nama_penulis_proyek_mahasiswa, kategori_proyek_mahasiswa, lokasi_proyek_mahasiswa) VALUES (:id_mhs, :id_proyek, :mulai, :selesai, 'Asisten', 'Asisten', :lokasi)");
            foreach ($asisten_ids as $mhs_id) {
                $stmtAsisten->execute([
                    ':id_mhs' => $mhs_id,
                    ':id_proyek' => $id_proyek,
                    ':mulai' => $_POST['edit_tgl_mulai'],
                    ':selesai' => $_POST['edit_tgl_selesai'],
                    ':lokasi' => $_POST['edit_lokasi']
                ]);
            }
        }

        $pdo->commit();
        $_SESSION['flash'] = "Proyek Dosen berhasil diperbarui!";
        header("Location: proyek.php"); exit;
    } catch (Exception $e) {
        $pdo->rollBack(); die("Error Update Dosen: " . $e->getMessage());
    }
}

// ==================================================================================
// 3. INSERT PROYEK MAHASISWA (+ FILE & FOTO)
// ==================================================================================
if (isset($_POST['create_mahasiswa'])) {
    try {
        $pdo->beginTransaction();

        $mhs_ids = $_POST['id_mahasiswa'] ?? [];
        if (count($mhs_ids) === 0) throw new Exception("Wajib memilih minimal satu mahasiswa.");

        $ketua_id = $mhs_ids[0];
        $id_pembimbing = !empty($_POST['dosen_pembimbing']) ? $_POST['dosen_pembimbing'] : null;

        // --- PROSES UPLOAD ---
        $foto = handleFileUpload('foto_proyek', ['jpg', 'jpeg', 'png', 'webp'], 2097152);
        $file = handleFileUpload('file_proyek', ['pdf', 'doc', 'docx', 'xls', 'xlsx'], 5242880);

        // 1. Insert Proyek Utama via function fn_insert_proyek_mahasiswa
        $stmt = $pdo->prepare("
            SELECT public.fn_insert_proyek_mahasiswa(
                :judul, :deskripsi, :tahun, :tipe, :id_mhs, :id_pembimbing,
                :tgl_mulai, :tgl_selesai, :penulis, :kategori, :lokasi,
                :foto, :file
            )
        ");
        $stmt->execute([
            ':judul' => $_POST['judul'],
            ':deskripsi' => $_POST['deskripsi'],
            ':tahun' => $_POST['tahun'],
            ':tipe' => $_POST['tipe'],
            ':id_mhs' => $ketua_id,
            ':id_pembimbing' => $id_pembimbing,
            ':tgl_mulai' => $_POST['tgl_mulai'],
            ':tgl_selesai' => $_POST['tgl_selesai'],
            ':penulis' => $_POST['nama_penulis'],
            ':kategori' => $_POST['kategori'],
            ':lokasi' => $_POST['lokasi'],
            ':foto' => $foto,
            ':file' => $file
        ]);
        $newId = (int)$stmt->fetchColumn();

        // 2. Insert Detail Tim Mahasiswa (anggota tambahan selain ketua)
        if (count($mhs_ids) > 1) {
            $stmt2 = $pdo->prepare("INSERT INTO detail_proyek_mahasiswa (id_mahasiswa, id_proyek, tanggal_mulai_proyek_mahasiswa, tanggal_selesai_proyek_mahasiswa, nama_penulis_proyek_mahasiswa, kategori_proyek_mahasiswa, lokasi_proyek_mahasiswa) VALUES (:id_mhs, :id_proyek, :mulai, :selesai, :penulis, :kategori, :lokasi)");
            foreach (array_slice($mhs_ids, 1) as $single_id) {
                $stmt2->execute([
                    ':id_mhs' => $single_id,
                    ':id_proyek' => $newId,
                    ':mulai' => $_POST['tgl_mulai'],
                    ':selesai' => $_POST['tgl_selesai'],
                    ':penulis' => $_POST['nama_penulis'],
                    ':kategori' => $_POST['kategori'],
                    ':lokasi' => $_POST['lokasi']
                ]);
            }
        }

        $pdo->commit();
        $_SESSION['flash'] = "Proyek Mahasiswa berhasil ditambahkan!";
        header("Location: proyek.php"); exit;
    } catch (Exception $e) {
        $pdo->rollBack(); die("Error Insert Mahasiswa: " . $e->getMessage());
    }
}

// ==================================================================================
// 4. UPDATE PROYEK MAHASISWA (+ FILE & FOTO)
// ==================================================================================
if (isset($_POST['update_mahasiswa'])) {
    try {
        $pdo->beginTransaction();
        $id_proyek = $_POST['edit_id_proyek_mhs'];

        $mhs_ids = $_POST['edit_id_mahasiswa'] ?? [];
        if (count($mhs_ids) === 0) throw new Exception("Tim tidak boleh kosong.");

        $ketua_id = $mhs_ids[0];
        $id_pembimbing = !empty($_POST['edit_dosen_pembimbing']) ? $_POST['edit_dosen_pembimbing'] : null;

        // --- PROSES UPLOAD UPDATE ---
        // Perhatikan nama input file di modal EDIT proyek mahasiswa (proyek.php) harus sesuai
        // Defaultnya biasanya tidak ada input file di modal edit mahasiswa di kode sebelumnya
        // Jika Anda belum menambah input file di modal EDIT mahasiswa, fungsi ini akan return null (aman)
        $fotoBaru = handleFileUpload('foto_proyek', ['jpg', 'jpeg', 'png', 'webp'], 2097152);
        $fileBaru = handleFileUpload('file_proyek', ['pdf', 'doc', 'docx', 'xls', 'xlsx'], 5242880);

        // Ambil data lama
        $stmtOld = $pdo->prepare("SELECT foto_proyek, file_proyek FROM proyek WHERE id_proyek = :id");
        $stmtOld->execute([':id' => $id_proyek]);
        $oldData = $stmtOld->fetch(PDO::FETCH_ASSOC);

        $finalFoto = $fotoBaru ? $fotoBaru : $oldData['foto_proyek'];
        $finalFile = $fileBaru ? $fileBaru : $oldData['file_proyek'];

        // 1. Update Proyek Utama
        $stmt = $pdo->prepare("UPDATE proyek SET judul_proyek=:judul, deskripsi_proyek=:deskripsi, tahun_proyek=:tahun, tipe_proyek=:tipe, id_mahasiswa=:id_mhs, id_dosen=:id_pembimbing, foto_proyek=:foto, file_proyek=:file WHERE id_proyek=:id");
        $stmt->execute([
            ':judul' => $_POST['edit_judul_mhs'],
            ':deskripsi' => $_POST['edit_deskripsi_mhs'],
            ':tahun' => $_POST['edit_tahun_mhs'],
            ':tipe' => $_POST['edit_tipe_mhs'],
            ':id_mhs' => $ketua_id,
            ':id_pembimbing' => $id_pembimbing,
            ':foto' => $finalFoto,
            ':file' => $finalFile,
            ':id' => $id_proyek
        ]);

        // 2. Update Detail
        $pdo->prepare("DELETE FROM detail_proyek_mahasiswa WHERE id_proyek = :id")->execute([':id' => $id_proyek]);
        $stmt2 = $pdo->prepare("INSERT INTO detail_proyek_mahasiswa (id_mahasiswa, id_proyek, tanggal_mulai_proyek_mahasiswa, tanggal_selesai_proyek_mahasiswa, nama_penulis_proyek_mahasiswa, kategori_proyek_mahasiswa, lokasi_proyek_mahasiswa) VALUES (:id_mhs, :id_proyek, :mulai, :selesai, :penulis, :kategori, :lokasi)");
        
        foreach ($mhs_ids as $single_id) {
            $stmt2->execute([
                ':id_mhs' => $single_id,
                ':id_proyek' => $id_proyek,
                ':mulai' => $_POST['edit_tgl_mulai'],
                ':selesai' => $_POST['edit_tgl_selesai'],
                ':penulis' => $_POST['edit_nama_penulis_mhs'],
                ':kategori' => $_POST['edit_kategori_mhs'],
                ':lokasi' => $_POST['edit_lokasi_mhs']
            ]);
        }

        $pdo->commit();
        $_SESSION['flash'] = "Proyek Mahasiswa berhasil diperbarui!";
        header("Location: proyek.php"); exit;
    } catch (Exception $e) {
        $pdo->rollBack(); die("Error Update Mahasiswa: " . $e->getMessage());
    }
}

// ==============================================
// 5. DELETE PROYEK (Hapus Database + File Fisik)
// ==============================================
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        // 1. AMBIL NAMA FILE DULU SEBELUM DATA DIHAPUS
        $stmtGet = $pdo->prepare("SELECT foto_proyek, file_proyek FROM proyek WHERE id_proyek = :id");
        $stmtGet->execute([':id' => $id]);
        $data = $stmtGet->fetch(PDO::FETCH_ASSOC);

        // 2. HAPUS FILE FISIK JIKA ADA
        if ($data) {
            // Tentukan Folder (Sesuai settingan upload Anda)
            $folderAdmin     = __DIR__ . 'uploads/proyek/';
            $folderDashboard = __DIR__ . '../uploads/proyek/';

            // Hapus Foto
            if (!empty($data['foto_proyek'])) {
                $fotoAdmin = $folderAdmin . $data['foto_proyek'];
                $fotoDash  = $folderDashboard . $data['foto_proyek'];

                if (file_exists($fotoAdmin)) unlink($fotoAdmin); // Hapus dari Admin
                if (file_exists($fotoDash))  unlink($fotoDash);  // Hapus dari Dashboard
            }

            // Hapus File Dokumen
            if (!empty($data['file_proyek'])) {
                $fileAdmin = $folderAdmin . $data['file_proyek'];
                $fileDash  = $folderDashboard . $data['file_proyek'];

                if (file_exists($fileAdmin)) unlink($fileAdmin);
                if (file_exists($fileDash))  unlink($fileDash);
            }
        }

        // 3. BARU HAPUS DATA DARI DATABASE
        $pdo->beginTransaction();
        $pdo->prepare("DELETE FROM detail_proyek_dosen WHERE id_proyek = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM detail_proyek_mahasiswa WHERE id_proyek = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM proyek WHERE id_proyek = :id")->execute([':id' => $id]);
        
        $pdo->commit();
        $_SESSION['flash'] = "Proyek dan file berhasil dihapus.";
        header("Location: proyek.php"); exit;

    } catch (PDOException $e) {
        $pdo->rollBack(); 
        die("Error Delete: " . $e->getMessage());
    }
}

header("Location: proyek.php"); exit;
?>