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
    <title>Solicitudes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../../public/css/styles.css">
</head>

<body>

    <!--Header-->
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
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Solicitudes</li>
                </ol>
            </nav>
            <span class="separador">|</span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm logout-btn" title="Cerrar sesión">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </header>

    <!--Barra lateral dedsplegable con offcanvas de bootsraps-->
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
                <li class="nav-item"><a class="nav-link active" href="gestion_solicitudes.php">Solicitudes</a></li>
            </ul>
        </div>
    </div>

    <!--Contenido principal-->
    <main class="main-content">

        <h2 class="text-center">Solicitudes</h2>

        <div id="notification" class="notification"></div>
        <!--Creamos la tabla que muestra las solicitudes pendientes-->
        <h3>Solicitudes Pendientes</h3>
        <div class="table-responsive">
            <table class="table" id="tabla-solicitudes-pendientes">
                <thead>
                    <tr class="table-primary">
                        <th scope="col">Nombre</th>
                        <th scope="col">Apellidos</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Fecha de Realización</th>
                        <th scope="col">Rol Propuesto</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody id="solicitudes-pendientes-lista">
                    <!--Aquí serán listados las solicitudes pendientes-->
                </tbody>
            </table>
        </div>

        <!--Tabla para ver las solicitudes aceptadas-->
        <h3>Solicitudes Aceptadas</h3>
        <div class="table-responsive">
            <table class="table" id="tabla-solicitudes-aceptadas">
                <thead>
                    <tr class="table-primary">
                        <th scope="col">Nombre</th>
                        <th scope="col">Apelidos</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Fecha de Realización</th>
                        <th scope="col">Rol Propuesto</th>
                    </tr>
                </thead>
                <tbody id="solicitudes-aceptadas-lista">
                    <!--Aquí serán listadas las solicitudes aceptadas-->
                </tbody>
            </table>
        </div>
        <!--Tabla para ver las solicitudes rechazadas-->
        <h3>Solicitudes Rechazadas</h3>
        <div class="table-responsive">
            <table class="table" id="tabla-solicitudes-rechazadas">
                <thead>
                    <tr class="table-primary">
                        <th scope="col">Nombre</th>
                        <th scope="col">Apellidos</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Fecha de Realización</th>
                        <th scope="col">Rol Propuesto</th>

                    </tr>
                </thead>
                <tbody id="solicitudes-rechazadas-lista">
                    <!--Aquí serán listadas las solicitudes rechazadas-->
                </tbody>
            </table>
        </div>
        <!--Modal que aparece al pulsar aceptar la solicitud para asignar grupos a un alumno.-->
        <div class="modal fade" id="asignarGruposModal" tabindex="-1" aria-labelledby="asignarGruposModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="asignarGruposModalLabel">Asignar Grupos</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="close"></button>
                    </div>
                    <!--Cuerpo del modal-->
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Asignaturas propuestas:</label>
                            <ul id="asignaturasPropuestas"></ul>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Seleccionar grupos:</label>
                            <select class="form-select" id="gruposSeleccionados" multiple>
                                <!--Los grupos disponibles se cargaran dinamicamente-->
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                aria-label="Cancelar">Cancelar</button>
                            <button type="button" class="btn btn-primary" onclick="confirmarAceptar()"
                                aria-label="Confirmar">Confirmar</button>
                        </div>
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

        //Obtengo el elemento de notificación para mostrar mensajes
        const notification = document.getElementById('notification');

        //función para mostrar notificaciones de éxito o error
        function mostrarNotificacion(mensaje, tipo) {
            notification.textContent = mensaje;
            notification.className = `notification ${tipo}`;
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }

        //Función para hacer soliictudes AJAX
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

        // Funcion para cargar las solicitudes al iniciar la página
        function cargarSolicitudes() {
            hacerSolicitud('../../APIs/solicitud_api.php', 'GET', null, function (status, response) {
                try {
                    const solicitudes = JSON.parse(response);
                    //obtenemos los elementos donde vamos a cargar los datos
                    const listaPendientes = document.getElementById("solicitudes-pendientes-lista");
                    const listaAceptadas = document.getElementById("solicitudes-aceptadas-lista");
                    const listaRechazadas = document.getElementById("solicitudes-rechazadas-lista");

                    //limpiamos las tablas
                    listaPendientes.innerHTML = '';
                    listaAceptadas.innerHTML = '';
                    listaRechazadas.innerHTML = '';

                    // Comenzamos creando las filas que vamos a introducir en las tablas con los datos de la solicitud
                    solicitudes.forEach(solicitud => {
                        const row = `
                        <tr>
                            <td>${solicitud.nombre}</td>
                            <td>${solicitud.apellidos}</td>
                            <td>${solicitud.correo}</td>
                            <td>${solicitud.estado}</td>
                            <td>${solicitud.fecha_realizacion}</td>
                            <td>${solicitud.rol_propuesto}</td>
                            ${solicitud.estado === 'pendiente' ? `
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-success btn-sm" onclick="aceptarSolicitud(${solicitud.id_solicitud}, ${solicitud.id_anonimo}, '${solicitud.rol_propuesto}')" aria-label="Aceptar">Aceptar</button>
                                        <button class = "btn btn-danger btn-sm" onclick="rechazarSolicitud(${solicitud.id_solicitud})" aria-label="Rechazar">Rechazar</button>
                                    </div>
                                </td>    
                                ` : ''}
                            </tr>
                            `;
                        //dependiendo del valor de la propiedad estado , se introduce la fila en una tabla u otra
                        if (solicitud.estado === 'pendiente') {
                            listaPendientes.innerHTML += row;
                        } else if (solicitud.estado === 'aceptado') {
                            listaAceptadas.innerHTML += row;
                        } else if (solicitud.estado === 'rechazado') {
                            listaRechazadas.innerHTML += row;
                        }
                    });
                } catch (e) {
                    console.error("Error al parsear la respuesta:", e.message);
                    // Cambio: Mostrar notificación si falla la carga de solicitudes
                    mostrarNotificacion("Error al cargar las solicitudes", "error");
                }
            });
        }

        // funcion para aceptar una solicitud
        function aceptarSolicitud(idSolicitud, idAnonimo, rolPropuesto) {

            const datos = {
                id_solicitud: idSolicitud,
                id_anonimo: idAnonimo,
                rol_propuesto: rolPropuesto
            };

            if (rolPropuesto !== 'alumno') {
                confirmarAceptar(datos, []); //los corchetes hacen referencia a la lista de grupos, que no hacen falta para los profersores, por eso está vacio
                return;
            }

            //obtener las asignaturas asociadas a la solicitud
            hacerSolicitud(`../../APIs/solicitud_api.php?getAsignaturas=${idSolicitud}`, 'GET', null, function (status, response) {
                try {
                    const asignaturas = JSON.parse(response);
                    if (status !== 200 || !asignaturas || asignaturas.length === 0) {
                        mostrarNotificacion("No se encontraron asignaturas");
                        return;
                    }

                    //Mostrar las asignaturas propuestas en el modal
                    const asignaturasPropuestas = document.getElementById("asignaturasPropuestas");
                    asignaturasPropuestas.innerHTML = '';
                    asignaturas.forEach(asignatura => {
                        const li = document.createElement('li');
                        li.textContent = asignatura.nombre;
                        asignaturasPropuestas.appendChild(li);
                    });


                    //ontener los grupos disponibles haciendo una solicitud a la api
                    hacerSolicitud('../../APIs/grupo_api.php', 'GET', null, function (status, response) {
                        try {
                            const grupos = JSON.parse(response);
                            if (status !== 200 || !grupos) {
                                mostrarNotificacion("Error al cargar los grupos");
                            }

                            const gruposSeleccionados = document.getElementById("gruposSeleccionados");
                            gruposSeleccionados.innerHTML = '';
                            grupos.forEach(grupo => {
                                const option = document.createElement('option');
                                option.value = grupo.id_grupo;
                                option.textContent = `${grupo.nombre} (${grupo.nombre_asignatura}) - Capacidad: ${grupo.numero_alumnos}/${grupo.capacidad_maxima}`;
                                gruposSeleccionados.appendChild(option);
                            });
                            //definimos el modal que se abree al aceptar la solicitud para asignar los grupos o grupo al alumno
                            const modal = new bootstrap.Modal(document.getElementById("asignarGruposModal"));
                            modal.show(); //se muestra el modal

                            //guardar los grupos seleccionados en el botón de confirmación para enviarlos
                            const confirmarBtn = document.querySelector('#asignarGruposModal .btn-primary');
                            confirmarBtn.onclick = () => confirmarAceptar(datos, Array.from(gruposSeleccionados.selectedOptions).map(option => parseInt(option.value)));
                        } catch (e) {
                            mostrarNotificacion("Error al cargar los grupos");
                        }
                    });
                } catch (e) {
                    mostrarNotificacion("Error al cargar las asignaturas");
                }
            });
        }

        // funcion para obtener los grupos seleccionados y enviarlos la backend como un array
        function confirmarAceptar(datos, grupos = []) {
            const payload = {
                id_solicitud: datos.id_solicitud,
                id_anonimo: datos.id_anonimo,
                rol_propuesto: datos.rol_propuesto,
                accion: 'aceptar',
                grupos: grupos
            };

            hacerSolicitud('../../APIs/solicitud_api.php', 'POST', payload, function (status, response) {
                console.log("Estado de la respuesta:", status);
                console.log("Respuesta del backend:", response);
                try {
                    const result = JSON.parse(response);
                    if (status === 200 && result.message === "Solicitud aceptada exitosamente") {
                        mostrarNotificacion("Solicitud aceptada con exito");
                        cargarSolicitudes();
                        const modal = bootstrap.Modal.getInstance(document.getElementById("asignarGruposModal"));
                        if (modal) {
                            modal.hide();
                        }
                    } else {
                        mostrarNotificacion(result.message || "Error al aceptar la solicitud");
                    }
                } catch (e) {
                    mostrarNotificacion("Error al aceptar la solicitud");
                }
            });

        }

        //funcion para rechazar una solicitud
        function rechazarSolicitud(idSolicitud) {
            const datos = { id_solicitud: idSolicitud, accion: 'rechazar' };
            hacerSolicitud('../../APIs/solicitud_api.php', 'POST', datos, function (status, response) {
                try {
                    const result = JSON.parse(response);
                    if (result.message === "Solicitud rechazada exitosamente") {
                        cargarSolicitudes(); // recargamos las tablas para mostrar los cambios
                    } else {
                        return false;
                    }
                } catch (e) {
                    console.error("Error al parsear la respuesta:", e.message);
                }
            });
        }

        //inicializamos para que al abrir la página se carguen las solicitudes en las tablas
        window.onload = cargarSolicitudes;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>