<?php
session_start();
include("conexion.php");

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    if ($usuario === 'admin' && $contrasena === '123') {
        $_SESSION['usuario'] = $usuario;
        header("Location: index.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Sistema de Incidencias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, rgba(92, 77, 88, 0.75), rgba(42, 34, 40, 0.8)), url('https://images.unsplash.com/photo-1522383225653-ed111181a951?q=80&w=1200&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4), 0 0 20px rgba(255, 117, 140, 0.3);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
            width: 100%;
            max-width: 400px;
            padding: 2.5rem;
        }
        .btn-neon-rosa {
            background: linear-gradient(135deg, #ff758c, #b5838d);
            border: none;
            color: white;
            padding: 0.7rem;
            border-radius: 10px;
            font-weight: 600;
            box-shadow: 0 0 12px rgba(255, 117, 140, 0.5);
            transition: all 0.3s ease;
        }
        .btn-neon-rosa:hover {
            background: linear-gradient(135deg, #ff5c77, #a3727c);
            color: white;
            box-shadow: 0 0 20px rgba(255, 117, 140, 0.8);
            transform: translateY(-2px);
        }
        .form-control {
            border-radius: 10px;
            padding: 0.7rem 1rem;
            border: 1px solid #dfcdd4;
            background-color: #fdfbfd;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #ff758c;
            box-shadow: 0 0 12px rgba(255, 117, 140, 0.4);
            background-color: #ffffff;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <div class="rounded-circle d-inline-flex p-3 mb-2" style="background-color: #f4ecf0; color: #ff758c; box-shadow: 0 0 12px rgba(255, 117, 140, 0.4);">
                <i class="fa-solid fa-user-shield fa-2x"></i>
            </div>
            <h3 class="fw-bold" style="color: #5c4d58;">Acceso Administrativo</h3>
            <p class="text-muted small">Ingrese sus credenciales para gestionar el sistema</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">Usuario</label>
                <input type="text" class="form-control" name="usuario" placeholder="Ej. admin" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-semibold text-muted">Contraseña</label>
                <input type="password" class="form-control" name="contrasena" placeholder="••••••" required>
            </div>
            <button type="submit" class="btn btn-neon-rosa w-100 mb-3">Ingresar al Sistema</button>
            <div class="text-center">
                <a href="index.php" class="text-decoration-none small" style="color: #8c7a85;"><i class="fa-solid fa-arrow-left me-1"></i> Volver al inicio</a>
            </div>
        </form>
    </div>

</body>
</html>