<?php
// es la página para crear, leer, editar y eliminar tareas
session_start();
//verificar el rol del usuario que ha iniciado sesión para permitir el acceso al archivo o no
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 'administrador') {
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
    <title>Tareas</title>
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
            padding: clamp(0.7rem, 2vw, 1.2rem) clamp(1.2rem, 2.8vw, 2rem);
            color: white;
            border-bottom: 1px solid #ffffff33;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
        }

        .header .d-flex {
            gap: clamp(0.5rem, 2vw, 1rem);
            align-items: center;
        }

        .header h1 {
            margin: 0;
            font-size: clamp(1.2rem, 4vw, 2rem);
            margin-left: clamp(0.5rem, 2vw, 1rem);
        }

        .navbar-toggler {
            font-size: clamp(0.8rem, 2.5vw, 1.2rem);
            margin-right: clamp(0.5rem, 2vw, 1rem);
            z-index: 1000;
        }

        .navbar-toggler-icon {
            color: white;
        }

        .breadcrumb-container {
            display: flex;
            align-items: center;
            gap: clamp(0.5rem, 1.5vw, 0.8rem);
        }

        .breadcrumb {
            background-color: transparent;
            padding: 0.5rem 0;
            margin-bottom: 0;
            font-size: clamp(0.75rem, 2.2vw, 1.1rem);
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

        .separator {
            color: #f8f9fa;
            font-size: clamp(0.85rem, 2.2vw, 1.2rem);
            margin: 0 clamp(0.5rem, 1.5vw, 0.8rem);
        }

        .logout-btn {
            font-size: clamp(1rem, 2.5vw, 1.8rem);
            color: #f8f9fa;
            border: none;
            background: transparent;
            padding: 0;
            line-height: 1;
        }

        .logout-btn:hover {
            color: #007bff;
            transform: scale(1.1);
            transition: color 0.2s, transform 0.2s;
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

        .btn-sm {
            font-size: clamp(0.8rem, 1.8vw, 1rem);
            padding: clamp(0.25rem, 0.8vw, 0.4rem) clamp(0.5rem, 1.2vw, 0.7rem);
            line-height: 1.5;
            min-height: 1.8rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: clamp(0.5rem, 1.2vw, 0.8rem);
            justify-content: center;
            align-items: center;
        }

        .error-message {
            background-color: #f8d7da;
            color: #dc3545;
            padding: 0.75rem;
            border-radius: 5px;
            margin-top: 1rem;
            display: none;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 0.75rem;
            border-radius: 5px;
            margin-top: 1rem;
            display: none;
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
    <!--Header de bootstrap-->
    <header class="header">
        <div class="d-flex align-items-center navbar-dark">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar"
                aria-label="Toggle Navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <h1 class="mx-auto">StudyFlow</h1>
        </div>
        <!--Elementos del breadcrumb-->
        <div class="breadcrumb-container">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboardAdmin.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tareas</li>
                </ol>
            </nav>
            <span class="separador">|</span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm logout-btn" title="Cerrar Sesión">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </header>

    <!--Barra lateral desplegable-->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasSidebarLabel">StudyFlow</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="close"></button>
        </div>
        <div class="offcanvas-body">
            <!--Lista de las diferentes pestañas de la barra lateral (las diferetesd páginas a las que se puede acceder)-->
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link " href="gestion_profesores.php">Profesores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestion_alumnos.php">Alumnos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestion_tutores.php">Tutores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestion_aulas.php">Aulas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestion_asignaturas.php">Asignaturas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestion_grupos.php">Grupos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestion_reservas.php">Reservas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="gestion_tareas.php">Tareas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestion_solicitudes.php">Solicitudes</a>
                </li>
            </ul>
        </div>
    </div>

    <!--Contenido principal-->
    <main class="main-content">
        <h2 class="text-center" id="form-title">Tareas</h2>
        <!--Formulario para crear la tarea-->
        <form id="tareas-form" class="row g-3">
            <div class="col-md-6">
                <label for="descripcion" class="form-label">Descripción:</label>
                <textarea class="form-control" id="descripcion" required></textarea>
                <div class="error-message" id="descripcion-error"></div>
            </div>
            <div class="col-md-6">
                <label for="fecha_entrega" class="form-label">Fecha de Entrega:</label>
                <input type="datetime-local" class="form-control" id="fecha_entrega" required>
                <div class="error-message" id="fecha-entrega-error"></div>
            </div>
            <div class="col-md-6">
                <label for="asignatura" class="form-label">Asignatura:</label>
                <select id="asignatura" class="form-select"></select>
                <div class="error-message" id="asignatura-error"></div>
            </div>
            <div class="col-md-6">
                <label for="grupo" class="form-label">Grupo:</label>
                <select id="grupo" class="form-select"></select>
                <div class="error-message" id="grupo-error"></div>
            </div>
            <input type="hidden" id="id_tarea">
            <div class="d-grid gap-2 d-md-block">
                <button class="btn btn-primary" type="submit">Crear</button>
                <button type="reset" class="btn btn-light">Limpiar</button>
            </div>
        </form>
        <div id="success-message" class="success-message"></div>
        <div id="error-message" class="success-message"></div>

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
    <!--Footer-->
    <footer class="footer">
        <p>© 2025 StudyFlow - Todos los derechos reservados</p>
    </footer>

    <script src="../validacion.js"></script><!--Integración del script con las funciones para validar los campos-->
    <script>
        //obtenemos los diferentes elementos del fontend que vamos a manejar
        const tareasForm = document.getElementById('tareas-form');
        const tareasLista = document.getElementById('tareas-lista');
        const formTitle = document.getElementById('form-title');

        //funcion para mostrar errores
        function mostrarMensaje(tipo, mensaje) {
            const elemento = document.getElementById(`${tipo}-message`);
            elemento.textContent = mensaje;
            elemento.style.display = 'block';
            setTimeout(() => {
                elemento.style.display = 'none';
            }, 5000);
        }

        //función para  hacer solicitudes AJAX
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

        //función para cargar las asignaturas en el select
        function cargarAsignaturas() {
            hacerSolicitud('../../APIs/asignatura_api.php', 'GET', null, function (status, response) {
                try {
                    const asignaturas = JSON.parse(response);
                    const asignaturaSelect = document.getElementById('asignatura');
                    asignaturaSelect.innerHTML = '<option value="">Seleccione una asignatura</option>';
                    asignaturas.forEach(asignatura => {
                        const option = document.createElement('option');
                        option.value = asignatura.id_asignatura;
                        option.textContent = asignatura.nombre;
                        asignaturaSelect.appendChild(option);
                    });
                } catch (e) {
                    mostrarError("Error al cargar las asignaturas: " + e.message);
                }
            });
        }
        //función para cargar los grupos en el select
        function cargarGrupos() {
            const id_asignatura = document.getElementById('asignatura').value;
            let url = '../../APIs/grupo_api.php';
            if (id_asignatura) {
                url += `?id_asignatura=${id_asignatura}`;
            }

            hacerSolicitud(url, 'GET', null, function (status, response) {
                try {
                    const grupos = JSON.parse(response);
                    const grupoSelect = document.getElementById('grupo');
                    grupoSelect.innerHTML = '<option value="">Seleccione un grupo</option>';
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
                } catch (e) {
                    mostrarMensaje('error', 'Error al cargar los grupos:' + e.message);
                }
            });
        }

        //función para cargar las todas las tareas (se le pasa el parametro todas a la api mediante la url)
        function cargarTareas() {
            hacerSolicitud('../../APIs/tarea_api.php?todas=1', 'GET', null, function (status, response) {
                try {
                    const tareas = JSON.parse(response);
                    tareasLista.innerHTML = '';
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
                                    <div class="action-buttons">
                                        <button class="btn btn-warning btn-sm me-1" onclick="editarTarea(${tarea.id_tarea})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="eliminarTarea(${tarea.id_tarea})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                        tareasLista.innerHTML += row;
                    });
                } catch (e) {
                    mostrarMensaje('error', 'Error al cargar las tareas: ' + e.message);
                }
            });
        }

        //función para editar una tarea
        function editarTarea(id_tarea) {
            hacerSolicitud(`../../APIs/tarea_api.php?id=${id_tarea}`, 'GET', null, function (status, response) {
                try {
                    const tarea = JSON.parse(response);
                    document.getElementById('id_tarea').value = tarea.id_tarea;
                    document.getElementById('descripcion').value = tarea.descripcion;
                    document.getElementById('fecha_entrega').value = tarea.fecha_entrega.replace(' ', 'T');
                    document.getElementById('asignatura').value = tarea.id_asignatura;
                    document.getElementById('grupo').value = tarea.id_grupo;
                    formTitle.textContent = 'Editar Tarea';
                    document.querySelector('#tareas-form button[type="submit"]').textContent = 'Actualizar';
                    cargarGrupos();
                } catch (e) {
                    mostrarMensaje('error', "Error al cargar la tarea para editar: " + e.message);
                }
            });
        }

        //funcion para eliminar una tarea
        function eliminarTarea(id_tarea) {
            if (!confirm('¿Estás seguro de eliminar esta tarea?')) return;
            hacerSolicitud('../../APIs/tarea_api.php', 'DELETE', { id_tarea: id_tarea }, function (status, response) {
                try {
                    const result = JSON.parse(response);
                    if (status === 200 && result.message === "Tarea eliminada exitosamente") {
                        cargarTareas();
                        mostrarMensaje('success', result.message || 'Tarea eliminada exitosamente');
                    } else {
                        mostrarMensaje('error', result.message || 'Error al eliminar la tarea');
                    }
                } catch (e) {
                    mostrarMensaje('error', 'Error al eliminar la tarea: ' + e.message)
                }
            });
        }

        //funcio para crear una tarea
        function crearTarea(event) {
            event.preventDefault();

            const id_tarea = document.getElementById("id_tarea").value;
            const descripcion = document.getElementById("descripcion").value;
            const fecha_entrega = document.getElementById("fecha_entrega").value;
            const id_asignatura = parseInt(document.getElementById("asignatura").value);
            const id_grupo = parseInt(document.getElementById("grupo").value);
            //almacenamos los datos del formulario para pasarselos a la API en la solicitud
            const tarea = { id_tarea, descripcion, fecha_entrega, id_asignatura, id_grupo };

            //si el parametro id_tarea está asignado ,entonces se hace la solicitud al metodo PUT para actualizar
            if (id_tarea) {
                hacerSolicitud('../../APIs/tarea_api.php', 'PUT', tarea, function (status, response) {
                    try {
                        const result = JSON.parse(response);
                        if (status === 200 && result.message === "Tarea actualizada exitosamente") {
                            document.getElementById("tareas-form").reset();
                            document.getElementById("id_tarea").value = null;
                            formTitle.textContent = "Tareas";
                            document.querySelector('#tareas-form button[type="submit"]').textContent = 'Crear';
                            cargarTareas();
                            mostrarMensaje('success', result.message || 'Tarea actualizada exitosamente');
                        } else {
                            mostrarMensaje('error', result.message || 'Error al actualizar la tarea');
                        }
                    } catch (e) {
                        mostrarMensaje('error', result.message || 'Error al actualizar la tarea: ' + e.message);
                    }
                });
            } else {
                //Si no se hace al metodo POST para crearla
                hacerSolicitud('../../APIs/tarea_api.php', 'POST', tarea, function (status, response) {
                    try {
                        const result = JSON.parse(response);
                        if (status === 200 && result.message === "Tarea creada exitosamente") {
                            document.getElementById("tareas-form").reset();
                            cargarTareas();
                            mostrarMensaje('success', result.message || 'Tarea creada exitosamente');
                        } else {
                            mostrarMensaje('error', result.message || 'Error al crear la tarea');
                        }
                    } catch (e) {
                        mostrarMensaje('error', 'Error al crear la tarea: ' + e.message);
                    }
                });
            }
        }
        // Validación y envío del formulario
        tareasForm.addEventListener('submit', async function (event) {
            event.preventDefault();
            //obtener los datos del formulario
            const datos = {
                descripcion: document.getElementById("descripcion").value,
                fecha_entrega: document.getElementById("fecha_entrega").value,
                id_asignatura: document.getElementById("asignatura").value,
                id_grupo: document.getElementById("grupo").value
            };
            //definimos las reglas de validación para cada campo, usndo las funciones definidas en validacion.js
            const reglas = {
                descripcion: {
                    validar: (valor) => validarTexto(valor, 2),
                    errorId: "descripcion-error"
                },
                fecha_entrega: {
                    validar: validarFechaEntrega,
                    errorId: "fecha-entrega-error"
                },
                id_asignatura: {
                    validar: (valor) => valor ? "" : "Seleccione una asignatura",
                    errorId: "asignatura-error"
                },
                id_grupo: {
                    validar: (valor) => valor ? "" : "Seleccione un grupo",
                    errorId: "grupo-error"
                }
            };

            const validation = await validarCampos(datos, reglas);
            if (validation.isValid) {
                // si todo está correcto, entonces se ejecuta la función que hace la solicitud para crear
                crearTarea(event);
            } else {
                //sino mostramos los errores de validación en los campos que han fallado
                Object.keys(validation.errors).forEach(field => {
                    document.getElementById(reglas[field].errorId).textContent = validation.errors[field];
                });
            }
        });

        document.getElementById('asignatura').addEventListener('change', cargarGrupos);

        //evento para resetear el formulario y los campos al pulsar el boton de limpiar
        tareasForm.addEventListener('reset', function () {
            formTitle.textContent = 'Tareas';
            document.querySelector('#tareas-form button[type="submit"]').textContent = 'Crear';
            document.getElementById('id_tarea').value = '';
            cargarGrupos();
            // Limpiar mensajes de error
            ['descripcion-error', 'fecha-entrega-error', 'asignatura-error', 'grupo-error'].forEach(id => {
                document.getElementById(id).textContent = '';
            });
        });

        // al iniciar la página , se cargan las asignaturas, grupos y tareas existentes
        window.onload = function () {
            cargarAsignaturas();
            cargarGrupos();
            cargarTareas();
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>