<?php

define('CLAVE', '6Ldsf5spAAAAAKpGsIjUhYZpm462Cp-6n-8Y9T_O');

$token = $_POST['token'];

$cu = curl_init();
curl_setopt($cu, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
curl_setopt($cu, CURLOPT_POST, 1);
curl_setopt($cu, CURLOPT_POSTFIELDS, http_build_query(array('secret' => CLAVE, 'response' => $token)));
curl_setopt($cu, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($cu);

if ($response === false) {
    die("Error al realizar la solicitud cURL: " . curl_error($cu));
}

curl_close($cu);

$datos = json_decode($response, true);
$datosEncoded = json_encode($datos);

if ($datos === null) {
    die("Error al decodificar la respuesta JSON de reCAPTCHA");
}


if($datos['success'] == 1 && $datos['score'] >= 0.5){
    if ($datos['action'] == 'validarUsuario') {
        if (empty($_POST["name"])) {
            die("Name is required");
        }
    
        if (! filter_var($_POST["email"],FILTER_VALIDATE_EMAIL)) {
            die("Valid email is required");
            }
    
        if (strlen($_POST["password"]) < 8) {
            die("Password must be at least 8 characters long");
        }
    
        if (! preg_match("/[a-z]/i", $_POST["password"])) {
            die("Password must contain at least one letter");
        }
    
        if (! preg_match("/[0-9]/i", $_POST["password"])) {
            die("Password must contain at least one number");
        }
    
        if ($_POST["password"] !== $_POST["password_confirmation"]){
            die("Password must match");
        }
    
        $password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);
    
        $mysqli = require __DIR__ . "/database.php";
    
        $sql = "INSERT INTO user (name, email, password_hash)
                VALUES (?, ?, ?)";
            
        $stmt = $mysqli->stmt_init(); 
    
        if ( ! $stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
    
        $stmt->bind_param("sss",
                        $_POST["name"],
                        $_POST["email"],
                        $password_hash);
        try {
            if ($stmt->execute()) {
                echo '<h1>Proceso de registro</h1>
                <p id="mensaje">Registro completado. Ahora ya puedes hacer <a href="login.php">log in</a>.</p>
                <p>Puedes ver los datos que devuelve la solicitud reCAPTCHA en la consola.<p/>';
            }
        } catch (mysqli_sql_exception $e) {
            if ($mysqli->errno === 1062) {
                die("Email already in use");
            } else {
                die($mysqli->error . " " . $mysqli->errno);
            }
        }
    }
    } else {
        echo '<h1>Validación reCAPTCHA fallida:</h1>
        <p>ERES UN ROBOT, o probablemente ya caducó tu token, es duplicado, no tienes internet, o simplemente tuviste mala suerte. Vuelve a intentarlo.<br>
        Puedes volver al registro haciendo click <a href="index.html">aquí</a>.</p>';
    };
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Sign up </title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    </head>
    <body>
        <script>
            var datosEncoded = <?php echo $datosEncoded; ?>;
            console.log(datosEncoded);
        </script>
    </body>
</html>
