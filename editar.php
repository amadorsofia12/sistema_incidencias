<?php
session_start();

// Validar si el usuario ha iniciado sesión como administrador
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include("conexion.php");

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit();
}

// Obtener los datos actuales de la incidencia
$sql = "SELECT * FROM incidencias WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    header("Location: index.php");
    exit();
}
$incidencia = $resultado->fetch_assoc();

// Procesar la actualización cuando se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reportado_por = $_POST['reportado_por'];
    $laboratorio = $_POST['laboratorio'];
    $equipo = $_POST['equipo'];
    $prioridad = $_POST['prioridad'];
    $descripcion = $_POST['descripcion'];
    $estado = $_POST['estado']; // Captura el estado seleccionado (Pendiente, En proceso, Resuelta)

    $sql_update = "UPDATE incidencias SET reportado_por=?, laboratorio=?, equipo=?, prioridad=?, descripcion=?, estado=? WHERE id=?";
    $stmt_update = $conexion->prepare($sql_update);
    $stmt_update->bind_param("ssssssi", $reportado_por, $laboratorio, $equipo, $prioridad, $descripcion, $estado, $id);
    
    if ($stmt_update->execute()) {
        header("Location: index.php");
        exit();
    } else {
        $error = "Error al actualizar la incidencia.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Incidencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 700px;">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Editar Incidencia #<?php echo $incidencia['id']; ?></h3>
            </div>
            <div class="card-body">
                <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Reportado por:</label>
                        <input type="text" class="form-control" name="reportado_por" value="<?php echo htmlspecialchars($incidencia['reportado_por']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Laboratorio:</label>
                        <input type="text" class="form-control" name="laboratorio" value="<?php echo htmlspecialchars($incidencia['laboratorio']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Equipo:</label>
                        <input type="text" class="form-control" name="equipo" value="<?php echo htmlspecialchars($incidencia['equipo']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prioridad:</label>
                        <select class="form-select" name="prioridad" required>
                            <option value="Baja" <?php if($incidencia['prioridad'] == 'Baja') echo 'selected'; ?>>Baja</option>
                            <option value="Media" <?php if($incidencia['prioridad'] == 'Media') echo 'selected'; ?>>Media</option>
                            <option value="Alta" <?php if($incidencia['prioridad'] == 'Alta') echo 'selected'; ?>>Alta</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado de la Incidencia:</label>
                        <select class="form-select" name="estado" required>
                            <option value="Pendiente" <?php if($incidencia['estado'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                            <option value="En proceso" <?php if($incidencia['estado'] == 'En proceso') echo 'selected'; ?>>En proceso</option>
                            <option value="Resuelta" <?php if($incidencia['estado'] == 'Resuelta') echo 'selected'; ?>>Resuelta</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción:</label>
                        <textarea class="form-control" name="descripcion" rows="3" required><?php echo htmlspecialchars($incidencia['descripcion']); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Actualizar Incidencia</button>
                    <a href="index.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>