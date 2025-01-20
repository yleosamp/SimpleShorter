<?php
require_once 'config.php';

$short_url = $_GET['url'] ?? '';

$stmt = $pdo->prepare("SELECT l.*, t.content as template_content 
                       FROM links l 
                       LEFT JOIN waiting_templates t ON l.template_id = t.id 
                       WHERE l.short_url = ?");
$stmt->execute([$short_url]);
$link = $stmt->fetch();

if ($link) {
    // Atualiza contador de cliques
    $stmt = $pdo->prepare("UPDATE links SET clicks = clicks + 1 WHERE id = ?");
    $stmt->execute([$link['id']]);

    $delay = $link['delay'];
    $template = $link['template_content'] ? json_decode($link['template_content'], true) : null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecionando...</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body style="background-color: <?= $template ? $template['background_color'] : '#121212' ?>; color: <?= $template ? $template['text_color'] : '#ffffff' ?>" 
      class="min-h-screen flex items-center justify-center p-4">
    
    <?php if ($template): ?>
        <?php if ($template['type'] === 'simple'): ?>
            <div class="text-center max-w-2xl">
                <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($template['title']) ?></h1>
                <p class="mb-4"><?= nl2br(htmlspecialchars($template['description'])) ?></p>
                <p class="text-xl">Redirecionando em <span id="countdown"><?= $delay ?></span> segundos...</p>
                <div class="animate-spin h-8 w-8 border-4 rounded-full border-t-transparent mx-auto mt-4"></div>
            </div>
        <?php elseif ($template['type'] === 'youtube'): ?>
            <div class="text-center max-w-4xl w-full">
                <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($template['title']) ?></h1>
                <div class="aspect-video mb-4">
                    <iframe 
                        src="<?= htmlspecialchars($template['youtube_url']) ?>" 
                        class="w-full h-full"
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                </div>
                <p class="mb-4"><?= nl2br(htmlspecialchars($template['description'])) ?></p>
                <p class="text-xl">Redirecionando em <span id="countdown"><?= $delay ?></span> segundos...</p>
            </div>
        <?php elseif ($template['type'] === 'drive'): ?>
            <div class="text-center max-w-4xl w-full">
                <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($template['title']) ?></h1>
                <div class="aspect-video mb-4">
                    <iframe 
                        src="<?= htmlspecialchars($template['drive_url']) ?>" 
                        class="w-full h-full"
                        frameborder="0" 
                        allowfullscreen>
                    </iframe>
                </div>
                <p class="mb-4"><?= nl2br(htmlspecialchars($template['description'])) ?></p>
                <p class="text-xl">Redirecionando em <span id="countdown"><?= $delay ?></span> segundos...</p>
            </div>
        <?php elseif ($template['type'] === 'bento'): ?>
            <div class="max-w-4xl w-full">
                <h1 class="text-2xl font-bold mb-4 text-center"><?= htmlspecialchars($template['title']) ?></h1>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <?php foreach ($template['bento_cards'] as $card): ?>
                        <div class="bg-opacity-10 bg-white backdrop-blur-sm p-6 rounded-lg border border-opacity-20 border-white">
                            <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($card['title']) ?></h3>
                            <p><?= nl2br(htmlspecialchars($card['description'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="text-xl text-center">Redirecionando em <span id="countdown"><?= $delay ?></span> segundos...</p>
                <div class="animate-spin h-8 w-8 border-4 rounded-full border-t-transparent mx-auto mt-4"></div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center">
            <h1 class="text-2xl mb-4">Redirecionando em <span id="countdown"><?= $delay ?></span> segundos...</h1>
            <div class="animate-spin h-8 w-8 border-4 border-gray-500 rounded-full border-t-transparent mx-auto"></div>
        </div>
    <?php endif; ?>

    <script>
        let seconds = <?= $delay ?>;
        const countdown = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            seconds--;
            countdown.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(timer);
                window.location.href = '<?= htmlspecialchars($link['original_url']) ?>';
            }
        }, 1000);
    </script>
</body>
</html>
<?php
} else {
    echo "Link não encontrado!";
}
?> 