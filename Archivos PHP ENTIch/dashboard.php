<?php
session_start();
if (!isset($_SESSION["id_creator"])) {
    header("Location: login.php");
    exit();
}

require_once("db_config.php");
require_once("template.php");

$id_creator = $_SESSION["id_creator"];
$conn = new mysqli($db_server, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("ERROR: No se pudo conectar a la base de datos: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT * FROM creators WHERE id_creator = ?");
$stmt->bind_param("i", $id_creator);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows != 1) {
    header("Location: login.php");
    exit();
}

$creator = $result->fetch_assoc();

printHead("Dashboard de " . htmlspecialchars($creator["name"], ENT_QUOTES, 'UTF-8'));
openBody("Dashboard de " . htmlspecialchars($creator["name"], ENT_QUOTES, 'UTF-8'));

require_once("dashboard_template.php");

openDashboard();

echo <<<EOD
<form method="POST" action="profile_update.php">
    <p><label for="profile_name">Nombre:</label>
    <input type="text" value="{$creator["name"]}" name="name" id="profile_name" required></p>
    <p><label for="profile_username">Usuario:</label>
    <input type="text" value="{$creator["username"]}" name="username" id="profile_username" required></p>
    <p><label for="profile_email">Email:</label>
    <input type="email" value="{$creator["email"]}" name="email" id="profile_email" required></p>
    <p><label for="profile_description">Descripción:</label>
    <textarea name="description" id="profile_description" required>{$creator["description"]}</textarea></p>
    <p><input type="submit" value="Actualizar"></p>
</form>
EOD;

closeDashboard();
closeBody();

$stmt->close();
$conn->close();
?>
