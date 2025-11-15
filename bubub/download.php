<?php
if (!isset($_GET['file']) || !isset($_GET['folder'])) {
    die("Parameter tidak lengkap.");
}

$folder = basename($_GET['folder']); // supaya aman
$file   = basename($_GET['file']);   // hilangkan path traversal

$path = __DIR__ . "/uploads/$folder/$file";

// Cek file
if (!file_exists($path)) {
    die("File tidak ditemukan di server.");
}

// Header download
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$file\"");
header("Content-Length: " . filesize($path));

readfile($path);
exit;
?>
