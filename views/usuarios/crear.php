<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo usuario - Gestion de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="public/css/turnos.css" rel="stylesheet">
</head>
<body>
    <div class="app-shell">
        <?php require __DIR__ . '/../layouts/sidebar.php'; ?>
    <main class="main-content page-shell narrow">
        <header class="page-header"><div><a class="back-link" href="index.php?action=usuarios"><i class="bi bi-arrow-left"></i><span>Usuarios</span></a><h1>Nuevo usuario</h1></div></header>
        <?php if (!empty($errores)): ?><div class="alert alert-danger"><?php foreach ($errores as $error): ?><div><?php echo htmlspecialchars($error); ?></div><?php endforeach; ?></div><?php endif; ?>
        <section class="form-panel">
            <form action="index.php?action=usuarios_crear" method="POST" class="row g-3 js-validate">
                <?php echo AuthController::csrfInput(); ?>
                <div class="col-12"><label class="form-label" for="nombre">Nombre</label><input class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" maxlength="100" required></div>
                <div class="col-12 col-md-6"><label class="form-label" for="email">Email</label><input class="form-control" type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" maxlength="150" required></div>
                <div class="col-12 col-md-6"><label class="form-label" for="rol">Rol</label><select class="form-select" id="rol" name="rol" required><?php foreach ($roles as $rolItem): ?><option value="<?php echo $rolItem; ?>" <?php echo ($usuario['rol'] === $rolItem) ? 'selected' : ''; ?>><?php echo ucfirst($rolItem); ?></option><?php endforeach; ?></select></div>
                <div class="col-12"><label class="form-label" for="password">Contraseña</label><input class="form-control" type="password" id="password" name="password" required></div>
                <div class="col-12 col-md-6 js-medico-fields">
                    <label class="form-label" for="especialidad">Especialidad</label>
                    <input class="form-control" id="especialidad" name="especialidad" value="<?php echo htmlspecialchars($usuario['especialidad'] ?? ''); ?>" maxlength="100">
                </div>
                <div class="col-12 col-md-6 js-medico-fields">
                    <label class="form-label" for="matricula">Matricula</label>
                    <input class="form-control" id="matricula" name="matricula" value="<?php echo htmlspecialchars($usuario['matricula'] ?? ''); ?>" maxlength="50">
                </div>
                <div class="form-actions"><a class="btn btn-outline-secondary" href="index.php?action=usuarios">Cancelar</a><button class="btn btn-primary" type="submit"><i class="bi bi-check-lg"></i><span>Guardar usuario</span></button></div>
            </form>
        </section>
    </main>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/app.js"></script>
    <script>
        $(function () {
            function toggleMedicoFields() {
                var isMedico = $('#rol').val() === 'medico';
                $('.js-medico-fields').toggle(isMedico);
                $('#especialidad, #matricula').prop('required', isMedico);
            }

            $('#rol').on('change', toggleMedicoFields);
            toggleMedicoFields();
        });
    </script>
</body>
</html>
