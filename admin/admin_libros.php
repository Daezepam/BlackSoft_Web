<?php
session_start();
// Ajustamos la ruta para llegar a bd.php desde la carpeta admin/
require_once __DIR__ . '/../php/bd.php';

/* PROTECCIÓN */
if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'Admin') {
    header("Location: ../index.php");
    exit;
}

$titulo   = $_POST['titulo'] ?? '';
$autor    = $_POST['autor'] ?? '';
$genero   = $_POST['genero'] ?? '';
$anio     = $_POST['anio'] ?? null;
$isbn     = $_POST['isbn'] ?? '';

// --- LÓGICA NUEVA PARA LA IMAGEN ---
$ruta_final_bd = "img/default.png"; // Imagen por defecto por si acaso

if (isset($_FILES['portada']) && $_FILES['portada']['error'] === 0) {
    $nombre_archivo = time() . "_" . $_FILES['portada']['name']; // Nombre único para que no se sobreescriban
    $directorio_subida = "../img/portadas/"; 

    // Crear la carpeta si no existe
    if (!file_exists($directorio_subida)) {
        mkdir($directorio_subida, 0777, true);
    }

    if (move_uploaded_file($_FILES['portada']['tmp_name'], $directorio_subida . $nombre_archivo)) {
        // Esta es la ruta que guardaremos en la BD
        $ruta_final_bd = "img/portadas/" . $nombre_archivo;
    }
}
// -----------------------------------

if ($titulo && $isbn) {

    // 1️⃣ Insertar libro (Usamos $ruta_final_bd en lugar de $imagen)
    $sqlLibro = "
        INSERT INTO Libros (ISBN, Titulo, Descripcion, Portada, Disponibilidad)
        VALUES (:isbn, :titulo, '', :portada, 1)
    ";
    $stmt = $pdo->prepare($sqlLibro);
    $stmt->execute([
        'isbn'    => $isbn,
        'titulo'  => $titulo,
        'portada' => $ruta_final_bd
    ]);

    $idLibro = $pdo->lastInsertId();

    // 2️⃣ Autor (Tu código original está bien aquí)
    if ($autor) {
        $stmtAutor = $pdo->prepare("SELECT Id FROM Autores WHERE Nombre = :nombre");
        $stmtAutor->execute(['nombre' => $autor]);
        $autorBD = $stmtAutor->fetch();

        if (!$autorBD) {
            $pdo->prepare("INSERT INTO Autores (Nombre) VALUES (:n)")
                ->execute(['n' => $autor]);
            $idAutor = $pdo->lastInsertId();
        } else {
            $idAutor = $autorBD['Id'];
        }

        $pdo->prepare("
            INSERT INTO Libros_Autores (Id_Libros, Id_Autores)
            VALUES (:libro, :autor)
        ")->execute([
            'libro' => $idLibro,
            'autor' => $idAutor
        ]);
    }

    // 3️⃣ Categoría (Tu código original está bien aquí)
    if ($genero) {
        $stmtCat = $pdo->prepare("SELECT Id FROM Categorias WHERE Nombre = :n");
        $stmtCat->execute(['n' => $genero]);
        $catBD = $stmtCat->fetch();

        if (!$catBD) {
            $pdo->prepare("INSERT INTO Categorias (Nombre) VALUES (:n)")
                ->execute(['n' => $genero]);
            $idCat = $pdo->lastInsertId();
        } else {
            $idCat = $catBD['Id'];
        }

        $pdo->prepare("
            INSERT INTO Libros_Categorias (Id_Libros, Id_Categorias)
            VALUES (:libro, :cat)
        ")->execute([
            'libro' => $idLibro,
            'cat'   => $idCat
        ]);
    }

    header("Location: ../admin.php?status=libro_creado");
} else {
    header("Location: ../admin.php?status=error");
}
exit;