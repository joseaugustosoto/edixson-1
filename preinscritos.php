<?php
require_once 'includes/db.php'; // Conexión a la base de datos
session_start();
require_once 'includes/auth.php';
require_login();

// Variables para paginación
$page = $_GET['page'] ?? 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Filtros dinámicos
$whereClauses = [];
$params = [];

if (!empty($_GET['periodo'])) {
    $whereClauses[] = "pr.periodo = :periodo";
    $params[':periodo'] = $_GET['periodo'];
}
if (!empty($_GET['pnf'])) {
    $whereClauses[] = "pn.pnf_id = :pnf";
    $params[':pnf'] = $_GET['pnf'];
}
if (!empty($_GET['aldea'])) {
    $whereClauses[] = "a.aldea_id = :aldea";
    $params[':aldea'] = $_GET['aldea'];
}
if (!empty($_GET['parroquia'])) {
    $whereClauses[] = "pa.parroquia_id = :parroquia";
    $params[':parroquia'] = $_GET['parroquia'];
}
if (!empty($_GET['municipio'])) {
    $whereClauses[] = "m.municipio_id = :municipio";
    $params[':municipio'] = $_GET['municipio'];
}
if (!empty($_GET['estado'])) {
    $whereClauses[] = "e.estado_id = :estado";
    $params[':estado'] = $_GET['estado'];
}

$whereSQL = $whereClauses ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

