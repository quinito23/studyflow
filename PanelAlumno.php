<?php
session_start();

if(!isset($_SESSION['correo'])){
    header("Location: login.html");
    exit();
}

if ($_SESSION['rol'] !== 'alumno') {
    header("Location: login.html");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h5>HOLA</h5>
</body>
</html>