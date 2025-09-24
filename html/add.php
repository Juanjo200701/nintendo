<?php
session_start();
require 'database.php';
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$errores = [];
// Obtener plataformas y categorías
$plataformas = $conexion->query("SELECT * FROM platforms");
$categorias = $conexion->query("SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $platform_id = $_POST['platform'];
    $category_id = $_POST['category'];
    $year = $_POST['year'];
    $cover = $_FILES['cover'];

    // Validaciones
    if (empty($title)) $errores[] = "El título es obligatorio.";
    if (empty($platform_id)) $errores[] = "La consola es obligatoria.";
    if (empty($category_id)) $errores[] = "La categoría es obligatoria.";
    if (empty($year)) $errores[] = "El año es obligatorio.";
    if ($cover['error'] !== UPLOAD_ERR_OK) $errores[] = "La portada es obligatoria.";

    if (empty($errores)) {
        // Subir portada
        $nombreArchivo = uniqid() . "_" . basename($cover['name']);
        move_uploaded_file($cover['tmp_name'], "../uploads/" . $nombreArchivo);

        // Insertar en la base de datos
        $stmt = $conexion->prepare("INSERT INTO games (title, platform_id, category_id, cover, year) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siisi", $title, $platform_id, $category_id, $nombreArchivo, $year);
        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit();
        } else {
            $errores[] = "Error al guardar el videojuego.";
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
    <title>nintengames - Add</title>
    <link rel="stylesheet" href="css/master.css">
</head>
<body>
    <main class="add">
        <header>
            <h2>Adicionar VideoJuego</h2>
            <a href="dashboard.php" class="back"></a>
            <a href="index.php" class="close"></a>
        </header>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Título">
            <select name="platform">
                <option value="">Seleccione Consola...</option>
                <?php while ($p = $plataformas->fetch_assoc()): ?>
                <option value="<?php echo $p['id']; ?>"><?php echo $p['name']; ?></option>
                <?php endwhile; ?>
            </select>
            <select name="category">
                <option value="">Seleccione Categoría...</option>
                <?php while ($c = $categorias->fetch_assoc()): ?>
                <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                <?php endwhile; ?>
            </select>
            <input type="file" name="cover" accept="image/*">
            <input type="text" name="year" placeholder="Año">
            <button type="submit" class="save">Guardar</button>
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