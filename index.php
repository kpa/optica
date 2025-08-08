<?php
session_start();
require 'config.php';

header("Content-Security-Policy: default-src 'self'; style-src 'self'; script-src 'self';");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Paginación
$registrosPorPagina = 10;
$paginaActual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($paginaActual - 1) * $registrosPorPagina;

// Total de registros
$totalRegistros = $mysqli->query("SELECT COUNT(*) as total FROM clientes")->fetch_assoc()['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

// Consulta paginada
$stmt = $mysqli->prepare("SELECT * FROM clientes ORDER BY id DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $registrosPorPagina, $offset);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Clientes Óptica</title>
    <link rel="stylesheet" href="estilos.css">
    <script src="confirmacion.js" defer></script>
</head>
<body>
    <h2>Listado de Clientes - Óptica</h2>
    <form method="GET" action="">
    <label for="buscar">Buscar:</label>
    <input type="text" id="buscar" name="buscar" placeholder="Nombre, Apellido o Email" value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>">
    <button type="submit">Buscar</button>
</form>
<?php
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

if ($busqueda !== '') {
    $busquedaLike = "%$busqueda%";

    // Contar resultados
    $stmtTotal = $mysqli->prepare("
        SELECT COUNT(*) as total 
        FROM clientes 
        WHERE nombre LIKE ? OR apellido LIKE ? OR email LIKE ?
    ");
    $stmtTotal->bind_param("sss", $busquedaLike, $busquedaLike, $busquedaLike);
    $stmtTotal->execute();
    $totalRegistros = $stmtTotal->get_result()->fetch_assoc()['total'];

    $totalPaginas = ceil($totalRegistros / $registrosPorPagina);

    // Buscar en varias columnas
    $stmt = $mysqli->prepare("
        SELECT * 
        FROM clientes 
        WHERE nombre LIKE ? OR apellido LIKE ? OR email LIKE ? 
        ORDER BY id DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("sssii", $busquedaLike, $busquedaLike, $busquedaLike, $registrosPorPagina, $offset);
} else {
    // Sin búsqueda
    $totalRegistros = $mysqli->query("SELECT COUNT(*) as total FROM clientes")->fetch_assoc()['total'];
    $totalPaginas = ceil($totalRegistros / $registrosPorPagina);

    $stmt = $mysqli->prepare("SELECT * FROM clientes ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $registrosPorPagina, $offset);
}

$stmt->execute();
$resultado = $stmt->get_result();
?>

    <a href="crear.php">Nuevo Cliente</a>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Nombre</th><th>Apellido</th><th>Email</th><th>Teléfono</th><th>Dirección</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['nombre']) ?></td>
                <td><?= htmlspecialchars($row['apellido']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['telefono']) ?></td>
                <td><?= htmlspecialchars($row['direccion']) ?></td>
                <td>
                    <a href="editar.php?id=<?= urlencode($row['id']) ?>">Editar</a>
                    <form action="eliminar.php" method="POST" class="delete-form">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <button type="button" class="delete-btn">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Modal de confirmación -->
    <div id="confirmModal">
        <div class="modal-content">
            <p>¿Estás seguro de que deseas eliminar este cliente?</p>
            <button id="confirmYes">Sí, eliminar</button>
            <button id="confirmNo">Cancelar</button>
        </div>
    </div>

    <div class="pagination">
    <?php if ($paginaActual > 1): ?>
        <a href="?pagina=<?= $paginaActual - 1 ?>&buscar=<?= urlencode($busqueda) ?>">&laquo; Anterior</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <a href="?pagina=<?= $i ?>&buscar=<?= urlencode($busqueda) ?>" <?= $i === $paginaActual ? 'class="active"' : '' ?>><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($paginaActual < $totalPaginas): ?>
        <a href="?pagina=<?= $paginaActual + 1 ?>"&buscar=<?= urlencode($busqueda) ?>>Siguiente &raquo;</a>

    <?php endif; ?>
</div>

</body>
</html>


