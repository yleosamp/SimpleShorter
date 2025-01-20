<?php
require_once 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->execute([$email, $password]);
        $_SESSION['message'] = "Conta criada com sucesso!";
        header("Location: login.php");
        exit;
    } catch(PDOException $e) {
        $error = "Email já cadastrado";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - ENVLD Shortener</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        dark: {
                            '900': '#121212',
                            '800': '#202020',
                            '700': '#2d2d2d',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark-900 text-gray-200 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8 text-center text-gray-100">Registro</h1>
        
        <div class="max-w-md mx-auto bg-dark-800 rounded-lg p-6 shadow-lg">
            <?php if (isset($error)): ?>
                <div class="bg-red-500 text-white p-3 rounded mb-4"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Email</label>
                    <input type="email" name="email" required 
                           class="w-full bg-dark-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 border border-gray-700">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2">Senha</label>
                    <input type="password" name="password" required 
                           class="w-full bg-dark-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 border border-gray-700">
                </div>
                
                <button type="submit" 
                        class="w-full bg-dark-700 hover:bg-dark-800 text-white font-medium py-2 px-4 rounded transition duration-200 border border-gray-700">
                    Registrar
                </button>

                <p class="text-center text-sm">
                    Já tem conta? <a href="login.php" class="text-gray-400 hover:text-gray-300">Faça login</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html> 