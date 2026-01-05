<?php
session_start();
require_once __DIR__ . '/php/bd.php';

/*PROTECC*/
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
      ¡Bienvenido <?php echo htmlspecialchars($_SESSION['nombre']); ?> (Admin)!
  </h2>

  <!-- ================= LIBROS ================= -->
  <section class="admin-section">
    <h3>Libros</h3>

    <!-- FORMULARIO CREAR LIBRO -->
    <form class="admin-form" action="admin/admin_libros.php" method="post">
      <input type="hidden" name="id">

      <input type="text" name="titulo" placeholder="Título" required>
      <input type="text" name="autor" placeholder="Autor" required>
      <input type="text" name="genero" placeholder="Género">
      <input type="number" name="anio" placeholder="Año">
      <input type="text" name="isbn" placeholder="ISBN" required>
      <input type="text" name="imagen" placeholder="URL portada">

      <button type="submit">Guardar libro</button>
    </form>

    <!-- LISTADO REAL DE LIBROS -->
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

                // Eliminar libro
                echo "<a href='admin/admin_eliminar_libro.php?id={$libro['Id']}'
                         class='admin-btn delete'
                         onclick=\"return confirm('¿Eliminar este libro?')\">
                         Eliminar
                      </a>";

                echo "</div>";
                echo "</div>";
            }
        }
    ?>
    </div>
  </section>

  <!-- ================= RESEÑAS ================= -->
  <section class="admin-section">
    <h3>Reseñas</h3>

    <div class="admin-grid">
        <!-- Aquí luego cargamos reseñas reales -->
        <p>no tenemooooooooooooooooos.</p>
    </div>
  </section>

  <!-- ================= ADMINS ================= -->
  <section class="admin-section">
    <h3>Administradores</h3>

    <form class="admin-form" action="admin_admins.php" method="post">
      <input type="text" name="usuario" placeholder="Nombre" required>
      <input type="email" name="email" placeholder="Correo" required>
      <input type="password" name="password" placeholder="Contraseña" required>
      <button type="submit">Crear admin</button>
    </form>

    <div class="admin-grid">
        <!-- Aquí luego puedes listar admins -->
        <p>aun no jala.</p>
    </div>
  </section>

</main>

<footer>
    <p>© 2025 BlackSoft - Todos los derechos reservados</p>
</footer>

<script src="script.js"></script>
</body>
</html>
