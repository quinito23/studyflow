<?php
session_start();

//verificar autenticacion y rol
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 'profesor') {
    header("Location: login.php");
    exit;
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Mis Reservas</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #112a4a;
            color: #f8f9fa;
            font-size: 16px;
            margin: 0px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #alumno-form {
            border: 2px solid #007bff;
            /* Borde azul */
            border-radius: 8px;
            padding: 1.5rem;
            background-color: #0d1f38;
            /* Fondo más oscuro */
            margin-bottom: 2rem;
        }

        /*Header*/
        .header {
            background-color: #0d1f38;
            padding: 1rem 2rem;
            color: white;
            border-bottom: 1px solid #ffffff33;
            display: flex;
            flex-wrap: wrap;
            /*Para permitir que los elementos se apilen si no caben*/
            justify-content: space-between;
            align-items: center;
        }

        .header .d-flex {
            gap: 1rem;
            align-items: center;
        }

        .header h1 {
            margin: 0;
            font-size: clamp(1.5rem, 5vw, 2.5rem);
            /*Hacemos que el tamaño sea dinamico*/
            margin-left: clamp(1rem, 3vw, 2rem);
        }

        .navbar-toggler {
            font-size: clamp(1rem, 3vw, 1.5rem);
            /*Hacemos que el tamaño sea dinamico*/
            margin-right: 1rem;
            z-index: 1000;
        }

        .navbar-toggler-icon {
            color: white;
        }

        .breadcrumb {
            background-color: transparent;
            padding: 0.5rem 0;
            margin-bottom: 0;
            font-size: clamp(0.7rem, 2vw, 0.9rem);
            /*Hacemos que el tamaño sea dinamico*/
        }

        /*Para cambiar el color del elemento que separa los elementos del breadcrumb ">"*/
        .breadcrumb-item+.breadcrumb-item::before {
            color: #007bff;
        }

        .breadcrumb-item a {
            color: #f8f9fa;
            text-decoration: none;
        }

        .breadcrumb-item .active {
            color: #d3d6db;
        }

        /*sidebar*/

        .offcanvas {
            background-color: #0d1f38;
            color: white;
            width: 20vw !important;
            min-width: 200px !important;
            /*ancho minimo para que no se corte cuando usamos un movil*/
        }

        .offcanvas-header {
            border-bottom: 1px solid #ffffff33;
        }

        .offcanvas-title {
            color: white;
            font-size: clamp(1rem, 3vw, 1.25rem);
        }

        .offcanvas-body .nav-link {
            color: #f8f9fa;
            padding: 0.75rem, 1rem;
            display: block;
            text-decoration: none;
            font-size: clamp(0.9rem, 2vw, 1rem);
            /*Hacemos que el tamaño sea dinamico*/
        }

        .offcanvas-body .nav-link.active {
            background-color: #007bff;
            color: white;
        }

        /*Contenido principal*/
        .main-content {
            padding: 2rem;
            flex: 1;
        }


        h2 {
            font-size: clamp(1.5rem, 4vw, 2rem);
            color: #ffffff;
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            color: white;
            font-size: clamp(0.9rem, 2vw, 1rem);
        }

        .form-control,
        .form-select {
            background-color: white;
            color: #333;
            border-radius: 5px;
            font-size: clamp(0.8rem, 2vw, 1rem);
        }

        .btn-primary {
            font-size: clamp(0.8rem, 2vw, 1rem);
        }

        .btn-light {
            font-size: clamp(0.8rem, 2vw, 1rem);
        }

        .table thead {
            background-color: #007bff;
            color: white;
        }

        .table {
            margin-top: 2rem;
            background-color: #e9ecef;
            color: #333;
            font-size: clamp(0.7rem, 1.5vw, 1rem);
        }

        .table tbody tr {
            background-color: #d3d6db;
        }

        .table th,
        .table td {
            text-align: center;
        }

        .error-message {
            background-color: #f8d7da;
            color: #dc3545;
            padding: 0.75rem;
            border-radius: 5px;
            margin-top: 1rem;
            display: none;
        }

        .form-title {
            align-items: center;
        }

        /*Footer*/
        .footer {
            background-color: #0d1f38;
            color: #f8f9fa;
            text-align: center;
            padding: 1rem;
            border-top: 1px solid #ffffff33;
            font-size: clamp(0.8rem, 2vw, 1rem);
        }
    </style>
</head>

<body>
    <!--Header-->
    <header class="header">
        <!--Ponemos navbar-dark para que se haga contraste entre el boton de hamburguesa y el fondo del header-->
        <div class="d-flex align-items-center navbar-dark">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar"
                aria-label="Toggle Navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <h1 class="mx-auto">StudyFlow</h1>
        </div>
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboardProfesor.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Mis Reservas</li>
            </ol>
        </nav>
    </header>

    <!--Barra lateral desplegable con offcanvas de bootstraps-->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasSidebarLabel">StudyFlow</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="reservas.php">Reservar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="mis_reservas.php">Mis Reservas</a>
                </li>
            </ul>
        </div>
    </div>

    <!--Contenido principal-->
    <main class="main-content">

        <h2 class="text-center">Mis Reservas</h2>

        <!--Creamos la tabla para mostrar las reservas-->
        <div class="table-responsive">
            <table class="table" id="tabla-reservas">
                <thead>
                    <tr class="table-primary">
                        <th scope="col">Fecha</th>
                        <th scope="col">Horario</th>
                        <th scope="col">Aula</th>
                        <th scope="col">Asignatura</th>
                        <th scope="col">Profesor</th>
                        <th scope="col">Grupo</th>
                        <th scope="col">Estado</th>
                    </tr>
                </thead>
                <tbody id="reservas-lista">
                    <!--Aqui serán listadas las reservas-->
                </tbody>
            </table>
        </div>

        <div id="error-message"></div>

    </main>

    <!--FOOTER-->
    <footer class="footer">
        <p>&copy; 2025 StudyFlow - Todos los derechos reservados</p>
    </footer>

    <script>
        const reservasForm = document.getElementById('reservas-form');
        const reservasLista = document.getElementById('reservas-lista');
        const errorMessage = document.getElementById('error-message');
        const formTitle = document.getElementById('form-title');

        function mostrarError(mensaje) {
            errorMessage.textContent = mensaje;
            errorMessage.style.display = 'block';
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 3000);
        }

        function hacerSolicitud(url, metodo, datos, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open(metodo, url, true);
            xhr.setRequestHeader('Content-type', 'application/json');
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4) {
                    callback(xhr.status, xhr.responseText);
                }
            };
            xhr.send(datos ? JSON.stringify(datos) : null);
        }

        function cargarReservas() {
            const id_usuario = <?php echo json_encode($_SESSION['id_usuario']); ?>;
            const url = `reserva_api.php?id_usuario=${id_usuario}`;
            hacerSolicitud(url, 'GET', null, function (status, response) {
                try {
                    const reservas = JSON.parse(response);
                    reservasLista.innerHTML = '';
                    reservas.forEach(reserva => {
                        const row = `
                        <tr>
                            <td>${reserva.fecha}</td>
                            <td>${reserva.hora_inicio} - ${reserva.hora_fin}</td>
                            <td>${reserva.aula}</td>
                            <td>${reserva.asignatura}</td>
                            <td>${reserva.profesor}</td>
                            <td>${reserva.grupo}</td>
                            <td>${reserva.estado}</td>
                        </tr>

                        `;
                        reservasLista.innerHTML += row;
                    });
                } catch (e) {
                    mostrarError("Error al cargar las reservas: " + e.message);
                }
            });
        }


        window.onload = function () {
            cargarReservas();
        };

    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>