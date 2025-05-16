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
            justify-content: center;
            align-items: center;
            padding: 2rem;
            width: 100%;
        }

        .main-content img {
            width: clamp(250px, 30vw, 400px);
            height: auto;
            max-width: 100%;
            margin-bottom: clamp(0rem, 0vw, 0rem);
        }

        .main-content h2 {
            text-align: center;
            color: #ffffff;
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .card-container {
            border: 2px solid #007bff;
            border-radius: 8px;
            padding: 6rem;
            background-color: #0d1f38;
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
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
        }

        .card-body i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .card-title {
            font-size: 1rem;
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

        /* Media Query para pantallas pequeñas */
        @media (max-width: 576px) {
            .main-content img {
                width: clamp(200px, 25vw, 150px);
                height: auto;
                max-width: 100%;
                margin-bottom: clamp(0.8rem, 2vw, 1rem);
            }

            .card-container {
                padding: clamp(1rem, 3vw, 1.5rem);
                border: 1px solid #007bff;
            }

            .card {
                max-height: clamp(120px, 30vw, 140px);
                border-radius: 10px;
            }

            .card-body {
                padding: clamp(0.8rem, 2vw, 1rem);
            }

            .card-body i {
                font-size: clamp(1.2rem, 4vw, 1.5rem);
                margin-bottom: clamp(0.3rem, 1vw, 0.4rem);
            }

            .card-title {
                font-size: clamp(0.7rem, 2vw, 0.85rem);
            }
        }
    </style>
</head>

<body>
    <div class="main-content">
        <img src="../StudyFlow3.svg" alt="Logotipo de StudyFlow" loading="lazy">
        <div class="container card-container">
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col">
                    <a href="gestion_alumnos.php" class="card-link">
                        <div class="card">
                            <div class="card-body">
                                <i class="fa-solid fa-users" style="color: #0f0f0f;"></i>
                                <h5 class="card-title">Alumnos</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="gestion_profesores.php" class="card-link">
                        <div class="card">
                            <div class="card-body">
                                <i class="fa-solid fa-chalkboard-teacher" style="color: #0f0f0f;"></i>
                                <h5 class="card-title">Profesores</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="gestion_tutores.php" class="card-link">
                        <div class="card">
                            <div class="card-body">
                                <i class="fa-solid fa-user-graduate" style="color: #0f0f0f;"></i>
                                <h5 class="card-title">Tutores</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="gestion_solicitudes.php" class="card-link">
                        <div class="card">
                            <div class="card-body">
                                <i class="fa-solid fa-file-signature" style="color: #0f0f0f;"></i>
                                <h5 class="card-title">Solicitudes</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="gestion_aulas.php" class="card-link">
                        <div class="card">
                            <div class="card-body">
                                <i class="fa-solid fa-school" style="color: #0f0f0f;"></i>
                                <h5 class="card-title">Aulas</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="gestion_asignaturas.php" class="card-link">
                        <div class="card">
                            <div class="card-body">
                                <i class="fa-solid fa-book" style="color: #0f0f0f;"></i>
                                <h5 class="card-title">Asignaturas</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="gestion_grupos.php" class="card-link">
                        <div class="card">
                            <div class="card-body">
                                <i class="fa-solid fa-user-group" style="color: #0f0f0f;"></i>
                                <h5 class="card-title">Grupos</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="gestion_reservas.php" class="card-link">
                        <div class="card">
                            <div class="card-body">
                                <i class="fa-solid fa-calendar-check" style="color: #0f0f0f;"></i>
                                <h5 class="card-title">Reservas</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="gestion_tareas.php" class="card-link">
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