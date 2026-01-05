<?php
session_start();
require_once __DIR__ . '/php/bd.php';

/** * IMPORTANTE: Para que no diga 'Invitado', en tu archivo de login.php 
 * debes tener una línea como esta: $_SESSION['usuario'] = $datos_del_usuario['usuario'];
 */
$nombre_usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Santiago'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reseñas - BlackSoft</title>
    <link rel="stylesheet" href="./CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background-color: #000; color: white; margin: 0; padding: 0; }
        
        /* CORRECCIÓN DE PROPORCIÓN DEL LOGO */
        .logo img {
            width: auto;
            height: 60px; /* Altura fija para mantener proporción */
            object-fit: contain;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 5%;
            background: #000;
        }

        nav a { color: white; text-decoration: none; margin-left: 20px; font-weight: bold; }

        .contenedor-principal { max-width: 800px; margin: 50px auto; padding: 20px; }

        /* Estilo del formulario */
        .form-resena {
            background: #111;
            padding: 30px;
            border-radius: 15px;
            border: 1px solid #333;
        }

        .input-dark {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px 0;
            background: #1a1a1a;
            border: 1px solid #444;
            color: white;
            border-radius: 8px;
            box-sizing: border-box;
        }

        .btn-publicar {
            background: #9d2c70; /* Color púrpura original */
            color: white;
            border: none;
            padding: 15px;
            width: 100%;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            font-size: 1.1rem;
        }

        /* Footer original */
        footer {
            text-align: center;
            padding: 30px 0;
            color: #444;
            font-size: 0.9rem;
            margin-top: 50px;
        }

        .card-resena {
            background: #151515;
            border-left: 4px solid #9d2c70;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <a href="index.php"><img src="img/logo2.png" alt="BlackSoft Logo"></a>
    </div>
    <nav>
        <a href="index.php">Inicio</a>
        <a href="perfil.php">Mi Perfil</a>
    </nav>
</header>

<main class="contenedor-principal">
    
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="font-size: 2.8rem; margin: 0;">Hola, <?php echo htmlspecialchars($nombre_usuario); ?></h1>
        <p style="color: #777; font-size: 1.2rem;">¿Qué te pareció tu última lectura?</p>
        <hr style="width: 100px; border: 2px solid #9d2c70; margin: 20px auto;">
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div style="background: #28a745; color: white; padding: 15px; border-radius: 10px; text-align: center; margin-bottom: 20px;">
            ¡Reseña publicada con éxito!
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

    <h2 style="margin-top: 60px; text-align: center;">Reseñas de la Comunidad</h2>

    <?php
    try {
        $sql = "SELECT R.Comentario, R.Calificacion, R.Fecha, L.Titulo AS TituloLibro 
                FROM resenas R
                JOIN Libros L ON R.Id_Libros = L.Id
                WHERE R.Estado = 'Activa'
                ORDER BY R.Fecha DESC";
        $stmt = $pdo->query($sql);
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $estrellas = str_repeat("⭐", $r['Calificacion']);
            echo "
            <div class='card-resena'>
                <h3 style='color: #9d2c70; margin: 0;'>{$r['TituloLibro']}</h3>
                <small style='color: #555;'>{$r['Fecha']}</small>
                <div style='margin: 10px 0;'>$estrellas</div>
                <p style='color: #ccc;'>\"" . htmlspecialchars($r['Comentario']) . "\"</p>
            </div>";
        }
    } catch (PDOException $e) {
        echo "<p style='text-align: center; color: #444;'>No hay reseñas aún.</p>";
    }
    ?>

</main>

<footer>
    © 2026 BlackSoft Web - Sistema de Reseñas
</footer>

</body>
</html>