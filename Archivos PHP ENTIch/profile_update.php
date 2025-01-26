<?php
session_start();

// REDIRIGIR SI NO HAY UNA SESIÓN ACTIVA
if (!isset($_SESSION["id_creator"])) {
    header("Location: login.php");
    exit();
}

// OBTENER EL ID DEL USUARIO ACTIVO
$id_creator = $_SESSION["id_creator"];

// COMPROBAR QUE TODOS LOS CAMPOS OBLIGATORIOS ESTÉN RELLENADOS
if (!isset($_POST["name"]) || !isset($_POST["username"]) || 
    !isset($_POST["email"]) || !isset($_POST["description"])) {
    die("ERROR 1: Formulario no enviado.");
}

// FUNCIONES DE LIMPIEZA Y VALIDACIÓN
function cleanData($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

$name = cleanData($_POST["name"]);
$username = cleanData($_POST["username"]);
$email = cleanData($_POST["email"]);
$description = isset($_POST["description"]) ? cleanData($_POST["description"]) : "";

// VALIDAR LONGITUD DE LOS CAMPOS
if (strlen($name) < 3) {
    die("ERROR 2: El nombre es demasiado corto.");
}

if (strlen($username) < 5) {
    die("ERROR 3: El nombre de usuario es demasiado corto.");
}

if (strlen($email) < 6) {
    die("ERROR 4: El correo electrónico es demasiado corto.");
}

// VALIDAR FORMATO DE EMAIL
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("ERROR 5: El correo electrónico no es válido.");
}

// CONECTAR A LA BASE DE DATOS
require_once("db_config.php");
$conn = new mysqli($db_server, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("ERROR 6: No se pudo conectar a la base de datos: " . $conn->connect_error);
}

// ACTUALIZAR DATOS DEL USUARIO ACTIVO
$query = $conn->prepare("UPDATE creators SET name = ?, username = ?, email = ?, description = ? WHERE id_creator = ?");
$query->bind_param("ssssi", $name, $username, $email, $description, $id_creator);
$result = $query->execute();

if (!$result) {
    die("ERROR 7: No se han podido actualizar los datos en la base de datos.");
}

// REDIRECCIONAR AL DASHBOARD
header("Location: dashboard.php");
exit();
?>
