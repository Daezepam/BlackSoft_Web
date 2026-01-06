<?php
session_start();
require_once __DIR__ . '/php/bd.php';

/* PROTECCIÓN */
if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'Admin') {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Admin - BS</title>
  <link rel="stylesheet" href="./CSS/style.css">
  <link rel="icon" href="img/logo.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
      /* Estilo rápido para la tabla de reseñas dentro del grid */
      .admin-table { width: 100%; border-collapse: collapse; color: white; margin-top: 10px; }
      .admin-table th, .admin-table td { padding: 10px; border-bottom: 1px solid #444; text-align: left; }
      .admin-table th { color: #9d2c70; }
      .btn-delete-icon { color: #e74c3c; cursor: pointer; font-size: 1.2rem; }
  </style>
</head>

<body class="admin-body">

<header>
  <a class="logo logomain" href="index.php">
    <img src="img/logoa.png" alt="Logo">
  </a>

  <nav>
    <button id="themeToggle" class="theme-btn">
        <i class="fa-solid fa-moon"></i>
    </button>
    <a href="logout.php">Cerrar sesión</a>
  </nav>
</header>

<main class="admin-main">

  <h2 class="admin-title">
      ¡Bienvenido <?php echo htmlspecialchars($_SESSION['usuario']); ?> (Admin)!
  </h2>

  <section class="admin-section">
    <h3><i class="fa-solid fa-book"></i> Gestión de Libros</h3>

    <form class="admin-form" action="admin/admin_libros.php" method="post" enctype="multipart/form-data">
      <input type="text" name="titulo" placeholder="Título del Libro" required>
      <input type="text" name="autor" placeholder="Nombre del Autor" required>
      <input type="text" name="genero" placeholder="Género/Categoría">
      <input type="number" name="anio" placeholder="Año de publicación">
      <input type="text" name="isbn" placeholder="ISBN" required>
      
      <label style="color: #aaa; font-size: 0.8rem; margin-left: 10px;">Subir Portada:</label>
      <input type="file" name="portada" accept="image/*" required>

      <button type="submit">Guardar libro</button>
    </form>

    <div class="admin-grid">
    <?php
        $sql = "SELECT Id, Titulo FROM Libros ORDER BY Id DESC";
        $stmt = $pdo->query($sql);

        if ($stmt->rowCount() === 0) {
            echo "<p>No hay libros registrados.</p>";
        } else {
            while ($libro = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='admin-card'>";
                echo "<div class='admin-card-title'>" . htmlspecialchars($libro['Titulo']) . "</div>";
                echo "<div class='admin-card-actions'>";
                echo "<a href='admin/admin_eliminar_libro.php?id={$libro['Id']}'
                           class='admin-btn delete'
                           onclick=\"return confirm('¿Eliminar este libro y sus reseñas?')\">
                           <i class='fa-solid fa-trash'></i> Eliminar
                      </a>";
                echo "</div>";
                echo "</div>";
            }
        }
    ?>
    </div>
  </section>

  <section class="admin-section">
    <h3><i class="fa-solid fa-comments"></i> Moderación de Reseñas</h3>

    <div class="admin-grid" style="display: block;"> <?php
        $sqlRes = "SELECT R.Id, U.Nombre as Usuario, L.Titulo as Libro, R.Comentario 
                   FROM resenas R
                   JOIN usuarios U ON R.Id_Usuarios = U.Id
                   JOIN libros L ON R.Id_Libros = L.Id
                   ORDER BY R.Fecha DESC";
        $stmtRes = $pdo->query($sqlRes);

        if ($stmtRes->rowCount() === 0) {
            echo "<p style='opacity:0.6;'>No hay reseñas para moderar.</p>";
        } else {
            echo "<table class='admin-table'>";
            echo "<thead><tr><th>Usuario</th><th>Libro</th><th>Comentario</th><th>Acción</th></tr></thead>";
            echo "<tbody>";
            while ($res = $stmtRes->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($res['Usuario']) . "</td>";
                echo "<td>" . htmlspecialchars($res['Libro']) . "</td>";
                echo "<td><small>\"" . htmlspecialchars(substr($res['Comentario'], 0, 50)) . "...\"</small></td>";
                echo "<td>
                        <a href='admin/eliminar_resena.php?id={$res['Id']}' 
                           onclick=\"return confirm('¿Borrar esta reseña?')\" class='btn-delete-icon'>
                           <i class='fa-solid fa-circle-xmark'></i>
                        </a>
                      </td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        }
        ?>
    </div>
  </section>

  <section class="admin-section">
    <h3><i class="fa-solid fa-user-shield"></i> Administradores</h3>

    <form class="admin-form" action="admin_admins.php" method="post">
      <input type="text" name="usuario" placeholder="Nombre de usuario" required>
      <input type="email" name="email" placeholder="Correo electrónico" required>
      <input type="password" name="password" placeholder="Contraseña" required>
      <button type="submit">Crear nuevo admin</button>
    </form>
  </section>

</main>

<footer>
    <p>© 2026 BlackSoft - Panel de Control</p>
</footer>

<script src="script.js"></script>
</body>
</html>