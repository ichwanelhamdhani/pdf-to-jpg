const express = require('express');
const multer = require('multer');
const path = require('path');
const fs = require('fs');
const { convert } = require('pdf-poppler');
const cors = require('cors');

const app = express();
const port = 3000;

app.use(cors());

const upload = multer({
    dest: 'uploads/',
    limits: { fileSize: 20 * 1024 * 1024 } // 20MB
});

const mime = require('mime');

// === Folder hasil konversi ===
const outputDir = path.join(__dirname, '../public/output');
if (!fs.existsSync(outputDir)) fs.mkdirSync(outputDir, { recursive: true });

// Sajikan folder hasil sebagai statik (bisa diakses dari browser)
app.use('/output', express.static(outputDir));

// === Konversi PDF ke JPG ===
async function convertPdfToImages(pdfPath, originalName) {
    const baseName = path.parse(originalName).name.replace(/\s+/g, '_') + '_' + Date.now();
    const outputBase = path.join(outputDir, baseName);

    const options = {
        format: 'jpeg',
        out_dir: outputDir,
        out_prefix: baseName,
        page: null,
        scale: 1024
    };

    await convert(pdfPath, options);

    const files = fs.readdirSync(outputDir);
    const resultFiles = files
        .filter(file => file.startsWith(baseName) && file.endsWith('.jpg'))
        .map(file => `/output/${file}`); // public URL

    return resultFiles;
}

// === Otomatisasi hapus file lama ===
// const CLEANUP_INTERVAL = 60 * 60 * 1000; // 1 jam
// const FILE_EXPIRY = 60 * 60 * 1000;      // 1 jam
const CLEANUP_INTERVAL = 5 * 60 * 1000; // 1 jam
const FILE_EXPIRY = 5 * 60 * 1000;      // 1 jam

function cleanupOldFiles() {
    const now = Date.now();
    fs.readdir(outputDir, (err, files) => {
        if (err) return;
        files.forEach(file => {
            const filePath = path.join(outputDir, file);
            fs.stat(filePath, (err, stats) => {
                if (!err && (now - stats.mtimeMs > FILE_EXPIRY)) {
                    fs.unlink(filePath, err => {
                        if (!err) console.log('🧹 Deleted:', filePath);
                    });
                }
            });
        });
    });
}
setInterval(cleanupOldFiles, CLEANUP_INTERVAL);

// === Endpoint konversi ===
app.post('/convert', upload.array('pdfs', 10), async (req, res) => {
    const files = req.files;
    if (!files || files.length === 0)
        return res.status(400).json({ error: 'No files uploaded' });

    const results = [];

    for (const file of files) {
        try {
            const images = await convertPdfToImages(file.path, file.originalname);
            results.push({ originalName: file.originalname, images });
        } catch (err) {
            results.push({ originalName: file.originalname, error: err.message });
        } finally {
            fs.unlink(file.path, () => {});
        }
    }

    res.json({ results });
});

app.get('/download', (req, res) => {
  const fileName = req.query.file;
  if (!fileName) return res.status(400).send('File not specified');

  // Cegah akses ke path di luar folder output (keamanan)
  const safeName = path.basename(fileName);
  const filePath = path.join(outputDir, safeName);

  // Cek file ada
  if (!fs.existsSync(filePath)) return res.status(404).send('File not found');

  const fileMime = mime.getType(filePath) || 'application/octet-stream';
  res.setHeader('Content-Type', fileMime);
  res.setHeader('Content-Disposition', `attachment; filename="${safeName}"`);

  fs.createReadStream(filePath).pipe(res);
});


// === Start server ===
app.listen(port, () => {
    console.log(`✅ Server running at http://localhost:${port}`);
});
