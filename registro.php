<?php
require_once __DIR__ . '/php/bd.php';

$mensaje = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre   = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nombre && $apellido && $email && $password) {

        // Verificar si el correo ya existe
        $check = $pdo->prepare("SELECT Id FROM Usuarios WHERE Email = :email");
        $check->execute(['email' => $email]);

        if ($check->fetch()) {
            $error = "Este correo ya está registrado";
        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "
                INSERT INTO Usuarios
                (Nombre, Apellido, Email, Password, Rol, Fecha_registro, Estado)
                VALUES
                (:nombre, :apellido, :email, :password, 'Usuario', CURDATE(), 'Activo')
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nombre'   => $nombre,
                'apellido' => $apellido,
                'email'    => $email,
                'password' => $hash
            ]);

            $mensaje = "Cuenta creada correctamente. Ahora puedes iniciar sesión.";
        }

    } else {
        $error = "Completa todos los campos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro - BS</title>
  <link rel="stylesheet" href="./CSS/style.css">
  <link rel="icon" href="img/logo.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>

<body>

<header>
    <div class="logo">
        <a href="index.php" class="logomain">
            <img src="img/logo2.png" alt="mainlogobtn">
        </a>
    </div>
    <nav>
        <button id="themeToggle" class="theme-btn">
            <i class="fa-solid fa-moon"></i>
        </button>
        <a href="info.php">Acerca</a>
        <a href="index.php">Inicio</a>
        <a href="login.php">Ingresar</a>
    </nav>
</header>

<main class="formulario">
    <div class="formlog">
        <h2>Crear Cuenta</h2><br>

        <?php if ($error): ?>
            <p style="color:red; margin-bottom:10px;">
                <?php echo $error; ?>
            </p>
        <?php endif; ?>

        <?php if ($mensaje): ?>
            <p style="color:green; margin-bottom:10px;">
                <?php echo $mensaje; ?>
            </p>
        <?php endif; ?>

        <form class="forma" method="POST">
            <h3>Ingresa tu nombre</h3>
            <input type="text" name="nombre" placeholder="Nombre" required>

            <h3>Ingresa tu apellido</h3>
            <input type="text" name="apellido" placeholder="Apellido" required>

            <h3>Ingresa tu correo</h3>
            <input type="email" name="email" placeholder="correo@gmail.com" required>

            <h3>Ingresa tu contraseña</h3>
            <input type="password" name="password" placeholder="Contraseña" required>

            <button type="submit">Registrar</button>
        </form>
    </div>
</main>

<div class="info-login">
    <h3>¿Ya tienes una cuenta?</h3>
    <a href="login.php" class="btn-reg">Inicia sesión</a>
</div>

<footer>
    <p>© 2025 BlackSoft</p>
</footer>

<script src="script.js"></script>
</body>
</html>
