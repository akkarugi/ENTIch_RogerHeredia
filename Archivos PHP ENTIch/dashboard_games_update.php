<?php
session_start();
if (!isset($_SESSION["id_creator"])) {
    header("Location: login.php");
    exit();
}

require_once("db_config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_game_submit"])) {
    $conn = new mysqli($db_server, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        die("ERROR: No se pudo conectar a la base de datos: " . $conn->connect_error);
    }

    $id_game = intval($_POST["id_game"]);
    $title = htmlspecialchars(trim($_POST["add_title"]), ENT_QUOTES, 'UTF-8');
    $link = htmlspecialchars(trim($_POST["add_link"]), ENT_QUOTES, 'UTF-8');
    $header = htmlspecialchars(trim($_POST["add_image"]), ENT_QUOTES, 'UTF-8');
    $price = floatval($_POST["add_price"]);
    $trailer = htmlspecialchars(trim($_POST["add_trailer"]), ENT_QUOTES, 'UTF-8');

    $stmt = $conn->prepare("UPDATE games SET title = ?, link = ?, header = ?, price = ?, trailer = ? WHERE id_game = ?");
    $stmt->bind_param("sssdsi", $title, $link, $header, $price, $trailer, $id_game);
    $result = $stmt->execute();

    if ($result) {
        header("Location: dashboard_games.php");
        exit();
    } else {
        echo "Error al actualizar el juego: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: dashboard_games.php");
    exit();
}
?>
