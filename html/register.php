<!-- register -->
<?php
session_start();
include 'database.php';
$errores = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  if (empty($name)) {
    $errores[] = "El campo de nombre es obligatorio.";
  }

  if (empty($email)) {
    $errores[] = "El campo de correo es obligatorio.";
  }

  if (empty($password)) {
    $errores[] = "El campo de contraseña es obligatorio.";
  }

  if (empty($errores)) {
    // Verificar si el correo ya está registrado
    $stmt = $conexion->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
      $errores[] = "Ya existe una cuenta con ese correo.";
    } else {
      // Insertar nuevo usuario
      $hashed_password = password_hash($password, PASSWORD_BCRYPT);
      $stmt = $conexion->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $name, $email, $hashed_password);

      if ($stmt->execute()) {
        header("Location: index.php");
        exit();
      } else {
        $errores[] = "Error al registrar el usuario. Inténtalo de nuevo.";
      }
    }

    $stmt->close();
  }
}




?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>nintengames - Register</title>
    <link rel="stylesheet" href="css/master.css" />
  </head>
  <body>
    <main class="register">
      <form action="" method="post">
        <input type="text" name="name" placeholder="Nombre" />
        <input type="text" name="email" placeholder="Correo Electrónico" />
        <input class="last_input" type="password" name="password" placeholder="Contraseña" />
        <button>Registrar</button>
        <a href="index.php">Ya tengo una cuenta</a>
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
