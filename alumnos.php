<?php
session_start();

//verificar autenticacion y rol
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

        /* Header */
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

        /* Sidebar */
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

        /* Contenido principal */
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
            /* Centra el título */
        }

        /* Contenedor para centrar las cards verticalmente */
        .content-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: calc(100vh - 160px);
            /* Reducido de 180px a 160px para subir las cartas */
        }

        /* Estilo para las tarjetas de asignaturas */
        #asignaturas-container {
            margin-top: 0rem;
            /* Reducido de 2rem a 0rem para subir las cartas */
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

        .modal-title {
            color: white;
            text-align: center;
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

        /* Estilo para la lista de reservas */
        #reservas-list {
            list-style: none;
            padding: 0;
            max-height: 300px;
            overflow-y: auto;
        }

        #reservas-list li {
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

        /* Footer */
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
    <header class="header">
        <div class="d-flex align-items-center navbar-dark">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar"
                aria-label="Toggle Navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <h1 class="mx-auto">StudyFlow</h1>
        </div>
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboardAlumno.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Mis Asignaturas</li>
            </ol>
        </nav>
    </header>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasSidebarLabel">StudyFlow</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="alumnos.php">Mis Asignaturas</a>
                </li>
            </ul>
        </div>
    </div>

    <main class="main-content">
        <h2>Mis Asignaturas</h2>
        <div class="content-container">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="asignaturas-container">
                <!--Las cartas se crearán dinamicamente aquí-->
            </div>
        </div>
    </main>

    <!--Creamos un modal emergente de botstrpas para mostrar las reservas al pulsar en la asignatura-->

    <div class="modal fade" id="reservasModal" tabindex="-1" aria-labelledby="reservasModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reservasModalLabel">Reservas</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="close"></button>
                </div>
                <div class="modal-body">
                    <ul id="reservas-list"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>© 2025 StudyFlow - Todos los derechos reservados</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const asignaturasContainer = document.getElementById('asignaturas-container');
        const reservasList = document.getElementById('reservas-list')
        const reservasModal = new bootstrap.Modal(document.getElementById('reservasModal'));

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

        function cargarAsignaturas() {
            const id_usuario = <?php echo json_encode($_SESSION['id_usuario']); ?>;
            hacerSolicitud(`alumno_api.php?asignaturas=1&id_usuario=${id_usuario}`, 'GET', null, function (status, response) {
                try {
                    const asignaturas = JSON.parse(response);
                    asignaturasContainer.innerHTML = '';
                    if (asignaturas.length > 0) {
                        asignaturas.forEach(asignatura => {
                            const card = document.createElement('div');
                            card.className = 'col';
                            card.innerHTML = `
                                <div class="card" onclick="mostrarReservas(${asignatura.id_asignatura})">
                                    <img src="asignaturas.png" class="card-img-top" alt="Imagen de Asignatura">
                                    <div class="card-body">
                                        <h5 class="card-title">${asignatura.nombre}</h5>
                                        <p class="card-text">${asignatura.descripcion || 'Sin descripcción disponible.'}</p>
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

        function mostrarReservas(id_asignatura) {
            const id_usuario = <?php echo json_encode($_SESSION['id_usuario']); ?>;
            if (!id_usuario) {
                reservasList.innerHTML = '<li>Error: No se encontró el ID de usuario.</li>';
                reservasModal.show();
                return;
            }
            const url = `reserva_api.php?asignatura=${id_asignatura}&id_usuario=${id_usuario}`;
            hacerSolicitud(url, 'GET', null, function (status, response) {
                console.log('Respuesta de reserva_api.php:', response); // Depuración
                console.log('Status:', status); // Depuración
                try {
                    const reservas = JSON.parse(response);
                    reservasList.innerHTML = '';
                    if (status !== 200) {
                        reservasList.innerHTML = `<li>Error: Estado de respuesta ${status}</li>`;
                        reservasModal.show();
                        return;
                    }
                    if (reservas.message) {
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
                    reservasModal.show();
                } catch (e) {
                    reservasList.innerHTML = `<li>Error al cargar las reservas: ${response}</li>`;
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