<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$link_id = $_GET['id'];

// Verifica se o link pertence ao usuário
$stmt = $pdo->prepare("SELECT id FROM links WHERE id = ? AND user_id = ?");
$stmt->execute([$link_id, $_SESSION['user_id']]);
if (!$stmt->fetch()) {
    header("Location: index.php");
    exit;
}

// Deleta o link
$stmt = $pdo->prepare("DELETE FROM links WHERE id = ?");
$stmt->execute([$link_id]);

header("Location: index.php"); 