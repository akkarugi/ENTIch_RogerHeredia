<?php

// Función para abrir el panel de control
function openDashboard() {
    echo <<<EOD
    <aside>
        <nav>
            <h2> OPCIONES </h2>
            <ul>
                 <li><a href="/dashboard.php" class="sidebar-link">Perfil</a></li>
                 <li><a href="/dashboard_games.php" class="sidebar-link">Juegos</a></li>
                 <li><a href="/dashboard_games.php?add_game=true" class="sidebar-link">Añadir un Juego</a></li>
            </ul>
        </nav>
    </aside>
    <article>
EOD;
}

// Función para cerrar el panel de control
function closeDashboard() {
    echo <<<EOD
    </article>
EOD;
}

?>
