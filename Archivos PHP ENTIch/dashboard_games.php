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

if (isset($_GET["add_game"])) {
    echo <<<EOD
<form method="POST" action="dashboard_games_add.php">
<h2>Nuevo Juego</h2>
<p><label for="add_title">Título: </label>
<input type="text" id="add_title" name="add_title" required></p>
<p><label for="add_link">Enlace a Steam: </label>
<input type="url" id="add_link" name="add_link" required></p>
<p><label for="add_image">Imagen (nombre de archivo): </label>
<input type="text" id="add_image" name="add_image" required></p>
<p><label for="add_price">Precio: </label>
<input type="number" id="add_price" name="add_price" min="0" step="0.01" required></p>
<p><label for="add_trailer">Enlace al tráiler de YouTube: </label>
<input type="url" id="add_trailer" name="add_trailer" required></p>
<p><input type="submit" name="add_game_submit" id="add_game_submit" value="Añadir Juego"></p>
</form>
EOD;
} elseif (isset($_GET["id_game"])) {
    $id_game = intval($_GET["id_game"]);
    $stmt_game = $conn->prepare("SELECT * FROM games WHERE id_game = ?");
    $stmt_game->bind_param("i", $id_game);
    $stmt_game->execute();
    $result_game = $stmt_game->get_result();

    if (!$result_game || $result_game->num_rows != 1) {
        echo "<p>El juego no existe o no está asociado a tu cuenta.</p>";
    } else {
        $game = $result_game->fetch_assoc();

        echo <<<EOD
<form method="POST" action="dashboard_games_update.php">
<h2>Modificar Juego</h2>
<input type="hidden" name="id_game" value="{$game['id_game']}">
<p><label for="add_title">Título: </label>
<input type="text" id="add_title" name="add_title" value="{$game['title']}" required></p>
<p><label for="add_link">Enlace a Steam: </label>
<input type="url" id="add_link" name="add_link" value="{$game['link']}" required></p>
<p><label for="add_image">Imagen (nombre de archivo): </label>
<input type="text" id="add_image" name="add_image" value="{$game['header']}" required></p>
<p><label for="add_price">Precio: </label>
<input type="number" id="add_price" name="add_price" value="{$game['price']}" min="0" step="0.01" required></p>
<p><label for="add_trailer">Enlace al tráiler de YouTube: </label>
<input type="url" id="add_trailer" name="add_trailer" value="{$game['trailer']}" required></p>
<p><input type="submit" name="update_game_submit" id="update_game_submit" value="Actualizar Juego"></p>
</form>
EOD;

        echo <<<EOD
<form method="POST" action="dashboard_games_delete.php">
    <input type="hidden" name="id_game" value="{$game['id_game']}">
    <button type="submit" class="delete-button">Eliminar Juego</button>
</form>
EOD;

        echo "<h3>Comentarios</h3>";
        $stmt_comments = $conn->prepare("SELECT comments.comment, comments.created_at, creators.name FROM comments INNER JOIN creators ON comments.id_creator = creators.id_creator WHERE comments.id_game = ? ORDER BY comments.created_at DESC");
        $stmt_comments->bind_param("i", $id_game);
        $stmt_comments->execute();
        $result_comments = $stmt_comments->get_result();

        if ($result_comments && $result_comments->num_rows > 0) {
            echo "<ul class='comments-list'>";
            while ($comment = $result_comments->fetch_assoc()) {
                echo "<li class='comment-item'>";
                echo "<strong>" . htmlspecialchars($comment['name'], ENT_QUOTES, 'UTF-8') . ":</strong> ";
                echo "<p>" . htmlspecialchars($comment['comment'], ENT_QUOTES, 'UTF-8') . "</p>";
                echo "<small>" . htmlspecialchars($comment['created_at'], ENT_QUOTES, 'UTF-8') . "</small>";
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No hay comentarios sobre este juego.</p>";
        }
    }
} else {
    echo <<<EOD
<h2>Tus Juegos</h2>
<div class="games-container">
EOD;

    $stmt_games = $conn->prepare("SELECT games.id_game, games.title, games.price, games.header, games.link, games.trailer FROM games INNER JOIN creators_games ON games.id_game = creators_games.id_game WHERE creators_games.id_creator = ?");
    $stmt_games->bind_param("i", $id_creator);
    $stmt_games->execute();
    $result_games = $stmt_games->get_result();

    if ($result_games && $result_games->num_rows > 0) {
        while ($game = $result_games->fetch_assoc()) {
            $trailer_id = htmlspecialchars(substr($game['trailer'], strpos($game['trailer'], "?v=") + 3), ENT_QUOTES, 'UTF-8');
            echo <<<EOD
<div class="game-card">
    <h3>{$game['title']}</h3>
    <img src="imgs/{$game['header']}" alt="{$game['title']}">
    <p>Precio: {$game['price']}€</p>
    <a href="{$game['link']}" target="_blank">Ver en Steam</a>
    <iframe src="https://www.youtube.com/embed/{$trailer_id}" frameborder="0" allowfullscreen></iframe>
    <a href="/dashboard_games.php?id_game={$game['id_game']}">Modificar</a>
    <form method="POST" action="dashboard_games_delete.php">
        <input type="hidden" name="id_game" value="{$game['id_game']}">
        <button type="submit" class="delete-button">Eliminar Juego</button>
    </form>
EOD;

            echo "<h3>Comentarios</h3>";
            $stmt_comments = $conn->prepare("SELECT comments.comment, comments.created_at, creators.name FROM comments INNER JOIN creators ON comments.id_creator = creators.id_creator WHERE comments.id_game = ? ORDER BY comments.created_at DESC");
            $stmt_comments->bind_param("i", $game['id_game']);
            $stmt_comments->execute();
            $result_comments = $stmt_comments->get_result();

            if ($result_comments && $result_comments->num_rows > 0) {
                echo "<ul class='comments-list'>";
                while ($comment = $result_comments->fetch_assoc()) {
                    echo "<li class='comment-item'>";
                    echo "<strong>" . htmlspecialchars($comment['name'], ENT_QUOTES, 'UTF-8') . ":</strong> ";
                    echo "<p>" . htmlspecialchars($comment['comment'], ENT_QUOTES, 'UTF-8') . "</p>";
                    echo "<small>" . htmlspecialchars($comment['created_at'], ENT_QUOTES, 'UTF-8') . "</small>";
                    echo "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No hay comentarios sobre este juego.</p>";
            }

            echo "</div>";
        }
    } else {
        echo "<p>No has añadido ningún juego aún.</p>";
    }

    echo "</div>";
}

closeDashboard();
closeBody();

$stmt->close();
$conn->close();
?>
