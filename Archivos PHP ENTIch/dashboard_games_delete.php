<?php
session_start();
if (!isset($_SESSION["id_creator"])) {
    header("Location: login.php");
    exit();
}

require_once("db_config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_game"])) {
    $id_game = intval($_POST["id_game"]);
    $id_creator = $_SESSION["id_creator"];

    $conn = new mysqli($db_server, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        die("ERROR: No se pudo conectar a la base de datos: " . $conn->connect_error);
    }

    // Verificar si el juego pertenece al creador actual
    $stmt_verify = $conn->prepare("SELECT * FROM creators_games WHERE id_creator = ? AND id_game = ?");
    $stmt_verify->bind_param("ii", $id_creator, $id_game);
    $stmt_verify->execute();
    $result_verify = $stmt_verify->get_result();

    if ($result_verify->num_rows != 1) {
        echo "ERROR: No tienes permiso para eliminar este juego.";
    } else {
        // Eliminar el juego de la tabla creators_games
        $stmt_delete_relation = $conn->prepare("DELETE FROM creators_games WHERE id_game = ?");
        $stmt_delete_relation->bind_param("i", $id_game);
        $stmt_delete_relation->execute();

        // Eliminar el juego de la tabla games
        $stmt_delete_game = $conn->prepare("DELETE FROM games WHERE id_game = ?");
        $stmt_delete_game->bind_param("i", $id_game);
        $stmt_delete_game->execute();

        header("Location: dashboard_games.php");
        exit();
    }

    $stmt_verify->close();
    $stmt_delete_relation->close();
    $stmt_delete_game->close();
    $conn->close();
} else {
    header("Location: dashboard_games.php");
    exit();
}
?>
