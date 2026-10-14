<?php
session_start();
include("conexion.php");

// Procesar el registro de una nueva incidencia
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrar'])) {
    $reportado_por = $_POST['reportado_por'];
    $laboratorio = $_POST['laboratorio'];
    $equipo = $_POST['equipo'];
    $prioridad = $_POST['prioridad'];
    $descripcion = $_POST['descripcion'];
    $estado = 'Pendiente';

    if (!empty($reportado_por) && !empty($laboratorio) && !empty($equipo) && !empty($descripcion)) {
        $sql_insert = "INSERT INTO incidencias (reportado_por, laboratorio, equipo, prioridad, descripcion, estado, fecha) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt_insert = $conexion->prepare($sql_insert);
        $stmt_insert->bind_param("ssssss", $reportado_por, $laboratorio, $equipo, $prioridad, $descripcion, $estado);
        $stmt_insert->execute();
        header("Location: index.php");
        exit();
    }
}

// Filtros de búsqueda
$filtro_lab = $_GET['filtro_lab'] ?? '';
$filtro_estado = $_GET['filtro_estado'] ?? '';
$filtro_prioridad = $_GET['filtro_prioridad'] ?? '';

$sql_query = "SELECT * FROM incidencias WHERE 1=1";
$params = [];
$types = "";

if (!empty($filtro_lab)) {
    $sql_query .= " AND laboratorio LIKE ?";
    $params[] = "%$filtro_lab%";
    $types .= "s";
}
if (!empty($filtro_estado)) {
    $sql_query .= " AND estado = ?";
    $params[] = "$filtro_estado";
    $types .= "s";
}
if (!empty($filtro_prioridad)) {
    $sql_query .= " AND prioridad = ?";
    $params[] = "$filtro_prioridad";
    $types .= "s";
}

