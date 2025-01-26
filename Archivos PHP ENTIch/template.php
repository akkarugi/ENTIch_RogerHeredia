<?php

function printHead($title)
{
    echo <<<EOD
<!doctype html>
<html>
<head>
    <title>{$title}</title>
    <link rel="stylesheet" href="estilo.css" />
</head>
EOD;
}

function getLoginOptions(){
    if(isset($_SESSION["id_creator"])){
        return <<<EOD
        <li><a href="/dashboard.php">Dashboard</a></li>
        <li> <a href="/logout.php">Logout</a> </li>
        EOD;
    }
    return <<<EOD
    <li> <a href="login.php">Login</a> </li>
    EOD;
}

function openBody ($title="ENTIch")
{
    $login_opt = getLoginOptions();
    echo <<<EOD
<body>

<header>
<h1>{$title}</h1>
<nav>
    <ul>
    <li><a href="/index.php">Home</a></li>
    {$login_opt}
    </ul>
</nav>
</header>
<main>
EOD;
}

function closeBody ()
{
    echo <<<EOD
</main>

<footer>

</footer>

</body>
</html>
EOD;
}

?>
