<?php
session_start();
require_once __DIR__ . '/php/bd.php';

if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'Admin') {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Admin - BlackSoft</title>
  <link rel="stylesheet" href="./CSS/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="admin-body">

<header>
  <a class="logo" href="index.php"><img src="img/logoa.png" alt="Logo"></a>
  <nav><a href="logout.php">Cerrar sesión</a></nav>
</header>

<main class="admin-main">
  <h2 class="admin-title">Panel de Control</h2>

  <?php if(isset($_GET['status'])): ?>
    <div class="alert">Acción procesada: <?php echo htmlspecialchars($_GET['status']); ?></div>
  <?php endif; ?>

  <section class="admin-section">
    <h3>Gestión de Libros</h3>
    <form class="admin-form" action="admin/admin_libros.php" method="post" enctype="multipart/form-data">
      <div class="input-group">
        <input type="text" name="titulo" id="tit_libro" maxlength="100" placeholder="Título" oninput="contar('tit_libro','c_tit')" required>
        <small><span id="c_tit">0</span>/100</small>
      </div>
      
      <input type="text" name="autor" maxlength="100" placeholder="Autor" required>
      <input type="text" name="genero" maxlength="50" placeholder="Género">
      <input type="text" name="isbn" maxlength="20" placeholder="ISBN" required>
      
      <label>Portada:</label>
      <input type="file" name="portada" accept="image/*" required>

      <button type="submit">Guardar libro</button>
    </form>

    <div class="admin-grid">
      <?php
      $stmt = $pdo->query("SELECT Id, Titulo FROM Libros ORDER BY Id DESC");
      while ($libro = $stmt->fetch()) {
          echo "<div class='admin-card'>
                  <span>{$libro['Titulo']}</span>
                  <a href='admin/admin_eliminar_libro.php?id={$libro['Id']}' onclick=\"return confirm('¿Borrar?')\">Eliminar</a>
                </div>";
      }
      ?>
    </div>
  </section>

  <section class="admin-section">
    <h3>Moderación de Reseñas</h3>
    <table class="admin-table" style="width:100%; color:white;">
      <thead>
        <tr><th>Usuario</th><th>Libro</th><th>Comentario</th><th>Acción</th></tr>
      </thead>
      <tbody>
        <?php
        $res = $pdo->query("SELECT R.Id, U.Nombre, L.Titulo, R.Comentario FROM resenas R JOIN usuarios U ON R.Id_Usuarios = U.Id JOIN libros L ON R.Id_Libros = L.Id");
        while($r = $res->fetch()){
            echo "<tr>
                    <td>{$r['Nombre']}</td>
                    <td>{$r['Titulo']}</td>
                    <td>".htmlspecialchars(substr($r['Comentario'],0,40))."...</td>
                    <td><a href='admin/eliminar_resena.php?id={$r['Id']}'>❌</a></td>
                  </tr>";
        }
        ?>
      </tbody>
    </table>
  </section>
</main>

<script>
function contar(idInput, idContador) {
    const input = document.getElementById(idInput);
    const contador = document.getElementById(idContador);
    contador.innerText = input.value.length;
}
</script>
</body>
</html>