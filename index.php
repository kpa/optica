<?php
session_start();
require 'config.php';

header("Content-Security-Policy: default-src 'self'");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$result = $mysqli->query("SELECT * FROM clientes ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Clientes Óptica</title>
    <style>
        table, th, td { border: 1px solid #333; border-collapse: collapse; padding: 8px; }
        th { background: #eee; }
        a { text-decoration: none; margin-right: 10px; }
    </style>
</head>
<body>
    <h2>Listado de Clientes - Óptica</h2>
    <a href="crear.php">Nuevo Cliente</a>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Nombre</th><th>Apellido</th><th>Email</th><th>Teléfono</th><th>Dirección</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['nombre']) ?></td>
                <td><?= htmlspecialchars($row['apellido']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['telefono']) ?></td>
                <td><?= htmlspecialchars($row['direccion']) ?></td>
                <td>
                    <a href="editar.php?id=<?= urlencode($row['id']) ?>">Editar</a>
                    <form action="eliminar.php" method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <button onclick="return confirm('¿Eliminar cliente?')">Eliminar</button>
                    </form>                    
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
