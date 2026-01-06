<?php
session_start();
require_once __DIR__ . '/bd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario_id'])) {
    $id_libro = $_POST['id_libro'];
    $id_usuario = $_SESSION['usuario_id'];
    $fecha_prest = date('Y-m-d');
    $fecha_devo = date('Y-m-d', strtotime('+15 days'));

    // 1. PRIMERO: Verificar si ya tiene ESTE libro activo
    $check = $pdo->prepare("SELECT COUNT(*) FROM prestamos WHERE Id_Libros = ? AND Id_Usuarios = ? AND (Estado = 'Activo' OR Estado IS NULL OR Estado = '')");
    $check->execute([$id_libro, $id_usuario]);
    
    if ($check->fetchColumn() > 0) {
        // Si ya lo tiene, lo mandamos de vuelta con un error
        header("Location: ../prest.php?error=ya_solicitado");
        exit;
    }

    // 2. SEGUNDO: Si no lo tiene, insertar con el estado 'Activo' por defecto
    $sql = "INSERT INTO prestamos (Id_Libros, Id_Usuarios, Fecha_prest, Fecha_devolucion, Estado) 
            VALUES (?, ?, ?, ?, 'Activo')";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$id_libro, $id_usuario, $fecha_prest, $fecha_devo])) {
        header("Location: ../prest.php?success=1");
    } else {
        header("Location: ../prest.php?error=1");
    }
} else {
    header("Location: ../login.php");
}