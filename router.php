<?php
if (php_sapi_name() === 'cli-server') {
    $url = parse_url($_SERVER['REQUEST_URI']);
    $path = $url['path'];
    
    // Se for um arquivo existente, serve ele diretamente
    if (is_file(__DIR__ . $path)) {
        return false;
    }
    
    // Se for um link curto (apenas uma string sem /)
    if (preg_match('/^\/[^\/]+$/', $path)) {
        $_GET['url'] = substr($path, 1); // Remove a / inicial
        require __DIR__ . '/redirect.php';
        return true;
    }
}

// Para todas as outras URLs, serve o arquivo normalmente
return false; 