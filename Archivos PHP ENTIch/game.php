<?php
session_start();
require_once("db_config.php");

if (!isset($_GET['id_game'])) {
    die("ERROR: No se ha especificado un juego.");
}

$id_game = intval($_GET['id_game']);
$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("ERROR: No se pudo conectar a la base de datos.");
}

$query = "SELECT * FROM games WHERE id_game = $id_game";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) != 1) {
    die("ERROR: No se pudo obtener la información del juego.");
}

$game = mysqli_fetch_array($result);

require_once("template.php");
printHead("ENTIch: " . htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8'));
openBody("ENTIch");

echo "<div class='game-details'>";
echo "<h2>" . htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8') . "</h2>";
echo "<figure><img src='imgs/" . htmlspecialchars($game['header'], ENT_QUOTES, 'UTF-8') . "' alt='" . htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8') . "'></figure>";
echo "<p>Precio: " . htmlspecialchars($game['price'], ENT_QUOTES, 'UTF-8') . "€</p>";
echo "<a href='" . htmlspecialchars($game['link'], ENT_QUOTES, 'UTF-8') . "' target='_blank' class='btn'>Ver en Steam</a>";
echo "<iframe width='560' height='315' src='https://www.youtube.com/embed/" . htmlspecialchars(substr($game['trailer'], strpos($game['trailer'], "?v=") + 3), ENT_QUOTES, 'UTF-8') . "' frameborder='0' allowfullscreen></iframe>";
echo "</div>";

echo "<div class='comments-section'>";
echo "<h3>Comentarios</h3>";
$query_comments = "SELECT comments.comment, comments.created_at, creators.name FROM comments INNER JOIN creators ON comments.id_creator = creators.id_creator WHERE comments.id_game = $id_game ORDER BY comments.created_at DESC";
$result_comments = mysqli_query($conn, $query_comments);

if ($result_comments && mysqli_num_rows($result_comments) > 0) {
    echo "<ul class='comments-list'>";
    while ($comment = mysqli_fetch_array($result_comments)) {
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

if (isset($_SESSION['id_creator'])) {
    echo <<<EOD
    <form method="POST" action="comment_insert.php" class="comment-form">
        <input type="hidden" name="id_game" value="$id_game">
        <textarea name="comment" required placeholder="Escribe tu comentario aquí..."></textarea>
        <input type="submit" value="Enviar comentario" class="btn">
    </form>
EOD;
}

echo "</div>";

closeBody();
mysqli_close($conn);
?>
