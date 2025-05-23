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
    <link rel="stylesheet" href="../../../public/css/styles.css">
</head>

<body>
    <!--Header de bootstrap-->
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
                <li class="nav-item"><a class="nav-link active" href="gestion_tareas.php">Tareas</a></li>
                <li class="nav-item"><a class="nav-link" href="gestion_solicitudes.php">Solicitudes</a></li>
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
                <button class="btn btn-primary" type="submit" aria-label="Crear">Crear</button>
                <button type="reset" class="btn btn-light" aria-label="Limpiar Formulario">Limpiar</button>
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

    <script src="../../../public/js/validacion.js"></script><!--Integración del script con las funciones para validar los campos-->
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
                                        <button class="btn btn-success btn-sm" onclick="editarTarea(${tarea.id_tarea})" aria-label="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="eliminarTarea(${tarea.id_tarea})" aria-label="Eliminar">
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