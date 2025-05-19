const express = require('express');
const multer = require('multer');
const path = require('path');
const fs = require('fs');
const { convert } = require('pdf-poppler');
const cors = require('cors');

const app = express();
const port = 3000;

app.use(cors());

// Sajikan folder output di luar backend-node
app.use('/output', express.static(path.join(__dirname, '../public/output')));

// Upload ke folder lokal (di backend-node/uploads)
const upload = multer({ dest: 'uploads/' });

app.post('/convert', upload.single('pdf'), async (req, res) => {
  const file = req.file;
  const outputFolderName = path.parse(file.filename).name; // gunakan hash unik dari multer
  const outputDir = path.join(__dirname, '../public/output/', outputFolderName);

  fs.mkdirSync(outputDir, { recursive: true });

  const options = {
    format: 'jpeg',
    out_dir: outputDir,
    out_prefix: path.parse(file.originalname).name.replace(/\s+/g, '_'),
    page: null,
  };

  try {
    await convert(file.path, options);
    fs.unlinkSync(file.path); // hapus PDF asli

    const images = fs.readdirSync(outputDir)
      .filter(f => f.endsWith('.jpg'))
      .map(img => `/output/${outputFolderName}/${img}`);

    res.json({ success: true, images });
  } catch (err) {
    res.status(500).json({ success: false, error: err.toString() });
  }
});

app.listen(port, () => {
  console.log(`✅ Server running at http://localhost:${port}`);
});
