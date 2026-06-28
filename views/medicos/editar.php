<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar medico - Gestion de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="public/css/turnos.css" rel="stylesheet">
</head>
<body>
    <div class="app-shell">
        <?php require __DIR__ . '/../layouts/sidebar.php'; ?>
    <main class="main-content page-shell narrow">
        <header class="page-header"><div><a class="back-link" href="index.php?action=medicos"><i class="bi bi-arrow-left"></i><span>Medicos</span></a><h1>Editar medico</h1></div></header>
        <?php if (!empty($errores)): ?><div class="alert alert-danger"><?php foreach ($errores as $error): ?><div><?php echo htmlspecialchars($error); ?></div><?php endforeach; ?></div><?php endif; ?>
        <section class="form-panel">
            <form action="index.php?action=medicos_editar&id=<?php echo (int) $id; ?>" method="POST" class="row g-3 js-validate">
                <input type="hidden" name="id" value="<?php echo (int) $id; ?>">
                <div class="col-12"><label class="form-label" for="nombre">Nombre</label><input class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($medico['nombre']); ?>" maxlength="100" required></div>
                <div class="col-12 col-md-6"><label class="form-label" for="email">Email</label><input class="form-control" type="email" id="email" name="email" value="<?php echo htmlspecialchars($medico['email']); ?>" maxlength="150" required></div>
                <div class="col-12 col-md-6"><label class="form-label" for="password">Nueva contraseña</label><input class="form-control" type="password" id="password" name="password" placeholder="Dejar vacio para mantener"></div>
                <div class="form-actions"><a class="btn btn-outline-secondary" href="index.php?action=medicos">Cancelar</a><button class="btn btn-primary" type="submit"><i class="bi bi-check-lg"></i><span>Actualizar medico</span></button></div>
            </form>
        </section>
    </main>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/app.js"></script>
</body>
</html>
