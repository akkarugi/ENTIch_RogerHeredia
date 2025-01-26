<?php
// COMPROBAR QUE TODOS LOS CAMPOS ESTÉN RELLENADOS
if (!isset($_POST["register_name"]) || !isset($_POST["register_username"]) ||
    !isset($_POST["register_email"]) || !isset($_POST["register_password"]) ||
    !isset($_POST["register_repass"])) {
    die("ERROR 1: Formulario no enviado.");
}

// LIMPIAR ESPACIOS Y VALIDAR LONGITUD DE LOS CAMPOS
$name = trim($_POST["register_name"]);
if (strlen($name) < 3) {
    die("ERROR 2: El nombre es demasiado corto.");
}

$username = trim($_POST["register_username"]);
if (strlen($username) < 5) {
    die("ERROR 3: El nombre de usuario es demasiado corto.");
}

$email = trim($_POST["register_email"]);
if (strlen($email) < 6) {
    die("ERROR 4: El correo electrónico es demasiado corto.");
}

$password = trim($_POST["register_password"]);
if (strlen($password) < 7) {
    die("ERROR 5: La contraseña es demasiado corta.");
}

// COMPROBAR CARACTERES PROBLEMÁTICOS 
$name = htmlspecialchars(addslashes($name), ENT_QUOTES, 'UTF-8');
$username = htmlspecialchars(addslashes($username), ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars(addslashes($email), ENT_QUOTES, 'UTF-8');
$password_temp = htmlspecialchars(addslashes($password), ENT_QUOTES, 'UTF-8');

// VALIDAR FORMATO DE EMAIL
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("ERROR 6: El correo electrónico no es válido.");
}

// COMPROBAR QUE EL PASSWORD Y REPASSWORD COINCIDEN
if ($password_temp !== $_POST["register_repass"]) {
    die("ERROR 7: Las contraseñas no coinciden.");
}

// GENERAR EL HASH MD5 DEL PASSWORD
$password_md5 = md5($password_temp);

/*MOSTRAR LAS VARIABLES FILTRADAS EN HTML
echo "<h2>Datos Registrados</h2>";
echo "<p><strong>Nombre:</strong> " . htmlentities($name) . "</p>";
echo "<p><strong>Usuario:</strong> " . htmlentities($username) . "</p>";
echo "<p><strong>Email:</strong> " . htmlentities($email) . "</p>";
echo "<p><strong>Password (MD5):</strong> " . htmlentities($password_md5) . "</p>";
*/

//CONECTAR A LA BASE DE DATOS
require_once("db_config.php");
$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

// COMPROBAR SI EL USUARIO O EL EMAIL YA EXISTEN
$check_query = "SELECT * FROM creators WHERE username = '{$username}' OR email = '{$email}'";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    die("ERROR 9: El nombre de usuario o el correo electrónico ya existen.");
}

// INSERTAR NUEVO USUARIO
$query = <<<EOD
INSERT INTO creators (name, username, password, email)
VALUES ('{$name}', '{$username}', '{$password_md5}', '{$email}');
EOD;

$result = mysqli_query($conn, $query);

if (!$result) {
    die("ERROR 10: No se ha insertado en la base de datos.");
}

// OBTENER ID DEL USUARIO INSERTADO Y CREAR SESIÓN
$id_creator = mysqli_insert_id($conn);
session_start();
$_SESSION["id_creator"] = $id_creator;

// REDIRECCIONAR AL DASHBOARD
header("Location: dashboard.php");
exit();


?>