<?php
$host = "localhost";
$db   = "blacksoft"; // Confirmado por tu imagen de phpMyAdmin
$user = "root";      // XAMPP usa 'root' por defecto
$pass = "";          // XAMPP no tiene contraseña por defecto

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Borré el "die" genérico para que si falla, nos diga el error real
} catch (PDOException $e) {
    echo "Error real: " . $e->getMessage();
}
?>
