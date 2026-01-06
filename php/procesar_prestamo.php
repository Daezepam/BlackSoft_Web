<?php
session_start();
require_once 'bd.php'; 

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_libro'])) {
    $id_usuario = $_SESSION['usuario_id'];
    $id_libro = $_POST['id_libro'];
    $fecha_actual = date('Y-m-d');
    $fecha_dev = date('Y-m-d', strtotime('+15 days'));

    try {
        // Validación: Id_Usuarios
        $check = $pdo->prepare("SELECT COUNT(*) FROM prestamos WHERE Id_Libros = ? AND Id_Usuarios = ? AND Estado = 'Activo'");
        $check->execute([$id_libro, $id_usuario]);
        
        if ($check->fetchColumn() == 0) {
            $sql = "INSERT INTO prestamos (Id_Libros, Id_Usuarios, Fecha_prest, Fecha_devolucion, Estado) 
                    VALUES (?, ?, ?, ?, 'Activo')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_libro, $id_usuario, $fecha_actual, $fecha_dev]);
        }

        header("Location: ../prest.php?success=1");
    } catch (PDOException $e) {
        header("Location: ../prest.php?error=1");
    }
}