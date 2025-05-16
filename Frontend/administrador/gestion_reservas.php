<?php
session_start(); //inicio de la sesión

//verificar autenticacion y rol
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
    <title>Reservar</title>
    <link rel="stylesheet" href="../styles.css">
</head>

<body>
    <!--Header de bootsraps-->
    <header class="header">
        <!--Ponemos navbar-dark para que se haga contraste entre el boton de hamburguesa y el fondo del header-->
        <div class="d-flex align-items-center navbar-dark">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar"
                aria-label="Barra Lateral">
                <span class="navbar-toggler-icon"></span>
            </button>
            <img src="../StudyFlow3.svg" alt="logotipo" loading="lazy">
        </div>
        <!--Definimos los elementos del breadcrumb-->
        <div class="breadcrumb-container">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboardProfesor.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Reservas</li>
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
                <li class="nav-item"><a class="nav-link active" href="gestion_reservas.php">Reservas</a></li>
                <li class="nav-item"><a class="nav-link" href="gestion_tareas.php">Tareas</a></li>
                <li class="nav-item"><a class="nav-link" href="gestion_solicitudes.php">Solicitudes</a></li>
            </ul>
        </div>
    </div>

    <!--Contenido principal-->
    <main class="main-content">

        <h2 class="text-center" id="form-title">Reservas</h2>
        <!--Formulario para crear o editar una reserva-->
        <form id="reservas-form" class="row g-3">
            <div class="col-md-6">
                <label for="fecha" class="form-label">Fecha:</label>
                <input type="date" class="form-control" id="fecha" required>
                <div class="error-message" id="fecha-error"></div>
            </div>
            <div class="col-md-6">
                <label for="hora_inicio" class="form-label">Hora de Inicio:</label>
                <input type="time" class="form-control" id="hora_inicio" required>
                <div class="error-message" id="hora-inicio-error"></div>
            </div>
            <div class="col-md-6">
                <label for="hora_fin" class="form-label">Hora de Finalización:</label>
                <input type="time" class="form-control" id="hora_fin" required>
                <div class="error-message" id="hora-fin-error"></div>
            </div>
            <div class="col-md-6">
                <label for="aula" class="form-label">Aula:</label>
                <select id="aula" class="form-select">
                    <!--Aquí serán listadas las aulas disponibles para elegir-->
                </select>
                <div class="error-message" id="aula-error"></div>
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
            <input type="hidden" id="id_reserva">
            <div class="d-grid gap-2 d-md-block">
                <button class="btn btn-primary" type="submit" aria-label="Crear">Crear</button>
                <button type="reset" class="btn btn-light" aria-label="Limpiar Formulario">Limpiar</button>
            </div>
        </form>
        <div id="success-message" class="success-message"></div>
        <div id="error-message" class="error-message"></div>

        <h2 class="text-center">Reservas</h2>

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




    </main>

    <!--FOOTER-->
    <footer class="footer">
        <p>&copy; 2025 StudyFlow - Todos los derechos reservados</p>
    </footer>

    <script src="../validacion.js"></script><!--Incluir el script para las validaciones-->
    <script>
        //obtenemos los elemenos con los que vamos a trabajar , de los que vamos a obtener datos y en los que vamos a cargar datos
        const reservasForm = document.getElementById('reservas-form');
        const reservasLista = document.getElementById('reservas-lista');
        const errorMessage = document.getElementById('error-message');
        const formTitle = document.getElementById('form-title');

        //funcion para mostrar mensajes de exito y error
        function mostrarMensaje(tipo, mensaje) {
            const elemento = document.getElementById(`${tipo}-message`);
            elemento.textContent = mensaje;
            elemento.style.display = 'block';
            setTimeout(() => {
                elemento.style.display = 'none';
            }, 5000);
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

        //Funcion para cargar las aulas disponibles , dependiendo de los parámetros que se le pasen en la url a la api
        function cargarAulas() {
            const fecha = document.getElementById('fecha').value;
            const hora_inicio = document.getElementById('hora_inicio').value;
            const hora_fin = document.getElementById('hora_fin').value;
            const id_grupo = document.getElementById('grupo').value;
            const id_asignatura = document.getElementById('asignatura').value;
            const id_reserva = document.getElementById('id_reserva').value;

            //dependiendo de si se seLecciona hora inicio y fin y grupo o no, la url será de una manera u otra , por lo que manejamos eso. Aqui se seleccionan las disponibles en esa hora
            let url = '../../APIs/aula_api.php';
            if (fecha && hora_inicio && hora_fin) {
                url += `?fecha=${fecha}&hora_inicio=${hora_inicio}&hora_fin=${hora_fin}`;
                if (id_grupo) url += `&id_grupo=${id_grupo}`;
                if (id_asignatura) url += `&id_asignatura=${id_asignatura}`;
                if (id_reserva) url += `&id_reserva=${id_reserva}`;
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
                    mostrarMensaje('error', 'Error al cargar las aulas: ' + e.message);
                }
            });
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
                    mostrarMensaje('error', 'Error al cargar las aulas: ' + e.message);
                }
            });
        }

        //función para cargar los grupos disponibles en el select
        function cargarGrupos() {
            const fecha = document.getElementById('fecha').value;
            const hora_inicio = document.getElementById('hora_inicio').value;
            const hora_fin = document.getElementById('hora_fin').value;
            const id_reserva = document.getElementById('id_reserva').value;

            //si se ha seleccionado fecha y horario se le pasan los parametros a la api por la url de la solicitud, y se muestran solo los disponibles en ese horario
            let url = '../../APIs/grupo_api.php';
            if (fecha && hora_inicio && hora_fin) {
                url += `?fecha=${fecha}&hora_inicio=${hora_inicio}&hora_fin=${hora_fin}`;
                if (id_reserva) url += `&id_reserva=${id_reserva}`;
            }

            //sino , se muestran todos
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
                    mostrarMensaje('error', 'Error al cargar los grupos: ' + e.message);
                }
            });
        }

        //función para cargar todas las reservas
        function cargarReservas() {
            //se le pasa el parametro todas=1 en la solicitud a la API para que las cargue todas
            hacerSolicitud('../../APIs/reserva_api.php?todas=1', 'GET', null, function (status, response) {
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
                            <div class="action-buttons">
                                <button class="btn btn-success btn-sm" onclick="editarReserva(${reserva.id_reserva})" aria-label="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="eliminarReserva(${reserva.id_reserva})" aria-label="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
                        reservasLista.innerHTML += row;
                    });
                } catch (e) {
                    mostrarMensaje('error', 'Error al cargar las reservas: ' + e.message);
                }
            });
        }

        //funcion para editar una tarea
        function editarReserva(id_reserva) {
            hacerSolicitud(`../../APIs/reserva_api.php?id=${id_reserva}`, 'GET', null, function (status, response) {
                try {
                    const reserva = JSON.parse(response);
                    document.getElementById('id_reserva').value = reserva.id_reserva;
                    document.getElementById('fecha').value = reserva.fecha;
                    document.getElementById('hora_inicio').value = reserva.hora_inicio;
                    document.getElementById('hora_fin').value = reserva.hora_fin;

                    // Guardar los valores que queremos seleccionar
                    const id_aula = reserva.id_aula;
                    const id_grupo = reserva.id_grupo;
                    const id_asignatura = reserva.id_asignatura;

                    formTitle.textContent = 'Editar Reserva';
                    document.querySelector('#reservas-form button[type="submit"]').textContent = 'Actualizar';

                    // recargar las aulas, grupos y asignaturas disponibles con el  horario y fecha seleccionados
                    cargarAulas();
                    cargarGrupos();
                    cargarAsignaturas();

                    // Asegurarse de que los valores correctos(los que tenia asignados anteriormente la reserva) estén seleccionados
                    setTimeout(() => {
                        document.getElementById('aula').value = id_aula;
                        document.getElementById('grupo').value = id_grupo;
                        document.getElementById('asignatura').value = id_asignatura;
                    }, 100); // Pequeño retraso para esperar a que las listas se carguen
                } catch (e) {
                    mostrarMensaje('error', 'Error al cargar la reserva para editar: ' + e.message);
                }
            });
        }

        //función para eliminar una reserva
        function eliminarReserva(id_reserva) {
            if (!confirm('¿Estás seguro de eliminar esta reserva?')) return;
            hacerSolicitud('../../APIs/reserva_api.php', 'DELETE', { id_reserva: id_reserva }, function (status, response) {
                try {
                    const result = JSON.parse(response);
                    if (status === 200 && result.message === "Reserva eliminada exitosamente") {
                        cargarReservas();
                        mostrarMensaje('success', result.message || 'Reserva eliminada exitosamente');
                    } else {
                        mostrarMensaje('error', result.message || "Error al eliminar la reserva");
                    }
                } catch (e) {
                    mostrarMensaje('error', 'Error al eliminar la reserva: ' + e.message);
                }
            });
        }

        //función para crear una reserva
        function crearReserva(event) {
            event.preventDefault();

            const id_reserva = document.getElementById("id_reserva").value;
            const fecha = document.getElementById("fecha").value;
            const hora_inicio = document.getElementById("hora_inicio").value;
            const hora_fin = document.getElementById("hora_fin").value;
            const id_aula = parseInt(document.getElementById("aula").value);
            const id_asignatura = parseInt(document.getElementById("asignatura").value);
            const id_grupo = parseInt(document.getElementById("grupo").value);

            //guardamos los datos introducidos en el formulario
            const reserva = { id_reserva, fecha, hora_inicio, hora_fin, id_aula, id_asignatura, id_grupo };

            //verificamos si tiene valor el parametro id_reserva, y si existe se hace la solicitud al metodo put para actualizar
            if (id_reserva) {
                hacerSolicitud('../../APIs/reserva_api.php', 'PUT', reserva, function (status, response) {
                    try {
                        const result = JSON.parse(response);
                        if (status === 200 && result.message === "Reserva actualizada exitosamente") {
                            document.getElementById("reservas-form").reset();
                            document.getElementById("id_reserva").value = null;
                            formTitle.textContent = "Reservas";
                            document.querySelector('#reservas-form button[type="submit"]').textContent = 'Crear';
                            cargarReservas();
                            mostrarMensaje('success', result.message || 'Reserva actualizada exitosamente');
                        } else {
                            mostrarMensaje('error', result.message || 'Error al actualizar la reserva');
                        }
                    } catch (e) {
                        mostrarMensaje('error', 'Error al actualizar la reserva: ' + e.message);
                    }
                });
            } else {
                //sino al post para crear
                hacerSolicitud('../../APIs/reserva_api.php', 'POST', reserva, function (status, response) {
                    try {
                        const result = JSON.parse(response);
                        if (status === 200 && result.message === "Reserva creada exitosamente") {
                            document.getElementById("reservas-form").reset();
                            cargarReservas();
                            mostrarMensaje('success', result.message || 'Reserva creada exitosamente');
                        } else {
                            mostrarMensaje('error', result.message || 'Error al crear la reserva');
                        }
                    } catch (e) {
                        mostrarMensaje('error', 'Error al crear la reserva: ' + e.message);
                    }
                });
            }
        }

        // Actualizar la lista de aulas al cambiar los campos fecha, horario y grupo
        ['fecha', 'hora_inicio', 'hora_fin'].forEach(id => {
            document.getElementById(id).addEventListener('change', function () {
                cargarAulas();
                cargarGrupos();
            });
        });

        // comenzamos la validación de los datos introducidos en el formulario antes de hacer la solicitud
        reservasForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            //recogemos los datos del formulario
            const datos = {
                fecha: document.getElementById("fecha").value,
                hora_inicio: document.getElementById("hora_inicio").value,
                hora_fin: document.getElementById("hora_fin").value,
                id_aula: document.getElementById("aula").value,
                id_asignatura: document.getElementById("asignatura").value,
                id_grupo: document.getElementById("grupo").value
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

            const validation = await validarCampos(datos, reglas);

            if (validation.isValid) {
                // si todo está correcto, entonces se ejecuta la función que hace la solicitud para crear
                crearReserva(event);
            } else {
                //sino mostramos los errores de validación en los campos que han fallado
                Object.keys(validation.errors).forEach(field => {
                    document.getElementById(reglas[field].errorId).textContent = validation.errors[field];
                });
            }
        });

        //evento para resetear el formulario y los campos al pulsar el boton de limpiar
        reservasForm.addEventListener('reset', function () {
            formTitle.textContent = 'Reservas';
            document.querySelector('#reservas-form button[type="submit"]').textContent = 'Crear';
            document.getElementById('id_reserva').value = '';
            cargarAulas();
            // Limpiar mensajes de error
            ['fecha-error', 'hora-inicio-error', 'hora-fin-error', 'aula-error', 'asignatura-error', 'grupo-error'].forEach(id => {
                const element = document.getElementById(id);
                if (element) element.textContent = '';
            });
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