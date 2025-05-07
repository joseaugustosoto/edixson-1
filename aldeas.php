<?php
require_once 'includes/db.php'; // Conexión a la base de datos

// Consultar los PNF, Aldeas y Parroquias asociadas
$query = "
    SELECT pnf.nombre_pnf, aldea.nombre_aldea, aldea.direccion, parroquia.nombre_parroquia
    FROM aldea_pnf
    INNER JOIN pnf ON aldea_pnf.pnf_id = pnf.pnf_id
    INNER JOIN aldea ON aldea_pnf.aldea_id = aldea.aldea_id
    INNER JOIN parroquia ON aldea.parroquia_id = parroquia.parroquia_id
    WHERE pnf.estado = 'Activo' AND aldea.estado = 'Activa'
    ORDER BY pnf.nombre_pnf, aldea.nombre_aldea
";
$stmt = $pdo->query($query);
$results = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PNF del Municipio Maracaibo</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .accordion {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .accordion-item {
            border-bottom: 1px solid #ddd;
        }
        .accordion-header {
            padding: 15px;
            cursor: pointer;
            background: #f1f1f1;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .accordion-header:hover {
            background: #e0e0e0;
        }
        .accordion-content {
            display: none;
            padding: 15px;
            background: #fff;
        }
        .accordion-content ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .accordion-content ul li {
            padding: 5px 0;
        }
    </style>
</head>
<body>
    <?php include 'includes/menu-publico.php'; ?> <!-- Incluye el menú de navegación publica -->

    <h1>Programas Nacionales de Formación Disponibles en Maracaibo</h1>

    <div class="accordion">
        <?php foreach ($results as $pnf => $aldeas): ?>
            <div class="accordion-item">
                <div class="accordion-header">
                    <?= htmlspecialchars($pnf) ?>
                    <span>+</span>
                </div>
                <div class="accordion-content">
                    <ul>
                        <?php foreach ($aldeas as $aldea): ?>
                            <li>
                                <a href="#" 
                                   class="aldea-link" 
                                   data-direccion="<?= htmlspecialchars($aldea['direccion']) ?>"
                                   data-parroquia="<?= htmlspecialchars($aldea['nombre_parroquia']) ?>">
                                    <?= htmlspecialchars($aldea['nombre_aldea']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Modal -->
    <div class="modal" id="aldeaModal">
        <div class="modal-content">
            <h3>Información de la Aldea</h3>
            <p><strong>Dirección:</strong> <span id="aldeaDireccion"></span></p>
            <p><strong>Parroquia:</strong> <span id="aldeaParroquia"></span></p>
            <button class="close-modal">Cerrar</button>
        </div>
    </div>

    <script>
        // Manejo del acordeón
        document.querySelectorAll('.accordion-header').forEach(header => {
            header.addEventListener('click', () => {
                const content = header.nextElementSibling;
                const isOpen = content.style.display === 'block';

                // Cerrar todos los demás
                document.querySelectorAll('.accordion-content').forEach(c => c.style.display = 'none');
                document.querySelectorAll('.accordion-header span').forEach(s => s.textContent = '+');

                // Abrir o cerrar el actual
                if (!isOpen) {
                    content.style.display = 'block';
                    header.querySelector('span').textContent = '-';
                }
            });
        });

        // Manejo del modal
        const modal = document.getElementById('aldeaModal');
        const modalDireccion = document.getElementById('aldeaDireccion');
        const modalParroquia = document.getElementById('aldeaParroquia');
        const closeModalButton = document.querySelector('.close-modal');

        document.querySelectorAll('.aldea-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const direccion = link.getAttribute('data-direccion');
                const parroquia = link.getAttribute('data-parroquia');
                modalDireccion.textContent = direccion;
                modalParroquia.textContent = parroquia;
                modal.style.display = 'flex';
            });
        });

        closeModalButton.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        // Cerrar el modal al hacer clic fuera del contenido
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>
</body>
</html>