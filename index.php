<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $original_url = $_POST['url'];
    $custom_url = $_POST['custom_url'];
    $delay = min(5, max(0, intval($_POST['delay'])));

    $stmt = $pdo->prepare("INSERT INTO links (original_url, short_url, delay) VALUES (?, ?, ?)");
    $stmt->execute([$original_url, $custom_url, $delay]);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encurtador de Links</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8 text-center">Encurtador de Links</h1>
        
        <div class="max-w-md mx-auto bg-gray-800 rounded-lg p-6 shadow-lg">
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">URL Original</label>
                    <input type="url" name="url" required 
                           class="w-full bg-gray-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2">URL Personalizada</label>
                    <input type="text" name="custom_url" required 
                           class="w-full bg-gray-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2">Delay (0-5 segundos)</label>
                    <input type="number" name="delay" min="0" max="5" required 
                           class="w-full bg-gray-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition duration-200">
                    Encurtar Link
                </button>
            </form>
        </div>

        <?php
        $stmt = $pdo->query("SELECT * FROM links ORDER BY created_at DESC LIMIT 5");
        $links = $stmt->fetchAll();
        
        if ($links): ?>
        <div class="max-w-md mx-auto mt-8 bg-gray-800 rounded-lg p-6 shadow-lg">
            <h2 class="text-xl font-semibold mb-4">Links Recentes</h2>
            <div class="space-y-3">
                <?php foreach ($links as $link): ?>
                    <div class="bg-gray-700 p-3 rounded">
                        <p class="text-sm text-gray-300">Original: <?= htmlspecialchars($link['original_url']) ?></p>
                        <p class="text-sm text-blue-400">
                            Encurtado: <a href="/<?= htmlspecialchars($link['short_url']) ?>" target="_blank">
                                <?= $_SERVER['HTTP_HOST'] ?>/<?= htmlspecialchars($link['short_url']) ?>
                            </a>
                        </p>
                        <p class="text-sm text-gray-400">Delay: <?= $link['delay'] ?> segundos</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html> 