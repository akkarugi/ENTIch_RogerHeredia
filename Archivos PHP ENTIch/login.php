<?php

require_once("template.php");

printHead("ENTIch: Login");

openBody("ENTIch");

echo <<<EOD
<form method="POST" action="login_check.php">
    <h2>Login</h2>
    <p>
        <label for="login_username">Username:</label>
        <input type="text" id="login_username" name="login_username" required>
    </p>
    <p>
        <label for="login_password">Password:</label>
        <input type="password" id="login_password" name="login_password" required>
    </p>
    <p>
        <input type="submit" name="login_submit" id="login_submit" value="Login">
    </p>
</form>

<form method="POST" action="register_check.php">
    <h2>Registro</h2>
    <p>
        <label for="register_name">Nombre:</label>
        <input type="text" id="register_name" name="register_name" required>
    </p>
    <p>
        <label for="register_username">Username:</label>
        <input type="text" id="register_username" name="register_username" required>
    </p>
    <p>
        <label for="register_email">Email:</label>
        <input type="email" id="register_email" name="register_email" required>
    </p>
    <p>
        <label for="register_password">Password:</label>
        <input type="password" id="register_password" name="register_password" required>
    </p>
    <p>
        <label for="register_repass">Confirm Password:</label>
        <input type="password" id="register_repass" name="register_repass" required>
    </p>
    <p>
        <input type="submit" name="register_submit" id="register_submit" value="Register">
    </p>
</form>
EOD;

closeBody();

?>
