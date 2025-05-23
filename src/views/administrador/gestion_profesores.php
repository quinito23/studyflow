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
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Profesores</title>
    <link rel="stylesheet" href="../../../public/css/styles.css">
</head>

<body>
    <!--Header de bootstraps-->
    <header class="header">
        <div class="d-flex align-items-center navbar-dark">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar"
                aria-label="Barra Lateral">
                <span class="navbar-toggler-icon"></span>
            </button>
            <img src="../../../public/imagenes/StudyFlow3.svg" alt="logotipo" loading="lazy">
        </div>
        <!--Breadcrumbs de bootstraps-->
        <div class="breadcrumb-container">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Profesores</li>
                </ol>
            </nav>
            <span class="separador">|</span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm logout-btn" title="Cerrar Sesión">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </header>

    <!--Barra lateral de bootstraps-->
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
                <li class="nav-item"><a class="nav-link active" href="gestion_profesores.php">Profesores</a></li>
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

        <h2 class="text-center" id="form-title">Nuevo Profesor</h2>
        <form id="profesor-form" class="row g-3">
            <div class="col-md-6">
                <label for="correo" class="form-label">Correo</label>
                <input type="email" class="form-control" id="correo" required>
                <span id="correo-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="contrasenia" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="contrasenia" required>
                <span id="contrasenia-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" required>
                <span id="nombre-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="apellidos" class="form-label">Apellidos</label>
                <input type="text" class="form-control" id="apellidos" required>
                <span id="apellidos-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="DNI" class="form-label">DNI</label>
                <input type="text" class="form-control" id="DNI" required>
                <span id="dni-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="telefono" required>
                <span id="telefono-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                <input type="date" class="form-control" id="fecha_nacimiento">
                <span id="fecha-nacimiento-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="rol" class="form-label">Rol</label>
                <select id="rol" class="form-select">
                    <option value="administrador">Administrador</option>
                    <option value="profesor" selected>Profesor</option>
                    <option value="alumno">Alumno</option>
                </select>
                <span id="rol-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="sueldo" class="form-label">Sueldo</label>
                <input type="number" class="form-control" id="sueldo">
                <span id="sueldo-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="jornada" class="form-label">Jornada</label>
                <select id="jornada" class="form-select">
                    <option value="tiempo_completo">Completa</option>
                    <option value="medio_tiempo">Media</option>
                    <option value="por_horas">Por horas</option>
                </select>
                <span id="jornada-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="fecha_inicio_contrato" class="form-label">Fecha Inicio de Contrato</label>
                <input type="date" class="form-control" id="fecha_inicio_contrato">
                <span id="fecha-inicio-contrato-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="fecha_fin_contrato" class="form Galway, Ireland">Fecha Fin de Contrato</label>
                <input type="date" class="form-control" id="fecha_fin_contrato">
                <span id="fecha-fin-contrato-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="asignaturas" class="form-label">Asignaturas</label>
                <select id="asignaturas" class="form-select" multiple>
                    <!-- Opciones cargadas dinámicamente -->
                </select>
                <span id="asignaturas-error" class="error-text"></span>
            </div>
            <input type="hidden" id="id_usuario">
            <div class="d-grid gap-2 d-md-block">
                <button class="btn btn-primary" type="submit" aria-label="Crear">Crear</button>
                <button type="reset" class="btn btn-light" aria-label="Limpiar Formulario">Limpiar</button>
            </div>
        </form>
        <!--Contenedores para los mensajes de error y exito-->
        <div id="success-message" class="success-message"></div>
        <div id="error-message" class="error-message"></div>

        <h2 class="text-center">Profesores</h2>
        <div class="table-responsive">
            <table class="table" id="tabla-profesores">
                <thead>
                    <tr class="table-primary">
                        <th scope="col">Nombre</th>
                        <th scope="col">Apellidos</th>
                        <th scope="col">DNI</th>
                        <th scope="col">Teléfono</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider" id="profesores-lista">
                    <!--Aqui se cargan dinamicamente los profesores-->
                </tbody>
            </table>
        </div>
        <!--Modal para mostrar la información de un profesor-->
        <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="infoModalLabel">Información del Profesor</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Nombre:</strong><span id="modal-nombre"></span></p>
                        <p><strong>Apellidos:</strong><span id="modal-apellidos"></span></p>
                        <p><strong>Correo:</strong><span id="modal-correo"></span></p>
                        <p><strong>Contraseña:</strong><span id="modal-contrasenia"></span></p>
                        <p><strong>DNI:</strong><span id="modal-DNI"></span></p>
                        <p><strong>Teléfono:</strong><span id="modal-telefono"></span></p>
                        <p><strong>Fecha de Nacimiento:</strong><span id="modal-fecha_nacimiento"></span></p>
                        <p><strong>Rol:</strong><span id="modal-rol"></span></p>
                        <p><strong>Sueldo:</strong><span id="modal-sueldo"></span></p>
                        <p><strong>Jornada:</strong><span id="modal-jornada"></span></p>
                        <p><strong>Fecha Inicio Contrato:</strong><span id="modal-fecha_inicio_contrato"></span></p>
                        <p><strong>Fecha Fin Contrato:</strong><span id="modal-fecha_fin_contrato"></span></p>
                        <p><strong>Asignaturas:</strong><span id="modal-asignaturas"></span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            aria-label="Cerrar">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

    </main>
    <!--FOOTER-->
    <footer class="footer">
        <p>© 2025 StudyFlow - Todos los derechos reservados</p>
    </footer>

    <script src="../../../public/js/validacion.js"></script><!--Script para validaciones-->
    <script>
        //Función para hacer solicitudes AJAX
        function hacerSolicitud(url, metodo, datos, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open(metodo, url, true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    callback(xhr.status, xhr.responseText);
                }
            };
            xhr.send(datos ? JSON.stringify(datos) : null);
        }
        //Funcion para mostrar mensajes
        function mostrarMensaje(tipo, mensaje) {
            const elemento = document.getElementById(`${tipo}-message`);
            elemento.innerText = mensaje;
            elemento.style.display = 'block';
            setTimeout(() => elemento.style.display = 'none', 5000);
        }
        //funcion para limpiar los errores de la validación para evitar confusiones
        function limpiarErrores() {
            ['correo-error', 'contrasenia-error', 'nombre-error', 'apellidos-error', 'dni-error',
                'telefono-error', 'fecha-nacimiento-error', 'rol-error', 'sueldo-error',
                'jornada-error', 'fecha-inicio-contrato-error', 'fecha-fin-contrato-error',
                'asignaturas-error', 'error-message'].forEach(id => {
                    const elemento = document.getElementById(id);
                    elemento.textContent = '';
                    elemento.style.display = 'none';
                });
        }
        //funcion para cargar las asignaturas a asignar al prfesor en el select.
        function cargarAsignaturas(id_usuario = null) {
            const selectAsignaturas = document.getElementById("asignaturas");
            selectAsignaturas.innerHTML = '';
            // Solo se cargarán las no asignadas, pasandole el parametro no_asignadas a la api en la url de la solicitud
            hacerSolicitud('../../APIs/asignatura_api.php?no_asignadas=1', 'GET', null, function (status, response) {
                if (status === 200) {
                    let asignaturasNoAsignadas;
                    try {
                        asignaturasNoAsignadas = JSON.parse(response);
                    } catch (e) {
                        mostrarMensaje('error', 'Error al procesar las asignaturas no asignadas');
                        return;
                    }
                    asignaturasNoAsignadas.forEach(asignatura => {
                        const option = document.createElement('option');
                        option.value = asignatura.id_asignatura;
                        option.textContent = asignatura.nombre;
                        selectAsignaturas.appendChild(option);
                    });
                    // si hay valor para id_usuario , se filtra también por las asignaturas asignadas al profesor
                    if (id_usuario) {
                        hacerSolicitud(`../../APIs/asignatura_api.php?id_usuario=${id_usuario}`, 'GET', null, function (status, response) {
                            if (status === 200) {
                                let asignaturasAsignadas;
                                try {
                                    asignaturasAsignadas = JSON.parse(response);
                                } catch (e) {
                                    mostrarMensaje('error', 'Error al procesar las asignaturas asignadas');
                                    return;
                                }
                                asignaturasAsignadas.forEach(asignatura => {
                                    const option = document.createElement('option');
                                    option.value = asignatura.id_asignatura;
                                    option.textContent = asignatura.nombre;
                                    option.selected = true;
                                    selectAsignaturas.appendChild(option);
                                });
                            } else {
                                mostrarMensaje('error', 'Error al cargar las asignaturas asignadas');
                            }
                        });
                    }
                } else {
                    mostrarMensaje('error', 'Error al cargar las asignaturas no asignadas');
                }
            });
        }
        // funcion para crear un profesor
        function crearProfesor(event, datos) {
            event.preventDefault();
            const profesor = {
                correo: datos.correo,
                contrasenia: datos.contrasenia,
                nombre: datos.nombre,
                apellidos: datos.apellidos,
                DNI: datos.DNI,
                telefono: datos.telefono,
                fecha_nacimiento: datos.fecha_nacimiento || null,
                rol: datos.rol,
                sueldo: datos.sueldo || null,
                jornada: datos.jornada,
                fecha_inicio_contrato: datos.fecha_inicio_contrato || null,
                fecha_fin_contrato: datos.fecha_fin_contrato || null,
                asignaturas: datos.asignaturas,
                id_usuario: datos.id_usuario
            };

            const metodo = datos.id_usuario ? 'PUT' : 'POST';

            hacerSolicitud('../../APIs/profesor_api.php', metodo, profesor, function (status, response) {
                let res;
                try {
                    res = JSON.parse(response);
                } catch (e) {
                    mostrarMensaje('error', 'Respuesta inválida del servidor');
                    return;
                }

                limpiarErrores();

                if (status === 200 || status === 201) {
                    document.getElementById("profesor-form").reset();
                    document.getElementById("id_usuario").value = '';
                    document.getElementById("form-title").innerText = "Nuevo Profesor";
                    document.querySelector("button[type='submit']").textContent = "Crear";
                    cargarProfesores();
                    cargarAsignaturas();
                    mostrarMensaje('success', res.message || (metodo === 'PUT' ? 'Profesor actualizado exitosamente' : 'Profesor creado exitosamente'));
                } else if (status === 400 && res.duplicados) {
                    res.duplicados.forEach(campo => {
                        if (campo === 'correo') {
                            document.getElementById('correo-error').textContent = 'Correo ya registrado';
                            document.getElementById('correo-error').style.display = 'block';
                        } else if (campo === 'contrasenia') {
                            document.getElementById('contrasenia-error').textContent = 'Contraseña ya registrada';
                            document.getElementById('contrasenia-error').style.display = 'block';
                        } else if (campo === 'DNI') {
                            document.getElementById('dni-error').textContent = 'DNI ya registrado';
                            document.getElementById('dni-error').style.display = 'block';
                        }
                    });
                } else {
                    mostrarMensaje('error', res.message || (metodo === 'PUT' ? 'Error al actualizar el profesor' : 'Error al crear el profesor'));
                }
            });
        }
        //funcion para cargar los profesores, para cargarlos en la tabla
        function cargarProfesores() {
            hacerSolicitud('../../APIs/profesor_api.php', 'GET', null, function (status, response) {
                let profesores;
                try {
                    profesores = JSON.parse(response);
                } catch (e) {
                    mostrarMensaje('error', 'Respuesta inválida del servidor');
                    return;
                }

                if (status === 200) {
                    const listaProfesores = document.getElementById("profesores-lista");
                    listaProfesores.innerHTML = '';
                    profesores.forEach(profesor => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${profesor.nombre || 'N/A'}</td>
                            <td>${profesor.apellidos || 'N/A'}</td>
                            <td>${profesor.DNI || 'N/A'}</td>
                            <td>${profesor.telefono || 'N/A'}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-info btn-sm" onclick="mostrarInfo(${profesor.id_usuario})" aria-label="Mostrar Información">
                                        <i class="bi bi-info-circle"></i>
                                    </button>
                                    <button class="btn btn-success btn-sm" onclick="editarProfesor(${profesor.id_usuario})" aria-label="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="eliminarProfesor(${profesor.id_usuario})" aria-label="Eliminar">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        `;
                        listaProfesores.appendChild(row);
                    });
                } else {
                    mostrarMensaje('error', profesores.message || 'Error al cargar los profesores');
                }
            });
        }
        //funcion para cargar los datos de un profesor especifico en el modal
        function mostrarInfo(id_usuario) {
            hacerSolicitud(`../../APIs/profesor_api.php?id=${id_usuario}`, 'GET', null, function (status, response) {
                let res;
                try {
                    res = JSON.parse(response);
                } catch (e) {
                    mostrarMensaje('error', 'Respuesta inválida del servidor');
                    return;
                }

                if (status === 200) {
                    const profesor = res;
                    if (profesor) {
                        document.getElementById("modal-nombre").textContent = profesor.nombre || 'N/A';
                        document.getElementById("modal-apellidos").textContent = profesor.apellidos || 'N/A';
                        document.getElementById("modal-correo").textContent = profesor.correo || 'N/A';
                        document.getElementById("modal-contrasenia").textContent = profesor.contrasenia || 'N/A';
                        document.getElementById("modal-DNI").textContent = profesor.DNI || 'N/A';
                        document.getElementById("modal-telefono").textContent = profesor.telefono || 'N/A';
                        document.getElementById("modal-fecha_nacimiento").textContent = profesor.fecha_nacimiento || 'N/A';
                        document.getElementById("modal-rol").textContent = profesor.rol || 'N/A';
                        document.getElementById("modal-sueldo").textContent = profesor.sueldo || 'N/A';
                        document.getElementById("modal-jornada").textContent = profesor.jornada || 'N/A';
                        document.getElementById("modal-fecha_inicio_contrato").textContent = profesor.fecha_inicio_contrato || 'N/A';
                        document.getElementById("modal-fecha_fin_contrato").textContent = profesor.fecha_fin_contrato || 'N/A';
                        document.getElementById("modal-asignaturas").textContent = profesor.asignaturas && profesor.asignaturas.length > 0 ? profesor.asignaturas.map(a => a.nombre).join(', ') : 'N/A';

                        const modal = new bootstrap.Modal(document.getElementById("infoModal"));
                        modal.show();
                    }
                } else {
                    mostrarMensaje('error', res.message || 'Error al cargar los datos del profesor');
                }
            });
        }
        // funcion para eliminar un profesor
        function eliminarProfesor(id_usuario) {
            if (confirm('¿Estás seguro de que quieres eliminar este profesor?')) {
                const data = { id_usuario };
                hacerSolicitud('../../APIs/profesor_api.php', 'DELETE', data, function (status, response) {
                    let res;
                    try {
                        res = JSON.parse(response);
                    } catch (e) {
                        mostrarMensaje('error', 'Respuesta inválida del servidor');
                        return;
                    }
                    if (status === 200) {
                        cargarProfesores();
                        cargarAsignaturas();
                        mostrarMensaje('success', res.message || 'Profesor eliminado exitosamente');
                    } else {
                        mostrarMensaje('error', res.message || 'Error al eliminar el profesor');
                    }
                });
            }
        }
        //funcion para editar un profesor
        function editarProfesor(id_usuario) {
            hacerSolicitud(`../../APIs/profesor_api.php?id=${id_usuario}`, 'GET', null, function (status, response) {
                let res;
                try {
                    res = JSON.parse(response);
                } catch (e) {
                    mostrarMensaje('error', 'Respuesta inválida del servidor');
                    return;
                }
                if (status === 200) {
                    const profesor = res;
                    if (profesor) {
                        document.getElementById("correo").value = profesor.correo || '';
                        document.getElementById("contrasenia").value = profesor.contrasenia || '';
                        document.getElementById("nombre").value = profesor.nombre || '';
                        document.getElementById("apellidos").value = profesor.apellidos || '';
                        document.getElementById("DNI").value = profesor.DNI || '';
                        document.getElementById("telefono").value = profesor.telefono || '';
                        document.getElementById("fecha_nacimiento").value = profesor.fecha_nacimiento || '';
                        document.getElementById("rol").value = profesor.rol || 'profesor';
                        document.getElementById("sueldo").value = profesor.sueldo || '';
                        document.getElementById("jornada").value = profesor.jornada || 'tiempo_completo';
                        document.getElementById("fecha_inicio_contrato").value = profesor.fecha_inicio_contrato || '';
                        document.getElementById("fecha_fin_contrato").value = profesor.fecha_fin_contrato || '';
                        document.getElementById("id_usuario").value = profesor.id_usuario;

                        cargarAsignaturas(profesor.id_usuario);

                        document.getElementById("form-title").innerText = "Editar Profesor";
                        document.querySelector("button[type='submit']").textContent = "Actualizar";
                    }
                } else {
                    mostrarMensaje('error', res.message || 'Error al cargar los datos del profesor');
                }
            });
        }
        //Hacemos la validación y despues si es correcta , la creación del profesor
        document.getElementById("profesor-form").addEventListener("submit", async function (event) {
            event.preventDefault();
            //obtenemos los datos del formulario
            const datos = {
                id_usuario: document.getElementById("id_usuario").value,
                correo: document.getElementById("correo").value,
                contrasenia: document.getElementById("contrasenia").value,
                nombre: document.getElementById("nombre").value,
                apellidos: document.getElementById("apellidos").value,
                DNI: document.getElementById("DNI").value,
                telefono: document.getElementById("telefono").value,
                fecha_nacimiento: document.getElementById("fecha_nacimiento").value,
                rol: document.getElementById("rol").value,
                sueldo: document.getElementById("sueldo").value,
                jornada: document.getElementById("jornada").value,
                fecha_inicio_contrato: document.getElementById("fecha_inicio_contrato").value,
                fecha_fin_contrato: document.getElementById("fecha_fin_contrato").value,
                asignaturas: Array.from(document.getElementById("asignaturas").selectedOptions).map(opt => parseInt(opt.value))
            };
            console.log('ID Usuario:', datos.id_usuario);
            //usamos las reglas definidas en validacion.js para validar los datos introducidos
            const reglas = {
                correo: { validar: validarCorreo, errorId: "correo-error" },
                contrasenia: { validar: validarContrasenia, errorId: "contrasenia-error" },
                nombre: { validar: (valor) => validarTexto(valor, 2), errorId: "nombre-error" },
                apellidos: { validar: (valor) => validarTexto(valor, 2), errorId: "apellidos-error" },
                DNI: { validar: validarDNI, errorId: "dni-error" },
                telefono: { validar: validarTelefono, errorId: "telefono-error" },
                fecha_nacimiento: {
                    validar: (valor) => validarFechaNacimiento(valor, document.getElementById("rol").value),
                    errorId: "fecha-nacimiento-error"
                },
                rol: { validar: validarRol, errorId: "rol-error" },
                sueldo: { validar: validarSueldo, errorId: "sueldo-error" },
                jornada: { validar: validarJornada, errorId: "jornada-error" },
                fecha_inicio_contrato: { validar: validarFechaContratoInicio, errorId: "fecha-inicio-contrato-error" },
                fecha_fin_contrato: { validar: (valor, datos) => validarFechaContratoFin(valor, datos), errorId: "fecha-fin-contrato-error" },
                asignaturas: { validar: validarAsignaturas, errorId: "asignaturas-error" },
                duplicados: {
                    validar: () => validarDuplicadosProfesor(
                        datos.correo,
                        datos.contrasenia,
                        datos.DNI,
                        datos.id_usuario || null
                    ),
                    errorId: null
                }
            };

            limpiarErrores();

            const validation = await validarCampos(datos, reglas);
            let isValid = validation.isValid;
            //validacion de duplicados
            if (validation.errors.duplicados && Object.keys(validation.errors.duplicados).length > 0) {
                isValid = false;
                if (validation.errors.duplicados.correo) {
                    document.getElementById('correo-error').textContent = validation.errors.duplicados.correo;
                    document.getElementById('correo-error').style.display = 'block';
                }
                if (validation.errors.duplicados.contrasenia) {
                    document.getElementById('contrasenia-error').textContent = validation.errors.duplicados.contrasenia;
                    document.getElementById('contrasenia-error').style.display = 'block';
                }
                if (validation.errors.duplicados.DNI) {
                    document.getElementById('dni-error').textContent = validation.errors.duplicados.DNI;
                    document.getElementById('dni-error').style.display = 'block';
                }
                if (validation.errors.duplicados.general) {
                    document.getElementById('error-message').textContent = validation.errors.duplicados.general;
                    document.getElementById('error-message').style.display = 'block';
                }
            }

            if (isValid) {
                //si todo va bien , se crea el profesor
                crearProfesor(event, datos);
            } else {
                //sino, se muestran los mensajes de error en los campos
                Object.keys(validation.errors).forEach(field => {
                    if (field !== 'duplicados' && reglas[field].errorId && validation.errors[field]) {
                        document.getElementById(reglas[field].errorId).textContent = validation.errors[field];
                        document.getElementById(reglas[field].errorId).style.display = 'block';
                    }
                });
            }
        });
        //resetear el formualrio
        document.getElementById("profesor-form").addEventListener("reset", function () {
            document.getElementById("id_usuario").value = '';
            document.getElementById("form-title").innerText = "Nuevo Profesor";
            document.querySelector("button[type='submit']").textContent = "Crear";
            cargarAsignaturas();
            limpiarErrores();
        });
        //evento al iniciar la página
        window.onload = function () {
            cargarProfesores();
            cargarAsignaturas();
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>