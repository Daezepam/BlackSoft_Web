<?php
session_start();
require_once __DIR__ . '/php/bd.php';

$nombre_usuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Santiago'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reseñas - BS</title>
    <link rel="stylesheet" href="./CSS/style.css">
    <link rel="icon" href="img/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body class="body-resena">

<header>
    <div class="logo">
        <a href="index.php"><img src="img/logo2.png" alt="BlackSoft Logo"></a>
    </div>
    <nav>
        <button id="themeToggle" class="theme-btn">
            <i class="fa-solid fa-moon"></i>
        </button>
        <a href="index.php">Inicio</a>
        <a href="perfil.php">Mi Perfil</a>
    </nav>
</header>

<main class="contenedor-principal">
    
    <div class="resenabienvenida">
        <h1>Hola, <?php echo htmlspecialchars($nombre_usuario); ?></h1>
        <p>¿Qué te pareció tu última lectura?</p>
        <div class="divisor-resena"></div>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'success'): ?>
            <div class="alert-success">
                <i class="fa-solid fa-circle-check"></i> ¡Reseña publicada con éxito!
            </div>
        <?php elseif ($_GET['status'] == 'ya_resenado'): ?>
            <div class="alert-warning">
                <i class="fa-solid fa-triangle-exclamation"></i> ¡YA HAS RESEÑADO ESTE LIBRO ANTERIORMENTE!
            </div>
        <?php elseif ($_GET['status'] == 'error_libro'): ?>
            <div class="alert-error">
                <i class="fa-solid fa-circle-xmark"></i> El libro escrito no existe. Verifica el título exacto.
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="form-resena">
        <form action="php/guardar_resena.php" method="POST">
            <label>Título del Libro</label>
            <input type="text" name="libro" class="input-dark" maxlength="100" placeholder="Ej: Don Quijote de la Mancha" required>

            <label>Tu Opinión</label>
            <textarea name="comentario" id="comentario_resena" class="input-dark" rows="5" maxlength="500" placeholder="Escribe tu reseña aquí..." oninput="actualizarContador()" required></textarea>
            
            <div class="contador-container">
                <span id="num_caracteres">0</span> / 500
            </div>

            <label>Calificación</label>
            <select name="puntos" class="input-dark">
                <option value="5">⭐⭐⭐⭐⭐ (Excelente)</option>
                <option value="4">⭐⭐⭐⭐ (Bueno)</option>
                <option value="3">⭐⭐⭐ (Regular)</option>
                <option value="2">⭐⭐ (Malo)</option>
                <option value="1">⭐ (Pésimo)</option>
            </select>

            <button type="submit" class="btn-publicar">Publicar Reseña</button>
        </form>
    </div>

    <h2 class="comunidad-titulo">Reseñas de la Comunidad</h2>

    <?php
    try {
        $sql = "SELECT R.Comentario, R.Calificacion, R.Fecha, L.Titulo AS TituloLibro 
                FROM resenas R
                JOIN libros L ON R.Id_Libros = L.Id
                ORDER BY R.Fecha DESC";
        $stmt = $pdo->query($sql);
        
        if ($stmt->rowCount() > 0) {
            while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $estrellas = str_repeat("⭐", $r['Calificacion']);
                echo "
                <div class='card-resena'>
                    <h3>" . htmlspecialchars($r['TituloLibro']) . "</h3>
                    <small class='resena-fecha'>" . $r['Fecha'] . "</small>
                    <div class='estrellas-container'>$estrellas</div>
                    <p class='resena-texto'>\"" . htmlspecialchars($r['Comentario']) . "\"</p>
                </div>";
            }
        } else {
            echo "<p class='no-resenas'>Aún no hay reseñas. ¡Sé el primero!</p>";
        }
    } catch (PDOException $e) {
        echo "<p class='no-resenas'>Error al cargar las reseñas.</p>";
    }
    ?>

</main>

<footer class="main-footer">
    © 2026 BlackSoft Web - Sistema de Reseñas
</footer>

<script>
function actualizarContador() {
    const area = document.getElementById('comentario_resena');
    const display = document.getElementById('num_caracteres');
    display.innerText = area.value.length;
    
    if (area.value.length >= 500) {
        display.classList.add('contador-limite');
    } else {
        display.classList.remove('contador-limite');
    }
}
</script>
<script src="script.js"></script>
</body>
</html>