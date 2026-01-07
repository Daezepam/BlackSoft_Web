<?php
session_start();
require_once __DIR__ . '/bd.php';
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}
$id_usuario = $_SESSION['usuario_id'];
$titulo_libro = trim($_POST['libro'] ?? '');
$comentario = trim($_POST['comentario'] ?? '');
$puntos = intval($_POST['puntos'] ?? 5);
if ($titulo_libro && $comentario) {
    try {
        $stmtLibro = $pdo->prepare("SELECT Id FROM Libros WHERE Titulo = ? LIMIT 1");
        $stmtLibro->execute([$titulo_libro]);
        $libro = $stmtLibro->fetch();
        if (!$libro) {
            header("Location: ../resenas.php?status=error_libro");
            exit();
        }
        $ins = $pdo->prepare("INSERT INTO Resenas (Id_Usuarios, Id_Libros, Comentario, Calificacion, Fecha, Estado) VALUES (?, ?, ?, ?, NOW(), 'Activa')");
        $ins->execute([$id_usuario, $libro['Id'], $comentario, $puntos]);
        header("Location: ../resenas.php?status=success");
        exit();
    } catch (PDOException $e) {
        header("Location: ../resenas.php?status=error");
        exit();
    }
}