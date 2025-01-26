<?php
// COMPROBAR QUE TODOS LOS CAMPOS ESTÉN RELLENADOS
if (!isset($_POST["login_username"]) || !isset($_POST["login_password"])) {
    die("ERROR 1: Login no enviado.");
}

// LIMPIAR ESPACIOS Y VALIDAR LONGITUD DE LOS CAMPOS
$username = trim($_POST["login_username"]);
if (strlen($username) < 5) {
    die("ERROR 3: El nombre de usuario es demasiado corto.");
}

$password = trim($_POST["login_password"]);
if (strlen($password) < 7) {
    die("ERROR 5: La contraseña es demasiado corta.");
}

// LIMPIAR Y ENCRIPTAR LOS DATOS
$username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
$password_md5 = md5($password); // Nota: MD5 no es seguro para contraseñas críticas, usa bcrypt en producción.

// CONECTAR A LA BASE DE DATOS
require_once("db_config.php");
$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("ERROR 8: No se pudo conectar a la base de datos: " . mysqli_connect_error());
}

// CONSULTA SEGURA PARA BUSCAR EL USUARIO Y CONTRASEÑA
$stmt = $conn->prepare("SELECT id_creator FROM creators WHERE username = ? AND password = ?");
$stmt->bind_param("ss", $username, $password_md5);
$stmt->execute();
$result = $stmt->get_result();

// VERIFICAR RESULTADO
if ($result->num_rows !== 1) {
    die("ERROR 9: Usuario o contraseña incorrectos.");
}

// OBTENER EL ID DEL USUARIO
$creator = $result->fetch_assoc();
session_start();
$_SESSION["id_creator"] = $creator["id_creator"];

// REDIRECCIONAR AL DASHBOARD
header("Location: dashboard.php");
exit;

// CERRAR CONEXIONES
$stmt->close();
$conn->close();
?>
