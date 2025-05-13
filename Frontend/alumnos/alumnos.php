<?php
session_start(); //inicio de sesión para poder acceder a los datos del usuario que se ha logeado
// Verifica si el usuario está autenticado y tiene el rol de alumno o administrador
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] != 'alumno' && $_SESSION['rol'] != 'administrador')) {
    header("Location: login.php");
    exit();
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
    <title>Alumnos</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #112a4a;
            color: #f8f9fa;
            font-size: 16px;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background-color: #0d1f38;
            padding: 1rem 2rem;
            color: white;
            border-bottom: 1px solid #ffffff33;
            display: flex;
            flex-wrap: wrap;
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
            margin-left: clamp(1rem, 3vw, 2rem);
        }

        .navbar-toggler {
            font-size: clamp(1rem, 3vw, 1.5rem);
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
        }

        .breadcrumb-item+.breadcrumb-item::before {
            color: #007bff;
        }

        .breadcrumb-item a {
            color: #f8f9fa;
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: #d3d6db;
        }

        .offcanvas {
            background-color: #0d1f38;
            color: white;
            width: 20vw !important;
            min-width: 200px !important;
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
            padding: 0.75rem 1rem;
            display: block;
            text-decoration: none;
            font-size: clamp(0.9rem, 2vw, 1rem);
        }

        .offcanvas-body .nav-link.active {
            background-color: #007bff;
            color: white;
        }

        .main-content {
            padding: 2rem;
            flex: 1;
        }

        h2,
        h3 {
            font-size: clamp(1.5rem, 4vw, 2rem);
            color: #ffffff;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .content-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: calc(100vh - 160px);
        }

        #asignaturas-container {
            margin-top: 0rem;
        }

        #asignaturas-container .card {
            background-color: #0d1f38;
            border: 2px solid #007bff;
            border-radius: 8px;
            color: #f8f9fa;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        #asignaturas-container .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
        }

        #asignaturas-container .card-img-top {
            height: 150px;
            object-fit: cover;
            border-bottom: 1px solid #ffffff33;
        }

        #asignaturas-container .card-body {
            padding: 1rem;
        }

        #asignaturas-container .card-title {
            font-size: clamp(1rem, 3vw, 1.25rem);
            color: #ffffff;
            margin-bottom: 0.5rem;
        }

        #asignaturas-container .card-text {
            font-size: clamp(0.8rem, 2vw, 1rem);
            color: #d3d6db;
        }

        .modal-content {
            background-color: #112a4a;
            color: #f8f9fa;
            border-radius: 5px;
        }

        .modal-header {
            border-color: #ffffff33;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .modal-header,
        .modal-title {
            color: white;
            text-align: center;
            flex-grow: 1;
            font-size: clamp(1rem, 3vw, 1.25rem);
        }

        .modal-body p {
            margin: 0.5rem 0;
            font-size: clamp(0.8rem, 2vw, 1rem);
        }

        .modal-body p strong {
            color: white;
            font-weight: 500;
            display: inline-block;
            width: clamp(120px, 30vw, 180px);
        }

        .modal-body p span {
            color: #d3d6db;
        }

        #reservas-list,
        #tareas-list {
            list-style: none;
            padding: 0;
            max-height: 300px;
            overflow-y: auto;
        }

        #reservas-list li,
        #tareas-list li {
            background-color: #0d1f38;
            border: 1px solid #007bff;
            border-radius: 5px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            font-size: clamp(0.8rem, 2vw, 1rem);
            color: #f8f9fa;
        }

        .btn-secondary {
            font-size: clamp(0.8rem, 2vw, 1rem);
            background-color: #6c757d;
            border-color: #6c757d;
            transition: background-color 0.2s;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
        }

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
    <!--Header de bootsraps-->
    <header class="header">
        <div class="d-flex align-items-center navbar-dark">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar"
                aria-label="Toggle Navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <h1 class="mx-auto">StudyFlow</h1>
        </div>
        <!--Aquí ponemos los elementos del breadcrumb-->
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../login.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Mis Asignaturas</li>
            </ol>
        </nav>
    </header>
    <!--Barra lateral de bootsraps-->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasSidebarLabel">StudyFlow</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column">
                <!--Elementos en la barra lateral-->
                <li class="nav-item">
                    <a class="nav-link active" href="alumnos.php">Mis Asignaturas</a>
                </li>
            </ul>
        </div>
    </div>
    <!--Contenido principal-->
    <main class="main-content">
        <h2>Mis Asignaturas</h2>
        <div class="content-container">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="asignaturas-container"></div>
        </div>
    </main>

    <!--Modal que se abre al pulsar sobre las tarjetas de las asignaturas para ver las tareas y reservas que tenga el alumno-->
    <div class="modal fade" id="reservasModal" tabindex="-1" aria-labelledby="reservasModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reservasModalLabel">Reservas y Tareas</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="close"></button>
                </div>
                <div class="modal-body">
                    <h3>Reservas</h3>
                    <ul id="reservas-list"></ul>
                    <h3>Tareas</h3>
                    <ul id="tareas-list"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!--footer-->
    <footer class="footer">
        <p>© 2025 StudyFlow - Todos los derechos reservados</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        //Obtenemos los elemetos del frontend con los que vamos a trabajar
        const asignaturasContainer = document.getElementById('asignaturas-container');
        const reservasList = document.getElementById('reservas-list');
        const tareasList = document.getElementById('tareas-list');
        const reservasModal = new bootstrap.Modal(document.getElementById('reservasModal'));

        //función para hacer solicitudes AJAX
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

        //función para cargar las asignaturas que tiene asociadas el alumno que haya inciado sesión
        function cargarAsignaturas() {
            //obtenemos el id del alumno desde la sesion
            const id_usuario = <?php echo json_encode($_SESSION['id_usuario']); ?>;
            hacerSolicitud(`../../APIs/alumno_api.php?asignaturas=1&id_usuario=${id_usuario}`, 'GET', null, function (status, response) {
                try {
                    const asignaturas = JSON.parse(response);
                    asignaturasContainer.innerHTML = '';
                    //por cada asignatura , se crea una carta
                    if (asignaturas.length > 0) {
                        asignaturas.forEach(asignatura => {
                            const card = document.createElement('div');
                            card.className = 'col';
                            card.innerHTML = `
                                <div class="card" onclick="mostrarReservasYTareas(${asignatura.id_asignatura})">
                                    <img src="../../asignaturas.png" class="card-img-top" alt="Imagen de Asignatura">
                                    <div class="card-body">
                                        <h5 class="card-title">${asignatura.nombre}</h5>
                                        <p class="card-text">${asignatura.descripcion || 'Sin descripción disponible.'}</p>
                                    </div>
                                </div>
                            `;
                            asignaturasContainer.appendChild(card);
                        });
                    } else {
                        asignaturasContainer.innerHTML = '<p class="text-center"> No estás inscrito en ninguna asignatura.</p>';
                    }
                } catch (e) {
                    asignaturasContainer.innerHTML = '<p class="text-center">Error al cargar las asignaturas.</p>';
                }
            });
        }
        //funcion para mostrar en el modal las reservas y las tareas que tiene el alumno para la asignatura
        function mostrarReservasYTareas(id_asignatura) {
            const id_usuario = <?php echo json_encode($_SESSION['id_usuario']); ?>;
            if (!id_usuario) {
                reservasList.innerHTML = '<li>Error: No se encontró el ID de usuario.</li>';
                tareasList.innerHTML = '<li>Error: No se encontró el ID de usuario.</li>';
                reservasModal.show();
                return;
            }

            // Cargar reservas del usuario para la asignatura
            const urlReservas = `../../APIs/reserva_api.php?asignatura=${id_asignatura}&id_usuario=${id_usuario}`;
            hacerSolicitud(urlReservas, 'GET', null, function (status, response) {
                try {
                    const reservas = JSON.parse(response);
                    reservasList.innerHTML = '';
                    if (status !== 200) {
                        reservasList.innerHTML = `<li>Error: Estado de respuesta ${status}</li>`;
                    } else if (reservas.message) {
                        reservasList.innerHTML = `<li>${reservas.message}</li>`;
                    } else if (reservas.length > 0) {
                        reservas.forEach(reserva => {
                            const li = document.createElement('li');
                            li.textContent = `${reserva.fecha} - ${reserva.hora_inicio} a ${reserva.hora_fin} | Aula: ${reserva.aula} | Profesor: ${reserva.profesor}`;
                            reservasList.appendChild(li);
                        });
                    } else {
                        reservasList.innerHTML = `<li>No hay reservas para esta asignatura.</li>`;
                    }
                } catch (e) {
                    reservasList.innerHTML = `<li>Error al cargar las reservas: ${response}</li>`;
                }
            });

            // Cargar tareas del alumno para la asignatura
            const urlTareas = `../../APIs/tarea_api.php?asignatura=${id_asignatura}&id_usuario=${id_usuario}`;
            hacerSolicitud(urlTareas, 'GET', null, function (status, response) {
                try {
                    const tareas = JSON.parse(response);
                    tareasList.innerHTML = '';
                    if (status !== 200) {
                        tareasList.innerHTML = `<li>Error: Estado de respuesta ${status}</li>`;
                    } else if (tareas.message) {
                        tareasList.innerHTML = `<li>${tareas.message}</li>`;
                    } else if (tareas.length > 0) {
                        tareas.forEach(tarea => {
                            const li = document.createElement('li');
                            li.textContent = `${tarea.descripcion} | Fecha Entrega: ${tarea.fecha_entrega} | Profesor: ${tarea.profesor}`;
                            tareasList.appendChild(li);
                        });
                    } else {
                        tareasList.innerHTML = `<li>No hay tareas para esta asignatura.</li>`;
                    }
                    reservasModal.show();
                } catch (e) {
                    tareasList.innerHTML = `<li>Error al cargar las tareas: ${response}</li>`;
                    reservasModal.show();
                }
            });
        }

        window.onload = function () {
            cargarAsignaturas();
        };
    </script>
</body>

</html>