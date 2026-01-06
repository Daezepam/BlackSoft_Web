<?php
session_start();
require_once __DIR__ . '/php/bd.php'; 

$nombre_usuario = $_SESSION['usuario'] ?? 'Invitado';
$usuario_id = $_SESSION['usuario_id'] ?? null;

// 1. LISTA NEGRA (Para bloquear botones de solicitar)
$libros_en_prestamo = [];
if ($usuario_id) {
    $stmt_check = $pdo->prepare("SELECT Id_Libros FROM prestamos WHERE Id_Usuarios = ? AND Estado IN ('Activo', 'Atrasado', 'Pendiente')");
    $stmt_check->execute([$usuario_id]);
    $libros_en_prestamo = $stmt_check->fetchAll(PDO::FETCH_COLUMN);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Pro - BlackSoft</title>
    <link rel="stylesheet" href="./CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Toast Centrado */
        #toast-container { position: fixed; top: 100px; left: 50%; transform: translateX(-50%); padding: 15px 30px; border-radius: 12px; color: white; z-index: 9999; transition: all 0.4s; min-width: 320px; text-align: center; }
        .toast-success { background: #2ecc71; border-bottom: 5px solid #27ae60; }
        .toast-error { background: #e74c3c; border-bottom: 5px solid #c0392b; }
        .toast-hidden { opacity: 0; top: 80px; pointer-events: none; }
        .toast-visible { opacity: 1; top: 100px; }

        /* Estilos de la tabla de préstamos */
        .badge { padding: 4px 8px; border-radius: 5px; font-size: 0.85em; font-weight: bold; }
        .badge-active { background: #9d2c70; color: white; }
        .badge-late { background: #e74c3c; color: white; animation: pulse 1.5s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
        
        .btn-action { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8em; margin-left: 5px; color: white; transition: 0.3s; }
        .btn-return { background: #3498db; }
        .btn-renew { background: #f39c12; }
        .btn-action:hover { filter: brightness(1.2); }
    </style>
</head>
<body>

<div id="toast-container" class="toast-hidden">
    <i id="toast-icon" class="fa-solid"></i> <span id="toast-message"></span>
</div>

<header>
    <div class="logo"><a href="index.php"><img src="img/logo2.png" alt="Logo" style="height: 45px;"></a></div>
    <nav>
        <a href="index.php">Inicio</a>
        <a href="perfil.php">Hola, <?php echo htmlspecialchars($nombre_usuario); ?></a>
        <a href="logout.php">Salir</a>
    </nav>
</header>

<main class="container">
    <section class="profile-card">
        <h2><i class="fa-solid fa-book-open"></i> Libros para Solicitar</h2>
        <?php
        $stmt = $pdo->query("SELECT Id, Titulo FROM libros");
        while ($libro = $stmt->fetch()) {
            $ya = in_array($libro['Id'], $libros_en_prestamo);
            echo "<div class='item'>
                    <p><strong>{$libro['Titulo']}</strong></p>";
            if ($usuario_id) {
                if ($ya) echo "<span class='badge badge-active'>Ya lo tienes</span>";
                else echo "<form action='php/acciones_prestamo.php' method='POST'><input type='hidden' name='id_libro' value='{$libro['Id']}'><input type='hidden' name='accion' value='solicitar'><button type='submit' class='btn-small'>Solicitar</button></form>";
            }
            echo "</div>";
        }
        ?>
    </section>

    <section class="profile-card">
        <h2><i class="fa-solid fa-clock-rotate-left"></i> Gestión de mis Préstamos</h2>
        <?php
        if ($usuario_id) {
            $sql = "SELECT p.*, l.Titulo FROM prestamos p JOIN libros l ON p.Id_Libros = l.Id 
                    WHERE p.Id_Usuarios = ? AND p.Estado != 'Devuelto' ORDER BY p.Fecha_devolucion ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$usuario_id]);
            $prestamos = $stmt->fetchAll();

            if ($prestamos) {
                foreach ($prestamos as $p) {
                    // CÁLCULO DE DÍAS RESTANTES
                    $hoy = new DateTime();
                    $vence = new DateTime($p['Fecha_devolucion']);
                    $diff = $hoy->diff($vence);
                    $dias = (int)$diff->format("%r%a");

                    $clase_badge = ($dias < 0) ? 'badge-late' : 'badge-active';
                    $texto_vence = ($dias < 0) ? "VENCIDO por " . abs($dias) . " días" : "Faltan $dias días";

                    echo "<div class='item' style='border-left: 5px solid ".($dias < 0 ? '#e74c3c' : '#9d2c70').";'>
                            <div style='flex-grow:1'>
                                <p><strong>{$p['Titulo']}</strong></p>
                                <small>$texto_vence ({$p['Fecha_devolucion']})</small>
                            </div>
                            <div>
                                <span class='badge $clase_badge'>{$p['Estado']}</span>
                                
                                <form action='php/acciones_prestamo.php' method='POST' style='display:inline;'>
                                    <input type='hidden' name='id_prestamo' value='{$p['Id']}'>
                                    <input type='hidden' name='accion' value='devolver'>
                                    <button type='submit' class='btn-action btn-return' title='Devolver'><i class='fa-solid fa-rotate-left'></i></button>
                                </form>

                                ".($dias >= 0 ? "
                                <form action='php/acciones_prestamo.php' method='POST' style='display:inline;'>
                                    <input type='hidden' name='id_prestamo' value='{$p['Id']}'>
                                    <input type='hidden' name='accion' value='renovar'>
                                    <button type='submit' class='btn-action btn-renew' title='Renovar 7 días'><i class='fa-solid fa-calendar-plus'></i></button>
                                </form>" : "")."
                            </div>
                          </div>";
                }
            } else { echo "<p>No tienes préstamos activos.</p>"; }
        }
        ?>
    </section>
</main>

<script>
function showToast(msj, tipo) {
    const toast = document.getElementById('toast-container');
    document.getElementById('toast-message').innerText = msj;
    toast.className = 'toast-visible ' + (tipo === 'success' ? 'toast-success' : 'toast-error');
    document.getElementById('toast-icon').className = 'fa-solid ' + (tipo === 'success' ? 'fa-check-circle' : 'fa-triangle-exclamation');
    setTimeout(() => { toast.className = 'toast-hidden'; }, 4000);
}
window.onload = () => {
    const p = new URLSearchParams(window.location.search);
    if (p.has('success')) showToast("¡Acción realizada con éxito!", "success");
    if (p.has('error')) showToast("Hubo un error en la operación.", "error");
};
</script>
</body>
</html>