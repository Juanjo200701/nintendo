<?php
session_start();
require 'database.php';
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$sql = "SELECT g.*, p.name AS platform, c.name AS category 
        FROM games g
        LEFT JOIN platforms p ON g.platform_id = p.id
        LEFT JOIN categories c ON g.category_id = c.id";
$resultado = $conexion->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>nintengames - Dashboard</title>
    <link rel="stylesheet" href="css/master.css">
</head>
<body>
    <main class="dashboard">
        <header>
            <?php if  ($_SESSION['usuario']['email'] === 'admin@gmail.com'): ?>
            <h2>Administrar Videojuegos</h2>
            <?php else: ?>
            <h2>Videojuegos</h2>
            <?php endif; ?>
            <a href="logout.php" class="close"></a>
        </header>
       <a href="add.php" class="add"></a>   
       <table>
            <?php while ($juego = $resultado->fetch_assoc()): ?>
           <tr>
                <td>
                    <figure class="cover">
                        <img src="../uploads/<?php echo htmlspecialchars($juego['cover']); ?>" alt="Portada">
                    </figure>
                    <div class="info">
                        <h3><?php echo htmlspecialchars($juego['platform']); ?></h3>
                        <h4><?php echo htmlspecialchars($juego['title']); ?></h4>
                        <p><?php echo htmlspecialchars($juego['category']); ?> | <?php echo htmlspecialchars($juego['year']); ?></p>
                    </div>
                    <div class="controls">
                        <a href="show.php?id=<?php echo $juego['id']; ?>" class="show"></a>
                        <a href="edit.php?id=<?php echo $juego['id']; ?>" class="edit"></a>
                        <a href="delete.php?id=<?php echo $juego['id']; ?>" class="delete" onclick="return confirm('¿Seguro que deseas eliminar este videojuego?');"></a>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </main>
</body>
</html>