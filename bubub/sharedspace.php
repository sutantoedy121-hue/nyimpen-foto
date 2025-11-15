<?php
session_start();
if (!isset($_SESSION['login'])) { 
    header("Location: login.php"); 
    exit(); 
}

$folder = "sharedspace";  // ruang bersama
$base_dir = __DIR__ . "/uploads/$folder";

function list_files($dir) {
    return array_filter(glob($dir . "/*"), "is_file");
}

$files = list_files($base_dir);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Ruang Kamu</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">

<style>
/* --- CARD 3:4 --- */
.gallery-item {
    width: 180px;
    aspect-ratio: 3 / 4;
    overflow: hidden;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 6px 18px rgba(0,0,0,0.12);
    cursor: pointer;
    transition: transform .2s;
}
.gallery-item:hover { transform: scale(1.03); }

/* --- MEDIA --- */
.gallery-item img,
.gallery-item video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* --- WRAPPER GRID --- */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, 180px);
    justify-content: center;
    gap: 20px;
}

/* tombol download/delete */
.file-actions {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-top: 8px;
}

.file-actions a {
    font-size: 14px;
    text-decoration: none;
    font-weight: 600;
}

.file-actions .download { color: #2563eb; }
.file-actions .delete   { color: #d93025; }

/* --- MODAL --- */
.modal {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,0.65);
    padding: 20px;
    z-index: 50;
}
.modal-content {
    background: white;
    padding: 12px;
    border-radius: 12px;
    max-width: 90%;
}
.modal img,
.modal video {
    max-height: 80vh;
    width: auto;
}
</style>
</head>

<body class="bg-gray-50">

<div class="max-w-5xl mx-auto px-5 py-10">
    <h1 class="text-3xl font-bold mb-6">Ruang Bersama</h1>

    <div class="gallery-grid">

        <?php foreach ($files as $p): 
            $file = basename($p);
            $url  = "uploads/$folder/" . $file;
            $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        ?>

        <div class="item">

            <!-- KOTAK 3:4 -->
            <div class="gallery-item" onclick="openModal('<?= $url ?>','<?= $ext ?>')">
                <?php if (in_array($ext, ['mp4','webm','ogg'])): ?>
                    <video src="<?= $url ?>" muted></video>
                <?php else: ?>
                    <img src="<?= $url ?>" alt="">
                <?php endif; ?>
            </div>

            <!-- TOMBOL DOWNLOAD & DELETE -->
            <div class="file-actions">
                <a class="download" href="download.php?file=<?= urlencode($file) ?>&folder=<?= $folder ?>">Download</a>
                <a class="delete" 
                   href="delete.php?file=<?= urlencode($file) ?>&folder=<?= $folder ?>"
                   onclick="return confirm('Yakin ingin menghapus file ini?')">
                   Hapus
                </a>
            </div>

        </div>

        <?php endforeach; ?>

    </div>
</div>

<!-- MODAL -->
<div id="modal" class="modal">
    <div class="modal-content">
        <button onclick="closeModal()" class="px-3 py-1 bg-red-500 text-white rounded mb-3">Tutup</button>
        <div id="modalBody"></div>
    </div>
</div>

<script>
function openModal(url, ext) {
    const modal = document.getElementById("modal");
    modal.style.display = "flex";

    if (["mp4","webm","ogg"].includes(ext)) {
        modalBody.innerHTML = `<video src="${url}" controls autoplay></video>`;
    } else {
        modalBody.innerHTML = `<img src="${url}">`;
    }
}

function closeModal() {
    const modal = document.getElementById("modal");
    modal.style.display = "none";
    modalBody.innerHTML = "";
}
</script>

</body>
</html>
