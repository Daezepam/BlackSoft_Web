<?php
session_start();
require_once __DIR__ . '/php/bd.php';

$nombre_usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Santiago'; 
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
    
    <style>
        /* ESTO HACE QUE EL RECUADRO SEA ESTÁTICO */
        textarea.input-dark {
            resize: none; /* Bloquea el estiramiento */
            overflow-y: auto; /* Permite scroll interno si el texto es muy largo */
        }

        /* Espaciado extra para que se vea bacano */
        .form-resena {
            margin-bottom: 50px;
        }

        .card-resena {
            margin-bottom: 20px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05); /* Toque oscuro transparente */
            border-radius: 10px;
            border-left: 4px solid #9d2c70;
        }

        /* Estilo para la alerta de advertencia */
        .alert-warning {
            background: #f1c40f; 
            color: #000; 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            text-align: center; 
            font-weight: bold;
            border: 1px solid #d4ac0d;
        }
    </style>
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

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert-success" style="background: #2ecc71; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
            <i class="fa-solid fa-circle-check"></i> ¡Reseña publicada con éxito!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'ya_resenado'): ?>
        <div class="alert-warning">
            <i class="fa-solid fa-triangle-exclamation"></i> ¡YA HAS RESEÑADO ESTE LIBRO ANTERIORMENTE!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'error_libro'): ?>
        <div class="alert-error" style="background: #e74c3c; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
            <i class="fa-solid fa-circle-xmark"></i> El libro escrito no existe. Verifica el título exacto.
        </div>
    <?php endif; ?>

    <div class="form-resena">
        <form action="php/guardar_resena.php" method="POST">
            <label>Título del Libro</label>
            <input type="text" name="libro" class="input-dark" placeholder="Ej: Don Quijote de la Mancha" required>

            <label>Tu Opinión</label>
            <textarea name="comentario" class="input-dark" rows="5" placeholder="Escribe tu reseña aquí..." required></textarea>

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
                WHERE R.Estado = 'Activa'
                ORDER BY R.Fecha DESC";
        $stmt = $pdo->query($sql);
        
        if ($stmt->rowCount() > 0) {
            while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $estrellas = str_repeat("⭐", $r['Calificacion']);
                echo "
                <div class='card-resena'>
                    <h3>" . htmlspecialchars($r['TituloLibro']) . "</h3>
                    <small style='color: #888;'>" . $r['Fecha'] . "</small>
                    <div class='estrellas-container' style='margin: 10px 0;'>$estrellas</div>
                    <p style='font-style: italic;'>\"" . htmlspecialchars($r['Comentario']) . "\"</p>
                </div>";
            }
        } else {
            echo "<p class='no-resenas' style='text-align: center; opacity: 0.6;'>Aún no hay reseñas. ¡Sé el primero!</p>";
        }
    } catch (PDOException $e) {
        echo "<p class='no-resenas'>Error al cargar las reseñas.</p>";
    }
    ?>

</main>

<footer style="text-align: center; padding: 40px 0; opacity: 0.7;">
    © 2026 BlackSoft Web - Sistema de Reseñas
</footer>

<script src="script.js"></script>
</body>
</html>