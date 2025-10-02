<?php
session_start();
require 'database.php';
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
$id = $_GET['id'] ?? 0;
$errores = [];
$stmt = $conexion->prepare("SELECT * FROM games WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$juego = $stmt->get_result()->fetch_assoc();
$stmt->close();

$plataformas = $conexion->query("SELECT * FROM platforms");
$categorias = $conexion->query("SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $platform_id = $_POST['platform'];
    $category_id = $_POST['category'];
    $year = $_POST['year'];
    $cover = $_FILES['cover'];

    if (empty($title)) $errores[] = "El título es obligatorio.";
    if (empty($platform_id)) $errores[] = "La consola es obligatoria.";
    if (empty($category_id)) $errores[] = "La categoría es obligatoria.";
    if (empty($year)) $errores[] = "El año es obligatorio.";

    $nombreArchivo = $juego['cover'];
    if ($cover['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = uniqid() . "_" . basename($cover['name']);
        move_uploaded_file($cover['tmp_name'], "../uploads/" . $nombreArchivo);
    }

    if (empty($errores)) {
        $stmt = $conexion->prepare("UPDATE games SET title=?, platform_id=?, category_id=?, cover=?, year=? WHERE id=?");
        $stmt->bind_param("siisii", $title, $platform_id, $category_id, $nombreArchivo, $year, $id);
        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit();
        } else {
            $errores[] = "Error al actualizar el videojuego.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>nintengames - Edit</title>
    <link rel="stylesheet" href="css/master.css">
</head>
<body>
    <main class="edit">
        <header>
            <h2>Modificar VideoJuego</h2>
            <a href="dashboard.php" class="back"></a>
            <a href="index.php" class="close"></a>
        </header>
        <form action="" method="post" enctype="multipart/form-data">
            <figure class="photo-preview">
                <?php if (!empty($juego['cover'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($juego['cover']); ?>" alt="Portada" style="width:120px;height:120px;">
                <?php else: ?>
                    <img src="images/icon-camera.svg" alt="Portada" style="width:120px;height:120px;">
                <?php endif; ?>
            </figure>
            <input type="text" name="title" value="<?php echo htmlspecialchars($juego['title']); ?>" placeholder="Título">
            <div class="select">
                <select name="platform">
                    <option value="">Seleccione Consola...</option>
                    <?php while ($p = $plataformas->fetch_assoc()): ?>
                    <option value="<?php echo $p['id']; ?>" <?php if ($juego['platform_id'] == $p['id']) echo 'selected'; ?>><?php echo $p['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="select">
                <select name="category">
                    <option value="">Seleccione Categoría...</option>
                    <?php while ($c = $categorias->fetch_assoc()): ?>
                    <option value="<?php echo $c['id']; ?>" <?php if ($juego['category_id'] == $c['id']) echo 'selected'; ?>><?php echo $c['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <input type="file" name="cover" accept="image/*">
            <input type="text" name="year" value="<?php echo htmlspecialchars($juego['year']); ?>" placeholder="Año">
            <button type="submit" class="update">Guardar Cambios</button>
            <?php if (!empty($errores)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errores as $error): ?>
                <div><?php echo $error; ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </form>
    </main>
</body>
</html>