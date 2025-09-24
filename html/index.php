<?php
session_start();
include 'database.php';
$errores = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = trim($_POST['email']);
  $password = $_POST['clave'];

  if (empty($email)) {
    $errores[] = "El campo de correo es obligatorio.";
  }

  if (empty($password)) {
    $errores[] = "El campo de contraseña es obligatorio.";
  }

  if (empty($errores)) {
    // Buscar usuario por email
    $stmt = $conexion->prepare("SELECT id, fullname, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows == 1) {
      $usuario = $resultado->fetch_assoc();

      if (password_verify($password, $usuario['password'])) {

        $_SESSION['usuario'] = [
          'id' => $usuario['id'],
          'fullname' => $usuario['fullname'],
          'email' => $email
        ];
        header("Location: dashboard.php");
        exit();
      } else {
        $errores[] = "Contraseña incorrecta.";
      }
    } else {
      $errores[] = "No se encontró una cuenta con ese correo.";
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
    <title>nintengames - Login</title>
    <link rel="stylesheet" href="css/master.css">
    </head>
    <body>
        <main class="login">
            <form action="" method="post">
              <input type="text" name="email" placeholder="Correo Electrónico">
                <input type="password" name="clave" placeholder="Contraseña">

                <button>Ingresar</button>
                <a class='error' href="register.php">Crear cuenta</a> 
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