<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit;
}

$mysqli = require __DIR__ . "/database.php";

$sql = "SELECT * FROM user WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Panel de Usuario</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    <h1>Bienvenido Usuario</h1>
    <h2>Información de perfil:</h2>
    <p><strong>Nombre:</strong> <?php echo $usuario['name']; ?></p>
    <p><strong>Email:</strong> <?php echo $usuario['email']; ?></p>

    <a href="logout.php">Cerrar Sesión</a>
</body>
</html>
