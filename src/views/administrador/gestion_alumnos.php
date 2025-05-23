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
    <title>Alumnos</title>
    <link rel="stylesheet" href="../../../public/css/styles.css">
</head>

<body>
    <!--Header de bootstraps-->
    <header class="header">
        <div class="d-flex align-items-center navbar-dark">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar"
                aria-label="Barra lateral">
                <span class="navbar-toggler-icon"></span>
            </button>
            <img src="../../../public/imagenes/StudyFlow3.svg" alt="logotipo" loading="lazy">
        </div>
        <!--Breadcrumb-->
        <div class="breadcrumb-container">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Alumnos</li>
                </ol>
            </nav>
            <span class="separador">|</span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm logout-btn" title="Cerrar Sesión"
                aria-label="Cerrar Sesión">
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
                <li class="nav-item"><a class="nav-link" href="gestion_profesores.php">Profesores</a></li>
                <li class="nav-item"><a class="nav-link active" href="gestion_alumnos.php">Alumnos</a></li>
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

        <h2 class="text-center" id="form-title">Nuevo Alumno</h2>
        <form id="alumno-form" class="row g-3">
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
                <input type="date" class="form-control" id="fecha_nacimiento" required>
                <span id="fecha-nacimiento-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="rol" class="form-label">Rol</label>
                <select id="rol" class="form-select">
                    <option value="administrador">Administrador</option>
                    <option value="profesor">Profesor</option>
                    <option value="alumno" selected>Alumno</option>
                </select>
                <span id="rol-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="tutor" class="form-label">Tutor Legal</label>
                <select id="tutor" class="form-select" multiple>
                    <!-- Opciones cargadas dinámicamente -->
                </select>
                <span id="tutor-error" class="error-text"></span>
            </div>
            <div class="col-md-6">
                <label for="grupo" class="form-label">Grupos</label>
                <select id="grupo" class="form-select" multiple>
                    <!-- grupos cargados dinámicamente -->
                </select>
                <span id="grupo-error" class="error-text"></span>
            </div>
            <input type="hidden" id="id_usuario">
            <div class="d-grid gap-2 d-md-block">
                <button class="btn btn-primary" type="submit" aria-label="Crear">Crear</button>
                <button type="reset" class="btn btn-light" aria-label="Limpiar formulario">Limpiar</button>
            </div>
        </form>
        <div id="success-message" class="success-message"></div>
        <div id="error-message" class="error-message"></div>

        <h2 class="text-center">Alumnos</h2>
        <div class="table-responsive">
            <table class="table" id="tabla-alumnos">
                <thead>
                    <tr class="table-primary">
                        <th scope="col">Nombre</th>
                        <th scope="col">Apellidos</th>
                        <th scope="col">DNI</th>
                        <th scope="col">Teléfono</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody id="alumnos-lista">
                    <!--Aquí se cargan dinamicamente los alumnos-->
                </tbody>
            </table>
        </div>

        <!--Modal que se abre con la información del alumno-->
        <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="infoModalLabel">Información del Alumno</h5>
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
                        <p><strong>Tutor Legal:</strong><span id="modal-tutores"></span></p>
                        <p><strong>Grupos:</strong><span id="modal-grupo"></span></p>
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
        <p>© 2025 StudyFlow - Todos los derechos reservados</p>
    </footer>

    <script src="../../../public/js/validacion.js"></script> <!--Script para la validación-->
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
        //función para mostrar mensajes
        function mostrarMensaje(tipo, mensaje) {
            const elemento = document.getElementById(`${tipo}-message`);
            elemento.innerText = mensaje;
            elemento.style.display = 'block';
            setTimeout(() => elemento.style.display = 'none', 5000);
        }
        //funcion para limpiar errores
        function limpiarErrores() {
            ['correo-error', 'contrasenia-error', 'nombre-error', 'apellidos-error', 'dni-error',
                'telefono-error', 'fecha-nacimiento-error', 'rol-error', 'tutor-error', 'error-message'].forEach(id => {
                    const elemento = document.getElementById(id);
                    elemento.textContent = '';
                    elemento.style.display = 'none';
                });
        }

        //función para cargar los autores de la base de datos y mostrarlos en el select
        function cargarTutores(id_usuario = null) {
            const selectTutores = document.getElementById("tutor");
            selectTutores.innerHTML = '';

            hacerSolicitud('../../APIs/tutor_api.php', 'GET', null, function (status, response) {
                if (status === 200) {
                    let tutores;
                    try {
                        tutores = JSON.parse(response);
                    } catch (e) {
                        mostrarMensaje('error', 'Error al procesar los tutores');
                        return;
                    }
                    tutores.forEach(tutor => {
                        const option = document.createElement('option');
                        option.value = tutor.id_tutor;
                        option.textContent = `${tutor.nombre} ${tutor.apellidos}`;
                        selectTutores.appendChild(option);
                    });

                    if (id_usuario) {
                        hacerSolicitud(`../../APIs/alumno_api.php?id=${id_usuario}`, 'GET', null, function (status, response) {
                            if (status === 200) {
                                let alumno;
                                try {
                                    alumno = JSON.parse(response);
                                } catch (e) {
                                    mostrarMensaje('error', 'Error al procesar los datos del alumno');
                                    return;
                                }
                                alumno.tutores.forEach(tutor => {
                                    const options = selectTutores.options;
                                    for (let option of options) {
                                        if (option.value == tutor.id_tutor) {
                                            option.selected = true;
                                        }
                                    }
                                });
                            } else {
                                mostrarMensaje('error', 'Error al cargar los tutores asignados');
                            }
                        });
                    }
                } else {
                    mostrarMensaje('error', 'Error al cargar los tutores');
                }
            });
        }

        //funcion para cargar los grupos en el select
        function cargarGrupos(id_usuario = null) {
            const selectGrupos = document.getElementById("grupo");
            selectGrupos.innerHTML = ''; // Limpiar opciones existentes

            hacerSolicitud('../../APIs/grupo_api.php', 'GET', null, function (status, response) {
                if (status === 200) {
                    let grupos;
                    try {
                        grupos = JSON.parse(response);
                    } catch (e) {
                        mostrarMensaje('error', 'Error al procesar los grupos');
                        return;
                    }
                    grupos.forEach(grupo => {
                        const option = document.createElement('option');
                        option.value = grupo.id_grupo;
                        option.textContent = grupo.nombre;
                        selectGrupos.appendChild(option);
                    });

                    // Si se está editando un alumno, preseleccionar todos los grupos asociados
                    if (id_usuario) {
                        hacerSolicitud(`../../APIs/alumno_api.php?id=${id_usuario}`, 'GET', null, function (status, response) {
                            if (status === 200) {
                                let alumno;
                                try {
                                    alumno = JSON.parse(response);
                                } catch (e) {
                                    mostrarMensaje('error', 'Error al procesar los datos del alumno');
                                    return;
                                }
                                // Marcar como seleccionados todos los grupos asociados
                                if (alumno.grupos && alumno.grupos.length > 0) {
                                    const options = selectGrupos.options;
                                    for (let option of options) {
                                        if (alumno.grupos.some(grupo => parseInt(grupo.id_grupo) == parseInt(option.value))) {
                                            option.selected = true;
                                        }
                                    }
                                }
                            } else {
                                mostrarMensaje('error', 'Error al cargar los grupos asignados');
                            }
                        });
                    }
                } else {
                    mostrarMensaje('error', 'Error al cargar los grupos');
                }
            });
        }


        //funcion para crear un alumno con validacion de duplicados
        function crearAlumno(event, datos) {
            event.preventDefault();
            const alumno = {
                correo: datos.correo,
                contrasenia: datos.contrasenia,
                nombre: datos.nombre,
                apellidos: datos.apellidos,
                DNI: datos.DNI,
                telefono: datos.telefono,
                fecha_nacimiento: datos.fecha_nacimiento || null,
                rol: datos.rol,
                tutores: datos.tutores,
                grupos: datos.grupos,
                id_usuario: datos.id_usuario
            };

            const metodo = datos.id_usuario ? 'PUT' : 'POST';

            hacerSolicitud('../../APIs/alumno_api.php', metodo, alumno, function (status, response) {
                let res;
                try {
                    res = JSON.parse(response);
                } catch (e) {
                    mostrarMensaje('error', 'Respuesta inválida del servidor');
                    return;
                }

                limpiarErrores();

                if (status === 200 || status === 201) {
                    document.getElementById("alumno-form").reset();
                    document.getElementById("id_usuario").value = '';
                    document.getElementById("form-title").innerText = "Nuevo Alumno";
                    document.querySelector("button[type='submit']").textContent = "Crear";
                    cargarAlumnos();
                    cargarTutores();
                    cargarGrupos();
                    mostrarMensaje('success', res.message || (metodo === 'PUT' ? 'Alumno actualizado exitosamente' : 'Alumno creado exitosamente'));
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
                    mostrarMensaje('error', res.message || (metodo === 'PUT' ? 'Error al actualizar el alumno' : 'Error al crear el alumno'));
                }
            });
        }
        //funcion para cargar alumnos en la tabla
        function cargarAlumnos() {
            hacerSolicitud('../../APIs/alumno_api.php', 'GET', null, function (status, response) {
                let alumnos;
                try {
                    alumnos = JSON.parse(response);
                } catch (e) {
                    mostrarMensaje('error', 'Respuesta inválida del servidor');
                    return;
                }

                if (status === 200) {
                    const listaAlumnos = document.getElementById("alumnos-lista");
                    listaAlumnos.innerHTML = '';
                    alumnos.forEach(alumno => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${alumno.nombre || 'N/A'}</td>
                            <td>${alumno.apellidos || 'N/A'}</td>
                            <td>${alumno.DNI || 'N/A'}</td>
                            <td>${alumno.telefono || 'N/A'}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-info btn-sm" onclick="mostrarInfo(${alumno.id_usuario})" aria-label="Ver información">
                                        <i class="bi bi-info-circle"></i>
                                    </button>
                                    <button class="btn btn-success btn-sm" onclick="editarAlumno(${alumno.id_usuario})" aria-label="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="eliminarAlumno(${alumno.id_usuario})" aria-label="Eliminar">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        `;
                        listaAlumnos.appendChild(row);
                    });
                } else {
                    mostrarMensaje('error', alumnos.message || 'Error al cargar los alumnos');
                }
            });
        }
        //funcion para mostrar la informacion de un alumno en el modal
        function mostrarInfo(id_usuario) {
            hacerSolicitud(`../../APIs/alumno_api.php?id=${id_usuario}`, 'GET', null, function (status, response) {
                let res;
                try {
                    res = JSON.parse(response);
                } catch (e) {
                    mostrarMensaje('error', 'Respuesta inválida del servidor');
                    return;
                }

                if (status === 200) {
                    const alumno = res;
                    if (alumno) {
                        document.getElementById("modal-nombre").textContent = alumno.nombre || 'N/A';
                        document.getElementById("modal-apellidos").textContent = alumno.apellidos || 'N/A';
                        document.getElementById("modal-correo").textContent = alumno.correo || 'N/A';
                        document.getElementById("modal-contrasenia").textContent = alumno.contrasenia || 'N/A';
                        document.getElementById("modal-DNI").textContent = alumno.DNI || 'N/A';
                        document.getElementById("modal-telefono").textContent = alumno.telefono || 'N/A';
                        document.getElementById("modal-fecha_nacimiento").textContent = alumno.fecha_nacimiento || 'N/A';
                        document.getElementById("modal-rol").textContent = alumno.rol || 'N/A';
                        document.getElementById("modal-tutores").textContent = alumno.tutores && alumno.tutores.length > 0 ?
                            alumno.tutores.map(tutor => `${tutor.nombre} ${tutor.apellidos}`).join(', ') : 'N/A';
                        document.getElementById("modal-grupo").textContent = alumno.grupos && alumno.grupos.length > 0 ?
                            alumno.grupos.map(grupo => grupo.nombre).join(', ') : 'N/A';

                        const modal = new bootstrap.Modal(document.getElementById("infoModal"));
                        modal.show();
                    }
                } else {
                    mostrarMensaje('error', res.message || 'Error al cargar los datos del alumno');
                }
            });
        }
        //funcion para eliminar un alumno
        function eliminarAlumno(id_usuario) {
            if (confirm('¿Estás seguro de que quieres eliminar este alumno?')) {
                const data = { id_usuario };
                hacerSolicitud('../../APIs/alumno_api.php', 'DELETE', data, function (status, response) {
                    let res;
                    try {
                        res = JSON.parse(response);
                    } catch (e) {
                        mostrarMensaje('error', 'Respuesta inválida del servidor');
                        return;
                    }
                    if (status === 200) {
                        cargarAlumnos();
                        cargarTutores();
                        cargarGrupos();
                        mostrarMensaje('success', res.message || 'Alumno eliminado exitosamente');
                    } else {
                        mostrarMensaje('error', res.message || 'Error al eliminar el alumno');
                    }
                });
            }
        }
        //funcion para editar un alumno
        function editarAlumno(id_usuario) {
            hacerSolicitud(`../../APIs/alumno_api.php?id=${id_usuario}`, 'GET', null, function (status, response) {
                let res;
                try {
                    res = JSON.parse(response);
                } catch (e) {
                    mostrarMensaje('error', 'Respuesta inválida del servidor');
                    return;
                }
                if (status === 200) {
                    const alumno = res;
                    if (alumno) {
                        document.getElementById("correo").value = alumno.correo || '';
                        document.getElementById("contrasenia").value = alumno.contrasenia || '';
                        document.getElementById("nombre").value = alumno.nombre || '';
                        document.getElementById("apellidos").value = alumno.apellidos || '';
                        document.getElementById("DNI").value = alumno.DNI || '';
                        document.getElementById("telefono").value = alumno.telefono || '';
                        document.getElementById("fecha_nacimiento").value = alumno.fecha_nacimiento || '';
                        document.getElementById("rol").value = alumno.rol || 'alumno';
                        document.getElementById("id_usuario").value = alumno.id_usuario;
                        // Cargar tutores y grupos, pasando el id_usuario para preseleccionar los valores
                        cargarTutores(alumno.id_usuario);
                        cargarGrupos(alumno.id_usuario);

                        document.getElementById("form-title").innerText = "Editar Alumno";
                        document.querySelector("button[type='submit']").textContent = "Actualizar";
                    }
                } else {
                    mostrarMensaje('error', res.message || 'Error al cargar los datos del alumno');
                }
            });
        }

        //validacion de campos y de duplicados y posteriormente ejecución de la creación del alumno
        document.getElementById("alumno-form").addEventListener("submit", async function (event) {
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
                tutores: Array.from(document.getElementById("tutor").selectedOptions).map(opt => parseInt(opt.value)),
                grupos: Array.from(document.getElementById("grupo").selectedOptions).map(opt => parseInt(opt.value))
            };
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
                tutores: { validar: validarTutores, errorId: "tutor-error" },
                duplicados: {
                    validar: () => validarDuplicadosAlumno(
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
                crearAlumno(event, datos);
                //si todo va bien , se crea el profesor
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
        //evento para resetear el formulario
        document.getElementById("alumno-form").addEventListener("reset", function () {
            document.getElementById("id_usuario").value = '';
            document.getElementById("form-title").innerText = "Nuevo Alumno";
            document.querySelector("button[type='submit']").textContent = "Crear";
            cargarTutores();
            cargarGrupos();
            limpiarErrores();
        });

        window.onload = function () {
            cargarAlumnos();
            cargarTutores();
            cargarGrupos();
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>