<?php
session_start();
require_once __DIR__ . '/php/bd.php';

if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'Admin') {
    header("Location: index.php");
    exit;
}

$titulo   = $_POST['titulo'] ?? '';
$autor    = $_POST['autor'] ?? '';
$genero   = $_POST['genero'] ?? '';
$anio     = $_POST['anio'] ?? null;
$isbn     = $_POST['isbn'] ?? '';
$imagen   = $_POST['imagen'] ?? '';

if ($titulo && $isbn) {

    // 1️⃣ Insertar libro
    $sqlLibro = "
        INSERT INTO Libros (ISBN, Titulo, Descripcion, Portada, Disponibilidad)
        VALUES (:isbn, :titulo, '', :portada, 1)
    ";
    $stmt = $pdo->prepare($sqlLibro);
    $stmt->execute([
        'isbn'    => $isbn,
        'titulo'  => $titulo,
        'portada' => $imagen
    ]);

    $idLibro = $pdo->lastInsertId();

    // 2️⃣ Autor
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

    // 3️⃣ Categoría
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
}

header("Location: admin.php");
exit;
