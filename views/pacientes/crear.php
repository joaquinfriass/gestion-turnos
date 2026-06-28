<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo paciente - Gestion de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="public/css/turnos.css" rel="stylesheet">
</head>
<body>
    <div class="app-shell">
        <?php require __DIR__ . '/../layouts/sidebar.php'; ?>
    <main class="main-content page-shell narrow">
        <header class="page-header">
            <div>
                <a class="back-link" href="index.php?action=<?php echo htmlspecialchars($cancelAction ?? 'pacientes'); ?>"><i class="bi bi-arrow-left"></i><span>Pacientes</span></a>
                <h1>Nuevo paciente</h1>
            </div>
        </header>

        <?php if (!empty($errores)): ?>
            <div class="alert alert-danger"><?php foreach ($errores as $error): ?><div><?php echo htmlspecialchars($error); ?></div><?php endforeach; ?></div>
        <?php endif; ?>

        <section class="form-panel">
            <form action="index.php?action=<?php echo htmlspecialchars($formAction ?? 'pacientes_crear'); ?>" method="POST" class="row g-3 js-validate">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="dni">DNI</label>
                    <input class="form-control" id="dni" name="dni" value="<?php echo htmlspecialchars($paciente['dni']); ?>" maxlength="20" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="telefono">Telefono</label>
                    <input class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($paciente['telefono']); ?>" maxlength="50">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="nombre">Nombre</label>
                    <input class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($paciente['nombre']); ?>" maxlength="100" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="apellido">Apellido</label>
                    <input class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($paciente['apellido']); ?>" maxlength="100" required>
                </div>
                <div class="form-actions">
                    <a class="btn btn-outline-secondary" href="index.php?action=<?php echo htmlspecialchars($cancelAction ?? 'pacientes'); ?>">Cancelar</a>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg"></i><span>Guardar paciente</span></button>
                </div>
            </form>
        </section>
    </main>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/app.js"></script>
</body>
</html>
