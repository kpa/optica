<?php
session_start();
require 'config.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("ID inválido");
}

$id = intval($_GET['id']);
$stmt = $mysqli->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();

if (!$cliente) {
    header('Location: index.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token inválido");
    }
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);

    if (!$nombre) $errors[] = "El nombre es obligatorio.";
    if (!$apellido) $errors[] = "El apellido es obligatorio.";
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido.";

    if (empty($errors)) {
        // Validar que no exista otro cliente con el mismo email
        $stmt2 = $mysqli->prepare("SELECT id FROM clientes WHERE email = ? AND id != ?");
        $stmt2->bind_param('si', $email, $id);
        $stmt2->execute();
        $stmt2->store_result();
        if ($stmt2->num_rows > 0) {
            $errors[] = "El email ya está registrado a otro cliente.";
        } else {
            $stmt = $mysqli->prepare("UPDATE clientes SET nombre=?, apellido=?, email=?, telefono=?, direccion=? WHERE id=?");
            $stmt->bind_param('sssssi', $nombre, $apellido, $email, $telefono, $direccion, $id);
            $stmt->execute();
            header('Location: index.php');
            exit;
        }
    }
} else {
    // Cargar datos para mostrar en el formulario
    $nombre = $cliente['nombre'];
    $apellido = $cliente['apellido'];
    $email = $cliente['email'];
    $telefono = $cliente['telefono'];
    $direccion = $cliente['direccion'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Cliente</title>
</head>
<body>
    <h2>Editar Cliente</h2>
    <a href="index.php">Volver al listado</a>

    <?php if ($errors): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $e) echo "<li>$e</li>"; ?>
        </ul>
    <?php endif; ?>

    <form method="POST">
        √<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <label>Nombre:<br><input type="text" name="nombre" value="<?= htmlspecialchars($nombre) ?>"></label><br><br>
        <label>Apellido:<br><input type="text" name="apellido" value="<?= htmlspecialchars($apellido) ?>"></label><br><br>
        <label>Email:<br><input type="email" name="email" value="<?= htmlspecialchars($email) ?>"></label><br><br>
        <label>Teléfono:<br><input type="text" name="telefono" value="<?= htmlspecialchars($telefono) ?>"></label><br><br>
        <label>Dirección:<br><input type="text" name="direccion" value="<?= htmlspecialchars($direccion) ?>"></label><br><br>
        <button type="submit">Actualizar</button>
    </form>
</body>
</html>