$sql_query .= " ORDER BY fecha DESC";
$stmt_list = $conexion->prepare($sql_query);
if (!empty($params)) {
    $stmt_list->bind_param($types, ...$params);
}
$stmt_list->execute();
$resultado = $stmt_list->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Incidencias</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --rosa-palo: #e0b1cb;
            --rosa-oscuro: #b5838d;
            --rosa-suave: #f7ede2;
            --malva-oscuro: #5c4d58;
            --texto-oscuro: #4a3b46;
            --neon-rosa: #ff758c;
            --neon-glow: 0 0 15px rgba(255, 117, 140, 0.6), 0 0 30px rgba(181, 131, 141, 0.4);
        }
        body {
            font-family: 'Poppins', sans-serif;
            /* Fondo de flores más visible (opacidad reducida en el degradado para que se luzca la imagen) */
            background: linear-gradient(135deg, rgba(251, 246, 248, 0.70), rgba(247, 237, 242, 0.75)), url('https://images.unsplash.com/photo-1522383225653-ed111181a951?q=80&w=1200&auto=format&fit=crop');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            color: var(--texto-oscuro);
        }
        .navbar {
            background: linear-gradient(135deg, rgba(92, 77, 88, 0.95), rgba(62, 50, 59, 0.95));
            box-shadow: 0 4px 20px rgba(92, 77, 88, 0.25);
            border-bottom: 2px solid rgba(255, 117, 140, 0.4);
            backdrop-filter: blur(5px);
        }
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(181, 131, 141, 0.2);
            /* Tarjetas ligeramente translúcidas para que dejen ver el fondo de flores sutilmente */
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(8px);
            transition: all 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 10px 30px rgba(255, 117, 140, 0.3);
        }
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(240, 230, 235, 0.8);
            font-weight: 600;
            color: var(--malva-oscuro);
            border-top-left-radius: 16px !important;
            border-top-right-radius: 16px !important;
            padding: 1.2rem 1.5rem;
        }
        .btn-neon-rosa {
            background: linear-gradient(135deg, #ff758c, #b5838d);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 10px;
            padding: 0.6rem 1.4rem;
            box-shadow: 0 0 12px rgba(255, 117, 140, 0.6);
            transition: all 0.3s ease;
        }
        .btn-neon-rosa:hover {
            background: linear-gradient(135deg, #ff5c77, #a3727c);
            color: white;
            box-shadow: var(--neon-glow);
            transform: translateY(-2px);
        }
        .form-control, .form-select {
            border-radius: 10px;
            padding: 0.6rem 1rem;
            border: 1px solid #dfcdd4;
            background-color: rgba(253, 251, 253, 0.9);
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #ff758c;
            box-shadow: 0 0 12px rgba(255, 117, 140, 0.4);
            background-color: #ffffff;
        }
        .table th {
            background-color: rgba(244, 236, 240, 0.85);
            color: var(--malva-oscuro);
            font-weight: 600;
            border-bottom: 2px solid #e8dbdf;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        .table td {
            vertical-align: middle;
            color: #554450;
            font-size: 0.9rem;
        }
        .badge {
            padding: 0.5em 0.9em;
            font-weight: 500;
            border-radius: 8px;
        }
        .neon-badge-action {
            transition: all 0.2s ease;
        }
        .neon-badge-action:hover {
            box-shadow: 0 0 8px rgba(255, 117, 140, 0.8);
            transform: scale(1.05);
        }
    </style>
</head>
<body>

    <!-- Barra Superior -->
    <nav class="navbar navbar-dark px-4 py-3 mb-5">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1 fw-bold" style="text-shadow: 0 0 10px rgba(224, 177, 203, 0.6);">
                <i class="fa-solid fa-desktop me-2" style="color: #ff758c; text-shadow: 0 0 10px #ff758c;"></i> Sistema de Incidencias
            </span>
            <div>
                <?php if (isset($_SESSION['usuario'])): ?>
                    <span class="text-light me-3 small"><i class="fa-solid fa-user-shield me-1" style="color: #ff758c;"></i> Admin: <strong><?php echo htmlspecialchars($_SESSION['usuario']); ?></strong></span>
                    <a href="logout.php" class="btn btn-outline-light btn-sm rounded-pill px-3 neon-badge-action"><i class="fa-solid fa-right-from-bracket me-1"></i> Cerrar Sesión</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-light btn-sm rounded-pill px-3 fw-semibold neon-badge-action" style="color: #5c4d58; box-shadow: 0 0 8px rgba(255,255,255,0.6);"><i class="fa-solid fa-lock me-1"></i> Admin Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        
        <!-- Formulario de Registro -->
        <div class="card mb-4">
            <div class="card-header fs-5">
                <i class="fa-solid fa-circle-plus me-2" style="color: #ff758c; text-shadow: 0 0 8px #ff758c;"></i> Registrar Nueva Incidencia
            </div>
            <div class="card-body p-4">
                <form method="POST">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-muted">Reportado por</label>
                            <input type="text" class="form-control" name="reportado_por" placeholder="Ej. Ana Pérez" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-muted">Laboratorio</label>
                            <input type="text" class="form-control" name="laboratorio" placeholder="Ej. Lab 1" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-muted">Equipo Afectado</label>
                            <input type="text" class="form-control" name="equipo" placeholder="Ej. PC-05" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-muted">Prioridad</label>
                            <select class="form-select" name="prioridad">
                                <option value="Baja">Baja</option>
                                <option value="Media" selected>Media</option>
                                <option value="Alta">Alta</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Descripción del Problema</label>
                        <textarea class="form-control" name="descripcion" rows="2" placeholder="Detalle breve de la falla..." required></textarea>
                    </div>
                    <button type="submit" name="registrar" class="btn btn-neon-rosa">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Incidencia
                    </button>
                </form>
            </div>
        </div>

        <!-- Filtros y Búsqueda -->
        <div class="card mb-4 p-3">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" style="border-color: #dfcdd4;"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0" name="filtro_lab" placeholder="Filtrar por Laboratorio..." value="<?php echo htmlspecialchars($filtro_lab); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="filtro_estado">
                        <option value="">Todos los Estados</option>
                        <option value="Pendiente" <?php if($filtro_estado=='Pendiente') echo 'selected'; ?>>Pendiente</option>
                        <option value="En proceso" <?php if($filtro_estado=='En proceso') echo 'selected'; ?>>En proceso</option>
                        <option value="Resuelta" <?php if($filtro_estado=='Resuelta') echo 'selected'; ?>>Resuelta</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="filtro_prioridad">
                        <option value="">Todas las Prioridades</option>
                        <option value="Baja" <?php if($filtro_prioridad=='Baja') echo 'selected'; ?>>Baja</option>
                        <option value="Media" <?php if($filtro_prioridad=='Media') echo 'selected'; ?>>Media</option>
                        <option value="Alta" <?php if($filtro_prioridad=='Alta') echo 'selected'; ?>>Alta</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn w-100 rounded-pill text-white fw-semibold neon-badge-action" style="background-color: #8c7a85; box-shadow: 0 0 8px rgba(140, 122, 133, 0.5);"><i class="fa-solid fa-filter me-1"></i> Filtrar</button>
                </div>
            </form>
        </div>

        <!-- Listado de Incidencias -->
        <div class="card shadow-sm">
            <div class="card-header fs-5">
                <i class="fa-solid fa-list-check me-2" style="color: #ff758c; text-shadow: 0 0 8px #ff758c;"></i> Listado de Incidencias
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle" style="background: transparent;">
                        <thead>
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Fecha</th>
                                <th>Reportado</th>
                                <th>Lab</th>
                                <th>Equipo</th>
                                <th>Descripción</th>
                                <th>Prioridad</th>
                                <th>Estado</th>
                                <th class="text-end pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($resultado->num_rows > 0): ?>
                                <?php while($row = $resultado->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold">#<?php echo $row['id']; ?></td>
                                        <td class="text-muted small"><?php echo $row['fecha']; ?></td>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($row['reportado_por']); ?></td>
                                        <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['laboratorio']); ?></span></td>
                                        <td><?php echo htmlspecialchars($row['equipo']); ?></td>
                                        <td class="text-truncate" style="max-width: 180px;" title="<?php echo htmlspecialchars($row['descripcion']); ?>">
                                            <?php echo htmlspecialchars($row['descripcion']); ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo ($row['prioridad'] == 'Alta') ? 'danger' : (($row['prioridad'] == 'Media') ? 'warning text-dark' : 'info'); 
                                            ?>">
                                                <?php echo $row['prioridad']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo ($row['estado'] == 'Resuelta') ? 'success' : (($row['estado'] == 'En proceso') ? 'primary' : 'secondary'); 
                                            ?>">
                                                <?php echo $row['estado']; ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <?php if (isset($_SESSION['usuario'])): ?>
                                                <a href="editar.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm text-dark px-2 py-1 me-1 neon-badge-action" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>
                                                <a href="eliminar.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm px-2 py-1 neon-badge-action" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta incidencia?');"><i class="fa-solid fa-trash"></i></a>
                                            <?php else: ?>
                                                <span class="text-muted small fst-italic">Solo Admin</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-folder-open fa-2x mb-2 d-block" style="color: #dfcdd4;"></i>
                                        No hay incidencias registradas.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</body>
</html>