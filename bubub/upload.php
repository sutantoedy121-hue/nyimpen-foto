<?php
// upload.php
// Upload file handler + UI untuk Private Drive
session_start();
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit(); }

// Konfigurasi
$allowed_types = ['image/jpeg','image/png','image/gif','video/mp4','video/webm','video/ogg'];
$max_size = 50 * 1024 * 1024; // 50 MB limit per file
$base_dir = __DIR__ . '/uploads';

// Pastikan folder ada
$folders = ['myspace','partnerspace','shared'];
foreach ($folders as $f) {
    if (!is_dir("$base_dir/$f")) mkdir("$base_dir/$f", 0755, true);
}

$messages = [];
if (isset($_POST['upload'])) {
    $dest = $_POST['destination'] ?? 'shared';
    if (!in_array($dest, $folders)) $dest = 'shared';

    if (!isset($_FILES['files'])) {
        $messages[] = ['type' => 'error', 'text' => 'Tidak ada file yang dipilih.'];
    } else {
        foreach ($_FILES['files']['tmp_name'] as $idx => $tmp) {
            $error = $_FILES['files']['error'][$idx];
            $name = basename($_FILES['files']['name'][$idx]);
            $type = $_FILES['files']['type'][$idx];
            $size = $_FILES['files']['size'][$idx];

            if ($error !== UPLOAD_ERR_OK) {
                $messages[] = ['type' => 'error', 'text' => "$name - Gagal upload (error $error)" ];
                continue;
            }
            if ($size > $max_size) {
                $messages[] = ['type' => 'error', 'text' => "$name - Melebihi batas ukuran 50MB"]; 
                continue;
            }
            if (!in_array($type, $allowed_types)) {
                $messages[] = ['type' => 'error', 'text' => "$name - Tipe file tidak diizinkan"]; 
                continue;
            }

            // Buat nama file aman untuk mencegah overwrite
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', pathinfo($name, PATHINFO_FILENAME));
            $target_name = $safe . '_' . time() . '.' . $ext;
            $target_path = "$base_dir/$dest/" . $target_name;

            if (move_uploaded_file($tmp, $target_path)) {
                $messages[] = ['type' => 'success', 'text' => "$name - Berhasil diupload ke $dest"];
            } else {
                $messages[] = ['type' => 'error', 'text' => "$name - Gagal memindahkan file ke server"];
            }
        }
    }
}

// Fungsi bantu untuk daftar file di galeri (dipakai juga di UI preview)
function list_files($dir) {
    $out = [];
    $files = glob($dir . '/*');
    foreach ($files as $f) {
        if (is_file($f)) $out[] = $f;
    }
    return $out;
}

// Ambil file untuk preview kecil di halaman upload
$preview_my = list_files($base_dir . '/myspace');
$preview_partner = list_files($base_dir . '/partnerspace');
$preview_shared = list_files($base_dir . '/shared');

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Upload — Private Drive</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
<style>
/* Bingkai modern & lightbox */
.thumb { border-radius: 18px; overflow: hidden; border: 6px solid #fff; box-shadow: 0 12px 30px rgba(0,0,0,0.12); }
.thumb img, .thumb video { width:100%; height:160px; object-fit:cover; display:block; }
.modal { position:fixed; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0.6); z-index:50; }
.modal-content { max-width:90%; max-height:90%; border-radius:12px; overflow:hidden; }
.modal-content img, .modal-content video { width:100%; height:auto; display:block; }
</style>
</head>
<body class="bg-gray-50 font-sans min-h-screen">

<nav class="bg-white py-4 shadow sticky top-0 z-20">
  <div class="max-w-6xl mx-auto px-4 flex justify-between items-center">
    <div class="flex items-center gap-4">
      <h1 class="text-xl font-bold">Private Drive</h1>
      <span class="text-sm text-gray-500">Upload & manage file</span>
    </div>
    <div class="flex items-center gap-3">
      <a href="dashboard.php" class="text-gray-700 hover:underline">Dashboard</a>
      <a href="?aksi=logout" class="text-red-600">Logout</a>
    </div>
  </div>
</nav>

