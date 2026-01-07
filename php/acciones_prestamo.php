<?php
session_start();
require_once __DIR__ . '/bd.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$accion = $_POST['accion'] ?? '';

try {
    if ($accion === 'solicitar' && isset($_POST['id_libro'])) {
        $id_libro = $_POST['id_libro'];
        
        // 1. Verificar disponibilidad real
        $stmt = $pdo->prepare("SELECT Disponibilidad FROM libros WHERE Id = ?");
        $stmt->execute([$id_libro]);
        $libro = $stmt->fetch();

        if ($libro && $libro['Disponibilidad'] == 1) {
            $pdo->beginTransaction();
            
            // 2. Crear el préstamo (7 días de plazo)
            $fecha_dev = date('Y-m-d', strtotime('+7 days'));
            $ins = $pdo->prepare("INSERT INTO prestamos (Id_Usuarios, Id_Libros, Fecha_prestamo, Fecha_devolucion, Estado) VALUES (?, ?, NOW(), ?, 'Prestado')");
            $ins->execute([$usuario_id, $id_libro, $fecha_dev]);

            // 3. Cambiar disponibilidad del libro
            $upd = $pdo->prepare("UPDATE libros SET Disponibilidad = 0 WHERE Id = ?");
            $upd->execute([$id_libro]);

            $pdo->commit();
        }
    } 
    
    if ($accion === 'devolver' && isset($_POST['id_prestamo'])) {
        $id_p = $_POST['id_prestamo'];

        $pdo->beginTransaction();

        // 1. Obtener el ID del libro antes de cerrar el préstamo
        $stmt = $pdo->prepare("SELECT Id_Libros FROM prestamos WHERE Id = ?");
        $stmt->execute([$id_p]);
        $res = $stmt->fetch();

        if ($res) {
            // 2. Marcar como devuelto
            $updP = $pdo->prepare("UPDATE prestamos SET Estado = 'Devuelto' WHERE Id = ?");
            $updP->execute([$id_p]);

            // 3. Liberar el libro
            $updL = $pdo->prepare("UPDATE libros SET Disponibilidad = 1 WHERE Id = ?");
            $updL->execute([$res['Id_Libros']]);
        }

        $pdo->commit();
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
}

header("Location: ../prestamos.php");
exit();