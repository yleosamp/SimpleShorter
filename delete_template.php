<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM waiting_templates WHERE id = ? AND user_id = ? AND is_preset = FALSE");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
}

header("Location: templates.php"); 