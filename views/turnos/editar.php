<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar turno - Gestion de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="public/css/turnos.css" rel="stylesheet">
</head>
<body>
    <main class="page-shell narrow">
        <header class="page-header">
            <div>
                <a class="back-link" href="index.php?action=turnos">
                    <i class="bi bi-arrow-left"></i>
                    <span>Turnos</span>
                </a>
                <h1>Editar turno</h1>
            </div>
        </header>

        <?php if (!empty($errores)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errores as $error): ?>
                    <div><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <section class="form-panel">
            <form action="index.php?action=turnos_editar&id=<?php echo (int) $id; ?>" method="POST" class="row g-3">
                <input type="hidden" name="id" value="<?php echo (int) $id; ?>">

                <div class="col-12 col-md-6">
                    <label class="form-label" for="id_paciente">Paciente</label>
                    <select class="form-select" id="id_paciente" name="id_paciente" required>
                        <option value="">Seleccionar paciente</option>
                        <?php foreach ($pacientes as $paciente): ?>
                            <option value="<?php echo (int) $paciente['id']; ?>" <?php echo ((int) $turno['id_paciente'] === (int) $paciente['id']) ? 'selected' : ''; ?>>
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
                            <option value="<?php echo (int) $medico['id']; ?>" <?php echo ((int) $turno['id_medico'] === (int) $medico['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($medico['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="fecha_hora">Fecha y hora</label>
                    <input
                        class="form-control"
                        type="datetime-local"
                        id="fecha_hora"
                        name="fecha_hora"
                        value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($turno['fecha_hora']))); ?>"
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
                    <textarea class="form-control" id="motivo" name="motivo" rows="4" maxlength="255"><?php echo htmlspecialchars($turno['motivo'] ?? ''); ?></textarea>
                </div>

                <div class="form-actions">
                    <a class="btn btn-outline-secondary" href="index.php?action=turnos">Cancelar</a>
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-check-lg"></i>
                        <span>Actualizar turno</span>
                    </button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
