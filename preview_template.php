<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    exit('Acesso negado');
}

$stmt = $pdo->prepare("SELECT * FROM waiting_templates WHERE id = ? AND (user_id = ? OR is_preset = TRUE)");
$stmt->execute([$_GET['id'], $_SESSION['user_id']]);
$template = $stmt->fetch();

if (!$template) {
    exit('Template não encontrado');
}

$content = json_decode($template['content'], true);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview - <?= htmlspecialchars($template['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body style="background-color: <?= $content['background_color'] ?>; color: <?= $content['text_color'] ?>">
    <div class="min-h-screen flex items-center justify-center p-4">
        <?php if ($content['type'] === 'simple'): ?>
            <div class="text-center max-w-2xl">
                <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($content['title']) ?></h1>
                <p class="mb-4"><?= nl2br(htmlspecialchars($content['description'])) ?></p>
                <div class="animate-spin h-8 w-8 border-4 rounded-full border-t-transparent mx-auto"></div>
            </div>
        <?php elseif ($content['type'] === 'youtube'): ?>
            <div class="text-center max-w-4xl w-full">
                <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($content['title']) ?></h1>
                <div class="aspect-video mb-4">
                    <iframe 
                        src="<?= htmlspecialchars($content['youtube_url']) ?>" 
                        class="w-full h-full"
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                </div>
                <p><?= nl2br(htmlspecialchars($content['description'])) ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 