<?php
session_start();
require_once __DIR__ . '/db.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Hanya admin & ketua lab
if (!in_array($_SESSION['role'], ['admin_sistem', 'ketua_lab'])) {
    echo "Akses Ditolak!";
    exit;
}

// ==============================================
// ========== INSERT PROYEK DOSEN ===============
// ==============================================
if (isset($_POST['create_dosen'])) {

    $id_dosen = $_POST['id_dosen'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $tahun = $_POST['tahun'];
    $tipe = $_POST['tipe'];

    try {
        // Insert proyek
        $stmt = $pdo->prepare("
            INSERT INTO proyek (judul_proyek, deskripsi_proyek, tahun_proyek, tipe_proyek)
            VALUES (:judul, :deskripsi, :tahun, :tipe)
            RETURNING id_proyek
        ");

        $stmt->execute([
            ':judul' => $judul,
            ':deskripsi' => $deskripsi,
            ':tahun' => $tahun,
            ':tipe' => $tipe
        ]);

        $newProjectId = $stmt->fetchColumn();

        // Insert detail proyek dosen
        $stmt2 = $pdo->prepare("
            INSERT INTO detail_proyek_dosen (id_dosen, id_proyek)
            VALUES (:id_dosen, :id_proyek)
        ");

        $stmt2->execute([
            ':id_dosen' => $id_dosen,
            ':id_proyek' => $newProjectId
        ]);

        header("Location: proyek.php?success=dosen_added");
        exit;

    } catch (PDOException $e) {
        die("Insert Proyek Dosen Error: " . $e->getMessage());
    }
}

// ==============================================
// ============ UPDATE PROYEK DOSEN =============
// ==============================================
if (isset($_POST['update_dosen'])) {

    $id_proyek = $_POST['edit_id_proyek'];
    $id_dosen = $_POST['edit_id_dosen'];
    $judul = $_POST['edit_judul'];
    $deskripsi = $_POST['edit_deskripsi'];
    $tahun = $_POST['edit_tahun'];
    $tipe = $_POST['edit_tipe'];

    try {
        // Update tabel proyek
        $stmt = $pdo->prepare("
            UPDATE proyek
            SET judul_proyek = :judul,
                deskripsi_proyek = :deskripsi,
                tahun_proyek = :tahun,
                tipe_proyek = :tipe
            WHERE id_proyek = :id_proyek
        ");

        $stmt->execute([
            ':judul' => $judul,
            ':deskripsi' => $deskripsi,
            ':tahun' => $tahun,
            ':tipe'   => $tipe,
            ':id_proyek' => $id_proyek
        ]);

        // Update relasi dosen
        $stmt2 = $pdo->prepare("
            UPDATE detail_proyek_dosen
            SET id_dosen = :id_dosen
            WHERE id_proyek = :id_proyek
        ");

        $stmt2->execute([
            ':id_dosen' => $id_dosen,
            ':id_proyek' => $id_proyek
        ]);

        header("Location: proyek.php?success=dosen_updated");
        exit;

    } catch (PDOException $e) {
        die("Update Proyek Dosen Error: " . $e->getMessage());
    }
}

// ==============================================
// ======= INSERT PROYEK MAHASISWA ===============
// ==============================================
if (isset($_POST['create_mahasiswa'])) {

    $id_mahasiswa = $_POST['id_mahasiswa'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $tahun = $_POST['tahun'];
    $tipe = $_POST['tipe'];
    $tgl_mulai = $_POST['tgl_mulai'];
    $tgl_selesai = $_POST['tgl_selesai'];
    $nama_penulis = $_POST['nama_penulis'];
    $kategori = $_POST['kategori'];
    $lokasi = $_POST['lokasi'];

    try {
        // Panggil function insert proyek mahasiswa
        $stmt = $pdo->prepare("
            SELECT fn_insert_proyek_mahasiswa(
                :judul,
                :deskripsi,
                :tahun,
                :tipe,
                :id_mahasiswa,
                :tgl_mulai,
                :tgl_selesai,
                :nama_penulis,
                :kategori,
                :lokasi
            )
        ");

        $stmt->execute([
            ':judul'        => $judul,
            ':deskripsi'    => $deskripsi,
            ':tahun'        => $tahun,
            ':tipe'         => $tipe,
            ':id_mahasiswa' => $id_mahasiswa,
            ':tgl_mulai'    => $tgl_mulai,
            ':tgl_selesai'  => $tgl_selesai,
            ':nama_penulis' => $nama_penulis,
            ':kategori'     => $kategori,
            ':lokasi'       => $lokasi
        ]);

        header("Location: proyek.php?success=mahasiswa_added");
        exit;

    } catch (PDOException $e) {
        die("Insert Proyek Mahasiswa Error: " . $e->getMessage());
    }
}

// ==============================================
// ================= DELETE =====================
// ==============================================
if (isset($_GET['delete'])) {
    $id_proyek = $_GET['delete'];

    try {
        // Hapus dari detail_proyek_dosen
        $pdo->prepare("DELETE FROM detail_proyek_dosen WHERE id_proyek = :id")
            ->execute([':id' => $id_proyek]);

        // Hapus dari detail_proyek_mahasiswa
        $pdo->prepare("DELETE FROM detail_proyek_mahasiswa WHERE id_proyek = :id")
            ->execute([':id' => $id_proyek]);

        // Hapus proyek utama
        $pdo->prepare("DELETE FROM proyek WHERE id_proyek = :id")
            ->execute([':id' => $id_proyek]);

        header("Location: proyek.php?success=deleted");
        exit;

    } catch (PDOException $e) {
        die("Delete Proyek Error: " . $e->getMessage());
    }
}

// Jika tidak ada aksi → kembali
header("Location: proyek.php");
exit;

?>