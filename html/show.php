<?php
session_start();
require 'database.php';
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
$id = $_GET['id'] ?? 0;
$stmt = $conexion->prepare("SELECT g.*, p.name AS platform, c.name AS category FROM games g LEFT JOIN platforms p ON g.platform_id = p.id LEFT JOIN categories c ON g.category_id = c.id WHERE g.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$juego = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>nintengames - Show</title>
    <link rel="stylesheet" href="css/master.css">
</head>
<body>
    <main class="show">
        <header>
            <h2>Consultar VideoJuego</h2>
            <a href="dashboard.php" class="back"></a>
            <a href="index.php" class="close"></a>
        </header>
        <figure class="photo-preview">
            <img src="../uploads/<?php echo htmlspecialchars($juego['cover']); ?>" alt="Portada" style="width:120px;height:120px;">
        </figure>
        <div class="info-blocks">
            <div class="info-row"><span class="label">Título:</span> <span class="value"><?php echo htmlspecialchars($juego['title']); ?></span></div>
            <div class="info-row"><span class="label">Consola:</span> <span class="value"><?php echo htmlspecialchars($juego['platform']); ?></span></div>
            <div class="info-row"><span class="label">Categoría:</span> <span class="value"><?php echo htmlspecialchars($juego['category']); ?></span></div>
            <div class="info-row"><span class="label">Año:</span> <span class="value"><?php echo htmlspecialchars($juego['year']); ?></span></div>
        </div>
    </main>
</body>
</html>