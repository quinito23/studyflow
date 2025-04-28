<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['correo'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - EduSynergy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body{
            background-color: #2D2C55;
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            color: white;
            font-family: 'Arial', sans-serif;
        }

        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            width: 100%;
        }

        .card {
            background-color: #ffffff;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 350px;
            max-height: 200px;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease;
        }

        .card-body {
            text-align: center;
            padding: 2.5rem;
        }

        .row {
            justify-content: center;
            gap: 2rem;
        }

        .cards-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 2rem;
            width: 100%;
            max-width: 800px;
        }

        .cards-container > div {
            flex: 0 0 auto; /* Evitar que las tarjetas crezcan más de lo necesario */
        }

        .card-link{
            text-decoration: none;
            color: inherit;
        }

        .card-link .card:hover{
            background-color: #f0f0f0;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
            transform: translateY(-5px);
        }
        

    </style>
</head>
<body>
    <div class="main-content">
        <div class="cards-container">
            
                
                    <div class="card">
                        <div class="card-body">
                            <i class="fa-solid fa-graduation-cap" style="color: #141414;"></i>
                            <h5 class="card-title">Alumnos</h5>
                        </div>
                    </div>
                
                
                    <a href="profesor.html" class="card-link">
                        <div class="card">
                            <div class="card-body">
                                <i class="fa-solid fa-user-tie" style="color: #0f0f0f;"></i>
                                <h5 class="card-title">Profesores</h5>
                            </div>
                    </div>
                    </a>
                
                
                    <a href="tutor.html" class="card-link">
                        <div class="card">
                            <div class="card-body">
                                <i class="fa-solid fa-user-tie" style="color: #0f0f0f;"></i>
                                <h5 class="card-title">Tutores</h5>
                            </div>
                    </div>
                    </a>
                
                
                    <a href="solicitud.html" class="card-link">
                        <div class="card">
                            <div class="card-body">
                                <i class="fa-solid fa-user-tie" style="color: #0f0f0f;"></i>
                                <h5 class="card-title">Solicitudes</h5>
                            </div>
                    </div>
                    </a>
                    
                

            
        </div>
    </div>
    
</body>
</html>