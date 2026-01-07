<?php
session_start();
require_once __DIR__ . '/php/bd.php'; 

// Verificación de sesión
$nombre_usuario = $_SESSION['usuario'] ?? 'Santiago';
$usuario_id = $_SESSION['usuario_id'] ?? null;

if (!$usuario_id) { 
    header("Location: login.php"); 
    exit(); 
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlackSoft - Biblioteca</title>
    <link rel="stylesheet" href="./CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="body-white">

<header class="header-light">
    <div class="logo">
        <a href="index.php">
            <img src="img/logo2.png" alt="Logo" class="logo-img">
        </a>
    </div>
    <nav class="nav-light">
        <a href="index.php">Inicio</a>
        <a href="perfil.php" class="nav-user-black">
            <i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($nombre_usuario); ?>
        </a>
        <a href="logout.php" class="nav-link-black">Salir</a>
    </nav>
</header>

<main class="container-prestamos">
    
    <section class="card-light">
        <h2 class="title-black">
            <i class="fa-solid fa-layer-group"></i> Libros para Solicitar
        </h2>
        <div class="divisor-black"></div>
        
        <div class="lista-items">
            <?php
            $stmt = $pdo->query("SELECT Id, Titulo FROM libros WHERE Disponibilidad = 1");
            $hay_libros = false;
            while ($libro = $stmt->fetch()) {
                $hay_libros = true;
                echo "
                <div class='item-prestamo-light'>
                    <div class='info-libro'>
                        <span class='label-disponible'>DISPONIBLE</span>
                        <p class='libro-titulo-black'>{$libro['Titulo']}</p>
                    </div>
                    <form action='php/acciones_prestamo.php' method='POST'>
                        <input type='hidden' name='id_libro' value='{$libro['Id']}'>
                        <input type='hidden' name='accion' value='solicitar'>
                        <button type='submit' class='btn-accion-black'>Solicitar</button>
                    </form>
                </div>";
            }
            if (!$hay_libros) echo "<p class='no-data-black'>No hay libros disponibles por ahora.</p>";
            ?>
        </div>
    </section>

    <section class="card-light section-margin">
        <h2 class="title-black">
            <i class="fa-solid fa-bookmark"></i> Mis Préstamos Activos
        </h2>
        <div class="divisor-black"></div>
        
        <div class="lista-items">
            <?php
            $stmt = $pdo->prepare("SELECT p.*, l.Titulo FROM prestamos p JOIN libros l ON p.Id_Libros = l.Id 
                                   WHERE p.Id_Usuarios = ? AND p.Estado != 'Devuelto'");
            $stmt->execute([$usuario_id]);
            $prestamos = $stmt->fetchAll();

            if ($prestamos) {
                foreach ($prestamos as $p) {
                    echo "
                    <div class='item-prestamo-light border-activo'>
                        <div class='info-libro'>
                            <p class='libro-titulo-black'>{$p['Titulo']}</p>
                            <small class='fecha-entrega-black'>📅 Entrega: {$p['Fecha_devolucion']}</small>
                        </div>
                        <div class='acciones-derecha'>
                            <span class='badge-status-black'>{$p['Estado']}</span>
                            <form action='php/acciones_prestamo.php' method='POST'>
                                <input type='hidden' name='id_prestamo' value='{$p['Id']}'>
                                <input type='hidden' name='accion' value='devolver'>
                                <button type='submit' class='btn-check-black' title='Devolver Libro'>
                                    <i class='fa-solid fa-circle-check'></i>
                                </button>
                            </form>
                        </div>
                    </div>";
                }
            } else {
                echo "<p class='no-data-black'>No tienes lecturas activas en este momento.</p>";
            }
            ?>
        </div>
    </section>
</main>

<footer class="footer-light">
    <p>© 2026 BlackSoft - Sistema de Biblioteca</p>
</footer>

<script src="script.js"></script>
</body>
</html>