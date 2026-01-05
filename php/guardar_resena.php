<?php
session_start();
require_once __DIR__ . '/bd.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['id'])) {
    $usuario_id = $_SESSION['id'];
    $nombre_libro = $_POST['libro'] ?? null; // Aquí recibimos el texto "Don Quijote..."
    $comentario = $_POST['comentario'] ?? null;
    $puntos = $_POST['puntos'] ?? 5;

    if ($nombre_libro && $comentario) {
        try {
            // 1. Buscamos el ID del libro usando el nombre escrito
            $stmtBusca = $pdo->prepare("SELECT Id FROM Libros WHERE Titulo = :titulo LIMIT 1");
            $stmtBusca->execute(['titulo' => $nombre_libro]);
            $libro = $stmtBusca->fetch(PDO::FETCH_ASSOC);

            if ($libro) {
                $id_libro_real = $libro['Id'];

                // 2. Insertamos usando los nombres exactos de tu tabla
                $sql = "INSERT INTO resenas (Id_Usuarios, Id_Libros, Calificacion, Comentario, Fecha, Estado) 
                        VALUES (:uid, :lid, :cal, :com, CURRENT_TIMESTAMP, 'Activa')";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'uid' => $usuario_id,
                    'lid' => $id_libro_real,
                    'cal' => $puntos,
                    'com' => $comentario
                ]);

                header("Location: ../resenas.php?status=success");
                exit;
            } else {
                // Si el libro escrito no existe en la tabla Libros
                die("Error: El libro '" . htmlspecialchars($nombre_libro) . "' no se encontró en nuestro sistema. Revisa que el título sea idéntico.");
            }

        } catch (PDOException $e) {
            die("Error de base de datos: " . $e->getMessage());
        }
    } else {
        header("Location: ../resenas.php?status=error_datos");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
?>