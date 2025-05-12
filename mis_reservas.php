<?php
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
    <title>Reservar</title>
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

        #reservas-form {
            border: 2px solid #007bff;
            /* Borde azul */
            border-radius: 8px;
            padding: 1.5rem;
            background-color: #0d1f38;
            /* Fondo más oscuro */
            margin-bottom: 2rem;
        }

        /*Header*/
        .header {
            background-color: #0d1f38;
            padding: 1rem 2rem;
            color: white;
            border-bottom: 1px solid #ffffff33;
            display: flex;
            flex-wrap: wrap;
            /*Para permitir que los elementos se apilen si no caben*/
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
            /*Hacemos que el tamaño sea dinamico*/
            margin-left: clamp(1rem, 3vw, 2rem);
        }

        .navbar-toggler {
            font-size: clamp(1rem, 3vw, 1.5rem);
            /*Hacemos que el tamaño sea dinamico*/
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
            /*Hacemos que el tamaño sea dinamico*/
        }

        /*Para cambiar el color del elemento que separa los elementos del breadcrumb ">"*/
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

        /*sidebar*/

        .offcanvas {
            background-color: #0d1f38;
            color: white;
            width: 20vw !important;
            min-width: 200px !important;
            /*ancho minimo para que no se corte cuando usamos un movil*/
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
            padding: 0.75rem, 1rem;
            display: block;
            text-decoration: none;
            font-size: clamp(0.9rem, 2vw, 1rem);
            /*Hacemos que el tamaño sea dinamico*/
        }

        .offcanvas-body .nav-link.active {
            background-color: #007bff;
            color: white;
        }

        /*Contenido principal*/
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

        .btn-primary {
            font-size: clamp(0.8rem, 2vw, 1rem);
        }

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

        .form-title {
            align-items: center;
        }

        /*Footer*/
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
        <!--Ponemos navbar-dark para que se haga contraste entre el boton de hamburguesa y el fondo del header-->
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
                <li class="breadcrumb-item active" aria-current="page">Reservas</li>
            </ol>
        </nav>
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
                <li class="nav-item">
                    <a class="nav-link" href="vista_reservas.php">Reservar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="mis_reservas.php">Mis Reservas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="mis_tareas.php">Tareas</a>
                </li>
            </ul>
        </div>
    </div>

    <!--Contenido principal-->
    <main class="main-content">
        <h2 class="text-center" id="form-title">Reservar</h2>
        <!--Formulario para crear o editar una reserva-->
        <form id="reservas-form" class="row g-3">
            <div class="col-md-6">
                <label for="fecha" class="form-label">Fecha:</label>
                <input type="date" class="form-control" id="fecha" required>
                <span id="fecha-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="hora_inicio" class="form-label">Hora de Inicio:</label>
                <input type="time" class="form-control" id="hora_inicio" required>
                <span id="hora-inicio-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="hora_fin" class="form-label">Hora de Finalización:</label>
                <input type="time" class="form-control" id="hora_fin" required>
                <span id="hora-fin-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="aula" class="form-label">Aula:</label>
                <select id="aula" class="form-select">
                    <!--Aquí serán listadas las aulas disponibles para elegir-->
                </select>
                <span id="aula-error" class="error-text"></span>
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
            <input type="hidden" id="id_reserva">
            <div class="d-grid gap-2 d-md-block">
                <button class="btn btn-primary" type="submit">Actualizar</button>
                <button type="reset" class="btn btn-light">Limpiar</button>
            </div>
        </form>
        <!--Contenedores para mostrar las notificaciones de error o exito-->
        <div id="success-message" class="success-message"></div>
        <div id="error-message" class="error-message"></div>

        <h2 class="text-center">Mis Reservas</h2>

        <!--Creamos la tabla para mostrar las reservas-->
        <div class="table-responsive">
            <table class="table" id="tabla-reservas">
                <thead>
                    <tr class="table-primary">
                        <th scope="col">Fecha</th>
                        <th scope="col">Horario</th>
                        <th scope="col">Aula</th>
                        <th scope="col">Asignatura</th>
                        <th scope="col">Profesor</th>
                        <th scope="col">Grupo</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody id="reservas-lista">
                    <!--Aqui serán listadas las reservas-->
                </tbody>
            </table>
        </div>
        <!--Contenedor para mostrar los mensajes de error-->
        <div id="error-message"></div>

    </main>

    <!--FOOTER-->
    <footer class="footer">
        <p>&copy; 2025 StudyFlow - Todos los derechos reservados</p>
    </footer>
    <script src="validacion.js"></script> <!--Incluimos el script de validación-->
    <script>
        //obtenemos los elemenos con los que vamos a trabajar , de los que vamos a obtener datos y en los que vamos a cargar datos
        const reservasForm = document.getElementById('reservas-form');
        const reservasLista = document.getElementById('reservas-lista');
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

        //funcion para mostrar mensajes
        function mostrarMensaje(tipo, mensaje) {
            const elemento = document.getElementById(`${tipo}-message`);
            elemento.textContent = mensaje;
            elemento.style.display = 'block';
            setTimeout(() => {
                elemento.style.display = 'none';
            }, 5000);
        }

        //funcion para limpiar los errores que de la validación, para evitar confusiones
        function limpiarErrores() {
            ['fecha-error', 'hora-inicio-error', 'hora-fin-error', 'aula-error', 'asignatura-error', 'grupo-error', 'error-message'].forEach(id => {
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

        //Funcion para cargar las aulas disponibles , dependiendo de los parámetros que se le pasen en la url a la api
        function cargarAulas() {
            const fecha = document.getElementById('fecha').value;
            const hora_inicio = document.getElementById('hora_inicio').value;
            const hora_fin = document.getElementById('hora_fin').value;
            const id_grupo = document.getElementById('grupo').value;
            const id_asignatura = document.getElementById('asignatura').value;

            //dependiendo de si se seLecciona hora inicio y fin y grupo o no, la url será de una manera u otra , por lo que manejamos eso. Aqui se seleccionan las disponibles en esa hora
            let url = 'aula_api.php';
            if (fecha && hora_inicio && hora_fin) {
                url += `?fecha=${fecha}&hora_inicio=${hora_inicio}&hora_fin=${hora_fin}`;
                if (id_grupo) url += `&id_grupo=${id_grupo}`;
                if (id_asignatura) url += `&id_asignatura=${id_asignatura}`;
            }

            // si no se seleccionan esos campos, se obtienen todas las aulas.
            hacerSolicitud(url, 'GET', null, function (status, response) {
                try {
                    const aulas = JSON.parse(response);
                    const aulaSelect = document.getElementById('aula');
                    aulaSelect.innerHTML = '<option value="">Seleccione un aula</option>';
                    if (aulas.length > 0) {
                        aulas.forEach(aula => {
                            const option = document.createElement('option');
                            option.value = aula.id_aula;
                            option.textContent = aula.nombre;
                            aulaSelect.appendChild(option);
                        });
                    } else {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'No hay aulas disponibles para este grupo/horario';
                        option.disabled = true;
                        aulaSelect.appendChild(option);
                    }
                } catch (e) {
                    mostrarError("Error al cargar las aulas: " + e.message);
                }
            });
        }

        //función para cargar las asignaturas en el select
        function cargarAsignaturas() {
            hacerSolicitud('asignatura_api.php', 'GET', null, function (status, response) {
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

        //función para cargar los grupos disponibles en el select
        function cargarGrupos() {
            const fecha = document.getElementById('fecha').value;
            const hora_inicio = document.getElementById('hora_inicio').value;
            const hora_fin = document.getElementById('hora_fin').value;

            let url = 'grupo_api.php';
            if (fecha && hora_inicio && hora_fin) {
                url += `?fecha=${fecha}&hora_inicio=${hora_inicio}&hora_fin=${hora_fin}`;
            }

            //si se ha seleccionado fecha y horario se le pasan los parametros a la api por la url de la solicitud, y se muestran solo los disponibles en ese horario
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
                        option.textContent = 'No hay grupos disponibles para este horario';
                        option.disabled = true;
                        grupoSelect.appendChild(option);
                    }

                } catch (e) {
                    mostrarError("Error al cargar los grupos: " + e.message);
                }
            });
        }

        //función para cargar todas las reservas del usuario que ha iniciado sesión
        function cargarReservas() {
            hacerSolicitud('reserva_api.php', 'GET', null, function (status, response) {
                try {
                    const reservas = JSON.parse(response);
                    reservasLista.innerHTML = '';
                    reservas.forEach(reserva => {
                        const row = `
                        <tr>
                            <td>${reserva.fecha}</td>
                            <td>${reserva.hora_inicio} - ${reserva.hora_fin}</td>
                            <td>${reserva.aula}</td>
                            <td>${reserva.asignatura}</td>
                            <td>${reserva.profesor}</td>
                            <td>${reserva.grupo}</td>
                            <td>${reserva.estado}</td>
                            <td>
                                <button class="btn btn-warning btn-sm me-1" onclick="editarReserva(${reserva.id_reserva})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="eliminarReserva(${reserva.id_reserva})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>

                        `;
                        reservasLista.innerHTML += row;
                    });
                } catch (e) {
                    mostrarError("Error al cargar las reservas: " + e.message);
                }
            });
        }

        //función para editar una reserva
        function editarReserva(id_reserva) {
            hacerSolicitud(`reserva_api.php?id=${id_reserva}`, 'GET', null, function (status, response) {
                try {
                    const reserva = JSON.parse(response);
                    document.getElementById('id_reserva').value = reserva.id_reserva;
                    document.getElementById('fecha').value = reserva.fecha;
                    document.getElementById('hora_inicio').value = reserva.hora_inicio;
                    document.getElementById('hora_fin').value = reserva.hora_fin;
                    document.getElementById('aula').value = reserva.id_aula;
                    document.getElementById('asignatura').value = reserva.id_asignatura;
                    document.getElementById('grupo').value = reserva.id_grupo;
                    formTitle.textContent = 'Editar Reserva';
                    cargarAulas(); //recargamos las aulas disponibles con el nuevo horario y grupo
                    cargarGrupos();//recargamos los grupos disponibles con el nuevo horario 
                } catch (e) {
                    mostrarError("Error al cargar la reserva para editar: " + e.message);
                }
            });
        }

        //función para eliminar la reserva
        function eliminarReserva(id_reserva) {
            if (!confirm('¿Estás seguro de eliminar esta reserva?')) return;
            hacerSolicitud('reserva_api.php', 'DELETE', { id_reserva: id_reserva }, function (status, response) {
                try {
                    const result = JSON.parse(response);
                    if (status === 200 && result.message === "Reserva eliminada exitosamente") {
                        cargarReservas();
                    } else {
                        mostrarError(result.message || "Error al eliminar la reserva");
                    }
                } catch (e) {
                    mostrarError("Error al eliminar la reserva: " + e.message);
                }
            });
        }

        //función para crear una reserva
        function crearReserva(event) {
            event.preventDefault();

            //obtenemos los datos introducimos en el formulario
            const id_reserva = document.getElementById("id_reserva").value;
            const fecha = document.getElementById("fecha").value;
            const hora_inicio = document.getElementById("hora_inicio").value;
            const hora_fin = document.getElementById("hora_fin").value;
            const id_aula = parseInt(document.getElementById("aula").value);
            const id_asignatura = parseInt(document.getElementById("asignatura").value);
            const id_grupo = parseInt(document.getElementById("grupo").value);

            //guardamos los datos introducidos en el formulario
            const reserva = { id_reserva, fecha, hora_inicio, hora_fin, id_aula, id_asignatura, id_grupo };

            //si se asigna un valor al id_reserva , hacemos una solicitud para actualizar la reserva
            if (id_reserva) {
                hacerSolicitud('reserva_api.php', 'PUT', reserva, function (status, response) {
                    try {
                        const result = JSON.parse(response);
                        if (status === 200 && result.message === "Reserva actualizada exitosamente") {
                            document.getElementById("reservas-form").reset();
                            document.getElementById("id_reserva").value = null;
                            formTitle.textContent = "Editar Reserva";
                            cargarReservas();
                        } else {
                            mostrarError("Error al actualizar la reserva");

                        }
                    } catch (e) {
                        mostrarError("Error al actualizar la reserva : " + e.message);
                    }
                });
            } else {
                //sino la hacemospara crearla en la base de datos
                hacerSolicitud('reserva_api.php', 'POST', reserva, function (status, response) {
                    try {
                        const result = JSON.parse(response);
                        if (status === 200 && result.message === "Reserva creada exitosamente") {
                            document.getElementById("reservas-form").reset();
                            cargarReservas();
                        } else {
                            mostrarError("Error al crear la reserva");
                        }
                    } catch (e) {
                        mostrarError('Error al crear la reserva : ' + e.message);
                    }
                });
            }

        }

        // Actualiza la lista de aulas y grupos al cambiar fecha o horario
        ['fecha', 'hora_inicio', 'hora_fin'].forEach(id => {
            document.getElementById(id).addEventListener('change', function () {
                cargarAulas();
                cargarGrupos();
            });
        });

        // comenzamos la validación de los datos introducidos en el formulario antes de hacer la solicitud de crear
        reservasForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            //recogemos los datos del formulario
            const datos = {
                fecha: document.getElementById('fecha').value,
                hora_inicio: document.getElementById('hora_inicio').value,
                hora_fin: document.getElementById('hora_fin').value,
                id_aula: document.getElementById('aula').value,
                id_asignatura: document.getElementById('asignatura').value,
                id_grupo: document.getElementById('grupo').value,
                id_reserva: document.getElementById('id_reserva').value
            };

            //definimos las reglas de validación para cada campo, usndo las funciones definidas en validacion.js
            const reglas = {
                fecha: { validar: validarFecha, errorId: "fecha-error" },
                hora_inicio: {
                    validar: (valor, datos) => validarHorarioReserva(valor, datos.hora_fin),
                    errorId: "hora-inicio-error"
                },
                hora_fin: {
                    validar: (valor, datos) => validarHorarioReserva(datos.hora_inicio, valor),
                    errorId: "hora-fin-error"
                },
                id_aula: {
                    validar: (valor) => valor ? "" : "Seleccione un aula",
                    errorId: "aula-error"
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

            limpiarErrores();

            const validation = await validarCampos(datos, reglas);
            let isValid = validation.isValid;

            if (isValid) {
                // si todo está correcto, entonces se ejecuta la función que hace la solicitud para crear
                crearReserva(event, datos);
            } else {
                //sino mostramos los errores de validación en los campos que han fallado
                Object.keys(validation.errors).forEach(field => {
                    if (reglas[field] && reglas[field].errorId && validation.errors[field]) {
                        document.getElementById(reglas[field].errorId).textContent = validation.errors[field];
                        document.getElementById(reglas[field].errorId).style.display = 'block';
                    }
                });
            }
        });

        //evento para resetear el formulario y los campos al pulsar el boton de limpiar
        reservasForm.addEventListener('reset', function () {
            formTitle.textContent = 'Nueva Reserva';
            document.getElementById('id_reserva').value = '';
            document.querySelector("button[type='submit']").textContent = 'Crear';
            cargarAulas();
            cargarGrupos();
            limpiarErrores();
        });

        // al iniciar la página , se cargan las asignaturas, aulas, grupos y resveras existentes
        window.onload = function () {
            cargarAulas();
            cargarAsignaturas();
            cargarGrupos();
            cargarReservas();
        };

    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>