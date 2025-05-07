<?php
require_once 'includes/db.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Iniciar una transacción para garantizar la integridad de los datos
        $pdo->beginTransaction();

        // Datos personales
        $nacionalidad = $_POST['nacionalidad'];
        $cedula = $_POST['cedula'];
        $primer_nombre = $_POST['primer_nombre'];
        $segundo_nombre = $_POST['segundo_nombre'];
        $primer_apellido = $_POST['primer_apellido'];
        $segundo_apellido = $_POST['segundo_apellido'];
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $estado_civil = $_POST['estado_civil'];
        $sexo = $_POST['sexo'];

        // Validar si la cédula ya existe
        $sql_check_cedula = "SELECT datos_personales_id FROM datos_personales WHERE cedula = :cedula";
        $stmt_check_cedula = $pdo->prepare($sql_check_cedula);
        $stmt_check_cedula->execute([':cedula' => $cedula]);
        $existing_person = $stmt_check_cedula->fetch();

        if ($existing_person) {
            // Si la cédula ya existe, redirigir con un mensaje de error
            header('Location: preinscripcion.php?status=error&message=' . urlencode('La cédula ya está registrada.'));
            exit();
        }

        // Insertar datos personales
        $sql_personal = "INSERT INTO datos_personales (nacionalidad, cedula, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, fecha_nacimiento, estado_civil, sexo)
                         VALUES (:nacionalidad, :cedula, :primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido, :fecha_nacimiento, :estado_civil, :sexo)";
        $stmt_personal = $pdo->prepare($sql_personal);
        $stmt_personal->execute([
            ':nacionalidad' => $nacionalidad,
            ':cedula' => $cedula,
            ':primer_nombre' => $primer_nombre,
            ':segundo_nombre' => $segundo_nombre,
            ':primer_apellido' => $primer_apellido,
            ':segundo_apellido' => $segundo_apellido,
            ':fecha_nacimiento' => $fecha_nacimiento,
            ':estado_civil' => $estado_civil,
            ':sexo' => $sexo
        ]);
        $datos_personales_id = $pdo->lastInsertId();

        // Dirección de habitación
        $estado = $_POST['estado'];
        $municipio = $_POST['municipio'];
        $parroquia = $_POST['parroquia'];
        $sector = $_POST['sector'];
        $avenida = $_POST['avenida'];
        $calle = $_POST['calle'];
        $casa_apto = $_POST['casa_apto'];
        $referencia = $_POST['referencia'];
        $telefono_celular = $_POST['telefono_celular'];
        $telefono_otro = $_POST['telefono_otro'];
        $correo = $_POST['correo'];

        // Insertar dirección de habitación
        $sql_direccion = "INSERT INTO direccion_habitacion (datos_personales_id, estado_id, municipio_id, parroquia_id, barrio_sector, avenida, calle, casa_apto, referencia, telefono_celular, telefono_otro, correo)
                          VALUES (:datos_personales_id, :estado_id, :municipio_id, :parroquia_id, :barrio_sector, :avenida, :calle, :casa_apto, :referencia, :telefono_celular, :telefono_otro, :correo)";
        $stmt_direccion = $pdo->prepare($sql_direccion);
        $stmt_direccion->execute([
            ':datos_personales_id' => $datos_personales_id,
            ':estado_id' => $estado,
            ':municipio_id' => $municipio,
            ':parroquia_id' => $parroquia,
            ':barrio_sector' => $sector,
            ':avenida' => $avenida,
            ':calle' => $calle,
            ':casa_apto' => $casa_apto,
            ':referencia' => $referencia,
            ':telefono_celular' => $telefono_celular,
            ':telefono_otro' => $telefono_otro,
            ':correo' => $correo
        ]);

        // Datos de la preinscripción
        $periodo = $_POST['periodo'];
        $pnf = $_POST['pnf'];
        $trayecto = $_POST['trayecto'];
        $aldea = $_POST['aldea'];
        $fecha_registro = date('Y-m-d H:i:s'); // Fecha actual

        // Insertar datos de la preinscripción
        $sql_preinscripcion = "INSERT INTO preinscripcion (datos_personales_id, periodo, pnf_id, trayecto, aldea_id, fecha_registro)
                               VALUES (:datos_personales_id, :periodo, :pnf_id, :trayecto, :aldea_id, :fecha_registro)";
        $stmt_preinscripcion = $pdo->prepare($sql_preinscripcion);
        $stmt_preinscripcion->execute([
            ':datos_personales_id' => $datos_personales_id,
            ':periodo' => $periodo,
            ':pnf_id' => $pnf,
            ':trayecto' => $trayecto,
            ':aldea_id' => $aldea,
            ':fecha_registro' => $fecha_registro
        ]);

        // Confirmar la transacción
        $pdo->commit();

        // Redirigir con un mensaje de éxito
        header('Location: preinscripcion.php?status=success');
        exit();
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $pdo->rollBack();

        // Redirigir con un mensaje de error
        header('Location: preinscripcion.php?status=error&message=' . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Redirigir si se accede al archivo directamente
    header('Location: preinscripcion.php');
    exit();
}
?>