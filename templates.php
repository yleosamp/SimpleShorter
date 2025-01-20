<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Salvar novo template
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $content = [
        'type' => $_POST['type'],
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'youtube_url' => $_POST['youtube_url'] ?? null,
        'drive_url' => $_POST['drive_url'] ?? null,
        'background_color' => $_POST['background_color'] ?? '#121212',
        'text_color' => $_POST['text_color'] ?? '#ffffff',
        'bento_cards' => []
    ];

    if ($_POST['type'] === 'bento') {
        for ($i = 1; $i <= 4; $i++) {
            if (!empty($_POST["bento_title_$i"])) {
                $content['bento_cards'][] = [
                    'title' => $_POST["bento_title_$i"],
                    'description' => $_POST["bento_desc_$i"]
                ];
            }
        }
    }

    $stmt = $pdo->prepare("INSERT INTO waiting_templates (user_id, name, content) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $name, json_encode($content)]);
    $success = "Template salvo com sucesso! 🎉";
}

// Buscar templates do usuário
$stmt = $pdo->prepare("SELECT * FROM waiting_templates WHERE user_id = ? OR is_preset = TRUE ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$templates = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Templates - ENVLD Shortener</title>
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
            <h1 class="text-3xl font-bold text-gray-100">Gerenciar Templates</h1>
            <a href="index.php" class="text-gray-400 hover:text-gray-300">← Voltar</a>
        </div>

        <!-- Formulário de Criação -->
        <div class="max-w-2xl mx-auto bg-dark-800 rounded-lg p-6 shadow-lg mb-8">
            <?php if (isset($success)): ?>
                <div class="bg-green-500 text-white p-3 rounded mb-4"><?= $success ?></div>
            <?php endif; ?>

            <h2 class="text-xl font-semibold mb-4 text-gray-100">Criar Novo Template</h2>
            <form method="POST" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Nome do Template</label>
                        <input type="text" name="name" required 
                               class="w-full bg-dark-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 border border-gray-700 text-gray-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Tipo</label>
                        <select name="type" class="w-full bg-dark-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 border border-gray-700 text-gray-200">
                            <option value="simple">Simples</option>
                            <option value="youtube">YouTube</option>
                            <option value="drive">Google Drive</option>
                            <option value="bento">Bento Grid</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Título</label>
                    <input type="text" name="title" required 
                           class="w-full bg-dark-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 border border-gray-700 text-gray-200">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Descrição</label>
                    <textarea name="description" rows="3" 
                              class="w-full bg-dark-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 border border-gray-700 text-gray-200"></textarea>
                </div>

                <div class="youtube-fields hidden">
                    <label class="block text-sm font-medium mb-2">URL do YouTube</label>
                    <input type="url" name="youtube_url" 
                           class="w-full bg-dark-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 border border-gray-700 text-gray-200">
                </div>

                <div class="drive-fields hidden">
                    <label class="block text-sm font-medium mb-2">URL do Google Drive</label>
                    <input type="url" name="drive_url" 
                           placeholder="https://drive.google.com/file/d/..."
                           class="w-full bg-dark-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 border border-gray-700 text-gray-200">
                </div>

                <div class="bento-fields hidden">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Card 1 - Título</label>
                            <input type="text" name="bento_title_1" 
                                   class="w-full bg-dark-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 border border-gray-700 text-gray-200">
                            <textarea name="bento_desc_1" rows="2" placeholder="Descrição"
                                      class="mt-2 w-full bg-dark-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 border border-gray-700 text-gray-200"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Card 2 - Título</label>
                            <input type="text" name="bento_title_2" 
                                   class="w-full bg-dark-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 border border-gray-700 text-gray-200">
                            <textarea name="bento_desc_2" rows="2" placeholder="Descrição"
                                      class="mt-2 w-full bg-dark-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 border border-gray-700 text-gray-200"></textarea>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Card 3 - Título</label>
                            <input type="text" name="bento_title_3" 
                                   class="w-full bg-dark-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 border border-gray-700 text-gray-200">
                            <textarea name="bento_desc_3" rows="2" placeholder="Descrição"
                                      class="mt-2 w-full bg-dark-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 border border-gray-700 text-gray-200"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Card 4 - Título</label>
                            <input type="text" name="bento_title_4" 
                                   class="w-full bg-dark-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 border border-gray-700 text-gray-200">
                            <textarea name="bento_desc_4" rows="2" placeholder="Descrição"
                                      class="mt-2 w-full bg-dark-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 border border-gray-700 text-gray-200"></textarea>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Cor de Fundo</label>
                        <input type="color" name="background_color" value="#121212" 
                               class="w-full h-10 bg-dark-700 rounded border border-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Cor do Texto</label>
                        <input type="color" name="text_color" value="#ffffff" 
                               class="w-full h-10 bg-dark-700 rounded border border-gray-700">
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-dark-700 hover:bg-dark-800 text-white font-medium py-2 px-4 rounded transition duration-200 border border-gray-700">
                    Salvar Template
                </button>
            </form>
        </div>

        <!-- Lista de Templates -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($templates as $template): ?>
                <?php $content = json_decode($template['content'], true); ?>
                <div class="bg-dark-800 rounded-lg p-4 shadow-lg border border-gray-700">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="font-medium text-gray-100"><?= htmlspecialchars($template['name']) ?></h3>
                        <div class="flex gap-2">
                            <button onclick="previewTemplate(<?= $template['id'] ?>)" 
                                    class="text-blue-400 hover:text-blue-300">👁️ Preview</button>
                            <?php if (!$template['is_preset']): ?>
                                <a href="delete_template.php?id=<?= $template['id'] ?>" 
                                   class="text-red-400 hover:text-red-300"
                                   onclick="return confirm('Tem certeza?')">🗑️</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="text-sm text-gray-400">
                        <p>Tipo: <?= ucfirst($content['type']) ?></p>
                        <p class="truncate">Título: <?= htmlspecialchars($content['title']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Mostrar/esconder campos do YouTube
        const typeSelect = document.querySelector('select[name="type"]');
        const youtubeFields = document.querySelector('.youtube-fields');
        const driveFields = document.querySelector('.drive-fields');
        const bentoFields = document.querySelector('.bento-fields');

        typeSelect.addEventListener('change', () => {
            youtubeFields.classList.toggle('hidden', typeSelect.value !== 'youtube');
            driveFields.classList.toggle('hidden', typeSelect.value !== 'drive');
            bentoFields.classList.toggle('hidden', typeSelect.value !== 'bento');
        });

        // Função de preview
        function previewTemplate(id) {
            window.open(`preview_template.php?id=${id}`, 'preview', 'width=800,height=600');
        }
    </script>
</body>
</html> 