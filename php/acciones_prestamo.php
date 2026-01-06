<?php
session_start();
require_once __DIR__ . '/bd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $accion = $_POST['accion'] ?? '';

    try {
        $pdo->beginTransaction();

        if ($accion === 'solicitar') {
            $id_libro = $_POST['id_libro'];
            $fecha_dev = date('Y-m-d', strtotime('+14 days'));
            
            // Insertar el nuevo préstamo
            $sql = "INSERT INTO prestamos (Id_Usuarios, Id_Libros, Fecha_devolucion, Estado) VALUES (?, ?, ?, 'Activo')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$usuario_id, $id_libro, $fecha_dev]);

            // Marcar el libro como NO disponible (0)
            $stmt_lib = $pdo->prepare("UPDATE libros SET Disponibilidad = 0 WHERE Id = ?");
            $stmt_lib->execute([$id_libro]);
        } 
        
        elseif ($accion === 'devolver') {
            $id_prestamo = $_POST['id_prestamo'];
            
            // 1. Buscamos qué libro es antes de marcarlo como devuelto
            $stmt_info = $pdo->prepare("SELECT Id_Libros FROM prestamos WHERE Id = ?");
            $stmt_info->execute([$id_prestamo]);
            $id_libro = $stmt_info->fetchColumn();

            // 2. Cambiamos el estado a 'Devuelto' (así desaparece de Mis Préstamos)
            $stmt = $pdo->prepare("UPDATE prestamos SET Estado = 'Devuelto' WHERE Id = ? AND Id_Usuarios = ?");
            $stmt->execute([$id_prestamo, $usuario_id]);

            // 3. ¡Lo liberamos! (Disponibilidad = 1 para que vuelva a aparecer arriba)
            if ($id_libro) {
                $stmt_lib = $pdo->prepare("UPDATE libros SET Disponibilidad = 1 WHERE Id = ?");
                $stmt_lib->execute([$id_libro]);
            }
        }

        $pdo->commit();
        header("Location: ../prest.php?success=1");
        exit();

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        header("Location: ../prest.php?error=1");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}