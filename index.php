<?php
require 'config.php';

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
                    <a href="editar.php?id=<?= $row['id'] ?>">Editar</a>
                    <a href="eliminar.php?id=<?= $row['id'] ?>" onclick="return confirm('¿Eliminar cliente?')">Eliminar</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
