<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo turno - Gestion de Turnos</title>
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
                <a class="back-link" href="index.php?action=<?php echo htmlspecialchars($cancelAction ?? 'turnos'); ?>">
                    <i class="bi bi-arrow-left"></i>
                    <span>Turnos</span>
                </a>
                <h1>Nuevo turno</h1>
            </div>
        </header>

        <?php if (!empty($errores)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errores as $error): ?>
                    <div><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($pacientes) || empty($medicos)): ?>
            <div class="alert alert-warning">
                Para crear turnos necesitas tener al menos un paciente y un usuario con rol medico.
            </div>
        <?php endif; ?>

        <section class="form-panel">
            <form action="index.php?action=<?php echo htmlspecialchars($formAction ?? 'turnos_crear'); ?>" method="POST" class="row g-3 js-validate js-turno-form">
                <?php echo AuthController::csrfInput(); ?>
                <div class="col-12">
                    <label class="form-label" for="buscar_paciente">Buscar paciente</label>
                    <input class="form-control js-filter-select" id="buscar_paciente" type="search" placeholder="DNI, nombre o apellido" data-target="#id_paciente">
                </div>

                <div class="col-12 col-md-6 js-horario-anchor">
                    <label class="form-label" for="id_paciente">Paciente</label>
                    <select class="form-select" id="id_paciente" name="id_paciente" required>
                        <option value="">Seleccionar paciente</option>
                        <?php foreach ($pacientes as $paciente): ?>
                            <option value="<?php echo (int) $paciente['id']; ?>" data-search="<?php echo htmlspecialchars(strtolower($paciente['apellido'] . ' ' . $paciente['nombre'] . ' ' . $paciente['dni'])); ?>" <?php echo ((int) $turno['id_paciente'] === (int) $paciente['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($paciente['apellido'] . ', ' . $paciente['nombre'] . ' - DNI ' . $paciente['dni']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="id_medico">Medico</label>
                    <select class="form-select" id="id_medico" name="id_medico" required>
                        <option value="">Seleccionar medico</option>
                        <?php foreach ($medicos as $medico): ?>
                            <option value="<?php echo (int) $medico['id']; ?>" data-especialidad="<?php echo htmlspecialchars(strtolower($medico['especialidad'] ?? '')); ?>" data-matricula="<?php echo htmlspecialchars(strtolower($medico['matricula'] ?? '')); ?>" <?php echo ((int) $turno['id_medico'] === (int) $medico['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($medico['nombre'] . ' - ' . ($medico['especialidad'] ?: 'Sin especialidad') . ' - ' . ($medico['matricula'] ?: 'Sin matricula')); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="filtro_especialidad">Especialidad</label>
                    <input class="form-control js-filter-medicos" id="filtro_especialidad" type="search" placeholder="Filtrar especialidad" data-field="especialidad">
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="filtro_matricula">Matricula</label>
                    <input class="form-control js-filter-medicos" id="filtro_matricula" type="search" placeholder="Filtrar matricula" data-field="matricula">
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="fecha_hora">Fecha y hora</label>
                    <input
                        class="form-control"
                        type="datetime-local"
                        id="fecha_hora"
                        name="fecha_hora"
                        value="<?php echo htmlspecialchars($turno['fecha_hora'] ? date('Y-m-d\TH:i', strtotime($turno['fecha_hora'])) : ''); ?>"
                        min="<?php echo date('Y-m-d\TH:i'); ?>"
                        required
                    >
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="estado">Estado</label>
                    <select class="form-select" id="estado" name="estado" required>
                        <?php foreach (['pendiente', 'confirmado', 'cancelado', 'atendido'] as $estado): ?>
                            <option value="<?php echo $estado; ?>" <?php echo ($turno['estado'] === $estado) ? 'selected' : ''; ?>>
                                <?php echo ucfirst($estado); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label" for="motivo">Motivo</label>
                    <textarea class="form-control" id="motivo" name="motivo" rows="4" maxlength="255"><?php echo htmlspecialchars($turno['motivo']); ?></textarea>
                </div>

                <div class="form-actions">
                    <a class="btn btn-outline-secondary" href="index.php?action=<?php echo htmlspecialchars($cancelAction ?? 'turnos'); ?>">Cancelar</a>
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-check-lg"></i>
                        <span>Guardar turno</span>
                    </button>
                </div>
            </form>
        </section>
    </main>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/app.js"></script>
    <script src="public/js/turnos.js"></script>
</body>
</html>
