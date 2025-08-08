<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        $stmt = $mysqli->prepare("DELETE FROM clientes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

header('Location: index.php');
exit;
