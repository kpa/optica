<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Método no permitido");
}

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("CSRF token inválido");
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("ID inválido");
}
if ($id) {
    $stmt = $mysqli->prepare("DELETE FROM clientes WHERE id = ?");
    $stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();
}

header('Location: index.php');
exit;
