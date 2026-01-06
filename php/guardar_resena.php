<?php
session_start();
require_once __DIR__ . '/bd.php'; 

// 1. Verificamos la sesión
$session_id = $_SESSION['usuario_id'] ?? $_SESSION['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $session_id) {
    $usuario_id = $session_id;
    $nombre_libro = trim($_POST['libro'] ?? ''); 
    $comentario = trim($_POST['comentario'] ?? '');
    $puntos = $_POST['puntos'] ?? 5;

    if (!empty($nombre_libro) && !empty($comentario)) {
        try {
            // 2. Buscamos el ID del libro
            $stmtBusca = $pdo->prepare("SELECT Id FROM Libros WHERE Titulo = :titulo LIMIT 1");
            $stmtBusca->execute(['titulo' => $nombre_libro]);
            $libro = $stmtBusca->fetch(PDO::FETCH_ASSOC);

            if ($libro) {
                $id_libro_real = $libro['Id'];

                // --- NUEVA VALIDACIÓN: ¿YA EXISTE UNA RESEÑA DE ESTE USUARIO PARA ESTE LIBRO? ---
                $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM resenas WHERE Id_Usuarios = ? AND Id_Libros = ? AND Estado = 'Activa'");
                $stmtCheck->execute([$usuario_id, $id_libro_real]);
                
                if ($stmtCheck->fetchColumn() > 0) {
                    // Si ya existe, lo mandamos de vuelta con un estado especial
                    header("Location: ../resenas.php?status=ya_resenado");
                    exit;
                }
                // -------------------------------------------------------------------------------

                // 3. Si no existe, insertamos la reseña
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
                header("Location: ../resenas.php?status=error_libro");
                exit;
            }

        } catch (PDOException $e) {
            header("Location: ../resenas.php?status=error_db");
            exit;
        }
    } else {
        header("Location: ../resenas.php?status=error_vacios");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}