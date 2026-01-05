<?php
session_start();
require_once __DIR__ . '/php/bd.php';

if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'Admin') {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    $pdo->prepare("DELETE FROM Libros_Autores WHERE Id_Libros = :id")->execute(['id' => $id]);
    $pdo->prepare("DELETE FROM Libros_Categorias WHERE Id_Libros = :id")->execute(['id' => $id]);
    $pdo->prepare("DELETE FROM Libros WHERE Id = :id")->execute(['id' => $id]);
}

header("Location: admin.php");
exit;
