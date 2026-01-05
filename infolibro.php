<?php
require_once __DIR__ . '/php/bd.php';

/* =========================
   VALIDAR ID
========================= */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Libro no válido");
}

$idLibro = $_GET['id'];

/* =========================
   CONSULTA DEL LIBRO
========================= */
$sqlLibro = "
    SELECT 
        L.Id,
        L.Titulo,
        L.Descripcion,
        L.Portada,
        L.ISBN
    FROM Libros L
    WHERE L.Id = :id
";

$stmtLibro = $pdo->prepare($sqlLibro);
$stmtLibro->execute(['id' => $idLibro]);
$libro = $stmtLibro->fetch(PDO::FETCH_ASSOC);

if (!$libro) {
    die("Libro no encontrado");
}

/* =========================
   AUTORES
========================= */
$sqlAutores = "
    SELECT A.Nombre
    FROM Autores A
    JOIN Libros_Autores LA ON A.Id = LA.Id_Autores
    WHERE LA.Id_Libros = :id
";
$stmtAutores = $pdo->prepare($sqlAutores);
$stmtAutores->execute(['id' => $idLibro]);

$autores = [];
while ($a = $stmtAutores->fetch(PDO::FETCH_ASSOC)) {
    $autores[] = $a['Nombre'];
}

/* =========================
   CATEGORÍAS
========================= */
$sqlCategorias = "
    SELECT C.Nombre
    FROM Categorias C
    JOIN Libros_Categorias LC ON C.Id = LC.Id_Categorias
    WHERE LC.Id_Libros = :id
";
$stmtCategorias = $pdo->prepare($sqlCategorias);
$stmtCategorias->execute(['id' => $idLibro]);

$categorias = [];
while ($c = $stmtCategorias->fetch(PDO::FETCH_ASSOC)) {
    $categorias[] = $c['Nombre'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?php echo $libro['Titulo']; ?> - BS</title>
  <link rel="stylesheet" href="./CSS/style.css">
  <link rel="icon" href="img/logo.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<header>
    <div class="logo">
        <a href="index.php">
            <img src="img/logo2.png" alt="BlackSoft - Biblioteca Virtual">
        </a>
    </div>

    <nav>
        <button id="themeToggle" class="theme-btn">
            <i class="fa-solid fa-moon"></i>
        </button>

        <a href="index.php">Inicio</a>
        <a href="libros.php">Libros</a>
        <a href="login.php">Ingresar</a>
    </nav>
</header>

<main class="book-main">

<section class="book-card">

    <div class="book-header">
        <h2 class="book-title">
            <?php echo $libro['Titulo']; ?>
        </h2>
    </div>

    <div class="book-content">

        <div class="book-image">
            <img 
              src="img/libros_populares/<?php echo $libro['Portada']; ?>" 
              alt="<?php echo $libro['Titulo']; ?>"
            >
        </div>

        <div class="book-info">
            <p><strong>Autor:</strong> <?php echo implode(', ', $autores); ?></p>
            <p><strong>Categoría:</strong> <?php echo implode(', ', $categorias); ?></p>
            <p><strong>ISBN:</strong> <?php echo $libro['ISBN']; ?></p>
        </div>

    </div>

    <div class="book-description">
        <h3>Descripción</h3>
        <p>
            <?php echo $libro['Descripcion']; ?>
        </p>
    </div>

    <div class="book-actions">
        <a href="resenar.php?id=<?php echo $libro['Id']; ?>" class="book-btn review">
            Reseñar
        </a>
        <a href="prestamo.php?id=<?php echo $libro['Id']; ?>" class="book-btn loan">
            Pedir préstamo
        </a>
    </div>

</section>

</main>

<footer>
  <p>© 2025 BlackSoft - Biblioteca Virtual BS</p>
</footer>

<script src="script.js"></script>
</body>
</html>
