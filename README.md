# 📄 PDF to JPEG Converter (Node.js)

Aplikasi web untuk mengonversi file PDF menjadi gambar JPEG secara cepat dan efisien. Dilengkapi fitur drag-and-drop, progress bar, preview hasil, download satuan atau ZIP, serta pembersihan file otomatis.

## 🚀 Fitur

- Upload banyak file PDF sekaligus
- Preview hasil konversi secara langsung
- Download per gambar atau gabung ZIP
- Progress bar real-time
- Drag and drop area
- Ceklis untuk download massal
- Otomatis hapus file lama setiap 5 menit

## 📦 Teknologi

- **Node.js + Express** — Backend dan REST API
- **Multer** — File upload
- **pdf-poppler** — Konversi PDF ke JPEG
- **JSZip** — Kompres file hasil ke ZIP
- **SweetAlert2** — Alert user-friendly
- **HTML + Bootstrap** — Tampilan antarmuka
- **JavaScript (XHR)** — Upload dan preview dinamis

## 🛠️ Instalasi

### 1. Clone Repo

```bash
git clone https://github.com/username/pdf-to-jpeg-converter.git
cd pdf-to-jpeg-converter
```

### 2. Install Dependency

```bash
npm install
```

### 3. Install Poppler (dibutuhkan oleh `pdf-poppler`)

#### Linux (Debian/Ubuntu)

```bash
sudo apt install poppler-utils
```

#### macOS (menggunakan brew)

```bash
brew install poppler
```

#### Windows

Download dari: [http://blog.alivate.com.au/poppler-windows/](http://blog.alivate.com.au/poppler-windows/)

Tambahkan path ke `poppler/bin` dalam `Environment Variables`.

### 4. Jalankan Server

```bash
npx nodemon index.js
```

Server akan berjalan di: [http://localhost:3000](http://localhost:3000)

## 📂 Struktur Direktori

```
backend-node/
├── uploads/               # Tempat file PDF di-upload
├── public/output/         # Hasil konversi JPG
├── index.js               # Server utama
└── services/
    └── converter.js       # Fungsi konversi PDF -> JPEG

public/
├── index.html             # Frontend utama
└── assets/                # CSS & JS tambahan
```

## 📌 Catatan

- Ukuran maksimal file: 20MB per PDF (dapat disesuaikan)
- File hasil akan otomatis dihapus setiap 5 menit
- Tidak ada database, hanya file system
- Konversi dilakukan per halaman PDF menjadi JPEG

## 📜 Lisensi

MIT License © [Nama Kamu]