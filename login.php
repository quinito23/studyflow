<?php
//empezamos la sesión para poder manejar los datos que guardamos en esta del usuario que se autentica
session_start();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <style>
        /*Estilo del cuerpo de la página */
        body {
            background-color: #2D2C55;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        /*Estilo de la tajeta de login */
        .login-card {
            max-width: 400px;
            width: 100%;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .login-card .card-header {
            background-color: #0d6efd;
            color: white;
            text-align: center;
            border-top-right-radius: 15px;
            border-top-left-radius: 15px;
        }

        .login-card .card-body {
            padding: 2rem;
        }

        .btn-primary {
            width: 100%;
            padding: 0.75rem;
            font-size: 1.1rem;
        }

        /*Estilo de las pestañas de navegación */
        .nav-tabs {
            justify-content: center;
            margin-bottom: 1rem;
            border-bottom: 1px solid #0d6efd;
        }

        .nav-tabs .nav-link {
            color: #f8f9fa;
            border: none;
            padding: 0.5rem 1rem;
            font-size: 1.1rem;
        }

        .nav-tabs .nav-link.active {
            color: #0d6efd;
            border-bottom: 2px solid #0d6efd;
            background-color: transparent;
        }

        /*Estilo del contenedor de las pestañas */
        .tab-content {
            width: 100%;
            max-width: 400px;
        }

        /*Estilo del mensaje de error */
        .error-message {
            color: #dc3545;
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }
    </style>
</head>

<body>
    <!--Definimos el contenedor donde se muestra notificaciones de exito o error al usuario-->
    <div id="notificacion" class="notificacion"></div>

    <!--Definimos las pestñas para cambiar entre el formulario de login y de resgitro-->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-tab-pane"
                type="button" role="tab" aria-controls="login-tab-pane" aria-selected="true">Iniciar Sesión</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="registro-tab" data-bs-toggle="tab" data-bs-target="#registro-tab-pane"
                type="button" role="tab" aria-controls="registro-tab-pane" aria-selected="false">Registro</button>
        </li>
    </ul>
    <!--Definimos el contenido de dichas pestañas-->
    <div class="tab-content" id="myTabContent">
        <!--Comenzamos con la de inicio de sesión-->
        <div class="tab-pane fade show active" id="login-tab-pane" role="tabpanel" aria-labelledby="login-tab"
            tabindex="0">
            <div class="card login-card">
                <div class="card-header">
                    <h3>Iniciar Sesión</h3>
                </div>
                <!--En el cuerpo creamos el formulario de inicio de sesión-->
                <div class="card-body">
                    <form id="login-form">
                        <div class="mb-3">
                            <label for="correo-login" class="form-label">Correo electronico</label>
                            <input type="email" class="form-control" id="correo-login" name="correo"
                                placeholder="Ingrese el correo electronico" required>
                        </div>
                        <div class="mb-3">
                            <label for="contrasenia-login" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="contrasenia-login" name="contrasenia"
                                placeholder="Ingrese la contraseña" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                    </form>
                </div>
            </div>
        </div>
        <!--Comenzamos con la de inicio de sesión-->
        <div class="tab-pane fade" id="registro-tab-pane" role="tabpanel" aria-labelledby="registro-tab" tabindex="0">
            <div class="card login-card">
                <div class="card-header">
                    <h3>Registro</h3>
                </div>
                <!--En el cuerpo creamos el formulario de inicio de sesión-->
                <div class="card-body">
                    <form id="registro-form">
                        <div class="mb-4">
                            <label for="correo-registro" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo-registro" name="correo"
                                placeholder="Ingrese el correo electrónico" required>
                            <div class="error-message" id="correo-error"></div>

                        </div>
                        <div class="mb-4">
                            <label for="contrasenia-registro" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="contrasenia-registro" name="contrasenia"
                                placeholder="Ingrese la contraseña" required>
                            <div class="error-message" id="contrasenia-error"></div>

                        </div>
                        <div class="mb-3">
                            <label for="nombre-registro" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre-registro" name="nombre"
                                placeholder="Ingrese su nombre" required>
                            <div class="error-message" id="nombre-error"></div>

                        </div>
                        <div class="mb-3">
                            <label for="apellidos-registro" class="form-label">Apellidos</label>
                            <input type="text" class="form-control" id="apellidos-registro" name="apellidos"
                                placeholder="Ingrese sus apellidos" required>
                            <div class="error-message" id="apellidos-error"></div>

                        </div>
                        <div class="mb-3">
                            <label for="telefono-registro" class="form-label">Telefono</label>
                            <input type="text" class="form-control" id="telefono-registro" name="telefono"
                                placeholder="Ingrese su teléfono" required>
                            <div class="error-message" id="telefono-error"></div>

                        </div>
                        <div class="mb-3">
                            <label for="dni-registro" class="form-label">DNI</label>
                            <input type="text" class="form-control" id="dni-registro" name="DNI"
                                placeholder="Ej. 12345678Z" required>
                            <div class="error-message" id="dni-error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="fecha-nacimiento-registro" class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="fecha-nacimiento-registro"
                                name="fecha_nacimiento" required>
                            <div class="error-message" id="fecha-nacimiento-error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="rol-registro" class="form-label">Rol propuesto</label>
                            <select class="form-control" id="rol-registro" name="rol_propuesto" required>
                                <option value="" disabled selected>Seleccione un rol</option>
                                <option value="alumno">Alumno</option>
                                <option value="profesor">Profesor</option>
                            </select>
                            <div class="error-message" id="rol-error"></div>

                        </div>
                        <div class="mb-3" id="asignaturas-section">
                            <label for="asignaturas-registro" class="form-label">Asignaturas</label>
                            <select class="form-control" id="asignaturas-registro" name="asignaturas" multiple>
                                <!--Las asignaturas son cargadas dinamicamente-->
                            </select>
                        </div>
                        <div class="error-message" id="asignaturas-error"></div>
                        <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Importamos el archivo de validaciones-->
    <script src="validacion.js"></script>
    <script>
        //Definimos la función necesaria para hacer solicitudes HTTP  a las APIs
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

        //función para mostrar la notificación al usuario, que incluye enlace a la pestaña de login en caso de que algún dato del registro sea duplicado
        function mostrarNotificacion(mensaje, esDuplicado = false) {
            const notificacion = document.getElementById('notificacion');
            notificacion.innerHTML = esDuplicado ? `${mensaje} <a href="#" onclick="document.getElementById('login-tab').click(); return false;">Inicia sesión</a>` : mensaje;
            notificacion.style.display = 'block';
            setTimeout(() => {
                notificacion.style.display = 'none';
            }, 5000);
        }
        //función para cargar las asignaturas disponibles para cargarlas en el select del formulario de registro
        function cargarAsignaturas() {
            hacerSolicitud('asignatura_api.php', 'GET', null, function (status, response) {
                try {
                    if (status === 200) {
                        const asignaturas = JSON.parse(response);
                        const selectAsignaturas = document.getElementById("asignaturas-registro");
                        selectAsignaturas.innerHTML = '';
                        asignaturas.forEach(asignatura => {
                            const option = document.createElement('option');
                            option.value = asignatura.id_asignatura;
                            option.textContent = asignatura.nombre;
                            selectAsignaturas.appendChild(option);
                        });
                    } else {
                        mostrarNotificacion("Error al cargar las asignaturas");
                    }
                } catch (e) {
                    mostrarNotificacion("Error al cargar las asignaturas");
                }
            });
        }


        //funcion para manejar el inicio de sesión. Hacemos solicitud a la API para que los compruebe con los de la base de datos y lo maneje
        function iniciarSesion(event) {
            event.preventDefault();

            const correo = document.getElementById("correo-login").value;
            const contrasenia = document.getElementById("contrasenia-login").value;

            const usuario = { correo: correo, contrasenia: contrasenia };

            hacerSolicitud('login_api.php', 'POST', usuario, function (status, response) {
                try {
                    const result = JSON.parse(response);
                    //bloque if para redirigir a una página u otra en función del rol del usuario que inicia sesión
                    if (result.message === "Login exitoso") {
                        if (result.rol === "administrador") {
                            window.location.href = 'dashboard.php';
                        } else if (result.rol === "alumno") {
                            window.location.href = 'PanelAlumno.php';
                        } else if (result.rol === "profesor") {
                            window.location.href = 'vista_reservas.php';
                        }
                    } else {
                        console.error("Error en login:", result.message);
                    }
                } catch (e) {
                    console.error("Error al parsear la respuesta:", e.message);
                }
            });
        }
        //función para manejar el registro de los nuevos usuarios y mandar los datos a la API
        function registrarAnonimo(event) {
            console.log("Función registrarAnonimo ejecutada");
            event.preventDefault();


            const correo = document.getElementById("correo-registro").value;
            const contrasenia = document.getElementById("contrasenia-registro").value;
            const nombre = document.getElementById("nombre-registro").value;
            const apellidos = document.getElementById("apellidos-registro").value;
            const telefono = document.getElementById("telefono-registro").value;
            const DNI = document.getElementById("dni-registro").value;
            const fecha_nacimiento = document.getElementById("fecha-nacimiento-registro").value;
            const rol_propuesto = document.getElementById("rol-registro").value;

            //Obtenemos las asignaturas seleccionadas por el usuario en el Select
            const selectedAsignaturas = document.getElementById("asignaturas-registro").selectedOptions;
            const asignaturas = Array.from(selectedAsignaturas).map(option => option.value);

            const datosRegistro = { correo, contrasenia, nombre, apellidos, telefono, DNI, fecha_nacimiento, rol_propuesto, asignaturas };
            console.log("Datos del registro:", datosRegistro);

            //hacemos la solicitud a la API con los datos recogidos
            hacerSolicitud('registro_api.php', 'POST', datosRegistro, function (status, response) {
                console.log("Status (registro_api.php):", status);
                console.log("Response (registro_api.php):", response);
                try {
                    const result = JSON.parse(response);
                    if (result.message === "Registro completado exitosamente") {
                        document.getElementById("registro-form").reset();
                    } else {
                        console.error("Error en registro:", result.message);
                    }
                } catch (e) {
                    console.error("Error al parsear la respuesta:", e.message);
                }
            });
        }

        document.getElementById("login-form").addEventListener("submit", iniciarSesion);
        // comenzamos la validación de los datos introducidos en el formulario de resgitro antes de hacer la solicitud
        document.getElementById("registro-form").addEventListener("submit", async function (event) {
            event.preventDefault();
            //recogemos los datos del formulario
            const datos = {
                correo: document.getElementById("correo-registro").value,
                contrasenia: document.getElementById("contrasenia-registro").value,
                nombre: document.getElementById("nombre-registro").value,
                apellidos: document.getElementById("apellidos-registro").value,
                telefono: document.getElementById("telefono-registro").value,
                DNI: document.getElementById("dni-registro").value,
                fecha_nacimiento: document.getElementById("fecha-nacimiento-registro").value,
                rol_propuesto: document.getElementById("rol-registro").value,
                asignaturas: Array.from(document.getElementById("asignaturas-registro").selectedOptions).map(opt => opt.value)
            };
            //definimos las reglas de validación para cada campo, usndo las funciones definidas en validacion.js
            const reglas = {
                correo: { validar: validarCorreo, errorId: "correo-error" },
                contrasenia: { validar: validarContrasenia, errorId: "contrasenia-error" },
                nombre: { validar: validarTexto, errorId: "nombre-error", minLength: 2 },
                apellidos: { validar: validarTexto, errorId: "apellidos-error", minLength: 2 },
                telefono: { validar: validarTelefono, errorId: "telefono-error" },
                DNI: { validar: validarDNI, errorId: "dni-error" },
                fecha_nacimiento: {
                    validar: (valor) => validarFechaNacimiento(valor, document.getElementById("rol-registro").value),
                    errorId: "fecha-nacimiento-error"
                },
                rol_propuesto: {
                    validar: (valor) => valor && ['alumno', 'profesor'].includes(valor) ? "" : "Seleccione un rol",
                    errorId: "rol-error"
                },
                asignaturas: { validar: validarAsignaturas, errorId: "asignaturas-error" },
                duplicados: { validar: validarDuplicados, errorId: "notificacion" }
            };

            const validation = await validarCampos(datos, reglas);
            if (validation.isValid) {
                // si todo está correcto, entonces se ejecuta la función que hace la solicitud
                registrarAnonimo(event);
            } else {
                //sino mostramos los errores de validación en los campos que han fallado
                Object.keys(validation.errors).forEach(field => {
                    if (field === 'duplicados') {
                        mostrarNotificacion(validation.errors[field], true);
                    } else {
                        document.getElementById(reglas[field].errorId).textContent = validation.errors[field];
                    }
                });
            }
        });

        //Limpiamos los mensajes de error al resetear el formulario para evitar confusiones
        document.getElementById("registro-form").addEventListener("reset", function () {
            ['correo-error', 'contrasenia-error', 'nombre-error', 'apellidos-error', 'telefono-error',
                'dni-error', 'fecha-nacimiento-error', 'rol-error', 'asignaturas-error'].forEach(id => {
                    document.getElementById(id).textContent = '';
                });
            document.getElementById('notificacion').style.display = 'none';
        });

        // al iniciar la página , se cargan las asignaturas del select
        window.onload = function () {
            cargarAsignaturas();
        }
    </script>
    <!--Script de bootstrap-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>