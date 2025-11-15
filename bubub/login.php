<?php
session_start();

// Daftar akun (username => password)
$accounts = [
    "tanto" => "tantoimut21",
    "pricilia" => "pikasayang16"
];

// Jika form dikirim dari index.php
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Sesuai dengan index.php:
    // <input name="username">
    // <input name="password">
    $username = $_POST['username'] ?? "";
    $password = $_POST['password'] ?? "";

    if ($username === "" || $password === "") {
        $error = "Username dan Password tidak boleh kosong";
    } elseif (isset($accounts[$username]) && $accounts[$username] === $password) {
        // Login berhasil → simpan session
        $_SESSION['login'] = 1;
        $_SESSION['username'] = $username;

        // Arahkan ke dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Username atau password salah";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Private Drive</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen font-sans">

    <div class="bg-white shadow-xl rounded-2xl p-10 w-96 animate-fadeIn">
        <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Private Drive Login</h2>

        <?php if (!empty($error)) { echo "<p class='text-red-500 text-center mb-3'>$error</p>"; } ?>

        <!-- Form ini tetap pakai nama "username" dan "password" -->
        <form method="POST">
            <label class="block mb-3">
                <span class="text-gray-700">Username</span>
                <input type="text" name="username" class="mt-1 w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300 focus:outline-none" required />
            </label>

            <label class="block mb-5">
                <span class="text-gray-700">Password</span>
                <input type="password" name="password" class="mt-1 w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300 focus:outline-none" required />
            </label>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold p-2 rounded-lg transition">
                Login
            </button>
        </form>

        <p class="text-center text-gray-500 text-sm mt-4">Website khusus kamu & pasangan ❤️</p>
    </div>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out;
        }
    </style>

</body>
</html>
