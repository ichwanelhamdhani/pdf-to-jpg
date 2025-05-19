<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Convert PDF to JPG</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style> .image-wrapper { position: relative; overflow: hidden; } .download-btn { position: absolute; bottom: 10px; right: 10px; opacity: 0; transition: opacity 0.3s; } .image-wrapper:hover .download-btn { opacity: 1; } </style>
</head>
<body class="bg-light">

<div class="container mt-5">
  <h3 class="mb-4">Convert PDF to JPG</h3>
  <form id="uploadForm">
    <div class="mb-3">
      <input type="file" name="pdf" class="form-control" required>
    </div>
    <div class="mb-3" id="progress-wrapper" style="display: none;">
        <div class="progress">
            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                style="width: 0%">0%</div>
        </div>
    </div>

    <button class="btn btn-primary">Convert</button>
  </form>

  <div id="result" class="mt-4 row"></div>
</div>

<!-- <script>
document.getElementById('uploadForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const formData = new FormData(this);

  Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

  const res = await fetch('http://localhost:3000/convert', {
    method: 'POST',
    body: formData
  });

  const data = await res.json();
  Swal.close();

  const result = document.getElementById('result');
  result.innerHTML = '';

  if (data.success) {
    data.images.forEach(img => {
      const col = document.createElement('div');
      col.className = 'col-md-4 mb-3';
      col.innerHTML = `<img src="${img}" class="img-fluid border rounded shadow-sm" />`;
      result.appendChild(col);
    });
    Swal.fire('Success', 'Conversion complete!', 'success');
  } else {
    Swal.fire('Error', data.error, 'error');
  }
});
</script> -->
<!-- <script>
document.getElementById('uploadForm').addEventListener('submit', function (e) {
  e.preventDefault();
  const form = this;
  const formData = new FormData(form);

  const xhr = new XMLHttpRequest();
  const progressWrapper = document.getElementById('progress-wrapper');
  const progressBar = document.getElementById('progress-bar');

  progressWrapper.style.display = 'block';
  progressBar.style.width = '0%';
  progressBar.innerText = '0%';

  xhr.upload.onprogress = function (e) {
    if (e.lengthComputable) {
      const percent = Math.round((e.loaded / e.total) * 100);
      progressBar.style.width = percent + '%';
      progressBar.innerText = percent + '%';
    }
  };

  xhr.onload = function () {
    progressWrapper.style.display = 'none';
    const data = JSON.parse(xhr.responseText);
    const result = document.getElementById('result');
    result.innerHTML = '';

    if (data.success) {
        data.images.forEach(img => {
        const col = document.createElement('div');
        col.className = 'col-md-4 mb-3';
        col.innerHTML = `<img src="http://localhost:3000${img}" class="img-fluid border rounded shadow-sm" />`;
        result.appendChild(col);
        });

      Swal.fire('Selesai', 'Konversi berhasil!', 'success');
    } else {
      Swal.fire('Gagal', data.error, 'error');
    }
  };

  xhr.onerror = function () {
    progressWrapper.style.display = 'none';
    Swal.fire('Gagal', 'Terjadi kesalahan jaringan.', 'error');
  };

  xhr.open('POST', 'http://localhost:3000/convert', true);
  xhr.send(formData);
});
</script> -->

<script> document.getElementById('uploadForm').addEventListener('submit', function (e) { e.preventDefault(); const form = this; const formData = new FormData(form); const xhr = new XMLHttpRequest(); const progressWrapper = document.getElementById('progress-wrapper'); const progressBar = document.getElementById('progress-bar'); progressWrapper.style.display = 'block'; progressBar.style.width = '0%'; progressBar.innerText = '0%'; xhr.upload.onprogress = function (e) { if (e.lengthComputable) { const percent = Math.round((e.loaded / e.total) * 100); progressBar.style.width = percent + '%'; progressBar.innerText = percent + '%'; } }; xhr.onload = function () { progressWrapper.style.display = 'none'; const data = JSON.parse(xhr.responseText); const result = document.getElementById('result'); result.innerHTML = ''; if (data.success) { data.images.forEach(img => { const imageUrl = `http://localhost:3000${img}`; const col = document.createElement('div'); col.className = 'col-md-4 mb-3'; col.innerHTML = ` <div class="image-wrapper border rounded shadow-sm"> <img src="${imageUrl}" class="img-fluid" /> <a href="${imageUrl}" download class="btn btn-sm btn-primary download-btn">Download</a> </div> `; result.appendChild(col); }); Swal.fire('Selesai', 'Konversi berhasil!', 'success'); } else { Swal.fire('Gagal', data.error, 'error'); } }; xhr.onerror = function () { progressWrapper.style.display = 'none'; Swal.fire('Gagal', 'Terjadi kesalahan jaringan.', 'error'); }; xhr.open('POST', 'http://localhost:3000/convert', true); xhr.send(formData); }); </script>

</body>
</html>
