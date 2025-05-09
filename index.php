<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #112a4a;
            color: #f8f9fa;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .header {
            background: linear-gradient(135deg, #007bff 0%, #0d1f38 100%);
            padding: 2rem 1rem;
            text-align: center;
        }

        .header h1 {
            font-size: clamp(2rem, 6vw, 4rem);
            color: #ffffff;
            margin: 0;
        }

        .header p {
            font-size: clamp(1rem, 3vw, 1.5rem);
            color: #d3d6db;
            margin: 0.5rem 0 1rem;
        }

        .info-section,
        .asignaturas-section {
            padding: 3rem 2rem;
            text-align: center;
            background-color: #0d1f38;
        }

        .info-section h2,
        .asignaturas-section h2 {
            font-size: clamp(1.5rem, 4vw, 2.5rem);
            color: #ffffff;
            margin-bottom: 2rem;
        }

        .info-section p {
            font-size: clamp(0.9rem, 2vw, 1.2rem);
            color: #d3d6db;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
        }

        .asignatura-card {
            background-color: #3b5e9c;
            border: 2px solid #007bff;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .asignatura-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 123, 255, 0.3);
        }

        .asignatura-card h3 {
            font-size: clamp(1rem, 3vw, 1.25rem);
            color: #ffffff;
        }

        .asignatura-card p {
            font-size: clamp(0.8rem, 2vw, 1rem);
            color: #d3d6db;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            font-size: clamp(0.8rem, 2vw, 1rem);
            border-radius: 25px;
            padding: 0.75rem 2rem;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .footer {
            background-color: #0d1f38;
            color: #f8f9fa;
            text-align: center;
            padding: 2rem;
            border-top: 1px solid #ffffff33;
            font-size: clamp(0.8rem, 2vw, 1rem);
        }

        .footer p {
            margin: 0.5rem 0;
        }
    </style>
</head>

<body>
    <header class="header">
        <h1>Academia StudyFlow</h1>
        <p>Tu lugar para aprender , mejorar y crecer.</p>
        <a href="login.php" class="btn btn-primary">Únete o Inicia Sesión</a>
    </header>

    <section class="info-section">
        <h2>Sobre Nosotros</h2>
        <p>Academia StudyFlow es un espacio dedicado a la educación de calidad.Ofrecemos cursos personalizados, un
            sistema para gestionar tareas y reservas de aulas ,y un ambiente colaborativo para alumnos y profesores</p>
    </section>

    <section class="asignaturas-section">
        <h2>Nuestros Cursos</h2>
        <div class="row" id="asignaturas-list">
            <!--Las asignaturas se cargarán aquí de manera dinámica-->
        </div>
    </section>

    <footer class="footer">
        <p><strong>Contáctanos</strong></p>
        <p>Estamos ubicados en: Calle Ejemplo 123, Ciudad Educativa</p>
        <p>Horario: Lunes a Viernes, 9:00 - 21:00</p>
        <p>Correo: <a href="mailto:info@studyflow.com" style="color: #007bff">info@studyflow.com</a></p>
        <p>Teléfono: <a href="tel:+1234567890" style="color: #007bff">+123 456 7890</a></p>
        <p>© 2025 Academia StudyFlow - Todos los derechos reservados</p>
    </footer>
    <script>
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

        function cargarAsignaturas() {
            hacerSolicitud('asignatura_api.php', 'GET', null, function (status, response) {
                try {
                    if (status === 200) {
                        const asignaturas = JSON.parse(response);
                        const listaAsignaturas = document.getElementById('asignaturas-list');
                        listaAsignaturas.innerHTML = '';
                        asignaturas.forEach(asignatura => {
                            const card = document.createElement('div');
                            card.className = 'col-md-4';
                            card.innerHTML = `
                                <div class="asignatura-card">
                                    <h3>${asignatura.nombre}</h3>
                                    <p>${asignatura.descripcion || 'Sin descripción disponible.'}</p>
                                </div>
                            `;
                            listaAsignaturas.appendChild(card);
                        });
                    } else {
                        console.error("Error al cargar los cursos:", response);
                    }
                } catch (e) {
                    console.error("Error al parsear la respuesta:", e.message);
                }
            });
        }

        window.onload = function () {
            cargarAsignaturas();
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>