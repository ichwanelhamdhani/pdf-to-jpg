<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>PDF to JPG Converter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        #drop-zone {
            border: 2px dashed #ccc;
            padding: 30px;
            text-align: center;
            color: #888;
            transition: border 0.3s ease;
        }

        #drop-zone.dragover {
            border-color: #0d6efd;
            background-color: #eaf1ff;
            color: #0d6efd;
        }

        .image-wrapper img {
            max-height: 250px;
            object-fit: contain;
        }
    </style>
</head>

<body class="p-4">
    <div class="container">
        <h2 class="mb-4">Konversi PDF ke Gambar (JPG)</h2>

        <form id="uploadForm" enctype="multipart/form-data">
            <div id="drop-zone" class="mb-3">
                Seret & letakkan file PDF di sini, atau klik untuk memilih.
                <input type="file" id="fileInput" name="pdfs" accept="application/pdf" multiple style="display: none;">
            </div>

            <ul id="file-list" class="list-group mb-3">
                <li class="list-group-item text-muted">Belum ada file dipilih</li>
            </ul>

            <div id="progress-wrapper" class="progress mb-3" style="display: none;">
                <div id="progress-bar" class="progress-bar progress-bar-striped" role="progressbar" style="width: 0%;">0%</div>
            </div>

            <button id="submitFiles" type="submit" class="btn btn-primary">Upload & Konversi</button>
            <button type="button" class="btn btn-danger" id="resetBtn">Reset</button>
            <button id="downloadSelected" class="btn btn-success">Download Terpilih (ZIP)</button>

        </form>

        <hr class="my-4" />

        <div id="result" class="row"></div>
    </div>

    <script>
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('fileInput');
        const fileList = document.getElementById('file-list');

        let droppedFiles = [];

        // Buka file dialog saat klik dropzone
        dropZone.addEventListener('click', () => fileInput.click());

        // Saat file di-drop
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');

            const dropped = Array.from(e.dataTransfer.files).filter(f => f.type === 'application/pdf');
            const duplicateFiles = dropped.filter(newFile => droppedFiles.some(existing => existing.name === newFile.name));

            if (duplicateFiles.length > 0) {
                Swal.fire('Duplikat Ditemukan', `File berikut sudah ditambahkan:\n\n${duplicateFiles.map(f => f.name).join('\n')}`, 'warning');
            }

            const uniqueFiles = dropped.filter(newFile => !droppedFiles.some(existing => existing.name === newFile.name));
            droppedFiles = droppedFiles.concat(uniqueFiles);
            updateFileList();
        });

        // Saat pilih dari file input
        fileInput.addEventListener('change', function() {
            const newFiles = Array.from(fileInput.files).filter(f => f.type === 'application/pdf');
            const duplicateFiles = newFiles.filter(newFile => droppedFiles.some(existing => existing.name === newFile.name));

            if (duplicateFiles.length > 0) {
                Swal.fire('Duplikat Ditemukan', `File berikut sudah ditambahkan:\n\n${duplicateFiles.map(f => f.name).join('\n')}`, 'warning');
            }

            const uniqueFiles = newFiles.filter(newFile => !droppedFiles.some(existing => existing.name === newFile.name));
            droppedFiles = droppedFiles.concat(uniqueFiles);
            updateFileList();
        });

        function updateFileList() {
            fileList.innerHTML = '';
            if (droppedFiles.length === 0) {
                fileList.innerHTML = '<li class="list-group-item text-muted">Belum ada file dipilih</li>';
                return;
            }
            droppedFiles.forEach(file => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `${file.name}<span class="badge bg-secondary">${(file.size / 1024).toFixed(1)} KB</span>`;
                fileList.appendChild(li);
            });
        }

        // Submit
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            // Hanya proses jika tombol submitFiles yang diklik
            if (e.submitter && e.submitter.id !== 'submitFiles') {
                return;
            }
            e.preventDefault();

            const formData = new FormData();
            droppedFiles.forEach(file => formData.append('pdfs', file));

            const xhr = new XMLHttpRequest();
            const progressWrapper = document.getElementById('progress-wrapper');
            const progressBar = document.getElementById('progress-bar');
            const result = document.getElementById('result');

            progressWrapper.style.display = 'block';
            progressBar.style.width = '0%';
            progressBar.innerText = '0%';
            result.innerHTML = '';

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percent + '%';
                    progressBar.innerText = percent + '%';
                }
            };

            xhr.onload = function() {
                progressWrapper.style.display = 'none';
                try {
                    const data = JSON.parse(xhr.responseText);
                    if (!data.results) throw new Error('Format tidak valid');

                    data.results.forEach(fileResult => {
                        if (fileResult.images) {
                            const title = document.createElement('h5');
                            title.innerText = `Hasil dari: ${fileResult.originalName}`;
                            result.appendChild(title);

                            fileResult.images.forEach(img => {
                                const imageUrl = `http://localhost:3000${img}`;
                                const fileName = img.split('/').pop();
                                const downloadUrl = `http://localhost:3000/download?file=${encodeURIComponent(fileName)}`;

                                const col = document.createElement('div');
                                col.className = 'col-md-3 mb-3';
                                col.innerHTML = `
                  <div class="image-wrapper border rounded shadow-sm d-flex flex-column align-items-center">
                    <input type="checkbox" class="download-checkbox" data-url="${fileName}" style="margin-top: 8px; margin-bottom: 8px; float: left;" />
                    <img src="${imageUrl}" class="img-fluid mb-2" />
                    <a href="${downloadUrl}" class="btn btn-sm btn-primary mb-2" download>Download</a>
                  </div>`;
                                result.appendChild(col);
                            });
                        } else {
                            const errorMsg = document.createElement('div');
                            errorMsg.innerText = `❌
                                                            Gagal konversi $ {
                                                                fileResult.originalName
                                                            }: $ {
                                                                fileResult.error
                                                            }
                                                            `;
                            errorMsg.classList.add('text-danger', 'mb-2');
                            result.appendChild(errorMsg);
                        }
                    });

                    Swal.fire({
                        title: 'Selesai',
                        text: 'Semua proses selesai.',
                        icon: 'success',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    const imageUrls = [];

                    data.results.forEach(fileResult => {
                        if (fileResult.images) {
                            fileResult.images.forEach(img => {
                                imageUrls.push(`http://localhost:3000${img}`);
                            });
                        }
                    });

                    monitorFiles(imageUrls);

                } catch (err) {
                    Swal.fire('Gagal', 'Gagal membaca respon dari server.', 'error');
                }
            };

            xhr.onerror = function() {
                progressWrapper.style.display = 'none';
                Swal.fire('Gagal', 'Terjadi kesalahan jaringan.', 'error');
            };

            xhr.open('POST', 'http://localhost:3000/convert', true);
            xhr.send(formData);
        });

        document.getElementById('resetBtn').addEventListener('click', function() {
            // Reset variabel file yang disimpan
            droppedFiles = [];

            // Reset input file
            document.getElementById('fileInput').value = '';

            // Kosongkan daftar file
            document.getElementById('file-list').innerHTML = '<li class="list-group-item text-muted">Belum ada file dipilih</li>';

            // Kosongkan hasil konversi
            document.getElementById('result').innerHTML = '';

            // Reset progress bar
            const progressWrapper = document.getElementById('progress-wrapper');
            const progressBar = document.getElementById('progress-bar');
            progressWrapper.style.display = 'none';
            progressBar.style.width = '0%';
            progressBar.innerText = '0%';

            Swal.fire({
                title: 'Direset',
                text: 'Form telah dikosongkan.',
                icon: 'info',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        });

        function monitorFiles(fileUrls, interval = 30000) { // default: cek tiap 30 detik
            const check = () => {
                Promise.all(fileUrls.map(url => fetch(url, {
                        method: 'HEAD'
                    })))
                    .then(responses => {
                        const allMissing = responses.every(r => !r.ok);
                        if (allMissing) {
                            Swal.fire({
                                icon: 'info',
                                title: 'File dihapus',
                                text: 'File hasil konversi sudah dihapus. Halaman akan dimuat ulang.',
                                timer: 3000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Gagal cek file:', err);
                    });
            };

            setInterval(check, interval);
        }

        document.getElementById('downloadSelected').addEventListener('click', async function() {
            const checked = Array.from(document.querySelectorAll('.download-checkbox:checked'));

            if (checked.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak ada file dipilih',
                    text: 'Silakan centang file yang ingin didownload.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const fileNames = checked.map(cb => cb.getAttribute('data-filename'));

            try {
                const res = await fetch('http://localhost:3000/download-zip', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        files: fileNames
                    })
                });

                if (!res.ok) throw new Error('Gagal membuat ZIP');

                const blob = await res.blob();
                const zipUrl = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = zipUrl;
                a.download = 'selected_files.zip';
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(zipUrl);
            } catch (err) {
                Swal.fire('Error', err.message, 'error');
            }
        });
    </script>
</body>

</html>