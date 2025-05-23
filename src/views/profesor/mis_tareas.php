<?php
// esta es la página que verá el profesor para manejar las tareas

session_start();//inicio de sesión para acceder a los datos guardados del usuario que ha iniciado sesión

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
    <title>Mis Tareas</title>
    <link rel="stylesheet" href="../../../public/css/styles.css">
</head>

<body>
    <!--Header de bootsraps-->
    <header class="header">
        <!--Ponemos navbar-dark para que se haga contraste entre el boton de hamburguesa y el fondo del header-->
        <div class="d-flex align-items-center navbar-dark">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar"
                aria-label="Toggle Navigation">
                <span class="Barra Lateral"></span>
            </button>
            <img src="../../../public/imagenes/StudyFlow3.svg" alt="logotipo" loading="lazy">
        </div>
        <!--Elementos del breadcrumb-->
        <div class="breadcrumb-container">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard_profesores.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mis Tareas</li>
                </ol>
            </nav>
            <span class="separador">|</span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm logout-btn" title="Cerrar Sesión">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </header>

    <!--Barra lateral desplegable con offcanvas de bootstraps-->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasSidebarLabel">StudyFlow</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="close"></button>
        </div>
        <div class="offcanvas-body">
            <!--Lista con las diferentes páginas a las que puede acceder desde la barra lateral-->
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

    <!--Contenido principal-->
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
                <select id="asignatura" class="form-select">
                    <!--Aquí serán listadas las asignaturas disponibles para elegir-->
                </select>
                <span id="asignatura-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="grupo" class="form-label">Grupo:</label>
                <select id="grupo" class="form-select">
                    <!--Aquí serán listados los grupos disponibles para elegir-->
                </select>
                <span id="grupo-error" class="error-text"></span>
            </div>
            <input type="hidden" id="id_tarea">
            <div class="d-grid gap-2 d-md-block">
                <button class="btn btn-primary" type="submit" aria-label="Crear">Crear</button>
                <button type="reset" class="btn btn-light" aria-label="Limpiar Formulario">Limpiar</button>
            </div>
        </form>

        <h2 class="text-center">Lista de Tareas</h2>
        <div class="table-responsive">
            <!--Tabla para mostrar las tareas-->
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
                <tbody id="tareas-lista">
                    <!--Aquí serán listadas las tareas-->
                </tbody>
            </table>

            <!--Contenedor para los mensajes de error-->
            <div id="error-message"></div>
        </div>
    </main>
    <!--Footer-->
    <footer class="footer">
        <p>© 2025 StudyFlow - Todos los derechos reservados</p>
    </footer>

    <script src="../../../public/js/validacion.js"></script><!--Incluimos el script de validación-->
    <script>
        //obtenemos los elemenos con los que vamos a trabajar , de los que vamos a obtener datos y en los que vamos a cargar datos
        const tareasForm = document.getElementById('tareas-form');
        const tareasLista = document.getElementById('tareas-lista');
        const errorMessage = document.getElementById('error-message');
        const formTitle = document.getElementById('form-title');

        //funcion para mostrar errores
        function mostrarError(mensaje) {
            errorMessage.textContent = mensaje;
            errorMessage.style.display = 'block';
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 3000);
        }

        //funcion para limpiar los errores que de la validación, para evitar confusiones
        function limpiarErrores() {
            ['descripcion-error', 'fecha-entrega-error', 'asignatura-error', 'grupo-error', 'error-message'].forEach(id => {
                const elemento = document.getElementById(id);
                elemento.textContent = '';
                elemento.style.display = 'none';
            });
        }

        //función para realizar una solicitud AJAX
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

        //funcion para cargar las asignaturas que imparte el usuario que se pasa por parámetro en la url de la solicitud, que es el id_usuario que se obtiene de la sesión
        function cargarAsignaturas() {
            const id_usuario = <?php echo json_encode($_SESSION['id_usuario']); ?>;
            hacerSolicitud(`../../APIs/asignatura_api.php?id_usuario=${id_usuario}`, 'GET', null, function (status, response) {
                try {
                    const asignaturas = JSON.parse(response);
                    const asignaturaSelect = document.getElementById('asignatura');
                    asignaturaSelect.innerHTML = '<option value="">Seleccione una asignatura</option>';


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

        //función para cargar los grupos en el select, opcionalmente filtrados por la asignatura que tiene asociada
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

        //función para cargar las tareas y mostrarlas en la tabla
        function cargarTareas() {
            hacerSolicitud('../../APIs/tarea_api.php', 'GET', null, function (status, response) {
                try {
                    const tareas = JSON.parse(response);
                    tareasLista.innerHTML = '';


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
                    } else {
                        mostrarError("Respuesta inesperada del servidor");
                    }
                } catch (e) {
                    mostrarError("Error al cargar las tareas: " + e.message);
                }
            });
        }

        //función para editar una tarea
        function editarTarea(id_tarea) {
            hacerSolicitud(`../../APIs/tarea_api.php?id=${id_tarea}`, 'GET', null, function (status, response) {
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
        //función para eliminar una tarea
        function eliminarTarea(id_tarea) {
            if (!confirm('¿Estás seguro de eliminar esta tarea?')) return;
            hacerSolicitud('../../APIs/tarea_api.php', 'DELETE', { id_tarea: id_tarea }, function (status, response) {
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

        //función para crear una tarea
        function crearTarea(event) {
            event.preventDefault();

            const id_tarea = document.getElementById("id_tarea").value;
            const descripcion = document.getElementById("descripcion").value;
            const fecha_entrega = document.getElementById("fecha_entrega").value;
            const id_asignatura = parseInt(document.getElementById("asignatura").value);
            const id_grupo = parseInt(document.getElementById("grupo").value);
            //guardamos los datos introducidos en el formulario
            const tarea = { id_tarea, descripcion, fecha_entrega, id_asignatura, id_grupo };
            //si se asigna un valor al id_tarea , hacemos una solicitud para actualizar la tarea
            if (id_tarea) {
                hacerSolicitud('../../APIs/tarea_api.php', 'PUT', tarea, function (status, response) {
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
                //Si no , creamos la nueva tarea
                hacerSolicitud('../../APIs/tarea_api.php', 'POST', tarea, function (status, response) {
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

        // comenzamos la validación de los datos introducidos en el formulario antes de hacer la solicitud de crear
        tareasForm.addEventListener('submit', async function (event) {
            event.preventDefault();
            //recogemos los datos del formulario
            const datos = {
                descripcion: document.getElementById('descripcion').value,
                fecha_entrega: document.getElementById('fecha_entrega').value,
                id_asignatura: document.getElementById('asignatura').value,
                id_grupo: document.getElementById('grupo').value,
                id_tarea: document.getElementById('id_tarea').value
            };
            //definimos las reglas de validación para cada campo, usndo las funciones definidas en validacion.js
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
                //si la validación es corrrecta , se crea la tarea
                crearTarea(event, datos);
            } else {
                //sino se muestran errores en los campos
                Object.keys(validation.errors).forEach(field => {
                    if (reglas[field] && reglas[field].errorId && validation.errors[field]) {
                        document.getElementById(reglas[field].errorId).textContent = validation.errors[field];
                        document.getElementById(reglas[field].errorId).style.display = 'block';
                    }
                });
            }
        });

        //evento para resetear el formulario y los campos al pulsar el boton de limpiar
        tareasForm.addEventListener('reset', function () {
            formTitle.textContent = 'Nueva Tarea';
            document.getElementById('id_tarea').value = '';
            document.querySelector('#tareas-form button[type="submit"]').textContent = 'Crear';
            cargarGrupos();
            limpiarErrores();
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