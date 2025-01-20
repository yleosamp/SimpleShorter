<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $original_url = $_POST['url'];
    $custom_url = $_POST['custom_url'];
    $delay = min(5, max(0, intval($_POST['delay'])));

    $stmt = $pdo->prepare("INSERT INTO links (original_url, short_url, delay, user_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$original_url, $custom_url, $delay, $_SESSION['user_id']]);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ENVLD Shortener</title>
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
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-100">ENVLD Shortener</h1>
            <div class="flex items-center gap-4">
                <span class="text-gray-400">Olá, <?= htmlspecialchars($_SESSION['email']) ?></span>
                <a href="logout.php" class="bg-dark-700 hover:bg-dark-800 px-4 py-2 rounded border border-gray-700">
                    Sair
                </a>
            </div>
        </div>
        
        <div class="max-w-md mx-auto bg-dark-800 rounded-lg p-6 shadow-lg">
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">URL Original</label>
                    <input type="url" name="url" required 
                           class="w-full bg-dark-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 border border-gray-700">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2">URL Personalizada</label>
                    <input type="text" name="custom_url" required 
                           class="w-full bg-dark-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 border border-gray-700">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2">Delay (0-5 segundos)</label>
                    <input type="number" name="delay" min="0" max="5" required 
                           class="w-full bg-dark-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 border border-gray-700">
                </div>
                
                <button type="submit" 
                        class="w-full bg-dark-700 hover:bg-dark-800 text-white font-medium py-2 px-4 rounded transition duration-200 border border-gray-700">
                    Encurtar Link
                </button>
            </form>
        </div>

        <?php
        $stmt = $pdo->prepare("SELECT * FROM links WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $links = $stmt->fetchAll();
        
        if ($links): ?>
        <div class="max-w-md mx-auto mt-8 bg-dark-800 rounded-lg p-6 shadow-lg">
            <h2 class="text-xl font-semibold mb-4 text-gray-100">Seus Links</h2>
            <div class="space-y-3">
                <?php foreach ($links as $link): ?>
                    <div class="bg-dark-700 p-3 rounded border border-gray-700">
                        <p class="text-sm text-gray-400">Original: <?= htmlspecialchars($link['original_url']) ?></p>
                        <p class="text-sm text-gray-300">
                            Encurtado: <a href="/<?= htmlspecialchars($link['short_url']) ?>" target="_blank" class="text-gray-100 hover:text-gray-300">
                                <?= $_SERVER['HTTP_HOST'] ?>/<?= htmlspecialchars($link['short_url']) ?>
                            </a>
                        </p>
                        <p class="text-sm text-gray-500">Delay: <?= $link['delay'] ?> segundos</p>
                        <p class="text-sm text-gray-500">Cliques: <?= $link['clicks'] ?></p>
                        <a href="delete.php?id=<?= $link['id'] ?>" 
                           class="text-red-400 text-sm hover:text-red-300"
                           onclick="return confirm('Tem certeza que deseja deletar este link?')">
                            Deletar
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html> 