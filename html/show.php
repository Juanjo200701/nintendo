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
        <figure class="cover">
            <img src="uploads/<?php echo htmlspecialchars($juego['cover']); ?>" alt="">
        </figure>
        <div class="info">
            <h3><?php echo htmlspecialchars($juego['platform']); ?></h3>
            <h4><?php echo htmlspecialchars($juego['title']); ?></h4>
            <p><?php echo htmlspecialchars($juego['category']); ?> | <?php echo htmlspecialchars($juego['year']); ?></p>
        </div>
        <a href="dashboard.php" class="back"></a>
    </main>
</body>
</html>