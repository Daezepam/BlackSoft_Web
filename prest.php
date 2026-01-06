<?php
session_start();
require_once __DIR__ . '/php/bd.php'; 

// Usamos las variables que definiste en tu login.php
$nombre_usuario = $_SESSION['usuario'] ?? 'Invitado';
$usuario_id = $_SESSION['usuario_id'] ?? null;

// Buscamos qué libros ya tiene prestados para bloquear el botón
$libros_prestados = [];
if ($usuario_id) {
    $stmt_check = $pdo->prepare("SELECT Id_Libros FROM prestamos WHERE Id_Usuarios = ? AND Estado = 'Activo'");
    $stmt_check->execute([$usuario_id]);
    $libros_prestados = $stmt_check->fetchAll(PDO::FETCH_COLUMN);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamos - BlackSoft</title>
    <link rel="stylesheet" href="./CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header>
    <div class="logo">
        <a href="index.php"><img src="img/logo2.png" alt="BlackSoft" style="height: 45px;"></a>
    </div>
    <nav>
        <a href="index.php">Inicio</a>
        <a href="resenas.php">Reseñas</a>
        <a href="perfil.php">Mi Perfil (<?php echo htmlspecialchars($nombre_usuario); ?>)</a>
    </nav>
</header>

<main class="container">
    <h1 class="titulo-seccion">Gestión de Préstamos</h1>

    <section class="profile-card">
        <h2><i class="fa-solid fa-book-open"></i> Catálogo Disponible</h2>
        <?php
        try {
            $stmt = $pdo->query("SELECT Id, Titulo FROM libros LIMIT 10");
            while ($libro = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='item'>
                        <div class='info-libro'>
                            <p><strong>Título:</strong> " . htmlspecialchars($libro['Titulo']) . "</p>
                        </div>";
                
                if (!$usuario_id) {
                    echo "<a href='login.php?redirect=prest.php' class='btn-small' style='text-decoration:none;'>Solicitar</a>";
                } else {
                    // Si ya existe en la tabla prestamos, deshabilitamos el botón
                    if (in_array($libro['Id'], $libros_prestados)) {
                        echo "<button class='btn-small' style='background-color: #555; cursor: not-allowed;' disabled>Ya solicitado</button>";
                    } else {
                        echo "<form action='php/procesar_prestamo.php' method='POST' style='display:inline;'>
                                <input type='hidden' name='id_libro' value='{$libro['Id']}'>
                                <button type='submit' class='btn-small'>Solicitar</button>
                              </form>";
                    }
                }
                echo "</div>";
            }
        } catch (PDOException $e) { echo "Error cargando libros."; }
        ?>
    </section>

    <section class="profile-card">
        <h2><i class="fa-solid fa-clock-rotate-left"></i> Mis préstamos activos</h2>
        <?php
        if ($usuario_id) {
            try {
                // Sincronizado con tu phpMyAdmin: Id_Usuarios y Estado 'Activo'
                $sql = "SELECT p.*, l.Titulo FROM prestamos p 
                        JOIN libros l ON p.Id_Libros = l.Id 
                        WHERE p.Id_Usuarios = ? AND p.Estado = 'Activo'";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$usuario_id]);
                
                $hay_prestamos = false;
                while ($pres = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $hay_prestamos = true;
                    echo "<div class='item'>
                            <p><strong>" . htmlspecialchars($pres['Titulo']) . "</strong> - Vence: {$pres['Fecha_devolucion']}</p>
                            <span class='status' style='color: #9d2c70;'>{$pres['Estado']}</span>
                          </div>";
                }
                
                if (!$hay_prestamos) {
                    echo "<p>No tienes préstamos activos.</p>";
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            echo "<p>Inicia sesión para ver tus préstamos.</p>";
        }
        ?>
    </section>
</main>

<footer style="text-align: right; padding: 20px 50px;">
    <p>© 2025 BlackSoft - Todos los derechos reservados</p>
</footer>

</body>
</html>