<?php
session_start();
require_once __DIR__ . '/php/bd.php';

/*PROTECCIÓN DE SESIÓN*/
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$idUsuario = $_SESSION['id'];

/*DATOS DEL USUARIO*/
$sqlUser = "
    SELECT Nombre, Apellido, Email, Fecha_registro
    FROM Usuarios
    WHERE Id = :id
";
$stmtUser = $pdo->prepare($sqlUser);
$stmtUser->execute(['id' => $idUsuario]);
$usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

/*PRÉSTAMOS ACTIVOS*/
$sqlActivos = "
    SELECT L.Titulo, P.Fecha_devolucion
    FROM Prestamos P
    JOIN Libros L ON P.Id_Libros = L.Id
    WHERE P.Id_Usuarios = :id
    AND P.Estado IN ('Pendiente','Atrasado')
";
$stmtActivos = $pdo->prepare($sqlActivos);
$stmtActivos->execute(['id' => $idUsuario]);

/*HISTORIAL DE PRÉSTAMOS*/
$sqlHistorial = "
    SELECT L.Titulo, P.Estado
    FROM Prestamos P
    JOIN Libros L ON P.Id_Libros = L.Id
    WHERE P.Id_Usuarios = :id
    AND P.Estado IN ('Devuelto','Entregado')
";
$stmtHistorial = $pdo->prepare($sqlHistorial);
$stmtHistorial->execute(['id' => $idUsuario]);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perfil - BS</title>
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
        <a href="index.php">Inicio</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>
</header>

<main class="container">

    <!-- ================= DATOS PERSONALES ================= -->
    <section class="profile-card">
        <h2>Datos personales</h2>
        <div class="info-grid">
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['Nombre']); ?></p>
            <p><strong>Apellido:</strong> <?php echo htmlspecialchars($usuario['Apellido']); ?></p>
            <p><strong>Correo:</strong> <?php echo htmlspecialchars($usuario['Email']); ?></p>
            <p><strong>Miembro desde:</strong> <?php echo $usuario['Fecha_registro']; ?></p>
        </div>
        <button class="btn">Editar perfil</button>
    </section>

    <!-- ================= PRÉSTAMOS ACTIVOS ================= -->
    <section class="profile-card">
        <h2>Préstamos activos</h2>

        <?php if ($stmtActivos->rowCount() === 0): ?>
            <p>No tienes préstamos activos.</p>
        <?php else: ?>
            <?php while ($p = $stmtActivos->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="item">
                    <p><strong>Título:</strong> <?php echo htmlspecialchars($p['Titulo']); ?></p>
                    <p><strong>Fecha límite:</strong> <?php echo $p['Fecha_devolucion']; ?></p>
                    <button class="btn-small">Renovar</button>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </section>

    <!-- ================= HISTORIAL ================= -->
    <section class="profile-card">
        <h2>Historial de préstamos</h2>

        <?php if ($stmtHistorial->rowCount() === 0): ?>
            <p>No hay historial aún.</p>
        <?php else: ?>
            <?php while ($h = $stmtHistorial->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="item">
                    <p><strong>Título:</strong> <?php echo htmlspecialchars($h['Titulo']); ?></p>
                    <p><strong>Estado:</strong> <?php echo $h['Estado']; ?></p>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </section>

</main>

<footer>
    <p>© 2025 BlackSoft - Todos los derechos reservados</p>
</footer>

<script src="script.js"></script>
</body>
</html>
