<?php
session_start();
require_once __DIR__ . '/php/bd.php';

$error = null;

// Capturamos si venimos de prest.php para volver allá después
$redirect = $_GET['redirect'] ?? 'index.php';

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

                // IMPORTANTE: Usamos 'usuario_id' y 'usuario' para que coincida con prest.php
                $_SESSION['usuario_id'] = $usuario['Id'];
                $_SESSION['usuario'] = $usuario['Nombre'];
                $_SESSION['rol'] = $usuario['Rol'];

                // Redirección inteligente
                header("Location: " . $redirect);
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
</head>

<body>

<header>
    <div class="logo">
        <a href="index.php" class="logomain">
            <img src="img/logo2.png" alt="mainlogobtn" style="height: 45px;">
        </a>
    </div>
    <nav>
        <a href="info.php">Acerca</a>
        <a href="index.php">Inicio</a>
        <a href="registro.php">Registro</a>
    </nav>
</header>

<main class="formulario">
    <div class="formlog">
        <h2>Iniciar Sesión</h2><br>

        <?php if ($error): ?>
            <p style="color:red; margin-bottom:10px;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form class="forma" method="POST" action="login.php?redirect=<?php echo urlencode($redirect); ?>">
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

<footer style="text-align: right; padding: 20px 50px;">
    <p>© 2025 BlackSoft - Todos los derechos reservados</p>
</footer>

<script src="script.js"></script>
</body>
</html>