<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Biblioteca BlackSoft</title>
  <link rel="stylesheet" href="./CSS/style.css">
  <link rel="icon" href="img/logo.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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

        <?php if (isset($_SESSION['usuario_id'])): ?>
            <a href="perfil.php">Mi Perfil</a>

            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Admin'): ?>
                <a href="admin.php" style="font-weight:bold; color: #f1c40f;">
                    Panel Admin
                </a>
            <?php endif; ?>

            <a href="logout.php">Cerrar sesión</a>
        <?php else: ?>
            <a href="login.php">Ingresar</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <section class="carrusel">
        <div class="slides">
            <img src="img/c3.png" alt="Imagen 1">
            <img src="img/c2.png" alt="Imagen 2">
            <img src="img/c1.png" alt="Imagen 3">
        </div>
        <button class="Anterior">&#10094;</button>
        <button class="Siguiente">&#10095;</button>
    </section>

    <section class="bienv">
        <div class="bienvenida">
            <a href="info.html" class="btn-img">
                <img src="img/logo2.png" alt="logobtn">
            </a>
            <h3>Tu portal de acceso a una amplia colección de libros digitales y recursos bibliográficos.</h3>
        </div>
    </section>

    <section class="tarjetas">
        <h2>SECCIONES</h2>
        <div class="cards-container">
            <a href="libros_populares.php" class="card">
                <img src="img/s1.png" alt="Libros Favoritos" class="card-img">
                <img src="img/ss1.png" class="card-img-hover">
                <h3>LIBROS POPULARES</h3>
                <p>Explora los libros más populares entre los lectores.</p>
            </a>

            <a href="libros.php" class="card">
                <img src="img/s2.png" alt="Lib" class="card-img">
                <img src="img/ss2.png" class="card-img-hover">
                <h3>LIBROS</h3>
                <p>Descubre toda la colección de libros de BlackSoft.</p>
            </a>

            <a href="resenas.php" class="card">
                <img src="img/s3.png" alt="Resen" class="card-img">
                <img src="img/ss3.png" class="card-img-hover">
                <h3>RESEÑAS</h3>
                <p>¡Lee reseñas de otros usuarios y deja la tuya!</p>
            </a>
        </div>
    </section>

    <section class="prest">
        <h2>PRÉSTAMOS</h2>
        <div class="prestamo">
            <a href="prest.php" class="pr">
                <img src="img/s4.png" alt="Prest" class="pr-img">
                <img src="img/ss4.png" class="pr-img-hover">
                <h3>PRÉSTAMOS</h3>
                <p>Pide un préstamo de tu libro favorito.</p>
            </a>
        </div>
    </section>
</main>

<footer>
    <p>© 2025 BlackSoft - Todos los derechos reservados</p>
</footer>

<script src="script.js"></script>
</body>
</html>