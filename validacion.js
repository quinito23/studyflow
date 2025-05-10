function validarContrasenia(contrasenia) {
    if (!contrasenia) return "Contraseña requerida";
    if (contrasenia.length < 8) return "Mínimo 8 caracteres";
    if (!/[A-Z]/.test(contrasenia)) return "Debe incluir una mayúscula";
    if (!/[a-zA-Z]/.test(contrasenia)) return "Debe incluir una letra";
    if (!/[0-9]/.test(contrasenia)) return "Debe incluir un número";
    return "";
}

function validarTelefono(telefono) {
    if (!telefono) return "Teléfono requerido";
    const regex = /^\+?\d{1,3}?\s?\d{9}$/;
    return regex.test(telefono) ? "" : "Teléfono inválido (ej. +34 123 456 789)";
}

function validarDNI(dni) {
    if (!dni) return "DNI requerido";
    const regex = /^\d{8}[A-Z]$/;
    if (!regex.test(dni)) return "DNI debe ser 8 dígitos y una letra (ej. 12345678Z)";
    const numero = parseInt(dni.substring(0, 8));
    const letra = dni.substring(8).toUpperCase();
    const letras = 'TRWAGMYFPDXBNJZSQVHLCKE';
    const letraCorrecta = letras[numero % 23];
    return letra === letraCorrecta ? "" : "Letra del DNI incorrecta";
}

function validarFechaNacimiento(fecha, rol) {
    if (!fecha) return "Fecha de nacimiento requerida";
    const fechaNac = new Date(fecha);
    const hoy = new Date('2025-05-10');
    const edad = hoy.getFullYear() - fechaNac.getFullYear() - (hoy.getMonth() < fechaNac.getMonth() || (hoy.getMonth() === fechaNac.getMonth() && hoy.getDate() < fechaNac.getDate()) ? 1 : 0);
    const minEdad = rol === "profesor" ? 18 : 12;
    if (isNaN(fechaNac.getTime())) return "Fecha inválida";
    if (edad < minEdad) return `Edad mínima es ${minEdad} años para ${rol}`;
    return "";
}

function validarCorreo(correo) {
    if (!correo) return "Correo requerido";
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo) ? "" : "Correo inválido";
}

function validarTexto(texto, minLength = 1) {
    if (!texto) return "Campo requerido";
    if (texto.trim().length < minLength) return `Mínimo ${minLength} caracteres`;
    return "";
}

function validarAsignaturas(asignaturas) {
    if (!asignaturas || asignaturas.length === 0) return "Seleccione al menos una asignatura";
    return "";
}

function validarTexto(texto, minLength = 1) {
    if (!texto) return "Campo requerido";
    if (texto.trim().length < minLength) return `Mínimo ${minLength} caracteres`;
    return "";
}

function validarCapacidad(capacidad) {
    if (!capacidad) return "Capacidad requerida";
    const num = parseInt(capacidad);
    if (isNaN(num)) return "Debe ser un número";
    if (num < 5) return "Mínimo 5 estudiantes";
    if (num > 30) return "Máximo 30 estudiantes";
    return "";
}

function validarFecha(fecha) {
    if (!fecha) return "Fecha requerida";
    const fechaSeleccionada = new Date(fecha);
    const hoy = new Date('2025-05-10');
    hoy.setHours(0, 0, 0, 0); // Normalizar a medianoche
    if (isNaN(fechaSeleccionada.getTime())) return "Fecha inválida";
    if (fechaSeleccionada < hoy) return "La fecha no puede ser anterior a hoy";
    return "";
}

function validarHorarioReserva(horaInicio, horaFin) {
    if (!horaInicio || !horaFin) return "Ambas horas son requeridas";
    
    const [inicioHoras, inicioMinutos] = horaInicio.split(':').map(Number);
    const [finHoras, finMinutos] = horaFin.split(':').map(Number);
    
    const inicioTotal = inicioHoras * 60 + inicioMinutos;
    const finTotal = finHoras * 60 + finMinutos;
    
    const minHora = 9 * 60; // 09:00 en minutos
    const maxHora = 21 * 60; // 21:00 en minutos
    
    if (inicioTotal < minHora || inicioTotal > maxHora) {
        return "La hora de inicio debe estar entre las 09:00 AM y las 20:00 PM";
    }
    if (finTotal < minHora || finTotal > maxHora) {
        return "La hora de finalización debe estar desde a partir de las 09:00 AM hasta las 21:00";
    }
    if (finTotal <= inicioTotal) {
        return "La hora de finalización debe ser posterior a la de inicio";
    }
}

function validarFechaEntrega(fechaEntrega) {
    if (!fechaEntrega) return "Fecha de entrega requerida";
    const fechaSeleccionada = new Date(fechaEntrega);
    const ahora = new Date('2025-05-10T00:00:00'); // Ajustar según la hora actual del cliente
    if (isNaN(fechaSeleccionada.getTime())) return "Fecha inválida";
    if (fechaSeleccionada <= ahora) return "La fecha de entrega debe ser posterior al momento actual";
    return "";
}

async function validarDuplicados(correo, contrasenia, DNI){
    try{
        const response = await new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            const params = new URLSearchParams();
            if(correo) params.append('correo', correo);
            if(contrasenia) params.append('contrasenia', contrasenia);
            if(DNI) params.append('DNI', DNI);
            xhr.open('GET', `registro_api.php?${params.toString()}`, true);
            xhr.onreadystatechange = function(){
                if(xhr.readyState === 4){
                    if(xhr.status === 200){
                        resolve(JSON.parse(xhr.responseText));
                    }else{
                        reject(new Error('Error en la solicitud'));
                    }
                }
            };
            xhr.send();
        });

        if(response.duplicados && response.duplicados.length > 0){
            return "Ya existe un usuario/solicitud con ese correo, contraseña o DNI";
        }
        return "";
    }catch(e){
        return "Error al verificar duplicados";
    }
}

async function validarCampos(datos, reglas) {
    const errors = {};
    let isValid = true;

    //validaciones sincronas
    for (let campo in reglas) {
        if(campo !== 'duplicados'){
            const regla = reglas[campo];
            const valor = datos[campo];
            errors[campo] = regla.validar(valor, datos);
            if (errors[campo]) isValid = false;
        }
    }

    //validacion de duplicados(asincrona)
    if(reglas.duplicados){
        errors.duplicados = await reglas.duplicados.validar(
            datos.correo,
            datos.contrasenia,
            datos.DNI
        );
        if(errors.duplicados) isValid = false;
    }

    return { isValid, errors };
}