<main class="max-w-6xl mx-auto p-6">
  <h2 class="text-2xl font-semibold mb-4">Upload Foto & Video</h2>

  <?php foreach ($messages as $m) {
      $cls = $m['type']==='success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800';
      echo "<div class='$cls border px-4 py-2 rounded mb-3'>" . htmlspecialchars($m['text']) . "</div>";
  } ?>

  <div class="bg-white p-6 rounded-2xl shadow mb-8">
    <form method="post" enctype="multipart/form-data" id="uploadForm">
      <div class="grid md:grid-cols-3 gap-4 items-center">
        <div>
          <label class="block text-sm text-gray-600 mb-1">Pilih Tujuan</label>
          <select name="destination" class="w-full p-2 border rounded">
            <option value="myspace">Ruang Tanto</option>
            <option value="partnerspace">Ruang Pricilia</option>
            <option value="shared">Ruang Bersama</option>
          </select>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm text-gray-600 mb-1">Pilih file (image/video)</label>
          <input type="file" name="files[]" id="files" multiple accept="image/*,video/*" class="w-full" />
        </div>
      </div>

      <div class="mt-4 flex items-center gap-3">
        <button type="submit" name="upload" class="px-5 py-2 bg-blue-600 text-white rounded-lg">Upload</button>
        <div id="previewSmall" class="flex gap-3"></div>
      </div>
    </form>
  </div>

  <!-- Preview section (mini gallery per folder) -->
  <section class="grid md:grid-cols-3 gap-6">
    <div class="bg-white p-4 rounded-2xl shadow">
      <h3 class="font-semibold mb-3">Ruang Tanto</h3>
      <div class="grid grid-cols-2 gap-3">
        <?php foreach ($preview_my as $p) {
            $url = 'uploads/myspace/' . basename($p);
            $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
            if (in_array($ext, ['mp4','webm','ogg'])) {
                echo "<div class='thumb' onclick=\"openModal('$url','video')\"><video src='$url' muted></video></div>";
            } else {
                echo "<div class='thumb' onclick=\"openModal('$url','image')\"><img src='$url' alt=''></div>";
            }
        } ?>
      </div>
    </div>

    <div class="bg-white p-4 rounded-2xl shadow">
      <h3 class="font-semibold mb-3 text-pink-500">Ruang Pricilia</h3>
      <div class="grid grid-cols-2 gap-3">
        <?php foreach ($preview_partner as $p) {
            $url = 'uploads/partnerspace/' . basename($p);
            $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
            if (in_array($ext, ['mp4','webm','ogg'])) {
                echo "<div class='thumb' onclick=\"openModal('$url','video')\"><video src='$url' muted></video></div>";
            } else {
                echo "<div class='thumb' onclick=\"openModal('$url','image')\"><img src='$url' alt=''></div>";
            }
        } ?>
      </div>
    </div>

    <div class="bg-white p-4 rounded-2xl shadow">
      <h3 class="font-semibold mb-3 text-green-600">Ruang Bersama</h3>
      <div class="grid grid-cols-2 gap-3">
        <?php foreach ($preview_shared as $p) {
            $url = 'uploads/shared/' . basename($p);
            $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
            if (in_array($ext, ['mp4','webm','ogg'])) {
                echo "<div class='thumb' onclick=\"openModal('$url','video')\"><video src='$url' muted></video></div>";
            } else {
                echo "<div class='thumb' onclick=\"openModal('$url','image')\"><img src='$url' alt=''></div>";
            }
        } ?>
      </div>
    </div>
  </section>

</main>

<!-- Modal untuk zoom / view -->
<div id="modal" style="display:none;" class="modal">
  <div class="modal-content bg-white rounded-lg p-2">
    <button onclick="closeModal()" class="px-3 py-1 bg-red-500 text-white rounded mb-2">Tutup</button>
    <div id="modalBody"></div>
  </div>
</div>

<script>
// Preview kecil sebelum upload
const filesInput = document.getElementById('files');
const previewSmall = document.getElementById('previewSmall');
filesInput?.addEventListener('change', (e) => {
    previewSmall.innerHTML = '';
    Array.from(e.target.files).slice(0,4).forEach(f => {
        const url = URL.createObjectURL(f);
        const wrapper = document.createElement('div');
        wrapper.className = 'thumb w-24';
        if (f.type.startsWith('video')) {
            wrapper.innerHTML = `<video src='${url}' muted></video>`;
        } else {
            wrapper.innerHTML = `<img src='${url}' />`;
        }
        previewSmall.appendChild(wrapper);
    });
});

// Modal open/close
function openModal(url, type){
    const modal = document.getElementById('modal');
    const body = document.getElementById('modalBody');
    modal.style.display = 'flex';
    if (type === 'video') {
        body.innerHTML = `<video controls autoplay src='${url}' style='max-height:80vh;max-width:100%'></video>`;
    } else {
        body.innerHTML = `<img src='${url}' style='max-height:80vh;max-width:100%'>`;
    }
}
function closeModal(){ document.getElementById('modal').style.display = 'none'; document.getElementById('modalBody').innerHTML = ''; }
</script>

</body>
</html>
