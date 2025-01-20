<?php
require_once 'config.php';

$short_url = $_GET['url'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM links WHERE short_url = ?");
$stmt->execute([$short_url]);
$link = $stmt->fetch();

if ($link) {
    $delay = $link['delay'];
?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Redirecionando...</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-2xl mb-4">Redirecionando em <span id="countdown"><?= $delay ?></span> segundos...</h1>
            <div class="animate-spin h-8 w-8 border-4 border-blue-500 rounded-full border-t-transparent mx-auto"></div>
        </div>
        
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