// Consulta para obtener los registros de preinscritos
$sql = "SELECT 
            p.cedula, 
            p.primer_nombre, 
            p.segundo_nombre, 
            p.primer_apellido, 
            p.segundo_apellido, 
            p.fecha_nacimiento, 
            p.estado_civil, 
            p.sexo, 
            d.telefono_celular, 
            d.telefono_otro, 
            d.correo, 
            d.barrio_sector, 
            d.avenida, 
            d.calle, 
            d.casa_apto, 
            d.referencia, 
            pn.nombre_pnf, 
            pr.trayecto, 
            pr.periodo, 
            pr.fecha_registro,
            a.nombre_aldea
        FROM preinscripcion pr
        JOIN datos_personales p ON pr.datos_personales_id = p.datos_personales_id
        JOIN direccion_habitacion d ON p.datos_personales_id = d.datos_personales_id
        JOIN aldea a ON pr.aldea_id = a.aldea_id
        JOIN parroquia pa ON a.parroquia_id = pa.parroquia_id
        JOIN municipio m ON pa.municipio_id = m.municipio_id
        JOIN estado e ON m.estado_id = e.estado_id
        JOIN pnf pn ON pr.pnf_id = pn.pnf_id
        $whereSQL
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total de registros para la paginación
$total_sql = "SELECT COUNT(*) FROM preinscripcion";
$total_stmt = $pdo->query($total_sql);
$total_records = $total_stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM datos_personales WHERE cedula = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_STR);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Registro eliminado correctamente.";
    } else {
        $_SESSION['error_message'] = "Error al eliminar el registro.";
    }
    header("Location: preinscritos.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Preinscritos</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
</head>
<body>
    <?php include 'includes/menu.php'; ?>
    <h1>Gestión de Preinscritos</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="success-message"><?= $_SESSION['success_message'] ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="error-message"><?= $_SESSION['error_message'] ?></div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <form id="filters" method="GET" class="filters-container">
        <div class="filter-group">
            <label for="periodo">Período:</label>
            <select name="periodo" id="periodo">
                <option value="">Todos</option>
                <?php
                $periodos = $pdo->query("SELECT DISTINCT periodo FROM preinscripcion")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($periodos as $periodo) {
                    echo "<option value='{$periodo['periodo']}'>{$periodo['periodo']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="filter-group">
            <label for="pnf">PNF:</label>
            <select name="pnf" id="pnf">
                <option value="">Todos</option>
                <?php
                $pnfs = $pdo->query("SELECT pnf_id, nombre_pnf FROM pnf WHERE estado = 'Activo'")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($pnfs as $pnf) {
                    echo "<option value='{$pnf['pnf_id']}'>{$pnf['nombre_pnf']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="filter-group">
            <label for="aldea">Aldea:</label>
            <select name="aldea" id="aldea">
                <option value="">Todas</option>
                <?php
                $aldeas = $pdo->query("SELECT aldea_id, nombre_aldea FROM aldea WHERE estado = 'Activa'")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($aldeas as $aldea) {
                    echo "<option value='{$aldea['aldea_id']}'>{$aldea['nombre_aldea']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="filter-group">
            <label for="parroquia">Parroquia:</label>
            <select name="parroquia" id="parroquia">
                <option value="">Todas</option>
                <?php
                $parroquias = $pdo->query("SELECT parroquia_id, nombre_parroquia FROM parroquia")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($parroquias as $parroquia) {
                    echo "<option value='{$parroquia['parroquia_id']}'>{$parroquia['nombre_parroquia']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="filter-group">
            <label for="municipio">Municipio:</label>
            <select name="municipio" id="municipio">
                <option value="">Todos</option>
                <?php
                $municipios = $pdo->query("SELECT municipio_id, nombre_municipio FROM municipio")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($municipios as $municipio) {
                    echo "<option value='{$municipio['municipio_id']}'>{$municipio['nombre_municipio']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="filter-group">
            <label for="estado">Estado:</label>
            <select name="estado" id="estado">
                <option value="">Todos</option>
                <?php
                $estados = $pdo->query("SELECT estado_id, nombre_estado FROM estado")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($estados as $estado) {
                    echo "<option value='{$estado['estado_id']}'>{$estado['nombre_estado']}</option>";
                }
                ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Cédula</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>PNF</th>
                <th>Aldea</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['cedula']) ?></td>
                    <td><?= htmlspecialchars($row['primer_nombre']) ?></td>
                    <td><?= htmlspecialchars($row['primer_apellido']) ?></td>
                    <td><?= htmlspecialchars($row['telefono_celular']) ?></td>
                    <td><?= htmlspecialchars($row['correo']) ?></td>
                    <td><?= htmlspecialchars($row['nombre_pnf']) ?></td>
                    <td><?= htmlspecialchars($row['nombre_aldea']) ?></td>
                    <td>
                        <button class="btn-info" onclick="showModal('<?= htmlspecialchars(json_encode($row)) ?>')">Ver</button>
                        <a href="editar.php?id=<?= $row['cedula'] ?>" class="btn-edit">Editar</a>
                        <a href="?action=delete&id=<?= $row['cedula'] ?>" class="btn-delete" onclick="return confirm('¿Estás seguro de que deseas eliminar este registro?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>

    <div id="modal" class="modal">
        <div class="modal-content">
            <h3>Información del Preinscrito</h3>
            <div id="modal-body"></div>
            <button class="close-modal" onclick="closeModal()">Cerrar</button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                }
            });
        });

        function showModal(data) {
            const parsedData = JSON.parse(data);
            const modalBody = document.getElementById('modal-body');
            modalBody.innerHTML = `
                <p><strong>Cédula:</strong> ${parsedData.cedula}</p>
                <p><strong>Nombre:</strong> ${parsedData.primer_nombre} ${parsedData.segundo_nombre || ''}</p>
                <p><strong>Apellido:</strong> ${parsedData.primer_apellido} ${parsedData.segundo_apellido || ''}</p>
                <p><strong>Fecha de Nacimiento:</strong> ${parsedData.fecha_nacimiento}</p>
                <p><strong>Estado Civil:</strong> ${parsedData.estado_civil}</p>
                <p><strong>Sexo:</strong> ${parsedData.sexo}</p>
                <p><strong>Teléfono:</strong> ${parsedData.telefono_celular}</p>
                <p><strong>Correo:</strong> ${parsedData.correo}</p>
                <p><strong>Dirección:</strong> ${parsedData.barrio_sector || ''}, ${parsedData.avenida || ''}, ${parsedData.calle || ''}, ${parsedData.casa_apto || ''}, ${parsedData.referencia || ''}</p>
                <p><strong>PNF:</strong> ${parsedData.nombre_pnf}</p>
                <p><strong>Trayecto:</strong> ${parsedData.trayecto}</p>
                <p><strong>Periodo:</strong> ${parsedData.periodo}</p>
                <p><strong>Fecha de Registro:</strong> ${parsedData.fecha_registro}</p>
            `;
            document.getElementById('modal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }

        // Cerrar el modal al hacer clic fuera del contenido
        document.getElementById('modal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('modal')) {
                closeModal();
            }
        });
    </script>
</body>
</html>
