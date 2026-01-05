<?php
session_start();
require_once __DIR__ . '/php/bd.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email && $password) {

        $sql = "
            SELECT Id, Nombre, Rol, Password, Estado
            FROM Usuarios
            WHERE Email = :email
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {

            if ($usuario['Estado'] !== 'Activo') {
                $error = "Tu cuenta está inactiva";
            } elseif (password_verify($password, $usuario['Password'])) {

                $_SESSION['id'] = $usuario['Id'];
                $_SESSION['nombre'] = $usuario['Nombre'];
                $_SESSION['rol'] = $usuario['Rol'];

                header("Location: index.php");
                exit;

            } else {
                $error = "Correo o contraseña incorrectos";
            }

        } else {
            $error = "Correo o contraseña incorrectos";
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
  <title>Login - BS</title>
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
        <a href="registro.php">Registro</a>
    </nav>
</header>

<main class="formulario">
    <div class="formlog">
        <h2>Iniciar Sesión</h2><br>

        <?php if ($error): ?>
            <p style="color:red; margin-bottom:10px;">
                <?php echo $error; ?>
            </p>
        <?php endif; ?>

        <form class="forma" method="POST">
            <h3>Ingresa tu correo</h3>
            <input type="email" name="email" placeholder="correo@gmail.com" required>

            <h3>Ingresa tu contraseña</h3>
            <input type="password" name="password" placeholder="Contraseña" required>

            <button type="submit">Ingresar</button>
        </form>
    </div>
</main>

<div class="info-login">
    <h3>¿No tienes una cuenta?</h3>
    <a href="registro.php" class="btn-reg">Regístrate aquí</a>
</div>

<div class="password">
    <a href="pass.php" class="btn-pass">¿Olvidaste tu contraseña?</a>
</div>

<footer>
    <p>© 2025 BlackSoft</p>
</footer>

<script src="script.js"></script>
</body>
</html>
