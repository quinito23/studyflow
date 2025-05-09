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
    <title>Bienvenido - StudyFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
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
            flex-direction: column;
            /* Stack h2 and container vertically */
            justify-content: center;
            align-items: center;
            padding: 2rem;
            width: 100%;
        }

        .main-content h2 {
            text-align: center;
            color: #ffffff;
            font-size: 4rem;
            /* Matches profesor.html h2 size */
            margin-bottom: 1rem;
            /* Space between h2 and container */
        }

        .card-container {
            border: 2px solid #007bff;
            /* Blue border like profesor-form */
            border-radius: 8px;
            padding: 6rem;
            background-color: #0d1f38;
            /* Darker background like profesor-form */
        }

        .card {
            background-color: #ffffff;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            max-height: 200px;

            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease;
        }

        .card-body {
            text-align: center;
            padding: 1.5rem;
            /* Reduced from 2.5rem to fit larger icons */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
        }

        .card-body i {
            font-size: 2rem;
            /* Larger but proportional icons */
            margin-bottom: 0.5rem;
        }

        .card-title {
            font-size: 1rem;
            /* Proportional to icons */
            margin: 0;
            color: #0f0f0f;
        }

        .card-link {
            text-decoration: none;
            color: inherit;
        }

        .card-link .card:hover {
            background-color: #f0f0f0;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
            transform: translateY(-5px);
        }
    </style>
</head>

<body>
    <div class="main-content">
        <h2>StudyFlow</h2>
        <div class="container card-container">
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col">
                    <a href="alumno.html" class="card-link">
                        <div class="card">
                            <div class="card-body">
                                <i class="fa-solid fa-users" style="color: #0f0f0f;"></i>
                                <h5 class="card-title">Alumnos</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="profesor.html" class="card-link">
                        <div class="card">
                            <div class="card-body">
                                <i class="fa-solid fa-chalkboard-teacher" style="color: #0f0f0f;"></i>
                                <h5 class="card-title">Profesores</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="tutor.html" class="card-link">
                        <div class="card">
                            <div class="card-body">
                                <i class="fa-solid fa-user-graduate" style="color: #0f0f0f;"></i>
                                <h5 class="card-title">Tutores</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="solicitud.html" class="card-link">
                        <div class="card">
                            <div class="card-body">
                                <i class="fa-solid fa-file-signature" style="color: #0f0f0f;"></i>
                                <h5 class="card-title">Solicitudes</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="aula.html" class="card-link">
                        <div class="card">
                            <div class="card-body">
                                <i class="fa-solid fa-school" style="color: #0f0f0f;"></i>
                                <h5 class="card-title">Aulas</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="asignatura.html" class="card-link">
                        <div class="card">
                            <div class="card-body">
                                <i class="fa-solid fa-book" style="color: #0f0f0f;"></i>
                                <h5 class="card-title">Asignaturas</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="grupo.html" class="card-link">
                        <div class="card">
                            <div class="card-body">
                                <i class="fa-solid fa-user-group" style="color: #0f0f0f;"></i>
                                <h5 class="card-title">Grupos</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="reservas.php" class="card-link">
                        <div class="card">
                            <div class="card-body">
                                <i class="fa-solid fa-calendar-check" style="color: #0f0f0f;"></i>
                                <h5 class="card-title">Reservas</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="tareas.php" class="card-link">
                        <div class="card">
                            <div class="card-body">
                                <i class="fa-solid fa-tasks" style="color: #0f0f0f;"></i>
                                <h5 class="card-title">Tareas</h5>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-oa8uG8fVvLdYKFCSzQANn0knfM0sOunB0o3BuONLY1YOtkrzqu2qV+RoL0gD9g6g"
        crossorigin="anonymous"></script>
</body>

</html>