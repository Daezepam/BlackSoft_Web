<?php
session_start();
require_once __DIR__ . '/php/bd.php';

/* 1. PROTECCIÓN DE SESIÓN: Usamos usuario_id para que coincida con tu login.php */
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$idUsuario = $_SESSION['usuario_id'];

/* 2. DATOS DEL USUARIO */
$sqlUser = "
    SELECT Nombre, Apellido, Email, Fecha_registro
    FROM Usuarios
    WHERE Id = :id
";
$stmtUser = $pdo->prepare($sqlUser);
$stmtUser->execute(['id' => $idUsuario]);
$usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

/* 3. PRÉSTAMOS ACTIVOS: Corregido para buscar 'Activo' e Id_Usuarios */
$sqlActivos = "
    SELECT L.Titulo, P.Fecha_devolucion
    FROM Prestamos P
    JOIN Libros L ON P.Id_Libros = L.Id
    WHERE P.Id_Usuarios = :id
    AND P.Estado IN ('Activo', 'Pendiente', 'Atrasado')
";
$stmtActivos = $pdo->prepare($sqlActivos);
$stmtActivos->execute(['id' => $idUsuario]);
$listaActivos = $stmtActivos->fetchAll(PDO::FETCH_ASSOC);

/* 4. HISTORIAL DE PRÉSTAMOS */
$sqlHistorial = "
    SELECT L.Titulo, P.Estado
    FROM Prestamos P
    JOIN Libros L ON P.Id_Libros = L.Id
    WHERE P.Id_Usuarios = :id
    AND P.Estado IN ('Devuelto','Entregado')
";
$stmtHistorial = $pdo->prepare($sqlHistorial);
$stmtHistorial->execute(['id' => $idUsuario]);
$listaHistorial = $stmtHistorial->fetchAll(PDO::FETCH_ASSOC);
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
            <img src="img/logo2.png" alt="mainlogobtn" style="height: 45px;">
        </a>
    </div>

    <nav>
        <button id="themeToggle" class="theme-btn">
            <i class="fa-solid fa-moon"></i>
        </button>
        <a href="index.php">Inicio</a>
        <a href="prest.php">Préstamos</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>
</header>

<main class="container">

    <section class="profile-card">
        <h2>Datos personales</h2>
        <div class="info-grid">
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['Nombre'] ?? 'No disponible'); ?></p>
            <p><strong>Apellido:</strong> <?php echo htmlspecialchars($usuario['Apellido'] ?? ''); ?></p>
            <p><strong>Correo:</strong> <?php echo htmlspecialchars($usuario['Email'] ?? ''); ?></p>
            <p><strong>Miembro desde:</strong> <?php echo $usuario['Fecha_registro'] ?? '2025'; ?></p>
        </div>
        <button class="btn">Editar perfil</button>
    </section>

    <section class="profile-card">
        <h2>Préstamos activos</h2>

        <?php if (empty($listaActivos)): ?>
            <p>No tienes préstamos activos.</p>
        <?php else: ?>
            <?php foreach ($listaActivos as $p): ?>
                <div class="item">
                    <p><strong>Título:</strong> <?php echo htmlspecialchars($p['Titulo']); ?></p>
                    <p><strong>Fecha límite:</strong> <?php echo $p['Fecha_devolucion']; ?></p>
                    <button class="btn-small">Renovar</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <section class="profile-card">
        <h2>Historial de préstamos</h2>

        <?php if (empty($listaHistorial)): ?>
            <p>No hay historial aún.</p>
        <?php else: ?>
            <?php foreach ($listaHistorial as $h): ?>
                <div class="item">
                    <p><strong>Título:</strong> <?php echo htmlspecialchars($h['Titulo']); ?></p>
                    <p><strong>Estado:</strong> <?php echo htmlspecialchars($h['Estado']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

</main>

<footer>
    <p>© 2025 BlackSoft - Todos los derechos reservados</p>
</footer>

<script src="script.js"></script>
</body>
</html>