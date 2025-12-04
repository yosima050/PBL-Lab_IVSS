<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda - Lab IVSS</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="css/Agenda.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    
    <div class="banner1"></div>
    
    <div class="container my-5">
        <div class="row">
            <!-- Main Content Area -->
            <div class="col-lg-8">
                <div class="announcement-card">
                    <div class="announcement-header">
                        <div class="announcement-meta">
                            <span><i class="far fa-comment"></i> 00 Comment</span>
                            <span><i class="far fa-clock"></i> 5 months ago</span>
                        </div>
                    </div>
                    
                    <div class="announcement-content">
                        <p><strong>Diseminasi Hasil Magang Semester 6 Tahun Ajaran 2024/2025 akan dilaksanakan pada:</strong></p>
                        <table class="info-table">
                            <tr>
                                <td>Hari/Tanggal</td>
                                <td>: Selasa, 8 Juni 2025</td>
                            </tr>
                            <tr>
                                <td>Waktu</td>
                                <td>: 08.00 – 12.00 WIB</td>
                            </tr>
                            <tr>
                                <td>Tempat</td>
                                <td>: https://jti.polinema.ac.id/ruang_daring</td>
                            </tr>
                        </table>
                        
                        <h5 class="mt-4">Pembagian Ruang:</h5>
                        
                        <div class="table-responsive">
                            <table class="schedule-table">
                                <thead>
                                    <tr>
                                        <th>SESI</th>
                                        <th>JAM</th>
                                        <th>NIM</th>
                                        <th>NAMA</th>
                                        <th>PRODI</th>
                                        <th>PERUSAHAAN</th>
                                        <th>ROOM</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>08.00-08.20</td>
                                        <td>2241720088</td>
                                        <td>Abdul Aziz</td>
                                        <td>D4 TI</td>
                                        <td>Venturo</td>
                                        <td>1</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>08.20-08.40</td>
                                        <td>2241720095</td>
                                        <td>Ana Bellatus Mustaqfiro</td>
                                        <td>D4 TI</td>
                                        <td>Venturo</td>
                                        <td>1</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>08.40-09.00</td>
                                        <td>2241720091</td>
                                        <td>Gaco Razan Kamil</td>
                                        <td>D4 TI</td>
                                        <td>Sekawan Media</td>
                                        <td>1</td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>09.00-09.20</td>
                                        <td>2241720160</td>
                                        <td>Maulidin Zakaria</td>
                                        <td>D4 TI</td>
                                        <td>Sekawan Media</td>
                                        <td>1</td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>08.00-08.20</td>
                                        <td>2241760074</td>
                                        <td>ARYO WAHYU NUGROHO</td>
                                        <td>D4 SIB</td>
                                        <td>MaxChat</td>
                                        <td>2</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Search Box -->
                <div class="sidebar-widget">
                    <h4 class="widget-title">Search</h4>
                    <div class="search-box">
                        <input type="text" class="form-control" placeholder="Search...">
                        <button class="btn-search">Search</button>
                    </div>
                </div>
                
                <!-- Recent Posts -->
                <div class="sidebar-widget">
                    <h4 class="widget-title">Recent Posts</h4>
                    <div class="recent-posts">
                        <div class="recent-post-item">
                            <i class="fas fa-angle-right"></i>
                            <a href="#">Mahasiswa D4 Sistem Informasi Bisnis Jurusan Teknologi Informasi meraih Juara 1 ajang RK INK BLEND COMPETITION!</a>
                        </div>
                        <div class="recent-post-item">
                            <i class="fas fa-angle-right"></i>
                            <a href="#">Kabar membanggakan kembali hadir dari mahasiswa Program Studi Sistem Informasi Bisnis</a>
                        </div>
                        <div class="recent-post-item">
                            <i class="fas fa-angle-right"></i>
                            <a href="#">Prestasi kembali diraih oleh mahasiswa Program Studi Sistem Informasi Bisnis dalam ajang Lomba Cipta Nusantara</a>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Comments -->
                <div class="sidebar-widget">
                    <h4 class="widget-title">Recent Comments</h4>
                    <div class="recent-comments">
                        <div class="recent-comment-item">
                            <div class="comment-avatar">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="comment-content">
                                <div class="comment-author">John Doe</div>
                                <div class="comment-text">Great information! Thanks for sharing...</div>
                                <div class="comment-post">on <a href="#">Pengumuman Magang</a></div>
                            </div>
                        </div>
                        <div class="recent-comment-item">
                            <div class="comment-avatar">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="comment-content">
                                <div class="comment-author">Jane Smith</div>
                                <div class="comment-text">Kapan pendaftaran dibuka?</div>
                                <div class="comment-post">on <a href="#">Info Lomba</a></div>
                            </div>
                        </div>
                        <div class="recent-comment-item">
                            <div class="comment-avatar">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="comment-content">
                                <div class="comment-author">Ahmad Rizki</div>
                                <div class="comment-text">Selamat untuk para pemenang!</div>
                                <div class="comment-post">on <a href="#">Prestasi Mahasiswa</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Posts Section -->
    <section class="related-posts-section">
        <div class="container">
            <div class="related-posts-header">
                <h2>Related Post</h2>
                <div class="navigation-arrows">
                    <button class="nav-arrow prev-arrow"><i class="fas fa-arrow-left"></i></button>
                    <button class="nav-arrow next-arrow"><i class="fas fa-arrow-right"></i></button>
                </div>
            </div>
            
            <div class="related-posts-slider">
                <!-- Post 1 -->
                <div class="related-post-card">
                    <div class="post-image">
                        <img src="Asset/bp3.jpg" alt="Kegiatan Prastudi">
                    </div>
                    <div class="post-content">
                        <div class="post-meta">
                            <span><i class="far fa-comment"></i> 00 Comment</span>
                            <span><i class="far fa-clock"></i> 3 months ago</span>
                        </div>
                        <h3 class="post-title">Kegiatan Prastudi Mahasiswa Baru Tahun Akademik 2025/2026 Jurusan Teknologi Informasi Politeknik Negeri Malang</h3>
                        <p class="post-excerpt">Malang, 20 Agustus 2025 – Jurusan Teknologi Informasi Politeknik Negeri Malang menyelenggarakan Kegiatan Prastudi mahasiswa baru sebagai bagian dari persiapan penyambutan mahasiswa baru tahun akademik 2025/2026. Kegiatan yang digelar di Aula Lantai...</p>
                    </div>
                </div>
                
                <!-- Post 2 -->
                <div class="related-post-card">
                    <div class="post-image">
                        <img src="Asset/bp2.jpg" alt="Sertifikasi Internasional">
                    </div>
                    <div class="post-content">
                        <div class="post-meta">
                            <span><i class="far fa-comment"></i> 00 Comment</span>
                            <span><i class="far fa-clock"></i> 5 months ago</span>
                        </div>
                        <h3 class="post-title">Dosen dan Tendik Jurusan Teknologi Informasi Polinema Ikuti Sertifikasi Internasional ITC</h3>
                        <p class="post-excerpt">Malang, 15 Juli 2025 – Sebanyak 68 dosen dan tenaga kependidikan (tendik) Jurusan Teknologi Informasi (JTI) Politeknik Negeri Malang (Polinema) mengikuti kegiatan sertifikasi internasional dari International Test Center (ITC) pada hari Selasa,...</p>
                    </div>
                </div>
                
                <!-- Post 3 -->
                <div class="related-post-card">
                    <div class="post-image">
                        <img src="Asset/BP1.jpg" alt="Rapat Yudisium">
                    </div>
                    <div class="post-content">
                        <div class="post-meta">
                            <span><i class="far fa-comment"></i> 00 Comment</span>
                            <span><i class="far fa-clock"></i> 5 months ago</span>
                        </div>
                        <h3 class="post-title">Rapat Yudisium Tahap 1 Semester Genap 2024/2025 Jurusan Teknologi Informasi</h3>
                        <p class="post-excerpt">Malang, 11 Juli 2025 – Jurusan Teknologi Informasi (JTI) Politeknik Negeri Malang (Polinema) sukses menyelenggarakan Rapat Yudisium Semester Genap Tahun Akademik 2024/2025 Tahap 1. Kegiatan penting ini dilaksanakan pada Jumat 11 Juli...</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <?php include 'footer.php'; ?>
</body>
</html>