<?php
require 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);

    if (!$nombre) $errors[] = "El nombre es obligatorio.";
    if (!$apellido) $errors[] = "El apellido es obligatorio.";
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido.";

    if (empty($errors)) {
        // Evitar emails duplicados
        $stmt = $mysqli->prepare("SELECT id FROM clientes WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "El email ya está registrado.";
        } else {
            $stmt = $mysqli->prepare("INSERT INTO clientes (nombre, apellido, email, telefono, direccion) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssss', $nombre, $apellido, $email, $telefono, $direccion);
            $stmt->execute();
            header('Location: index.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Nuevo Cliente</title>
</head>
<body>
    <h2>Agregar Cliente</h2>
    <a href="index.php">Volver al listado</a>

    <?php if ($errors): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $e) echo "<li>$e</li>"; ?>
        </ul>
    <?php endif; ?>

    <form method="POST">
        <label>Nombre:<br><input type="text" name="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"></label><br><br>
        <label>Apellido:<br><input type="text" name="apellido" value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>"></label><br><br>
        <label>Email:<br><input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"></label><br><br>
        <label>Teléfono:<br><input type="text" name="telefono" value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>"></label><br><br>
        <label>Dirección:<br><input type="text" name="direccion" value="<?= htmlspecialchars($_POST['direccion'] ?? '') ?>"></label><br><br>
        <button type="submit">Guardar</button>
    </form>
</body>
</html>
