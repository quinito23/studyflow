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
    <title>Grupos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../../public/css/styles.css">
</head>

<body>
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
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Grupos</li>
                </ol>
            </nav>
            <span class="separador">|</span>
            <a href="../logout.php" class=" btn btn-outline-light btn-sm logout-btn" title="Cerrar Sesión">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </header>

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
                <li class="nav-item"><a class="nav-link active" href="gestion_grupos.php">Grupos</a></li>
                <li class="nav-item"><a class="nav-link" href="gestion_reservas.php">Reservas</a></li>
                <li class="nav-item"><a class="nav-link" href="gestion_tareas.php">Tareas</a></li>
                <li class="nav-item"><a class="nav-link" href="gestion_solicitudes.php">Solicitudes</a></li>
            </ul>
        </div>
    </div>

    <main class="main-content">

        <h2 class="text-center">Gestión de Grupos</h2>

        <div id="grupo-form" class="container">
            <h3 id="form-title">Crear Grupo</h3>
            <form id="form-grupo">
                <input type="hidden" id="id_grupo" name="id_grupo">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                    <div class="error-message" id="nombre-error"></div>
                </div>
                <div class="mb-3">
                    <label for="capacidad_maxima" class="form-label">Capacidad Máxima</label>
                    <input type="number" class="form-control" id="capacidad_maxima" name="capacidad_maxima" required>
                    <div class="error-message" id="capacidad-maxima-error"></div>
                </div>
                <div class="mb-3">
                    <label for="id_asignatura" class="form-label">Asignatura</label>
                    <select class="form-control" id="id_asignatura" name="id_asignatura" required>
                        <option value="">-- Selecciona una asignatura --</option>
                    </select>
                    <div class="error-message" id="id-asignatura-error"></div>
                </div>
                <button type="submit" class="btn btn-primary" aria-label="Crear">Crear</button>
                <button type="reset" class="btn btn-light" aria-label="Limpiar Formulario">Limpiar</button>
            </form>
        </div>
        <div id="success-message" class="success-message"></div>
        <div id="error-message" class="error-message"></div>

        <h3 class="text-center">Lista de Grupos</h3>
        <div class="table-responsive">
            <table class="table" id="tabla-grupos">
                <thead>
                    <tr class="table-primary">
                        <th scope="col">Nombre</th>
                        <th scope="col">Capacidad Máxima</th>
                        <th scope="col">Número de Alumnos</th>
                        <th scope="col">Asignatura</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody id="grupos-lista"></tbody>
            </table>
        </div>

    </main>

    <footer class="footer">
        <p>© 2025 StudyFlow - Todos los derechos reservados</p>
    </footer>

    <script src="../../../public/js/validacion.js"></script>
    <script>

        function mostrarMensaje(tipo, mensaje) {
            const elemento = document.getElementById(`${tipo}-message`);
            elemento.textContent = mensaje;
            elemento.style.display = 'block';
            setTimeout(() => {
                elemento.style.display = 'none';
            }, 5000);
        }

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

        function cargarAsignaturas(id_asignatura = null) {
            const selectAsignaturas = document.getElementById('id_asignatura');
            selectAsignaturas.innerHTML = '<option value="">-- Selecciona una asignatura --</option>';

            // Cargar asignaturas no asignadas a grupos
            hacerSolicitud('../../APIs/asignatura_api.php?sin_grupo=1', 'GET', null, function (status, response) {
                if (status === 200) {
                    const asignaturas = JSON.parse(response);
                    asignaturas.forEach(asignatura => {
                        const option = document.createElement('option');
                        option.value = asignatura.id_asignatura;
                        option.textContent = asignatura.nombre;
                        selectAsignaturas.appendChild(option);
                    });

                    // Si se proporciona id_asignatura, cargar también la asignatura asignada
                    if (id_asignatura) {
                        hacerSolicitud(`../../APIs/asignatura_api.php?id=${id_asignatura}`, 'GET', null, function (status, response) {
                            if (status === 200) {
                                const asignatura = JSON.parse(response);
                                const option = document.createElement('option');
                                option.value = asignatura.id_asignatura;
                                option.textContent = asignatura.nombre;
                                option.selected = true; // Preseleccionar la asignatura asignada
                                selectAsignaturas.appendChild(option);
                            } else {
                                mostrarMensaje('error', 'Error al cargar la asignatura asignada');
                            }
                        });
                    }
                } else {
                    mostrarMensaje('error', 'Error al cargar las asignaturas');
                }
            });
        }

        function cargarGrupos() {
            hacerSolicitud('../../APIs/grupo_api.php', 'GET', null, function (status, response) {
                if (status === 200) {
                    const grupos = JSON.parse(response);
                    const listaGrupos = document.getElementById("grupos-lista");
                    listaGrupos.innerHTML = '';

                    grupos.forEach(grupo => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${grupo.nombre}</td>
                            <td>${grupo.capacidad_maxima}</td>
                            <td>${grupo.numero_alumnos}</td>
                            <td>${grupo.nombre_asignatura}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-success btn-sm" onclick="editarGrupo(${grupo.id_grupo})" aria-label="Editar">
                                        <i class="bi bi-pencil-square"></i> 
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="eliminarGrupo(${grupo.id_grupo})" aria-label="Eliminar">
                                        <i class="bi bi-trash3"></i> 
                                    </button> 
                                </div>
                            </td>
                        `;
                        listaGrupos.appendChild(row);
                    });
                } else {
                    mostrarMensaje('error', 'Error al cargar los grupos');
                }
            });
        }

        function crearGrupo(event) {
            event.preventDefault();

            const id_grupo = document.getElementById("id_grupo").value;
            const nombre = document.getElementById("nombre").value;
            const capacidad_maxima = parseInt(document.getElementById("capacidad_maxima").value);
            const id_asignatura = parseInt(document.getElementById("id_asignatura").value);

            const grupo = { id_grupo, nombre, capacidad_maxima, id_asignatura };

            if (id_grupo) {
                hacerSolicitud('../../APIs/grupo_api.php', 'PUT', grupo, function (status, response) {
                    const result = JSON.parse(response);
                    if (status === 200) {
                        document.getElementById("form-grupo").reset();
                        document.getElementById("id_grupo").value = '';
                        document.getElementById("form-title").innerText = "Crear Grupo";
                        document.querySelector("button[type='submit']").textContent = "Crear Grupo";
                        cargarGrupos();
                        cargarAsignaturas(); // Volver a cargar solo asignaturas no asignadas
                        mostrarMensaje('success', result.message || 'Grupo actualizado exitosamente');
                    } else {
                        mostrarMensaje('error', result.message || 'Error al actualizar el grupo');
                    }
                });
            } else {
                hacerSolicitud('../../APIs/grupo_api.php', 'POST', grupo, function (status, response) {
                    const result = JSON.parse(response);
                    if (status === 200) {
                        document.getElementById("form-grupo").reset();
                        cargarGrupos();
                        cargarAsignaturas(); // Volver a cargar solo asignaturas no asignadas
                        mostrarMensaje('success', result.message || 'Grupo creado exitosamente');
                    } else {
                        mostrarMensaje('error', result.message || 'Error al crear el grupo');
                    }
                });
            }
        }

        function editarGrupo(id_grupo) {
            hacerSolicitud(`../../APIs/grupo_api.php?id=${id_grupo}`, 'GET', null, function (status, response) {
                if (status === 200) {
                    const grupo = JSON.parse(response);
                    if (grupo) {
                        document.getElementById("nombre").value = grupo.nombre;
                        document.getElementById("capacidad_maxima").value = grupo.capacidad_maxima;
                        document.getElementById("id_grupo").value = grupo.id_grupo;
                        cargarAsignaturas(grupo.id_asignatura); // Cargar asignaturas con la asignatura asignada preseleccionada
                        document.getElementById("form-title").innerText = "Editar Grupo";
                        document.querySelector("button[type='submit']").textContent = "Actualizar";
                    }
                } else {
                    mostrarMensaje('error', 'Error al cargar los datos del grupo');
                }
            });
        }

        function eliminarGrupo(id_grupo) {
            if (confirm('¿Estás seguro de que quieres eliminar este grupo?')) {
                const data = { "id_grupo": id_grupo };
                hacerSolicitud('../../APIs/grupo_api.php', 'DELETE', data, function (status, response) {
                    const result = JSON.parse(response);
                    if (status === 200) {
                        cargarGrupos();
                        cargarAsignaturas(); // Volver a cargar solo asignaturas no asignadas
                        mostrarMensaje('success', result.message || 'Grupo eliminado exitosamente');
                    } else {
                        mostrarMensaje('error', result.message || 'Error al eliminar el grupo');
                    }
                });
            }
        }

        // Validación y envío del formulario
        document.getElementById("form-grupo").addEventListener("submit", async function (event) {
            event.preventDefault();

            const datos = {
                nombre: document.getElementById("nombre").value,
                capacidad_maxima: document.getElementById("capacidad_maxima").value,
                id_asignatura: document.getElementById("id_asignatura").value
            };

            const reglas = {
                nombre: {
                    validar: (valor) => validarTexto(valor, 2),
                    errorId: "nombre-error"
                },
                capacidad_maxima: {
                    validar: validarCapacidad,
                    errorId: "capacidad-maxima-error"
                },
                id_asignatura: {
                    validar: (valor) => valor ? "" : "Seleccione una asignatura",
                    errorId: "id-asignatura-error"
                }
            };

            const validation = await validarCampos(datos, reglas);
            if (validation.isValid) {
                crearGrupo(event);
            } else {
                Object.keys(validation.errors).forEach(field => {
                    document.getElementById(reglas[field].errorId).textContent = validation.errors[field];
                });
            }
        });


        //evento para resetear el formulario
        document.getElementById("form-grupo").addEventListener("reset", function () {
            document.getElementById("id_grupo").value = '';
            document.getElementById("form-title").innerText = "Nuevo Grupo";
            document.querySelector("button[type='submit']").textContent = "Crear";
            cargarAsignaturas();
            cargarGrupos();
        });

        window.onload = function () {
            cargarAsignaturas();
            cargarGrupos();
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>