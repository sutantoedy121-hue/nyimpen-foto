<?php
session_start();

// Logout
if (isset($_GET['aksi']) && $_GET['aksi'] === "logout") {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== 1) {
    header("Location: login.php");
    exit();
}

$nama = isset($_SESSION['username']) ? ucfirst($_SESSION['username']) : 'User';
?>

<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Private Drive</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <style>
        /* Fade-in all sections */
        .fade-in {
            opacity: 0;
            transform: translateY(10px);
            animation: fadeInUp .6s ease forwards;
        }
        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }

        /* Smooth dark mode */
        * { transition: background-color .25s, color .25s; }
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900 font-sans min-h-screen">

    <!-- NAVBAR ELEGAN GLASS -->
    <nav class="backdrop-blur-lg bg-white/70 dark:bg-gray-800/70 shadow-lg p-4 
                flex justify-between items-center fixed top-0 left-0 w-full z-10 border-b dark:border-gray-700">

        <div class="flex items-center gap-3">
            <img src="assets/images/logo.png"
                 alt="logo"
                 class="w-10 h-10 rounded-lg object-cover shadow"
                 onerror="this.style.display='none'">
            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Private Drive</h1>
        </div>

        <div class="flex items-center gap-5">
            <button onclick="toggleDarkMode()"
                    class="text-2xl text-gray-700 dark:text-gray-200 hover:scale-110 transition">
                <i class="ph-moon-stars" id="darkIcon"></i>
            </button>

            <span class="text-gray-600 dark:text-gray-300">Hi, <b><?= htmlspecialchars($nama); ?></b></span>

            <a href="?aksi=logout"
               class="text-red-500 dark:text-red-400 hover:text-red-600 font-semibold">Logout</a>
        </div>
    </nav>

    <!-- KONTEN -->
    <div class="pt-28 px-6 max-w-6xl mx-auto fade-in">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-2">
                    Selamat Datang, <?= htmlspecialchars($nama); ?> 👋
                </h2>
                <p class="text-gray-600 dark:text-gray-300">
                    Pilih ruang penyimpanan kalian
                </p>
            </div>

            <a href="upload.php"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 
                      text-white rounded-lg shadow transition">
                + Upload
            </a>
        </div>

        <!-- CARD MENU UPGRADE -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 fade-in">

            <!-- RUANG TANTO -->
            <a href="myspace.php" class="group">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 text-center 
                            transition transform duration-300 group-hover:-translate-y-2 group-hover:shadow-2xl">

                    <i class="ph-user text-5xl mb-4 text-blue-600 dark:text-blue-400"></i>

                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Ruang Tanto</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Foto & video tanto</p>
                </div>
            </a>

            <!-- RUANG PASANGAN -->
            <a href="partnerspace.php" class="group">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 text-center 
                            transition transform duration-300 group-hover:-translate-y-2 group-hover:shadow-2xl">

                    <i class="ph-heart text-5xl mb-4 text-pink-500"></i>

                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Ruang Pricilia</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Foto & video pricilia</p>
                </div>
            </a>

            <!-- RUANG BERSAMA -->
            <a href="sharedspace.php" class="group">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 text-center 
                            transition transform duration-300 group-hover:-translate-y-2 group-hover:shadow-2xl">

                    <i class="ph-folder text-5xl mb-4 text-green-600 dark:text-green-400"></i>

                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Ruang Berdua</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Kenangan kalian berdua</p>
                </div>
            </a>

        </div>

        <footer class="mt-10 text-center text-gray-500 dark:text-gray-400 text-sm fade-in">
            &copy; <?= date('Y'); ?> Private Drive — Untuk kamu & pasangan 💙
        </footer>
    </div>


    <!-- SCRIPT DARK MODE -->
    <script>
        function toggleDarkMode() {
            document.documentElement.classList.toggle("dark");

            let icon = document.getElementById("darkIcon");
            icon.classList.toggle("ph-sun");
            icon.classList.toggle("ph-moon-stars");

            localStorage.setItem("darkMode", document.documentElement.classList.contains("dark"));
        }

        // Load preference
        if (localStorage.getItem("darkMode") === "true") {
            document.documentElement.classList.add("dark");
        }
    </script>

</body>
</html>
