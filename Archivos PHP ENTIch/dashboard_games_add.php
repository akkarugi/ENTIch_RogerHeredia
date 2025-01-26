<?php
session_start();
if (!isset($_SESSION["id_creator"])) {
    header("Location: login.php");
    exit();
}

require_once("db_config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_game_submit"])) {
    $conn = new mysqli($db_server, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        die("ERROR: No se pudo conectar a la base de datos: " . $conn->connect_error);
    }

    $id_creator = $_SESSION['id_creator'];
    $title = htmlspecialchars(trim($_POST["add_title"]), ENT_QUOTES, 'UTF-8');
    $link = htmlspecialchars(trim($_POST["add_link"]), ENT_QUOTES, 'UTF-8');
    $header = htmlspecialchars(trim($_POST["add_image"]), ENT_QUOTES, 'UTF-8');
    $price = floatval($_POST["add_price"]);
    $trailer = htmlspecialchars(trim($_POST["add_trailer"]), ENT_QUOTES, 'UTF-8');

    // Inserción del juego en la tabla games
    $stmt = $conn->prepare("INSERT INTO games (title, link, header, price, trailer) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssds", $title, $link, $header, $price, $trailer);
    $result = $stmt->execute();

    if ($result) {
        // Obtener el id del juego insertado
        $id_game = $stmt->insert_id;

        // Insertar la relación en la tabla creators_games
        $stmt_creators_games = $conn->prepare("INSERT INTO creators_games (id_creator, id_game) VALUES (?, ?)");
        $stmt_creators_games->bind_param("ii", $id_creator, $id_game);
        $stmt_creators_games->execute();

        header("Location: dashboard_games.php");
        exit();
    } else {
        echo "Error al añadir el juego: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: dashboard_games.php");
    exit();
}
?>
