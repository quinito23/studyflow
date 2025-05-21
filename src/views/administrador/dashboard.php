<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 'administrador') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - StudyFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!--Importamos la hoja de estilos styles.css para el estilo del header, barra lateral y footer-->
    <link rel="stylesheet" href="../../../public/css/styles.css">
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
    <!--Header-->
    <header class="header">
        <div class="d-flex align-items-center navbar-dark">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar"
                aria-label="Barra Lateral">
                <span class="navbar-toggler-icon"></span>
            </button>
            <img src="../../../public/imagenes/StudyFlow3.svg" alt="logotipo" loading="lazy">
        </div>
        <!--Elementos del breadcrumb-->
        <div class="breadcrumb-container">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active"><a href="dashboardAdmin.php">Home</a></li>
                </ol>
            </nav>
            <span class="separador">|</span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm logout-btn" title="Cerrar Sesión">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </header>

    <!--Barra lateral-->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasSidebarLabel">StudyFlow</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <!--Listamos las diferentes páginas que aparecerán en la barra lateral-->
            <ul class="nav flex-column">
                <li class="nav-item">
                    <h6 class="nav-header">Gestión de Usuarios</h6>
                </li>
                <li class="nav-item"><a class="nav-link" href="gestion_profesores.php">Profesores</a></li>
                <li class="nav-item"><a class="nav-link" href="gestion_alumnos.php">Alumnos</a></li>
                <li class="nav-item"><a class="nav-link" href="gestion_tutores.php">Tutores</a></li>
                <li class="nav-item">
                    <h6 class="nav-header">Gestión Académica</h6>
                </li>
                <li class="nav-item"><a class="nav-link" href="gestion_aulas.php">Aulas</a></li>
                <li class="nav-item"><a class="nav-link" href="gestion_asignaturas.php">Asignaturas</a></li>
                <li class="nav-item"><a class="nav-link" href="gestion_grupos.php">Grupos</a></li>
                <li class="nav-item"><a class="nav-link" href="gestion_reservas.php">Reservas</a></li>
                <li class="nav-item"><a class="nav-link" href="gestion_tareas.php">Tareas</a></li>
                <li class="nav-item"><a class="nav-link" href="gestion_solicitudes.php">Solicitudes</a></li>
            </ul>
        </div>
    </div>




    <div class="main-content">
        <img src="../../../public/imagenes/StudyFlow3.svg" alt="Logotipo de StudyFlow" loading="lazy">
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

    <!--Footer-->
    <footer class="footer">
        <p>© 2025 StudyFlow - Todos los derechos reservados | <a href="mapa_sitio.php">Mapa del Sitio</a> | <a
                href="mailto:info@studyflow.com">Contacto</a></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>