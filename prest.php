<?php
session_start();
require_once __DIR__ . '/php/bd.php'; 
$nombre_usuario = $_SESSION['usuario'] ?? 'Santiago';
$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) { header("Location: login.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlackSoft - Biblioteca</title>
    <link rel="stylesheet" href="./CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .item { 
            transition: transform 0.2s, box-shadow 0.2s; 
            border-left: 5px solid #9d2c70 !important;
            display: flex; 
            justify-content: space-between; 
            align-items: center;
        }
        .item:hover { 
            transform: scale(1.01); 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
        }
        .libro-info { flex-grow: 1; padding-right: 20px; }
        .badge { font-weight: bold; text-transform: uppercase; font-size: 0.8rem; }
    </style>
</head>
<body>
<header>
    <div class="logo"><a href="index.php"><img src="img/logo2.png" alt="Logo" style="height: 50px;"></a></div>
    <nav>
        <a href="index.php">Inicio</a>
        <a href="perfil.php"><strong>Hola, <?php echo htmlspecialchars($nombre_usuario); ?></strong></a>
        <a href="logout.php">Salir</a>
    </nav>
</header>
<main class="container" style="max-width: 800px; margin-top: 50px;">
    <section class="profile-card" style="padding: 30px;">
        <h2 style="font-size: 1.8rem;"><i class="fa-solid fa-layer-group"></i> Libros para Solicitar</h2>
        <div class="divisor"></div>
        <?php
        $stmt = $pdo->query("SELECT Id, Titulo FROM Libros WHERE Disponibilidad = 1");
        while ($libro = $stmt->fetch()) {
            echo "<div class='item' style='margin-bottom: 15px; padding: 25px;'>
                    <div class='libro-info'>
                        <span style='color: #666; font-size: 0.9rem;'>Disponible</span>
                        <p style='font-size: 1.2rem; margin: 5px 0;'><strong>{$libro['Titulo']}</strong></p>
                    </div>
                    <form action='php/acciones_prestamo.php' method='POST'>
                        <input type='hidden' name='id_libro' value='{$libro['Id']}'>
                        <input type='hidden' name='accion' value='solicitar'>
                        <button type='submit' class='btn-small' style='padding: 12px 25px; min-width: 120px;'>Solicitar</button>
                    </form>
                  </div>";
        }
        ?>
    </section>
    <section class="profile-card" style="padding: 30px; margin-top: 40px;">
        <h2 style="font-size: 1.8rem;"><i class="fa-solid fa-bookmark"></i> Mis Préstamos</h2>
        <div class="divisor"></div>
        <?php
        $stmt = $pdo->prepare("SELECT p.*, l.Titulo FROM Prestamos p JOIN Libros l ON p.Id_Libros = l.Id 
                               WHERE p.Id_Usuarios = ? AND p.Estado != 'Devuelto'");
        $stmt->execute([$usuario_id]);
        $prestamos = $stmt->fetchAll();
        if ($prestamos) {
            foreach ($prestamos as $p) {
                echo "<div class='item' style='margin-bottom: 15px; padding: 20px; border-left: 5px solid #2ecc71 !important;'>
                        <div class='libro-info'>
                            <p style='font-size: 1.1rem;'><strong>{$p['Titulo']}</strong></p>
                            <small style='color: #888;'>📅 Entrega: {$p['Fecha_devolucion']}</small>
                        </div>
                        <div style='display: flex; align-items: center; gap: 15px;'>
                            <span class='badge' style='color: #2ecc71;'>{$p['Estado']}</span>
                            <form action='php/acciones_prestamo.php' method='POST'>
                                <input type='hidden' name='id_prestamo' value='{$p['Id']}'>
                                <input type='hidden' name='accion' value='devolver'>
                                <button type='submit' style='background: none; border: none; cursor: pointer; color: #9d2c70; font-size: 1.6rem;'>
                                    <i class='fa-solid fa-circle-check'></i>
                                </button>
                            </form>
                        </div>
                      </div>";
            }
        } else {
            echo "<p style='text-align: center; color: #999; padding: 20px;'>No tienes lecturas activas. ¡Pide un libro!</p>";
        }
        ?>
    </section>
</main>
<footer style="background: #000; color: #fff; padding: 30px; text-align: center; margin-top: 50px;">
    <p>© 2026 BlackSoft - El conocimiento es poder</p>
</footer>
</body>
</html>