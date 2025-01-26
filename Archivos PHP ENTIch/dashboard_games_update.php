<?php
session_start();
if (!isset($_SESSION["id_creator"])) {
    header("Location: login.php");
    exit();
}

require_once("db_config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_game_submit"])) {
    $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
    if (!$conn) {
        die("ERROR: No se pudo conectar a la base de datos.");
    }

    $id_game = intval($_POST["id_game"]);
    $title = mysqli_real_escape_string($conn, $_POST["add_title"]);
    $link = mysqli_real_escape_string($conn, $_POST["add_link"]);
    $header = mysqli_real_escape_string($conn, $_POST["add_image"]);
    $price = floatval($_POST["add_price"]);
    $trailer = mysqli_real_escape_string($conn, $_POST["add_trailer"]);

    $query = <<<EOD
    UPDATE games 
    SET title = '{$title}', link = '{$link}', header = '{$header}', price = {$price}, trailer = '{$trailer}' 
    WHERE id_game = {$id_game}
    EOD;

    $result = mysqli_query($conn, $query);

    if ($result) {
        header("Location: dashboard_games.php");
        exit();
    } else {
        echo "Error al actualizar el juego: " . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    header("Location: dashboard_games.php");
    exit();
}
?>
