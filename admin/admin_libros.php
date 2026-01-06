<?php
session_start();
require_once __DIR__ . '/../php/bd.php';

if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'Admin') {
    header("Location: ../index.php"); exit;
}

$titulo = substr(trim($_POST['titulo']), 0, 100);
$autor  = substr(trim($_POST['autor']), 0, 100);
$genero = substr(trim($_POST['genero']), 0, 50);
$isbn   = substr(trim($_POST['isbn']), 0, 20);
$ruta_bd = "img/portadas/default.png";

if (isset($_FILES['portada']) && $_FILES['portada']['error'] === 0) {
    $dir = "../img/portadas/";
    if (!file_exists($dir)) mkdir($dir, 0777, true);
    
    $ext = pathinfo($_FILES['portada']['name'], PATHINFO_EXTENSION);
    $nombre = time() . "_" . uniqid() . "." . $ext;
    
    if (move_uploaded_file($_FILES['portada']['tmp_name'], $dir . $nombre)) {
        $ruta_bd = "img/portadas/" . $nombre;
    }
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO Libros (ISBN, Titulo, Portada, Disponibilidad) VALUES (?, ?, ?, 1)");
    $stmt->execute([$isbn, $titulo, $ruta_bd]);
    $idLibro = $pdo->lastInsertId();

    if ($autor) {
        $st = $pdo->prepare("SELECT Id FROM Autores WHERE Nombre = ?");
        $st->execute([$autor]);
        $aBD = $st->fetch();
        $idA = $aBD ? $aBD['Id'] : null;
        if (!$idA) {
            $pdo->prepare("INSERT INTO Autores (Nombre) VALUES (?)")->execute([$autor]);
            $idA = $pdo->lastInsertId();
        }
        $pdo->prepare("INSERT INTO Libros_Autores (Id_Libros, Id_Autores) VALUES (?, ?)")->execute([$idLibro, $idA]);
    }

    if ($genero) {
        $stC = $pdo->prepare("SELECT Id FROM Categorias WHERE Nombre = ?");
        $stC->execute([$genero]);
        $cBD = $stC->fetch();
        $idC = $cBD ? $cBD['Id'] : null;
        if (!$idC) {
            $pdo->prepare("INSERT INTO Categorias (Nombre) VALUES (?)")->execute([$genero]);
            $idC = $pdo->lastInsertId();
        }
        $pdo->prepare("INSERT INTO Libros_Categorias (Id_Libros, Id_Categorias) VALUES (?, ?)")->execute([$idLibro, $idC]);
    }

    $pdo->commit();
    header("Location: ../admin.php?status=success");
} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: ../admin.php?status=error");
}