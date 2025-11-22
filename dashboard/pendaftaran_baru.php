<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pendaftaran Baru - Admin Sistem</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
  <div class="flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg h-screen p-6">
      <h2 class="text-2xl font-semibold mb-6">Admin Sistem</h2>
      <nav class="space-y-3">
        <a href="#" class="block p-2 rounded hover:bg-blue-100">Dashboard</a>
        <a href="#" class="block p-2 rounded bg-blue-600 text-white">Pendaftaran Baru</a>
        <a href="#" class="block p-2 rounded hover:bg-blue-100">Daftar User</a>
        <a href="#" class="block p-2 rounded hover:bg-blue-100">Pengaturan</a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-10">
      <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-xl p-8">
        <h1 class="text-3xl font-semibold text-gray-800 mb-6">Pendaftaran Baru</h1>

        <!-- Form -->
        <form class="grid grid-cols-2 gap-6">
          <!-- ID Users -->
          <div class="col-span-2">
            <label class="block text-gray-700 mb-2">ID Users</label>
            <input type="text" class="w-full p-3 border rounded-lg focus:ring focus:ring-blue-300" placeholder="Masukkan ID User" />
          </div>

          <!-- NIM -->
          <div>
            <label class="block text-gray-700 mb-2">NIM</label>
            <input type="text" class="w-full p-3 border rounded-lg" placeholder="Masukkan NIM" />
          </div>

          <!-- Nama Mahasiswa -->
          <div>
            <label class="block text-gray-700 mb-2">Nama Mahasiswa</label>
            <input type="text" class="w-full p-3 border rounded-lg" placeholder="Masukkan Nama" />
          </div>

          <!-- Prodi -->
          <div>
            <label class="block text-gray-700 mb-2">Prodi</label>
            <input type="text" class="w-full p-3 border rounded-lg" placeholder="Masukkan Prodi" />
          </div>

          <!-- Email Mahasiswa -->
          <div>
            <label class="block text-gray-700 mb-2">Email Mahasiswa</label>
            <input type="email" class="w-full p-3 border rounded-lg" placeholder="Email" />
          </div>

          <!-- Status Mahasiswa -->
          <div>
            <label class="block text-gray-700 mb-2">Status Mahasiswa</label>
            <select class="w-full p-3 border rounded-lg">
              <option value="Aktif">Aktif</option>
              <option value="Non-Aktif">Non-Aktif</option>
            </select>
          </div>

          <!-- Nama Dosen -->
          <div>
            <label class="block text-gray-700 mb-2">Nama Dosen Pembimbing</label>
            <input type="text" class="w-full p-3 border rounded-lg" placeholder="Masukkan Nama Dosen" />
          </div>

          <!-- Diteruskan Oleh -->
          <div>
            <label class="block text-gray-700 mb-2">Diteruskan Oleh</label>
            <input type="text" class="w-full p-3 border rounded-lg" placeholder="Admin / Petugas" />
          </div>

          <!-- Disetujui Oleh -->
          <div>
            <label class="block text-gray-700 mb-2">Disetujui Oleh</label>
            <input type="text" class="w-full p-3 border rounded-lg" placeholder="Nama Penyetujui" />
          </div>

          <!-- Password -->
          <div class="col-span-2">
            <label class="block text-gray-700 mb-2">Password Akun</label>
            <input type="password" class="w-full p-3 border rounded-lg" placeholder="Masukkan Password" />
          </div>

          <!-- Buttons -->
          <div class="col-span-2 flex justify-end space-x-4 mt-4">
            <button type="reset" class="px-6 py-3 bg-gray-300 rounded-lg hover:bg-gray-400">Reset</button>
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
          </div>
        </form>
      </div>
    </main>
  </div>
</body>
</html>