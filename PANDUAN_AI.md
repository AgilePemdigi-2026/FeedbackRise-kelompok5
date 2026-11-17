# Menjalankan analisis AI FeedbackRise untuk project tugas

Folder: FeedbackRise-kelompok5 - Copy For Testing.

## 1. Token dan .env

Buka .env di root project (sejajar dengan artisan), bukan .env.example.
Jangan menyalin .env.example ke .env lagi karena bisa menimpa konfigurasi lama.

Isi GITHUB_TOKEN dengan fine-grained personal access token dari GitHub:
Settings > Developer settings > Personal access tokens > Fine-grained tokens.
Gunakan akses minimum untuk membaca repository publik, tanpa izin menulis,
serta tanggal kedaluwarsa. Token selalu memiliki akses baca ke repository publik.

Konfigurasi di .env:
    GITHUB_TOKEN=isi_token_sendiri
    GITHUB_ISSUE_LIMIT=20
    GITHUB_MAX_ISSUES=500
    GITHUB_ISSUE_STATE=open
    AI_SERVICE_URL=http://127.0.0.1:8001
    GEMINI_API_KEY=isi_key_sendiri
    GEMINI_MODEL=gemini-3.5-flash-lite

GEMINI_API_KEY yang sudah ada dapat dipertahankan. Setiap variabel cukup satu kali.
.env sudah masuk .gitignore. Jangan memasukkan token ke JavaScript atau membagikannya.

## 2. Terminal pertama: Laravel

Dari root project:
    php artisan config:clear
    php artisan serve --host=127.0.0.1 --port=8000

Biarkan terminal ini tetap berjalan.

## 3. Terminal kedua: Python

Dari root project:
    cd ai-service
    .\.venv\Scripts\python.exe -m uvicorn main:app --host 127.0.0.1 --port 8001 --reload

Menggunakan python.exe di .venv langsung tidak memerlukan aktivasi.
Dependency Python sudah terpasang di folder testing ini.
Jika menyiapkan komputer lain:
    py -m venv .venv
    .\.venv\Scripts\python.exe -m pip install -r requirements.txt

Jika mengubah .env, stop Uvicorn dengan Ctrl+C lalu jalankan lagi.
--reload tidak selalu membaca perubahan .env.

## 4. Pastikan kedua server hidup

Laravel: http://127.0.0.1:8000/home
AI service: http://127.0.0.1:8001/
Dokumentasi API untuk tes manual: http://127.0.0.1:8001/docs

Endpoint root AI hanya memeriksa proses dan keberadaan key, belum membuktikan
key/model/kuota valid. Pembuktian tersebut terjadi saat analisis sungguhan.

## 5. Analisis dari Home

Masukkan URL seperti https://github.com/scenario-labs/skills/issues.
Isi Batas issue = 20 dan Status = Open. Klik tombol panah satu kali dan tunggu.
Jika tidak ada issue terbuka, coba Open + Closed.

Ringkasan dan maksimal tiga rekomendasi tampil di bawah form, beserta jumlah
issue pendukung dan tautan buktinya. JSON juga dicetak di F12 > Console >
Hasil analisis. Pada F12 > Network, respons POST /api/github/issues memuat data yang sama.

## 6. Mengubah batas

Field Batas issue menentukan batas per analisis: coba 20, lalu 50, lalu 100.
GITHUB_ISSUE_LIMIT adalah nilai awal field. GITHUB_MAX_ISSUES adalah batas server
(maksimal aplikasi 500). Setelah mengubah .env, jalankan config:clear dan restart
Uvicorn. Angka 500 adalah batas aplikasi, bukan jaminan kapasitas kuota Gemini.

per_page=100 di GitHub adalah ukuran satu halaman, bukan batas total analisis.
Aplikasi mengambil halaman tambahan bila diperlukan sampai jumlah issue
biasa mencapai batas. Pull Request tidak dihitung. Maksimal 20 halaman dipindai.
Sampel diambil dari issue terbaru berdasarkan tanggal dibuat dengan status pilihan,
bukan semua issue dalam repository. Persentase memakai sampel yang benar-benar dianalisis.

## 7. Alur AI: TF-IDF dan K-Means

Laravel mengambil judul, body, nomor, dan URL issue. Komentar tidak diambil.
Python membatasi teks satu issue hingga 2.000 karakter untuk mengontrol input.
TF-IDF membentuk vektor bobot kata dan pasangan kata (unigram dan bigram), maksimal
10.000 fitur. K-Means mengelompokkan vektor tersebut secara lokal dengan
random_state=42 dan n_init=10.

Untuk input yang memadai, aplikasi menguji K=2 sampai maksimal 4, dibatasi oleh
jumlah sampel dan vektor unik. Nilai silhouette score tertinggi dipilih.
Input satu issue atau vektor identik membentuk satu kelompok; dua issue berbeda
ditangani secara khusus karena silhouette score tidak valid untuk dua label
pada dua sampel. Data yang hanya berupa tanda baca ditolak dengan pesan jelas.
Hasil JSON memuat clustering.best_k, clustering.best_silhouette_score,
clustering.silhouette_results, jumlah fitur, bentuk matriks, serta label cluster.

Tiga kelompok terbesar dipilih berdasarkan jumlah issue, lalu Gemini diminta
membuat ringkasan/rekomendasi dalam satu panggilan generatif.
Tidak ada panggilan API embeddings. Gemini dipakai setelah pengelompokan lokal.

Satu issue masuk satu kelompok. Jika hanya terbentuk satu atau dua kelompok,
aplikasi menampilkan rekomendasi sebanyak kelompok tersebut. Kelompok otomatis
belum tentu satu tema yang benar; periksa tautan bukti dan kata kunci kelompok.
Sebagian teks panjang dan contoh yang dikirim ke peringkas dibatasi, sehingga
detail di luar potongan tersebut mungkin tidak terwakili.

TF-IDF membandingkan kosakata, sehingga permintaan bermakna sama dengan bahasa
atau sinonim berbeda belum tentu tergabung. Keluaran Gemini berbahasa Indonesia
tidak berarti teks sudah diterjemahkan sebelum clustering.
Penerjemahan ke satu bahasa bisa menjadi prapemrosesan tambahan jika diizinkan
dalam tugas, tetapi belum diterapkan. Itu menambah permintaan API tersendiri.
Untuk evaluasi awal, gunakan issue yang dominan dalam bahasa yang sama.


## 8. Error yang umum

GITHUB_TOKEN belum diisi: isi di .env root, jalankan config:clear.
Token GitHub tidak valid: buat token baru lalu ganti yang lama.
GitHub 403/429: lihat github_rate_limit pada respons dan tunggu waktu reset.
Kuota Gemini pada tahap ringkasan: periksa model GEMINI_MODEL di AI Studio.
Model tidak tersedia: isi nama model yang bisa digunakan project Anda lalu restart Uvicorn.
AI Service tidak terhubung: pastikan port 8001 berjalan dan AI_SERVICE_URL benar.
Respons bukan JSON: periksa terminal Laravel dan storage/logs/laravel.log.

GitHub token dan GEMINI_API_KEY memiliki kuota berbeda. Menambah token GitHub
tidak menambah kuota Gemini. Batas aktif Gemini harus dilihat pada project yang
sama dengan API key di https://aistudio.google.com/rate-limit.
