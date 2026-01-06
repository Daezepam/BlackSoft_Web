<?php
session_start();
// Subimos un nivel (../) para entrar a la carpeta php y conectar la BD
require_once __DIR__ . '/../php/bd.php';

// Verificación de seguridad: Solo el Admin pasa
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'Admin') {
    header("Location: ../index.php");
    exit;
}

// Obtenemos el ID de la reseña
$id_resena = $_GET['id'] ?? null;

if ($id_resena) {
    try {
        $stmt = $pdo->prepare("DELETE FROM resenas WHERE Id = ?");
        $stmt->execute([$id_resena]);
        
        // Redirigimos de vuelta al panel con un mensaje de éxito
        header("Location: ../admin.php?status=resena_borrada");
        exit;
    } catch (PDOException $e) {
        header("Location: ../admin.php?status=error");
        exit;
    }
} else {
    header("Location: ../admin.php");
    exit;
}
?>