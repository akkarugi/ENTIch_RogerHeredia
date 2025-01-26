<?php
session_start();
require_once("db_config.php");

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("ERROR: No se pudo conectar a la base de datos.");
}

$query = "SELECT id_game, title, price, header FROM games";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("ERROR: No se pudo obtener la lista de juegos.");
}

require_once("template.php");
printHead("ENTIch: Home");
openBody("ENTIch");

echo "<h1>Lista de Juegos</h1>";
echo "<ul class='game-list'>";

while ($game = mysqli_fetch_array($result)) {
    echo "<li class='game-item'>";
    echo "<h2>" . htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8') . "</h2>";
    echo "<img src='imgs/" . htmlspecialchars($game['header'], ENT_QUOTES, 'UTF-8') . "' alt='" . htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8') . "'>";
    echo "<p>Precio: " . htmlspecialchars($game['price'], ENT_QUOTES, 'UTF-8') . "€</p>";
    echo "<a href='game.php?id_game=" . htmlspecialchars($game['id_game'], ENT_QUOTES, 'UTF-8') . "'>Ver detalles</a>";
    echo "</li>";
}

echo "</ul>";

closeBody();
mysqli_close($conn);
?>
