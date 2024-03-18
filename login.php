<?php

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $mysqli = require __DIR__ . "/database.php";

        $sql = sprintf("SELECT * FROM user 
                        WHERE email = '%s'",
                        $mysqli->real_escape_string($_POST["email"]));

        $result = $mysqli->query($sql);

        $user = $result->fetch_assoc();

        if ($user) {
            if (password_verify($_POST["password"], $user["password_hash"])){

                die("Login succesful");
                
            }
        }
    }


?>



<!DOCTYPE html>
<html>
    <head>
        <title>Login </title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    </head>
    <body>
        <h1>Login </h1>

        <form method="post">
            <label for="email">Email</label>
            <input type="email" id="email" name="email">

            <label for="password">Password</label>
            <input type="password" id="password" name="password">

            <button>Log in</button>
        </form>

    </body>
</html>
