<?php
session_start();
if (!isset($_SESSION["id_creator"])) {
    header("Location: login.php");
    exit();
}

require_once("template.php");
require_once("db_config.php");

$query = "SELECT * FROM creators WHERE id_creator=" . $_SESSION["id_creator"];
$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("ERROR: No se pudo conectar a la base de datos.");
}

$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) != 1) {
    header("Location: login.php");
    exit();
}

$creator = mysqli_fetch_array($result);

printHead("Dashboard de " . $creator["name"]);
openBody("Dashboard de " . $creator["name"]);
require_once("dashboard_template.php");
openDashboard();

if (isset($_GET["add_game"])) {
    echo <<<EOD
<form method="POST" action="dashboard_games_add.php">
<h2>Nuevo Juego</h2>
<p><label for="add_title">Título: </label>
<input type="text" id="add_title" name="add_title" required></p>
<p><label for="add_link">Enlace a Steam: </label>
<input type="text" id="add_link" name="add_link" required></p>
<p><label for="add_image">Imagen (nombre de archivo): </label>
<input type="text" id="add_image" name="add_image" required></p>
<p><label for="add_price">Precio: </label>
<input type="number" id="add_price" name="add_price" min="0" step="0.01" required></p>
<p><label for="add_trailer">Enlace al tráiler de YouTube: </label>
<input type="text" id="add_trailer" name="add_trailer" required></p>
<p><input type="submit" name="add_game_submit" id="add_game_submit" value="Añadir Juego"></p>
</form>
EOD;
} elseif (isset($_GET["id_game"])) {
    $id_game = intval($_GET["id_game"]);
    $query_game = "SELECT * FROM games WHERE id_game = {$id_game}";
    $result_game = mysqli_query($conn, $query_game);

    if (!$result_game || mysqli_num_rows($result_game) != 1) {
        echo "<p>El juego no existe o no está asociado a tu cuenta.</p>";
    } else {
        $game = mysqli_fetch_array($result_game);

        echo <<<EOD
<form method="POST" action="dashboard_games_update.php">
<h2>Modificar Juego</h2>
<input type="hidden" name="id_game" value="{$game['id_game']}">
<p><label for="add_title">Título: </label>
<input type="text" id="add_title" name="add_title" value="{$game['title']}" required></p>
<p><label for="add_link">Enlace a Steam: </label>
<input type="text" id="add_link" name="add_link" value="{$game['link']}" required></p>
<p><label for="add_image">Imagen (nombre de archivo): </label>
<input type="text" id="add_image" name="add_image" value="{$game['header']}" required></p>
<p><label for="add_price">Precio: </label>
<input type="number" id="add_price" name="add_price" value="{$game['price']}" min="0" step="0.01" required></p>
<p><label for="add_trailer">Enlace al tráiler de YouTube: </label>
<input type="text" id="add_trailer" name="add_trailer" value="{$game['trailer']}" required></p>
<p><input type="submit" name="update_game_submit" id="update_game_submit" value="Actualizar Juego"></p>
</form>
EOD;

        echo "<h3>Comentarios</h3>";
        $query_comments = "SELECT comments.comment, comments.created_at, creators.name FROM comments INNER JOIN creators ON comments.id_creator = creators.id_creator WHERE comments.id_game = $id_game ORDER BY comments.created_at DESC";
        $result_comments = mysqli_query($conn, $query_comments);

        if ($result_comments && mysqli_num_rows($result_comments) > 0) {
            echo "<ul>";
            while ($comment = mysqli_fetch_array($result_comments)) {
                echo "<li>";
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

    $query_games = <<<EOD
    SELECT games.id_game, games.title, games.price, games.header, games.link, games.trailer
    FROM games
    INNER JOIN creators_games ON games.id_game = creators_games.id_game
    WHERE creators_games.id_creator = {$_SESSION["id_creator"]}
    EOD;

    $result_games = mysqli_query($conn, $query_games);

    if ($result_games && mysqli_num_rows($result_games) > 0) {
        while ($game = mysqli_fetch_array($result_games)) {
            $trailer_id = substr($game['trailer'], strpos($game['trailer'], "?v=") + 3);
            echo <<<EOD
<div class="game-card">
    <h3>{$game['title']}</h3>
    <img src="imgs/{$game['header']}" alt="{$game['title']}">
    <p>Precio: {$game['price']}€</p>
    <a href="{$game['link']}" target="_blank">Ver en Steam</a>
    <iframe src="https://www.youtube.com/embed/{$trailer_id}" frameborder="0" allowfullscreen></iframe>
    <a href="/dashboard_games.php?id_game={$game['id_game']}">Modificar</a>
</div>
EOD;

            echo "<h3>Comentarios</h3>";
            $query_comments = "SELECT comments.comment, comments.created_at, creators.name FROM comments INNER JOIN creators ON comments.id_creator = creators.id_creator WHERE comments.id_game = {$game['id_game']} ORDER BY comments.created_at DESC";
            $result_comments = mysqli_query($conn, $query_comments);

            if ($result_comments && mysqli_num_rows($result_comments) > 0) {
                echo "<ul>";
                while ($comment = mysqli_fetch_array($result_comments)) {
                    echo "<li>";
                    echo "<strong>" . htmlspecialchars($comment['name'], ENT_QUOTES, 'UTF-8') . ":</strong> ";
                    echo "<p>" . htmlspecialchars($comment['comment'], ENT_QUOTES, 'UTF-8') . "</p>";
                    echo "<small>" . htmlspecialchars($comment['created_at'], ENT_QUOTES, 'UTF-8') . "</small>";
                    echo "</li>";
                }
                echo "</ul>";
           
            }
        }
    } else {
        echo "<p>No has añadido ningún juego aún.</p>";
    }

    echo "</div>";
}

closeDashboard();
closeBody();
?>
