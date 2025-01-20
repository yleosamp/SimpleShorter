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

    $stmt = $pdo->prepare("SELECT id FROM links WHERE short_url = ?");
    $stmt->execute([$custom_url]);
    $existing_link = $stmt->fetch();

    if ($existing_link) {
        $error = "Este link personalizado já está em uso. Por favor, escolha outro.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO links (original_url, short_url, delay, user_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$original_url, $custom_url, $delay, $_SESSION['user_id']]);
        $success = "Link encurtado com sucesso! 🎉";
    }
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
            <?php if (isset($error)): ?>
                <div class="bg-red-500 text-white p-3 rounded mb-4">
                    ⚠️ <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="bg-green-500 text-white p-3 rounded mb-4">
                    <?= $success ?>
                </div>
            <?php endif; ?>

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
            <h2 class="text-xl font-semibold mb-4 text-gray-100">
                Seus Links
            </h2>
            <div class="space-y-4">
                <?php foreach ($links as $link): ?>
                    <div class="bg-dark-700 p-4 rounded border border-gray-700 hover:border-gray-600 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-300 font-medium">🔗 Link #<?= $link['id'] ?></span>
                            <a href="delete.php?id=<?= $link['id'] ?>" 
                               class="text-red-400 text-sm hover:text-red-300 flex items-center gap-1"
                               onclick="return confirm('Tem certeza que deseja deletar este link?')">
                                <span>🗑️</span> Deletar
                            </a>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex items-start gap-2">
                                <span class="text-gray-400 min-w-[70px]">Original:</span>
                                <span class="text-gray-300 break-all"><?= htmlspecialchars($link['original_url']) ?></span>
                            </div>
                            
                            <div class="flex items-start gap-2">
                                <span class="text-gray-400 min-w-[70px]">Encurtado:</span>
                                <a href="/<?= htmlspecialchars($link['short_url']) ?>" 
                                   target="_blank" 
                                   class="text-gray-100 hover:text-gray-300 break-all">
                                    <?= $_SERVER['HTTP_HOST'] ?>/<?= htmlspecialchars($link['short_url']) ?>
                                </a>
                            </div>
                            
                            <div class="flex gap-4 mt-2 text-sm">
                                <span class="text-gray-500">⏱️ <?= $link['delay'] ?> segundos</span>
                                <span class="text-gray-500">👆 <?= $link['clicks'] ?> cliques</span>
                                <span class="text-gray-500">📅 <?= date('d/m/Y', strtotime($link['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html> 