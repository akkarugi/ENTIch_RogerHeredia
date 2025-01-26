<?php
session_start();
require_once("db_config.php");

if (!isset($_SESSION['id_creator'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["comment"]) && isset($_POST["id_game"])) {
    $id_creator = $_SESSION['id_creator'];
    $id_game = intval($_POST['id_game']);
    $comment = trim($_POST['comment']);

    if (strlen($comment) < 1) {
        die("ERROR: El comentario no puede estar vacío.");
    }

    $conn = new mysqli($db_server, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        die("ERROR: No se pudo conectar a la base de datos: " . $conn->connect_error);
    }

    $comment = htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');

    $stmt = $conn->prepare("INSERT INTO comments (comment, id_creator, id_game) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $comment, $id_creator, $id_game);
    $result = $stmt->execute();

    if ($result) {
        header("Location: game.php?id_game=$id_game");
        exit();
    } else {
        echo "ERROR: No se pudo insertar el comentario: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: index.php");
    exit();
}
?>
