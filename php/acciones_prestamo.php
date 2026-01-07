<?php
session_start();
require_once __DIR__ . '/bd.php'; 
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}
$id_usuario = $_SESSION['usuario_id'];
$accion = $_POST['accion'] ?? '';
try {
    if ($accion === 'solicitar' && isset($_POST['id_libro'])) {
        $id_libro = $_POST['id_libro'];
        $pdo->beginTransaction();
        $fecha_prestamo = date('Y-m-d');
        $fecha_devolucion = date('Y-m-d', strtotime('+7 days'));
        $stmtIns = $pdo->prepare("INSERT INTO Prestamos (Id_Usuarios, Id_Libros, Fecha_prestamo, Fecha_devolucion, Estado) VALUES (?, ?, ?, ?, 'Prestado')");
        $stmtIns->execute([$id_usuario, $id_libro, $fecha_prestamo, $fecha_devolucion]);
        $stmtUpd = $pdo->prepare("UPDATE Libros SET Disponibilidad = 0 WHERE Id = ?");
        $stmtUpd->execute([$id_libro]);
        $pdo->commit();
    } 
    if ($accion === 'devolver' && isset($_POST['id_prestamo'])) {
        $id_p = $_POST['id_prestamo'];
        $pdo->beginTransaction();
        $stmtFind = $pdo->prepare("SELECT Id_Libros FROM Prestamos WHERE Id = ?");
        $stmtFind->execute([$id_p]);
        $prestamo = $stmtFind->fetch();
        if ($prestamo) {
            $updP = $pdo->prepare("UPDATE Prestamos SET Estado = 'Devuelto' WHERE Id = ?");
            $updP->execute([$id_p]);
            $updL = $pdo->prepare("UPDATE Libros SET Disponibilidad = 1 WHERE Id = ?");
            $updL->execute([$prestamo['Id_Libros']]);
        }
        $pdo->commit();
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
}
header("Location: ../prestamos.php");
exit();