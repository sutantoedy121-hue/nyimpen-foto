<?php
session_start();
if (!isset($_SESSION['login'])) { 
    header("Location: login.php"); 
    exit(); 
}

if (!isset($_GET['file']) || !isset($_GET['folder'])) {
    die("Parameter tidak lengkap.");
}

$folder = basename($_GET['folder']); // myspace / partnerspace / shared
$file   = basename($_GET['file']);   // nama file

$path = __DIR__ . "/uploads/$folder/$file";

if (!file_exists($path)) {
    die("File tidak ditemukan.");
}

// HAPUS FILE
unlink($path);

// Kembali ke halaman ruang
header("Location: $folder.php");
exit;
?>
