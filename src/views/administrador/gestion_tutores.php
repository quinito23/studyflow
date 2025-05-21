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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Tutores Legales</title>
    <link rel="stylesheet" href="../../../public/css/styles.css">


</head>

<body>
    <header class="header">
        <!--Ponemos navbar-dark para que se haga contraste entre el boton de hamburguesa y el fondo del header-->
        <div class="d-flex align-items-center navbar-dark">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar"
                aria-label="Barra Lateral">
                <span class="navbar-toggler-icon"></span>
            </button>
            <img src="../../../public/imagenes/StudyFlow3.svg" alt="logotipo" loading="lazy">
        </div>
        <div class="breadcrumb-container">
            <nav style="--bs-breadcrumb-divider : '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tutores</li>
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
                <li class="nav-item"><a class="nav-link active" href="gestion_tutores.php">Tutores</a></li>
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

    <!--Contenido principal-->
    <main class="main-content">

        <h2 class="text-center" id="form-title">Nuevo Tutor</h2>
        <!--Formulario para crear o editar un tutor -->
        <form id="tutor-form" class="row g-3">
            <div class="col-md-6">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" class="form-control" id="nombre" required>
            </div>
            <div class="col-md-6">
                <label for="apellidos" class="form-label">Apellidos:</label>
                <input type="text" class="form-control" id="apellidos" required>
            </div>
            <div class="col-md-6">
                <label for="telefono" class="form-label">Teléfono:</label>
                <input type="text" class="form-control" id="telefono" required>
            </div>
            <input type="hidden" id="id_tutor">
            <div class="d-grid gap-2 d-md-block">
                <button class="btn btn-primary" type="submit" aria-label="Crear">Crear</button>
                <button type="reset" class="btn btn-light" aria-label="Limpiar Formulario">Limpiar</button>
            </div>

        </form>
        <div id="success-message" class="success-message"></div>
        <div id="error-message" class="error-message"></div>

        <h2 class="text-center">Tutores</h2>

        <!--Creamos la tabla para mostrar a los tutores-->

        <div class="table-responsive">
            <table class="table" id="tabla-tutores">
                <thead>
                    <tr class="table-primary">
                        <th scope="col">Nombre</th>
                        <th scope="col">Apellidos</th>
                        <th scope="col">Telefono</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tutores-lista">
                    <!--Aquí serán listados los tutores-->
                </tbody>
            </table>
        </div>


        <div id="error-message"></div>

        <!--Creamos un modal emergente de botstrpas para mostrar la informacion de un tutor al pulsar el botón de info-->

        <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="infoModalLabel">Información del tutor</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Nombre:</strong><span id="modal-nombre"></span></p>
                        <p><strong>Apellidos:</strong><span id="modal-apellidos"></span></p>
                        <p><strong>Teléfono:</strong><span id="modal-telefono"></span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            aria-label="Cerrar">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!--Footer-->
    <footer class="footer">
        <p>&copy; 2025 StudyFlow - Todos los derechos reservados</p>
    </footer>

    <script>
        //creamos la funcio para enviar la solicitud ajax a la API con los datos del formulario
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

        //funcion para mostrar mensajes de exito y error
        function mostrarMensaje(tipo, mensaje) {
            const elemento = document.getElementById(`${tipo}-message`);
            elemento.textContent = mensaje;
            elemento.style.display = 'block';
            setTimeout(() => {
                elemento.style.display = 'none';
            }, 5000);
        }

        //creamos la funcion para crear un nuevo tutor
        function crearTutor(event) {
            event.preventDefault();

            const id_tutor = document.getElementById("id_tutor").value;
            const nombre = document.getElementById("nombre").value;
            const apellidos = document.getElementById("apellidos").value;
            const telefono = document.getElementById("telefono").value;

            const tutor = { nombre, apellidos, telefono, id_tutor };

            if (id_tutor) {
                //si existe el id del tutor , significa que estamos actualizando el tutor , por lo que hacemos una solicitud PUT

                hacerSolicitud('../../APIs/tutor_api.php', 'PUT', tutor, function (status, response) {
                    const result = JSON.parse(response);
                    if (status === 200) {
                        document.getElementById("tutor-form").reset();
                        cargarTutores();
                        // resetear el titulo y el boton
                        document.getElementById("id_tutor").value = null;
                        document.getElementById("form-title").innerText = "Nuevo tutor";
                        document.querySelector("button[type='submit']").textContent = "Crear tutor";
                        mostrarMensaje('success', result.message || 'Tutor actualizad exitosamente');
                    } else {
                        mostrarMensaje('error', result.message || 'Error al actualizar el tutor');
                    }
                });
            } else {
                hacerSolicitud('../../APIs/tutor_api.php', 'POST', tutor, function (status, response) {
                    const result = JSON.parse(response);
                    if (status === 200) {
                        // limpiamos el formulario y recargamos la lista de tutores
                        document.getElementById("tutor-form").reset();
                        cargarTutores();
                        mostrarMensaje('success', result.message || 'Tutor creado exitosamente');
                    } else {
                        mostrarMensaje('error', result.message || 'Error al crear el Tutor');
                    }
                });
            }
        }

        //funcion para cragar todos los tutores de la base de datos

        function cargarTutores() {
            hacerSolicitud('../../APIs/tutor_api.php', 'GET', null, function (status, response) {
                const result = JSON.parse(response);
                if (status === 200) {
                    const tutores = JSON.parse(response);
                    const listaTutores = document.getElementById("tutores-lista");
                    listaTutores.innerHTML = '';
                    tutores.forEach(tutor => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${tutor.nombre}</td>
                            <td>${tutor.apellidos}</td>
                            <td>${tutor.telefono}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-info btn-sm" onclick = "mostrarInfo(${tutor.id_tutor})" aria-label="Mostrar Información">
                                        <i class="bi bi-info-circle"></i>
                                    </button>
                                    <button class="btn btn-success btn-sm" onclick = "editarTutor(${tutor.id_tutor})" aria-label="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick = "eliminarTutor(${tutor.id_tutor})" aria-label="Eliminar">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        `;
                        listaTutores.appendChild(row);
                    });
                } else {
                    mostrarMensaje('error', result.message || 'Error al cargar los tutores');
                }
            });
        }

        //funcion para cargar la información del tutor en el modal para mostrarla

        function mostrarInfo(id_tutor) {
            hacerSolicitud(`../../APIs/tutor_api.php?id=${id_tutor}`, 'GET', null, function (status, response) {
                if (status === 200) {
                    const tutor = JSON.parse(response);
                    if (tutor) {
                        document.getElementById("modal-nombre").textContent = tutor.nombre || 'N/A';
                        document.getElementById("modal-apellidos").textContent = tutor.apellidos || 'N/A';
                        document.getElementById("modal-telefono").textContent = tutor.telefono || 'N/A';

                        //mostramos el modal usando Bootstraps
                        const modal = new bootstrap.Modal(document.getElementById("infoModal"));
                        modal.show();

                    }
                } else {
                    mostrarMensaje('error', 'Error al cargar la información del tutor');
                }
            });
        }

        //funcion para eliminar un tutor

        function eliminarTutor(id_tutor) {
            if (confirm('¿Estás seguro de que quieres eliminar este tutor?')) {
                //creamos un objeto json con el id del tutor
                const data = { "id_tutor": id_tutor }

                //hacemos la solicitud
                hacerSolicitud('../../APIs/tutor_api.php', 'DELETE', data, function (status, response) {
                    const result = JSON.parse(response);
                    if (status === 200) {
                        cargarTutores();
                        mostrarMensaje('success', 'Tutor eliminado exitosamente');
                    } else {
                        mostrarMensaje('error', 'Error al eliminar el tutor');
                    }
                });
            }
        }

        //funcion para editar un tutor

        function editarTutor(id_tutor) {
            // primero tenemos que obtener los datos del tutor a editar para cargar sus datos en el formulario
            hacerSolicitud(`../../APIs/tutor_api.php?id=${id_tutor}`, 'GET', null, function (status, response) {
                if (status === 200) {
                    const tutor = JSON.parse(response);
                    if (tutor) {
                        document.getElementById("nombre").value = tutor.nombre;
                        document.getElementById("apellidos").value = tutor.apellidos;
                        document.getElementById("telefono").value = tutor.telefono;
                        document.getElementById("id_tutor").value = tutor.id_tutor;
                        document.getElementById("form-title").innerText = "Actualizar tutor";
                        document.querySelector("button[type='submit']").textContent = "Actualizar"
                    }
                } else {
                    mostrarMensaje('error', 'Error al cargar los datos del tutor');
                }
            });
        }

        //inicializar 

        document.getElementById("tutor-form").addEventListener("submit", crearTutor);
        //evento para resetear el formulario
        document.getElementById("tutor-form").addEventListener("reset", function () {
            document.getElementById("id_tutor").value = '';
            document.getElementById("form-title").innerText = "Nuevo Tutor";
            document.querySelector("button[type='submit']").textContent = "Crear";
            cargarTutores();
            limpiarErrores();
        });
        window.onload = cargarTutores;

    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>