<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['link_id'])) {
    header("Location: index.php");
    exit;
}

$link_id = $_POST['link_id'];
$template_id = $_POST['template_id'] ?: null;

// Verifica se o link pertence ao usuário
$stmt = $pdo->prepare("SELECT id FROM links WHERE id = ? AND user_id = ?");
$stmt->execute([$link_id, $_SESSION['user_id']]);
if (!$stmt->fetch()) {
    header("Location: index.php");
    exit;
}

// Atualiza o template
$stmt = $pdo->prepare("UPDATE links SET template_id = ? WHERE id = ?");
$stmt->execute([$template_id, $link_id]);

header("Location: index.php"); 