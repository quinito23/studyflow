<?php
session_start();

//verificar autenticacion y rol
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] != 'alumno' || $_SESSION['rol'] != 'administrador')) {
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
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="asignaturas-container">
            <!--Las cartas se crearán dinamicamente aquí-->
        </div>

        <div id="reservas-section">
            <h3>Reservas de la Asignatura</h3>
            <ul id="reservas-list"></ul>
            <button class="btn btn-primary mt-3 onclick=volverAsignaturas()">volverAsignaturas</button>
        </div>

    </main>

    <footer class="footer">
        <p>© 2025 StudyFlow - Todos los derechos reservados</p>
    </footer>

    <script>
        const asignaturasContainer = document.getElementById('asignaturas-container');
        const reservasSection = document.getElementById('reservas-section');
        const reservasList = document.getElementById('reservas-list');

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
                                    <img src="https://image" class="card-img-top" alt="Imagen de Asignatura">
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
            hacerSolicitud(`reserva_api.php?asignatura=${id_asignatura}`, 'GET', null, function (status, response) {
                try {
                    const reservas = JSON.parse(response);
                    asignaturasContainer.style.display = 'none';
                    reservasSection.style.display = 'block';
                    reservasList.innerHTML = '';
                    if (reservas.length > 0) {
                        reservas.forEach(reserva => {
                            const li = document.createElement('li');
                            li.textContent = `${reserva.fecha} - ${reserva.hora_inicio} a ${reserva.hora_fin} | Aula: ${reserva.aula} | Profesor: ${reserva.profesor}`;
                            reservasList.appendChild(li);
                        });
                    } else {
                        reservasList.innerHTML = `<li> No hay reservas para esta asignatura.</li>`;
                    }
                } catch (e) {
                    reservasList.innerHTML = '<li> Error al cargar las reservas</li>';
                }
            });
        }

        function volverAsignaturas() {
            reservasSection.style.display = 'none';
            asignaturasContainer.style.display = 'flex';
            cargarAsignaturas();
        }

        window.onload = function () {
            cargarAsignaturas();
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>