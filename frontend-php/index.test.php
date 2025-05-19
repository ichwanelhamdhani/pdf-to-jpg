<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Convert PDF to JPG</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    .image-wrapper {
      position: relative;
      overflow: hidden;
    }

    .download-btn {
      position: absolute;
      bottom: 10px;
      right: 10px;
      opacity: 0;
      transition: opacity 0.3s;
    }

    .image-wrapper:hover .download-btn {
      opacity: 1;
    }
  </style>
</head>

<body class="bg-light">

  <div class="container mt-5">
    <h3 class="mb-4">Convert PDF to JPG</h3>
    <form id="uploadForm">
      <div class="mb-3">
        <input type="file" name="pdfs[]" id="fileInput" accept="application/pdf" multiple />
      </div>
      <div class="mb-3" id="progress-wrapper" style="display: none;">
        <div class="progress">
          <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
            style="width: 0%">0%</div>
        </div>
      </div>
      <button class="btn btn-primary">Convert</button>
      <div id="preview-list" class="mt-3">
        <h5>📄 Preview File</h5>
        <ul id="file-list" class="list-group"></ul>
      </div>


    </form>

    <div id="result" class="mt-4 row"></div>
  </div>

  <script>
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const form = this;
      const fileInput = document.getElementById('fileInput');
      const formData = new FormData();

      // Ambil semua file PDF
      for (let i = 0; i < fileInput.files.length; i++) {
        formData.append('pdfs', fileInput.files[i]);
      }

      const xhr = new XMLHttpRequest();
      const progressWrapper = document.getElementById('progress-wrapper');
      const progressBar = document.getElementById('progress-bar');
      progressWrapper.style.display = 'block';
      progressBar.style.width = '0%';
      progressBar.innerText = '0%';

      xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
          const percent = Math.round((e.loaded / e.total) * 100);
          progressBar.style.width = percent + '%';
          progressBar.innerText = percent + '%';
        }
      };

      xhr.onload = function() {
        progressWrapper.style.display = 'none';

        const result = document.getElementById('result');
        result.innerHTML = '';

        try {
          const data = JSON.parse(xhr.responseText);
          if (data.results) {
            data.results.forEach(file => {
              file.images?.forEach(img => {
                const imageUrl = `http://localhost:3000${img}`;
                const col = document.createElement('div');
                col.className = 'col-md-4 mb-3';
                col.innerHTML = `
        <div class="image-wrapper border rounded shadow-sm">
          <img src="${imageUrl}" class="img-fluid" />
          <a href="http://localhost:3000/download?file=${encodeURIComponent(img.split('/').pop())}" class="btn btn-sm btn-primary mt-2">Download</a>
        </div>
      `;
                result.appendChild(col);
              });
            });

            Swal.fire('Selesai', 'Konversi berhasil!', 'success');
          } else {
            Swal.fire('Gagal', 'Respons tidak valid.', 'error');
          }


          Swal.fire('Selesai', 'Semua proses selesai.', 'success');
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

    document.getElementById('fileInput').addEventListener('change', function() {
      const fileList = document.getElementById('file-list');
      fileList.innerHTML = ''; // Clear list

      Array.from(this.files).forEach(file => {
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center';
        li.innerHTML = `
      ${file.name}
      <span class="badge bg-secondary">${(file.size / 1024).toFixed(1)} KB</span>
    `;
        fileList.appendChild(li);
      });

      if (!this.files.length) {
        fileList.innerHTML = '<li class="list-group-item text-muted">Belum ada file dipilih</li>';
      }
    });
  </script>


</body>

</html>