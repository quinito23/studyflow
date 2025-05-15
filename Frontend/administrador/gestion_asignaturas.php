<?php

//iniciamos la sesión para acceder a los datos almacenados del usuario y verificar su rol
session_start();

////verificar autenticacion y rol
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 'administrador') {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignaturas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../styles.css">
</head>

<body>
    <!--Header de bootstraps-->
    <header class="header">
        <div class="d-flex align-items-center navbar-dark">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar"
                aria-label="Barra lateral">
                <span class="navbar-toggler-icon"></span>
            </button>
            <h1 class="mx-auto">StudyFlow</h1>
        </div>
        <!--Elementos del breadcrumb-->
        <div class="breadcrumb-container">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Asignaturas</li>
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
            <!--Lista con las pestañas de la barra lateral-->
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
                    <a class="nav-link active" href="gestion_asignaturas.php">Asignaturas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestion_grupos.php">Grupos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestion_reservas.php">Reservas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestion_tareas.php">Tareas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestion_solicitudes.php">Solicitudes</a>
                </li>
            </ul>
        </div>
    </div>

    <!--Contenido principal-->

    <main class="main-content">

        <h2 class="text-center">Gestión de Asignaturas</h2>
        <!--Contenedor para mostrar notificaciones-->
        <div id="notificacion" class="notificacion"></div>
        <!--formulario-->
        <div id="asignatura-form" class="container">
            <h3 id="form-title">Crear Asignatura</h3>
            <form id="form-asignatura">
                <input type="hidden" id="id_asignatura" name="id_asignatura">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="nivel" class="form-label">Nivel</label>
                    <input type="text" class="form-control" id="nivel" name="nivel" required>
                </div>
                <div class="mb-3">
                    <label for="id_usuario" class="form-label">Profesor</label>
                    <select class="form-select" id="id_usuario" name="id_usuario" required>
                        <option value="">N/A</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" aria-label="Crear">Crear</button>
                <button type="reset" class="btn btn-light" aria-label="Limpiar Formulario">Limpiar</button>
            </form>
        </div>
        <div id="success-message" class="success-message"></div>
        <div id="error-message" class="error-message"></div>

        <h3 class="text-center">Lista de Asignaturas</h3>
        <div class="table-responsive">
            <table class="table" id="tabla-asignaturas">
                <thead>
                    <tr class="table-primary">
                        <th scope="col">Nombre</th>
                        <th scope="col">Descripción</th>
                        <th scope="col">Nivel</th>
                        <th scope="col">Profesor</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody id="lista-asignaturas"></tbody>
            </table>
        </div>

    </main>

    <!--Footer-->
    <footer class="footer">
        <p>&copy; 2025 StudyFlow - Todos los derechos reservados</p>
    </footer>

    <script>

        //función para mostrar los mensajes
        function mostrarMensaje(tipo, mensaje) {
            const elemento = document.getElementById(`${tipo}-message`);
            elemento.textContent = mensaje;
            elemento.style.display = 'block';
            setTimeout(() => {
                elemento.style.display = 'none';
            }, 5000);
        }

        //Función para hacer solicitues AJAX
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

        // función para cargar los profesores en el select
        function cargarProfesores() {
            hacerSolicitud('../../APIs/profesor_api.php', 'GET', null, function (status, response) {
                if (status === 200) {
                    try {
                        const profesores = JSON.parse(response);
                        const selectProfesor = document.getElementById("id_usuario");
                        selectProfesor.innerHTML = '<option value="">N/A</option>';
                        profesores.forEach(profesor => {
                            const option = document.createElement('option');
                            option.value = profesor.id_usuario;
                            option.textContent = `${profesor.nombre} ${profesor.apellidos}`;
                            selectProfesor.appendChild(option);
                        });
                    } catch (e) {
                        mostrarMensaje('error', 'Error al cargar los profesores');
                    }
                } else {
                    mostrarMensaje('error', 'Error al cargar los profesores');
                }
            });
        }

        //función para cargar las asignaturas de la base de datos
        function cargarAsignaturas() {
            hacerSolicitud('../../APIs/asignatura_api.php', 'GET', null, function (status, response) {
                if (status === 200) {
                    try {
                        const asignaturas = JSON.parse(response);
                        const listaAsignaturas = document.getElementById("lista-asignaturas");
                        listaAsignaturas.innerHTML = '';

                        asignaturas.forEach(asignatura => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${asignatura.nombre}</td>
                                <td>${asignatura.descripcion}</td>
                                <td>${asignatura.nivel}</td>
                                <td>${asignatura.profesor || 'N/A'}</td>
                                <td>

                                    <div class="d-grid gap-2 d-md-block">
                                        <button class="btn btn-info btn-sm" onclick = "mostrarInfo(${asignatura.id_asignatura})" aria-label="Mostrar información">
                                            <i class="bi bi-info-circle"></i>
                                        </button>
                                        <button class="btn btn-success btn-sm" onclick = "editarAsignatura(${asignatura.id_asignatura})" aria-label="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick = "eliminarAsignatura(${asignatura.id_asignatura})" aria-label="Eliminar">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                </td>
                                    `;
                            listaAsignaturas.appendChild(row);
                        });

                    } catch (e) {
                        mostrarNotificacion('error', 'Error al cargar las asignaturas : ' + e.message);
                    }
                } else {
                    mostrarNotificacion('error', 'Error al cargar las asignaturas');
                }
            });
        }

        //función para crear una asignatura nueva
        function crearAsignatura(event) {
            event.preventDefault();
            //obtenemos los datos  del formulario
            const id_asignatura = document.getElementById("id_asignatura").value;
            const nombre = document.getElementById("nombre").value;
            const descripcion = document.getElementById("descripcion").value;
            const nivel = document.getElementById("nivel").value;
            const id_usuario = document.getElementById("id_usuario").value || null;

            //guardamos los datos obtenidos del formulario para pasarselos a la solicitud
            const asignatura = { id_asignatura, nombre, descripcion, nivel, id_usuario };

            //si el id_asignatura tiene valor ,entonces la solicitud se le hace a PUT para actualizarla
            if (id_asignatura) {
                hacerSolicitud('../../APIs/asignatura_api.php', 'PUT', asignatura, function (status, response) {
                    const result = JSON.parse(response);
                    if (status === 200) {
                        document.getElementById("form-asignatura").reset();
                        document.getElementById("id_asignatura").value = null;
                        document.getElementById("form-title").innerText = "Editar Asignatura";
                        cargarAsignaturas();
                        mostrarMensaje('success', result.message || 'Asignatura actualizada exitosamente');
                    } else {
                        mostrarMensaje('error', result.message || 'Error al actualizar la asignatura');
                    }
                });
            } else {
                //sino se le hace  a POST para crear la asignatura
                hacerSolicitud('../../APIs/asignatura_api.php', 'POST', asignatura, function (status, response) {
                    const result = JSON.parse(response);
                    if (status === 200) {
                        document.getElementById("form-asignatura").reset();
                        document.getElementById("id_asignatura").value = null;
                        document.getElementById("form-title").innerText = "Crear Asignatura";
                        document.querySelector("button[type='submit']").textContent = "Crear";
                        mostrarMensaje('success', result.message || 'Asignatura creada exitosamente')

                        cargarAsignaturas();
                    } else {
                        mostrarMensaje('error', result.message || 'Error al crear la asignatura');
                    }
                })
            }
        }

        //función para editar una asignatura
        function editarAsignatura(id_asignatura) {
            hacerSolicitud(`../../APIs/asignatura_api.php?id=${id_asignatura}`, 'GET', null, function (status, response) {
                if (status === 200) {
                    const asignatura = JSON.parse(response);
                    if (asignatura) {
                        document.getElementById("nombre").value = asignatura.nombre;
                        document.getElementById("descripcion").value = asignatura.descripcion;
                        document.getElementById("nivel").value = asignatura.nivel;
                        document.getElementById("id_asignatura").value = asignatura.id_asignatura;
                        document.getElementById("id_usuario").value = asignatura.id_usuario || '';
                        document.getElementById("form-title").innerText = "Editar Asignatura";
                        document.querySelector("button[type='submit']").textContent = "Actualizar";

                    } else {
                        mostrarMensaje('error', 'Error al cargar los datos de la asignatura');
                    }
                } else {
                    mostrarMensaje('error', 'error al cargar los datos de la asignatura');
                }
            });
        }
        // funcion para eliminar una asignatura
        function eliminarAsignatura(id_asignatura) {
            if (confirm('¿Estás seguro de que quieres eliminar esta asignatura?')) {
                const data = { "id_asignatura": id_asignatura };
                hacerSolicitud('../../APIs/asignatura_api.php', 'DELETE', data, function (status, response) {
                    const result = JSON.parse(response);
                    if (status === 200) {
                        cargarAsignaturas();
                        mostrarMensaje('success', result.message || 'Asignatura creada exitosamente');
                    } else {
                        mostrarMensaje('error', result.message || 'Error al eliminar la asignatura');
                    }
                });
            }
        }

        //inicializar
        document.getElementById("form-asignatura").addEventListener("submit", crearAsignatura);
        //evento para resetear el formulario
        document.getElementById("asignatura-form").addEventListener("reset", function () {
            document.getElementById("id_asignatura").value = '';
            document.getElementById("form-title").innerText = "Crear Asignatura";
            document.querySelector("button[type='submit']").textContent = "Crear";
            cargarAsignaturas();
            cargarProfesores();
        });
        window.onload = function () {
            cargarAsignaturas();
            cargarProfesores();
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>