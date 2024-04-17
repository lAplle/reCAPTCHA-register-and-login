<?php
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header("Location: login.php"); 
    exit;
}

$mysqli = require __DIR__ . "/database.php";

$sql_solicitudes = "SELECT * FROM user WHERE status = 'pending'";
$result_solicitudes = $mysqli->query($sql_solicitudes);

$sql_aprobados = "SELECT * FROM user WHERE status = 'accepted'";
$result_aprobados = $mysqli->query($sql_aprobados);

$sql_rechazados = "SELECT * FROM user WHERE status = 'rejected'";
$result_rechazados = $mysqli->query($sql_rechazados);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit_user'])) {
    $id_usuario = $_POST['edit_user'];
    header("Location: edit_user.php?id=$id_usuario");
    exit;
}

$sql_productos = "SELECT * FROM productos";
$result_productos = $mysqli->query($sql_productos);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Panel de Administrador</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    </head>
    <body>
        <h1>Panel de Administrador</h1>
        <p><a href="logout.php">Cerrar Sesión</a></p>

    <h2>Solicitudes de Registro Pendientes</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($usuario = $result_solicitudes->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $usuario['name']; ?></td>
                    <td><?php echo $usuario['email']; ?></td>
                    <td>
                        <form method="post" action="aprobar.php" style="display: inline;">
                            <a href="aprobar.php?id=<?php echo $usuario['id']; ?>">Aprobar</a> |
                        </form>
                        <form method="post" action="rechazar.php" style="display: inline;">
                            <a href="rechazar.php?id=<?php echo $usuario['id']; ?>">Rechazar</a>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Usuarios Aprobados</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($usuario = $result_aprobados->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $usuario['name']; ?></td>
                    <td><?php echo $usuario['email']; ?></td>
                    <td>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="edit_user" value="<?php echo $usuario['id']; ?>">
                            <button type="submit">Editar</button>
                        </form>
                        <form method="post" action="eliminar.php" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                            <button type="submit" name="eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Usuarios Rechazados</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($rechazado = $result_rechazados->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $rechazado['name']; ?></td>
                    <td><?php echo $rechazado['email']; ?></td>
                    <td>
                        <form method="post" action="eliminar.php" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $rechazado['id']; ?>">
                        <button type="submit" name="eliminar">Eliminar registro</button>
                    </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <h2>Agregar Nuevo Producto</h2>
    <form method="post" action="agregar_producto.php">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required><br>
        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion" required></textarea><br>
        <label for="precio">Precio:</label>
        <input type="number" id="precio" name="precio" step="0.01" required><br>
        <label for="imagen">Imagen (URL):</label>
        <input type="url" id="imagen" name="imagen" required><br>
        <button type="submit">Agregar Producto</button>
    </form>
<h2>Productos Aprobados</h2>
<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Precio</th>
            <th>Imagen</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($producto = $result_productos->fetch_assoc()): ?>
            <tr>
                <td><?php echo $producto['nombre']; ?></td>
                <td><?php echo $producto['descripcion']; ?></td>
                <td><?php echo $producto['precio']; ?></td>
                <td><img src="<?php echo $producto['imagen']; ?>" alt="Imagen del Producto" width="100"></td>
                <td>
                    <form method="post" action="editar_producto.php" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                        <button type="submit">Editar</button>
                    </form>
                    <form method="post" action="eliminar_producto.php" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                        <button type="submit" name="eliminar">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</body>
</html>
