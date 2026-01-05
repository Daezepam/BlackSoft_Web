<?php
session_start();
require_once __DIR__ . '/php/bd.php';

/* CATEGORÍA SELECCIONADA */
$categoria = $_GET['cat'] ?? null;

/* CONSULTA DE LIBROS */
if ($categoria) {
    $sql = "
        SELECT L.Id, L.Titulo, L.Descripcion, L.Portada
        FROM Libros L
        JOIN Libros_Categorias LC ON L.Id = LC.Id_Libros
        JOIN Categorias C ON LC.Id_Categorias = C.Id
        WHERE C.Nombre = :categoria
        AND L.Disponibilidad = 1
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['categoria' => $categoria]);
} else {
    $sql = "
        SELECT Id, Titulo, Descripcion, Portada
        FROM Libros
        WHERE Disponibilidad = 1
    ";
    $stmt = $pdo->query($sql);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libros - BS</title>
    <link rel="stylesheet" href="./CSS/style.css">
    <link rel="icon" href="img/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <div class="logo">
        <a href="index.php" class="logomain">
            <img src="img/logo2.png" alt="mainlogobtn">
        </a>
    </div>
    
    <div class="search">
        <input id="busca" type="text" placeholder="Buscar..." class="busca">
        <button id="searchbtn" class="buscabtn">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </div>

    <nav>
        <button id="themeToggle" class="theme-btn">
            <i class="fa-solid fa-moon"></i>
        </button>
        <a href="info.php">Acerca</a>
        <a href="index.php">Inicio</a>
        <?php if(isset($_SESSION['nombre'])): ?>
            <a href="perfil.php">Perfil</a>
        <?php else: ?>
            <a href="login.php">Ingresar</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <div class="titulo">
        <h2><?php echo $categoria ? "Categoría: " . htmlspecialchars($categoria) : "Nuestra Colección"; ?></h2>
    </div>

    <div class="categorias-wrapper">
        <div class="categorias-boton" onclick="toggleCategorias()">
            Explorar Categorías <i class="fa-solid fa-chevron-down" style="margin-left:10px;"></i>
        </div>

        <div class="categorias-desplegable" id="menuCategorias">
            <div class="categorias-grid">
                <a href="libros.php?cat=Aventura">Aventura</a>
                <a href="libros.php?cat=Fantasía">Fantasía</a>
                <a href="libros.php?cat=Ciencia Ficción">Ciencia Ficción</a>
                <a href="libros.php?cat=Historia">Historia</a>
                <a href="libros.php?cat=Negocios">Negocios</a>
                <a href="libros.php?cat=Psicología">Psicología</a>
                <a href="libros.php?cat=Poesía">Poesía</a>
                <a href="libros.php?cat=Filosofía">Filosofía</a>
                <a href="libros.php?cat=Arte">Arte</a>
                <a href="libros.php" class="ver-todos">Ver todos los libros</a>
            </div>
        </div>
    </div>

    <div class="libros-container">
        <?php
        $hayLibros = false;
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $hayLibros = true;
            echo "<a href='infolibro.php?id={$fila['Id']}' class='libro-link'>";
            echo "<div class='libro-card'>";
            echo "<img src='img/libros_populares/{$fila['Portada']}' class='libro-img' alt='Portada'>";
            echo "<div class='libro-info'>";
            echo "<div class='libro-titulo'>".htmlspecialchars($fila['Titulo'])."</div>";
            echo "<div class='libro-descripcion'>".htmlspecialchars($fila['Descripcion'])."</div>";
            echo "</div>";
            echo "</div>";
            echo "</a>";
        }

        if (!$hayLibros) {
            echo "<div style='grid-column: 1/-1; text-align:center; padding:50px;'>";
            echo "<i class='fa-solid fa-book-open' style='font-size:3rem; color:var(--mof); opacity:0.5;'></i>";
            echo "<p style='margin-top:15px; font-size:1.2rem;'>No hay libros disponibles en esta categoría.</p>";
            echo "</div>";
        }
        ?>
    </div>
</main>

<footer>
    © 2026 BlackSoft - Todos los derechos reservados
</footer>

<script>
function toggleCategorias() {
    let menu = document.getElementById("menuCategorias");
    if (menu.style.display === "block") {
        menu.style.display = "none";
    } else {
        menu.style.display = "block";
    }
}
// Cerrar menú si se hace clic fuera
window.onclick = function(event) {
    if (!event.target.matches('.categorias-boton')) {
        let dropdown = document.getElementById("menuCategorias");
        if (dropdown.style.display === "block") {
            dropdown.style.display = "none";
        }
    }
}
</script>
<script src="script.js"></script>
</body>
</html>