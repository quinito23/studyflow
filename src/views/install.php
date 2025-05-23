<?php
// Prevent access if already installed
if (file_exists('../../db/DBConnection.php')) {
    header('Location: ../../public/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación de StudyFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>

<body>
    <header class="header">
        <div class="d-flex align-items-center navbar-dark">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar"
                aria-label="Barra Lateral">
                <span class="navbar-toggler-icon"></span>
            </button>
            <img src="../../public/imagenes/StudyFlow3.svg" alt="logotipo" loading="lazy">
        </div>
        <div class="breadcrumb-container">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">Instalación</li>
                </ol>
            </nav>
        </div>
    </header>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasSidebarLabel">StudyFlow</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link active" href="install.php">Instalación</a></li>
            </ul>
        </div>
    </div>

    <main class="main-content">
        <h2 class="text-center">Instalación de StudyFlow</h2>
        <form id="install-form" class="row g-3">
            <div class="col-md-6">
                <label for="host" class="form-label">Host de la Base de Datos</label>
                <input type="text" class="form-control" id="host" name="host" value="localhost" required>
            </div>
            <div class="col-md-6">
                <label for="dbname" class="form-label">Nombre de la Base de Datos</label>
                <input type="text" class="form-control" id="dbname" name="dbname" value="studyflow" required>
            </div>
            <div class="col-md-6">
                <label for="username" class="form-label">Usuario de la Base de Datos</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="col-md-6">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="d-grid gap-2 d-md-block">
                <button class="btn btn-primary" type="submit" aria-label="Instalar">Instalar</button>
                <button type="reset" class="btn btn-light" aria-label="Limpiar Formulario">Limpiar</button>
            </div>
        </form>
        <div id="success-message" class="success-message"></div>
        <div id="error-message" class="error-message"></div>
    </main>

    <footer class="footer">
        <p>© 2025 StudyFlow - Todos los derechos reservados</p>
    </footer>

    <script>
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

        function mostrarMensaje(tipo, mensaje) {
            const elemento = document.getElementById(`${tipo}-message`);
            elemento.textContent = mensaje;
            elemento.style.display = 'block';
            setTimeout(() => {
                elemento.style.display = 'none';
            }, 5000);
        }

        document.getElementById('install-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const host = document.getElementById('host').value;
            const dbname = document.getElementById('dbname').value;
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const datos = { host, dbname, username, password };

            hacerSolicitud('../APIs/install_api.php', 'POST', datos, function (status, response) {
                const result = JSON.parse(response);
                if (status === 200 && result.success) {
                    mostrarMensaje('success', result.message || 'Instalación completada con éxito');
                    setTimeout(() => window.location.href = '../../public/index.php', 2000);
                } else {
                    mostrarMensaje('error', result.message || 'Error durante la instalación');
                }
            });
        });

        document.getElementById('install-form').addEventListener('reset', function () {
            document.getElementById('success-message').style.display = 'none';
            document.getElementById('error-message').style.display = 'none';
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>