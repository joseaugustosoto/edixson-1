<!-- filepath: e:\xampp\htdocs\edixson-1\preinscripcion.php -->
<?php
require_once 'includes/db.php'; // Conexión a la base de datos

// Mostrar mensajes de éxito o error
$status = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proceso de Preinscripción</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    
<?php include 'includes/menu-publico.php'; ?> <!-- Incluye el menú de navegación publica -->

    <h1>Proceso de Preinscripción</h1>

    <?php if ($status === 'success'): ?>
        <div class="success-message">
            ¡La preinscripción se guardó exitosamente!
        </div>
    <?php endif; ?>

    <?php if ($status === 'error'): ?>
        <div class="error-message">
            Ocurrió un error al guardar la preinscripción: <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form action="guardar_preinscripcion.php" method="POST">
        <!-- Datos Personales -->
        <fieldset>
            <legend>Datos Personales</legend>
            <label for="nacionalidad">Nacionalidad:</label>
            <select name="nacionalidad" id="nacionalidad" required>
                <option value="V">Venezolano</option>
                <option value="E">Extranjero</option>
                <option value="P">Pasaporte</option>
            </select><br>

            <label for="cedula">Nro. de Cédula/Pasaporte:</label>
            <input type="number" name="cedula" id="cedula" required><br>

            <label for="primer_nombre">Primer Nombre:</label>
            <input type="text" name="primer_nombre" id="primer_nombre" required><br>

            <label for="segundo_nombre">Segundo Nombre:</label>
            <input type="text" name="segundo_nombre" id="segundo_nombre"><br>

            <label for="primer_apellido">Primer Apellido:</label>
            <input type="text" name="primer_apellido" id="primer_apellido" required><br>

            <label for="segundo_apellido">Segundo Apellido:</label>
            <input type="text" name="segundo_apellido" id="segundo_apellido"><br>

            <label for="fecha_nacimiento">F. Nacimiento:</label>
            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required><br>

            <label for="estado_civil">Estado Civil:</label>
            <select name="estado_civil" id="estado_civil" required>
                <option value="Soltero">Soltero</option>
                <option value="Casado">Casado</option>
                <option value="Divorciado">Divorciado</option>
                <option value="Viudo">Viudo</option>
            </select><br>

            <label for="sexo">Sexo:</label>
            <select name="sexo" id="sexo" required>
                <option value="M">Masculino</option>
                <option value="F">Femenino</option>
            </select><br>
        </fieldset>

        <!-- Dirección de Habitación -->
        <fieldset>
            <legend>Dirección de Habitación</legend>
            <label for="estado">Estado:</label>
            <select name="estado" id="estado" required>
                <option value="">Seleccione un estado</option>
                <?php
                // Cargar estados desde la base de datos
                $query = "SELECT estado_id, nombre_estado FROM estado";
                $result = $pdo->query($query);
                while ($row = $result->fetch()) {
                    echo "<option value='{$row['estado_id']}'>{$row['nombre_estado']}</option>";
                }
                ?>
            </select><br>

            <label for="municipio">Municipio:</label>
            <select name="municipio" id="municipio" required>
                <option value="">Seleccione un municipio</option>
            </select><br>

            <label for="parroquia">Parroquia:</label>
            <select name="parroquia" id="parroquia" required>
                <option value="">Seleccione una parroquia</option>
            </select><br>

            <label for="sector">Sector:</label>
            <input type="text" name="sector" id="sector"><br>

            <label for="avenida">Avenida:</label>
            <input type="text" name="avenida" id="avenida"><br>

            <label for="calle">Calle:</label>
            <input type="text" name="calle" id="calle"><br>

            <label for="casa_apto">Casa/Apto:</label>
            <input type="text" name="casa_apto" id="casa_apto" required><br>

            <label for="referencia">Punto de Referencia:</label>
            <input type="text" name="referencia" id="referencia"><br>

            <label for="telefono_celular">Teléfono Celular:</label>
            <input type="tel" name="telefono_celular" id="telefono_celular" required><br>

            <label for="telefono_otro">Teléfono (Otro):</label>
            <input type="tel" name="telefono_otro" id="telefono_otro"><br>

            <label for="correo">Correo Electrónico:</label>
            <input type="email" name="correo" id="correo" required><br>
        </fieldset>

        <!-- Datos de la Pre-inscripción -->
        <fieldset>
            <legend>Datos de la Pre-inscripción</legend>
            <label for="periodo">Periodo Académico:</label>
            <input type="text" name="periodo" id="periodo" value="2025-2" readonly><br>

            <label for="pnf">Programa de Formación:</label>
            <select name="pnf" id="pnf" required>
                <option value="">Seleccione un PNF</option>
                <?php
                $query = "SELECT pnf_id, nombre_pnf FROM pnf WHERE estado = 'Activo'";
                $result = $pdo->query($query);
                while ($row = $result->fetch()) {
                    echo "<option value='{$row['pnf_id']}'>{$row['nombre_pnf']}</option>";
                }
                ?>
            </select><br>

            <label for="trayecto">Trayecto:</label>
            <input type="text" name="trayecto" id="trayecto" value="Inicial" readonly><br>

            <label for="aldea">Aldea:</label>
            <select name="aldea" id="aldea" required>
                <option value="">Seleccione una Aldea</option>
            </select><br>
            <h5><a href="/edixson-1/aldeas.php" target="_new">*Para conocer los PNF disponibles por Aldea haz click aquí</a></h5>
        </fieldset>

        <button type="submit">Guardar</button>
    </form>

    <script>
        document.getElementById('estado').addEventListener('change', function () {
            const estadoId = this.value;

            if (estadoId) {
                // Cargar municipios
                fetch(`cargar_municipios.php?estado_id=${estadoId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error al cargar municipios');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const municipioSelect = document.getElementById('municipio');
                        municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
                        data.forEach(municipio => {
                            municipioSelect.innerHTML += `<option value="${municipio.municipio_id}">${municipio.nombre_municipio}</option>`;
                        });

                        // Limpiar parroquias
                        document.getElementById('parroquia').innerHTML = '<option value="">Seleccione una parroquia</option>';
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                // Limpiar municipios y parroquias si no se selecciona un estado
                document.getElementById('municipio').innerHTML = '<option value="">Seleccione un municipio</option>';
                document.getElementById('parroquia').innerHTML = '<option value="">Seleccione una parroquia</option>';
            }
        });

        document.getElementById('municipio').addEventListener('change', function () {
            const municipioId = this.value;

            if (municipioId) {
                // Cargar parroquias
                fetch(`cargar_parroquias.php?municipio_id=${municipioId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error al cargar parroquias');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const parroquiaSelect = document.getElementById('parroquia');
                        parroquiaSelect.innerHTML = '<option value="">Seleccione una parroquia</option>';
                        data.forEach(parroquia => {
                            parroquiaSelect.innerHTML += `<option value="${parroquia.parroquia_id}">${parroquia.nombre_parroquia}</option>`;
                        });
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                // Limpiar parroquias si no se selecciona un municipio
                document.getElementById('parroquia').innerHTML = '<option value="">Seleccione una parroquia</option>';
            }
        });

        document.getElementById('pnf').addEventListener('change', function () {
            const pnfId = this.value;

            if (pnfId) {
                // Cargar Aldeas habilitadas para el PNF seleccionado
                fetch(`cargar_aldeas.php?pnf_id=${pnfId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error al cargar Aldeas');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const aldeaSelect = document.getElementById('aldea');
                        aldeaSelect.innerHTML = '<option value="">Seleccione una Aldea</option>';
                        data.forEach(aldea => {
                            aldeaSelect.innerHTML += `<option value="${aldea.aldea_id}">${aldea.nombre_aldea}</option>`;
                        });
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                // Limpiar Aldeas si no se selecciona un PNF
                document.getElementById('aldea').innerHTML = '<option value="">Seleccione una Aldea</option>';
            }
        });
    </script>
</body>
</html>
