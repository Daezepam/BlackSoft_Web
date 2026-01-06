<?php
session_start();
require_once __DIR__ . '/bd.php';

// 1. Verificar sesión activa
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}

$id_usuario = $_SESSION['id'];
$titulo_libro = trim($_POST['libro'] ?? '');
// Cortamos el comentario a 500 caracteres por seguridad del lado del servidor
$comentario = substr(trim($_POST['comentario'] ?? ''), 0, 500);
$puntos = intval($_POST['puntos'] ?? 5);

if ($titulo_libro && $comentario) {
    try {
        // 2. Buscar si el libro existe en la base de datos
        $stmtLibro = $pdo->prepare("SELECT Id FROM Libros WHERE Titulo = :titulo LIMIT 1");
        $stmtLibro->execute(['titulo' => $titulo_libro]);
        $libro = $stmtLibro->fetch();

        if (!$libro) {
            // El libro no existe en nuestra base de datos
            header("Location: ../resenas.php?status=error_libro");
            exit;
        }

        $id_libro = $libro['Id'];

        // 3. Verificar si el usuario ya reseñó este mismo libro (Evitar spam)
        $stmtCheck = $pdo->prepare("SELECT Id FROM resenas WHERE Id_Usuarios = :u AND Id_Libros = :l");
        $stmtCheck->execute(['u' => $id_usuario, 'l' => $id_libro]);

        if ($stmtCheck->rowCount() > 0) {
            header("Location: ../resenas.php?status=ya_resenado");
            exit;
        }

        // 4. Insertar la reseña
        $sqlInsert = "INSERT INTO resenas (Id_Usuarios, Id_Libros, Comentario, Calificacion, Fecha, Estado) 
                      VALUES (:u, :l, :c, :p, NOW(), 'Activa')";
        
        $stmtInsert = $pdo->prepare($sqlInsert);
        $stmtInsert->execute([
            'u' => $id_usuario,
            'l' => $id_libro,
            'c' => $comentario,
            'p' => $puntos
        ]);

        header("Location: ../resenas.php?status=success");
        exit;

    } catch (PDOException $e) {
        // En caso de error técnico, redirigir con error
        header("Location: ../resenas.php?status=error_tecnico");
        exit;
    }
} else {
    header("Location: ../resenas.php?status=datos_incompletos");
    exit;
}