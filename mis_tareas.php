<?php
session_start();

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
    <title>Mis Tareas</title>
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

        #tareas-form {
            border: 2px solid #007bff;
            border-radius: 8px;
            padding: 1.5rem;
            background-color: #0d1f38;
            margin-bottom: 2rem;
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

        .btn-primary,
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

        .error-text {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
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
                <li class="breadcrumb-item"><a href="dashboardProfesor.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Mis Tareas</li>
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
                    <a class="nav-link" href="vista_reservas.php">Reservar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="mis_reservas.php">Mis Reservas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="mis_tareas.php">Mis Tareas</a>
                </li>
            </ul>
        </div>
    </div>

    <main class="main-content">
        <h2 class="text-center" id="form-title">Mis Tareas</h2>
        <form id="tareas-form" class="row g-3">
            <div class="col-md-6">
                <label for="descripcion" class="form-label">Descripción:</label>
                <textarea class="form-control" id="descripcion" required></textarea>
                <span id="descripcion-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="fecha_entrega" class="form-label">Fecha de Entrega:</label>
                <input type="datetime-local" class="form-control" id="fecha_entrega" required>
                <span id="fecha-entrega-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="asignatura" class="form-label">Asignatura:</label>
                <select id="asignatura" class="form-select"></select>
                <span id="asignatura-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="grupo" class="form-label">Grupo:</label>
                <select id="grupo" class="form-select"></select>
                <span id="grupo-error" class="error-text"></span>
            </div>
            <input type="hidden" id="id_tarea">
            <div class="d-grid gap-2 d-md-block">
                <button class="btn btn-primary" type="submit">Crear</button>
                <button type="reset" class="btn btn-light">Limpiar</button>
            </div>
        </form>

        <h2 class="text-center">Lista de Tareas</h2>
        <div class="table-responsive">
            <table class="table" id="tabla-tareas">
                <thead>
                    <tr class="table-primary">
                        <th scope="col">Descripción</th>
                        <th scope="col">Fecha Creación</th>
                        <th scope="col">Fecha Entrega</th>
                        <th scope="col">Asignatura</th>
                        <th scope="col">Grupo</th>
                        <th scope="col">Profesor</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tareas-lista"></tbody>
            </table>
        </div>
        <div id="error-message"></div>
    </main>

    <footer class="footer">
        <p>© 2025 StudyFlow - Todos los derechos reservados</p>
    </footer>
    
    <script src="validacion.js"></script>
    <script>
        const tareasForm = document.getElementById('tareas-form');
        const tareasLista = document.getElementById('tareas-lista');
        const errorMessage = document.getElementById('error-message');
        const formTitle = document.getElementById('form-title');

        function mostrarError(mensaje) {
            errorMessage.textContent = mensaje;
            errorMessage.style.display = 'block';
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 3000);
        }

        function limpiarErrores() {
            ['descripcion-error', 'fecha-entrega-error', 'asignatura-error', 'grupo-error', 'error-message'].forEach(id => {
                const elemento = document.getElementById(id);
                elemento.textContent = '';
                elemento.style.display = 'none';
            });
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

        function cargarAsignaturas() {
            const id_usuario = <?php echo json_encode($_SESSION['id_usuario']); ?>;
            hacerSolicitud(`asignatura_api.php?id_usuario=${id_usuario}`, 'GET', null, function (status, response) {
                try {
                    const asignaturas = JSON.parse(response);
                    const asignaturaSelect = document.getElementById('asignatura');
                    asignaturaSelect.innerHTML = '<option value="">Seleccione una asignatura</option>';

                    // Check if response is an error object
                    if (asignaturas.message) {
                        mostrarError(asignaturas.message);
                        return;
                    }

                    // Handle empty array or valid array
                    if (Array.isArray(asignaturas)) {
                        if (asignaturas.length === 0) {
                            mostrarError("No tienes asignaturas asignadas");
                        } else {
                            asignaturas.forEach(asignatura => {
                                const option = document.createElement('option');
                                option.value = asignatura.id_asignatura;
                                option.textContent = asignatura.nombre;
                                asignaturaSelect.appendChild(option);
                            });
                        }
                    } else {
                        mostrarError("Respuesta inesperada del servidor");
                    }
                } catch (e) {
                    mostrarError("Error al cargar las asignaturas: " + e.message);
                }
            });
        }

        function cargarGrupos() {
            const id_asignatura = document.getElementById('asignatura').value;
            let url = 'grupo_api.php';
            if (id_asignatura) {
                url += `?id_asignatura=${id_asignatura}`;
            }

            hacerSolicitud(url, 'GET', null, function (status, response) {
                try {
                    const grupos = JSON.parse(response);
                    const grupoSelect = document.getElementById('grupo');
                    grupoSelect.innerHTML = '<option value="">Seleccione un grupo</option>';

                    // Check if response is an error object
                    if (grupos.message) {
                        mostrarError(grupos.message);
                        return;
                    }

                    // Handle empty array or valid array
                    if (Array.isArray(grupos)) {
                        if (grupos.length > 0) {
                            grupos.forEach(grupo => {
                                const option = document.createElement('option');
                                option.value = grupo.id_grupo;
                                option.textContent = grupo.nombre;
                                grupoSelect.appendChild(option);
                            });
                        } else {
                            const option = document.createElement('option');
                            option.value = '';
                            option.textContent = 'No hay grupos disponibles';
                            option.disabled = true;
                            grupoSelect.appendChild(option);
                        }
                    } else {
                        mostrarError("Respuesta inesperada del servidor");
                    }
                } catch (e) {
                    mostrarError("Error al cargar los grupos: " + e.message);
                }
            });
        }

        function cargarTareas() {
            hacerSolicitud('tarea_api.php', 'GET', null, function (status, response) {
                try {
                    const tareas = JSON.parse(response);
                    tareasLista.innerHTML = '';

                    // Check if response is an error object
                    if (tareas.message) {
                        mostrarError(tareas.message);
                        return;
                    }

                    // Handle empty array or valid array
                    if (Array.isArray(tareas)) {
                        tareas.forEach(tarea => {
                            const row = `
                                <tr>
                                    <td>${tarea.descripcion}</td>
                                    <td>${tarea.fecha_creacion}</td>
                                    <td>${tarea.fecha_entrega}</td>
                                    <td>${tarea.asignatura}</td>
                                    <td>${tarea.grupo}</td>
                                    <td>${tarea.profesor}</td>
                                    <td>${tarea.estado}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm me-1" onclick="editarTarea(${tarea.id_tarea})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="eliminarTarea(${tarea.id_tarea})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                            tareasLista.innerHTML += row;
                        });
                    } else {
                        mostrarError("Respuesta inesperada del servidor");
                    }
                } catch (e) {
                    mostrarError("Error al cargar las tareas: " + e.message);
                }
            });
        }

        function editarTarea(id_tarea) {
            hacerSolicitud(`tarea_api.php?id=${id_tarea}`, 'GET', null, function (status, response) {
                try {
                    const tarea = JSON.parse(response);
                    if (tarea.message) {
                        mostrarError(tarea.message);
                        return;
                    }
                    document.getElementById('id_tarea').value = tarea.id_tarea;
                    document.getElementById('descripcion').value = tarea.descripcion;
                    document.getElementById('fecha_entrega').value = tarea.fecha_entrega.replace(' ', 'T');
                    document.getElementById('asignatura').value = tarea.id_asignatura;
                    document.getElementById('grupo').value = tarea.id_grupo;
                    formTitle.textContent = 'Editar Tarea';
                    document.querySelector('#tareas-form button[type="submit"]').textContent = 'Actualizar';
                    cargarGrupos();
                } catch (e) {
                    mostrarError("Error al cargar la tarea para editar: " + e.message);
                }
            });
        }

        function eliminarTarea(id_tarea) {
            if (!confirm('¿Estás seguro de eliminar esta tarea?')) return;
            hacerSolicitud('tarea_api.php', 'DELETE', { id_tarea: id_tarea }, function (status, response) {
                try {
                    const result = JSON.parse(response);
                    if (status === 200 && result.message === "Tarea eliminada exitosamente") {
                        cargarTareas();
                    } else {
                        mostrarError(result.message || "Error al eliminar la tarea");
                    }
                } catch (e) {
                    mostrarError("Error al eliminar la tarea: " + e.message);
                }
            });
        }

        function crearTarea(event) {
            event.preventDefault();

            const id_tarea = document.getElementById("id_tarea").value;
            const descripcion = document.getElementById("descripcion").value;
            const fecha_entrega = document.getElementById("fecha_entrega").value;
            const id_asignatura = parseInt(document.getElementById("asignatura").value);
            const id_grupo = parseInt(document.getElementById("grupo").value);

            const tarea = { id_tarea, descripcion, fecha_entrega, id_asignatura, id_grupo };

            if (id_tarea) {
                hacerSolicitud('tarea_api.php', 'PUT', tarea, function (status, response) {
                    try {
                        const result = JSON.parse(response);
                        if (status === 200 && result.message === "Tarea actualizada exitosamente") {
                            document.getElementById("tareas-form").reset();
                            document.getElementById("id_tarea").value = null;
                            formTitle.textContent = "Mis Tareas";
                            document.querySelector('#tareas-form button[type="submit"]').textContent = 'Crear';
                            cargarTareas();
                        } else {
                            mostrarError(result.message || "Error al actualizar la tarea");
                        }
                    } catch (e) {
                        mostrarError("Error al actualizar la tarea: " + e.message);
                    }
                });
            } else {
                hacerSolicitud('tarea_api.php', 'POST', tarea, function (status, response) {
                    try {
                        const result = JSON.parse(response);
                        if (status === 200 && result.message === "Tarea creada exitosamente") {
                            document.getElementById("tareas-form").reset();
                            cargarTareas();
                        } else {
                            mostrarError(result.message || "Error al crear la tarea");
                        }
                    } catch (e) {
                        mostrarError("Error al crear la tarea: " + e.message);
                    }
                });
            }
        }

        document.getElementById('asignatura').addEventListener('change', cargarGrupos);

        tareasForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            const datos = {
                descripcion: document.getElementById('descripcion').value,
                fecha_entrega: document.getElementById('fecha_entrega').value,
                id_asignatura: document.getElementById('asignatura').value,
                id_grupo: document.getElementById('grupo').value,
                id_tarea: document.getElementById('id_tarea').value
            };

            const reglas = {
                descripcion: { validar: validarTexto, errorId: 'descripcion-error' },
                fecha_entrega: { validar: validarFechaEntrega, errorId: 'fecha-entrega-error' },
                id_asignatura: { validar: validarSeleccion, errorId: 'asignatura-error' },
                id_grupo: { validar: validarSeleccion, errorId: 'grupo-error' }
            };

            limpiarErrores();

            const validation = await validarCampos(datos, reglas);
            let isValid = validation.isValid;

            if (isValid) {
                crearTarea(event, datos);
            } else {
                Object.keys(validation.errors).forEach(field => {
                    if (reglas[field] && reglas[field].errorId && validation.errors[field]) {
                        document.getElementById(reglas[field].errorId).textContent = validation.errors[field];
                        document.getElementById(reglas[field].errorId).style.display = 'block';
                    }
                });
            }
        });

        tareasForm.addEventListener('reset', function () {
            formTitle.textContent = 'Nueva Tarea';
            document.getElementById('id_tarea').value = '';
            document.querySelector('#tareas-form button[type="submit"]').textContent = 'Crear';
            cargarGrupos();
            limpiarErrores();
        });

        window.onload = function () {
            cargarAsignaturas();
            cargarGrupos();
            cargarTareas();
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>