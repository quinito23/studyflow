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
    <title>Aulas</title>
    <link rel="stylesheet" href="../../../public/css/styles.css">
</head>

<body>
    <!--Header de bootsraps-->
    <header class="header">
        <div class="d-flex align-items-center navbar-dark">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar"
                aria-label="Barra Lateral">
                <span class="navbar-toggler-icon"></span>
            </button>
            <img src="../../../public/imagenes/StudyFlow3.svg" alt="logotipo" loading="lazy">
        </div>
        <div class="breadcrumb-container">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <!--Definimos los elementos del breadcrumb-->
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboardProfesor.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Aulas</li>
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
                <li class="nav-item"><a class="nav-link" href="gestion_tutores.php">Tutores</a></li>
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

        <h2 class="text-center" id="form-title">Gestionar Aulas</h2>
        <!--Formulario para crear o editar un aula-->
        <form id="aula-form" class="row g-3">
            <div class="col-md-6">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" class="form-control" id="nombre" required>
                <span id="nombre-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="capacidad" class="form-label">Capacidad:</label>
                <input type="number" class="form-control" id="capacidad" min="1" required>
                <span id="capacidad-error" class="error-text"></span>
            </div>
            <div class="col-md-12">
                <label for="equipamiento" class="form-label">Equipamiento:</label>
                <input type="text" class="form-control" id="equipamiento">
                <span id="equipamiento-error" class="error-text"></span>
            </div>
            <input type="hidden" id="id_aula">
            <div class="d-grid gap-2 d-md-block">
                <button class="btn btn-primary" type="submit" aria-label="Crear">Crear</button>
                <button type="reset" class="btn btn-light" aria-label="Limpiar Formulario">Limpiar</button>
            </div>
        </form>
        <div id="success-message" class="success-message"></div>
        <div id="error-message" class="error-message"></div>

        <h2 class="text-center">Lista de Aulas</h2>
        <div class="table-responsive">
            <table class="table" id="tabla-aulas">
                <thead>
                    <tr class="table-primary">
                        <th scope="col">Nombre</th>
                        <th scope="col">Capacidad</th>
                        <th scope="col">Equipamiento</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody id="aulas-lista">
                    <!--En esta lista se cargarán dinamicamente las aulas-->
                </tbody>
            </table>
        </div>

    </main>
    <!--FOOTER-->
    <footer class="footer">
        <p>© 2025 StudyFlow - Todos los derechos reservados</p>
    </footer>

    <script>
        //obtenemos los elemenos con los que vamos a trabajar , de los que vamos a obtener datos y en los que vamos a cargar datos
        const aulaForm = document.getElementById('aula-form');
        const aulasLista = document.getElementById('aulas-lista');
        const errorMessage = document.getElementById('error-message');
        const successMessage = document.getElementById('success-message');
        const formTitle = document.getElementById('form-title');

        //funcion para mostrar errores
        function mostrarMensaje(tipo, mensaje) {
            const elemento = document.getElementById(`${tipo}-message`);
            elemento.textContent = mensaje;
            elemento.style.display = 'block';
            setTimeout(() => elemento.style.display = 'none', 5000);
        }

        //funcion para limpiar los errores de validacion, para evitar confusiones
        function limpiarErrores() {
            ['nombre-error', 'capacidad-error', 'equipamiento-error', 'error-message'].forEach(id => {
                const elemento = document.getElementById(id);
                elemento.textContent = '';
                elemento.style.display = 'none';
            });
        }

        //función para realizar una solicitud AJAX a la API
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

        //Funcion para cargar las aulas 
        function cargarAulas() {
            hacerSolicitud('../../APIs/aula_api.php', 'GET', null, function (status, response) {
                try {
                    const aulas = JSON.parse(response);
                    aulasLista.innerHTML = '';
                    aulas.forEach(aula => {
                        const row = `
                            <tr>
                                <td>${aula.nombre}</td>
                                <td>${aula.capacidad}</td>
                                <td>${aula.equipamiento || '-'}</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-success btn-sm" onclick="editarAula(${aula.id_aula})" aria-label="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="eliminarAula(${aula.id_aula})" aria-label="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>`;
                        aulasLista.innerHTML += row;
                    });
                } catch (e) {
                    mostrarMensaje('error', "Error al cargar las aulas: " + e.message);
                }
            });
        }

        //función para editar un aula
        function editarAula(id_aula) {
            hacerSolicitud(`../../APIs/aula_api.php?id=${id_aula}`, 'GET', null, function (status, response) {
                try {
                    const aula = JSON.parse(response);
                    document.getElementById('id_aula').value = aula.id_aula;
                    document.getElementById('nombre').value = aula.nombre;
                    document.getElementById('capacidad').value = aula.capacidad;
                    document.getElementById('equipamiento').value = aula.equipamiento || '';
                    formTitle.textContent = 'Editar Aula';
                    document.querySelector('#aula-form button[type="submit"]').textContent = 'Actualizar';
                } catch (e) {
                    mostrarMensaje('error', "Error al cargar el aula: " + e.message);
                }
            });
        }

        //funcion para eliminar un aula
        function eliminarAula(id_aula) {
            if (!confirm('¿Estás seguro de eliminar esta aula?')) return;
            hacerSolicitud('../../APIs/aula_api.php', 'DELETE', { id_aula: id_aula }, function (status, response) {
                try {
                    const result = JSON.parse(response);
                    if (status === 200 && result.message === "Aula eliminada exitosamente") {
                        cargarAulas();
                        mostrarMensaje('success', 'Aula eliminada exitosamente');
                    } else {
                        mostrarMensaje('error', result.message || "Error al eliminar el aula");
                    }
                } catch (e) {
                    mostrarMensaje('error', "Error al eliminar el aula: " + e.message);
                }
            });
        }


        aulaForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const id_aula = document.getElementById('id_aula').value;
            const nombre = document.getElementById('nombre').value.trim();
            const capacidad = parseInt(document.getElementById('capacidad').value);
            const equipamiento = document.getElementById('equipamiento').value.trim();

            // Validación básica
            let errors = {};
            if (!nombre) errors.nombre = "El nombre es obligatorio";
            if (!capacidad || capacidad < 1) errors.capacidad = "La capacidad debe ser un número mayor a 0";

            if (Object.keys(errors).length > 0) {
                limpiarErrores();
                Object.keys(errors).forEach(field => {
                    document.getElementById(`${field}-error`).textContent = errors[field];
                    document.getElementById(`${field}-error`).style.display = 'block';
                });
                return;
            }
            //guardamos los datos introducidos en el formulario
            const aula = { id_aula, nombre, capacidad, equipamiento };

            //si hay un valor asignado para id_aula , entonces se envia solicitud PUT para actualizar el aula
            if (id_aula) {
                hacerSolicitud('../../APIs/aula_api.php', 'PUT', aula, function (status, response) {
                    try {
                        const result = JSON.parse(response);
                        if (status === 200 && result.message === "Aula actualizada exitosamente") {
                            aulaForm.reset();
                            document.getElementById('id_aula').value = '';
                            formTitle.textContent = 'Gestionar Aulas';
                            document.querySelector('#aula-form button[type="submit"]').textContent = 'Crear';
                            cargarAulas();
                            mostrarMensaje('success', 'Aula actualizada exitosamente');
                        } else {
                            mostrarMensaje('error', result.message || "Error al actualizar el aula");
                        }
                    } catch (e) {
                        mostrarMensaje('error', "Error al actualizar el aula: " + e.message);
                    }
                });
            } else {
                //Si no , se manda una solicitud POST y se crea
                hacerSolicitud('../../APIs/aula_api.php', 'POST', aula, function (status, response) {
                    try {
                        const result = JSON.parse(response);
                        if (status === 200 && result.message === "Aula creada exitosamente") {
                            aulaForm.reset();
                            cargarAulas();
                            mostrarMensaje('success', 'Aula creada exitosamente');
                        } else {
                            mostrarMensaje('error', result.message || "Error al crear el aula");
                        }
                    } catch (e) {
                        mostrarMensaje('error', "Error al crear el aula: " + e.message);
                    }
                });
            }
        });

        //evento para resetear el formulario al pulsar el botón de limpiar
        aulaForm.addEventListener('reset', function () {
            document.getElementById('id_aula').value = '';
            formTitle.textContent = 'Gestionar Aulas';
            document.querySelector('#aula-form button[type="submit"]').textContent = 'Crear';
            limpiarErrores();
        });
        //cargar las aulas en el select al iniciar la página
        window.onload = cargarAulas;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>