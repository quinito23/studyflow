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

        #aula-form {
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

        .form-control {
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

        .error-message,
        .success-message {
            padding: 0.75rem;
            border-radius: 5px;
            margin-top: 1rem;
            display: none;
        }

        .error-message {
            background-color: #f8d7da;
            color: #dc3545;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
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
    <!--Header de bootsraps-->
    <header class="header">
        <div class="d-flex align-items-center navbar-dark">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar"
                aria-label="Toggle Navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <h1 class="mx-auto">StudyFlow</h1>
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
                aria-label="close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="gestion_profesores.php">Profesores</a></li>
                <li class="nav-item"><a class="nav-link" href="gestion_alumnos.php">Alumnos</a></li>
                <li class="nav-item"><a class="nav-link" href="gestion_tutores.php">Tutores</a></li>
                <li class="nav-item"><a class="nav-link active" href="gestion_aulas.php">Aulas</a></li>
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
                <button class="btn btn-primary" type="submit">Crear</button>
                <button type="reset" class="btn btn-light">Limpiar</button>
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
                                        <button class="btn btn-warning btn-sm me-1" onclick="editarAula(${aula.id_aula})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="eliminarAula(${aula.id_aula})">
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