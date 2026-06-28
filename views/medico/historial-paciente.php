<?php
function historialBadge(string $estado): string
{
    return match ($estado) {
        'confirmado' => 'text-bg-success',
        'cancelado' => 'text-bg-danger',
        'atendido' => 'text-bg-secondary',
        default => 'text-bg-warning',
    };
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial del paciente - Gestion de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="public/css/turnos.css" rel="stylesheet">
</head>
<body>
    <div class="app-shell">
        <?php require __DIR__ . '/../layouts/sidebar.php'; ?>
        <main class="main-content page-shell">
            <header class="page-header">
                <div>
                    <a class="back-link" href="index.php?action=medico_pacientes"><i class="bi bi-arrow-left"></i><span>Pacientes</span></a>
                    <h1><?php echo $paciente ? htmlspecialchars($paciente['paciente']) : 'Historial del paciente'; ?></h1>
                    <?php if ($paciente): ?>
                        <p class="text-secondary mb-0">DNI <?php echo htmlspecialchars($paciente['paciente_dni']); ?> - <?php echo htmlspecialchars($paciente['paciente_telefono'] ?: 'Sin telefono'); ?></p>
                    <?php endif; ?>
                </div>
            </header>

            <section class="data-panel">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Fecha y hora</th><th>Motivo</th><th>Estado</th></tr></thead>
                        <tbody>
                            <?php if (empty($turnos)): ?><tr><td colspan="3" class="empty-state">No hay historial disponible para este paciente.</td></tr><?php endif; ?>
                            <?php foreach ($turnos as $turno): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars(date('d/m/Y', strtotime($turno['fecha_hora']))); ?></strong><span><?php echo htmlspecialchars(date('H:i', strtotime($turno['fecha_hora']))); ?></span></td>
                                    <td><?php echo htmlspecialchars($turno['motivo'] ?: 'Sin motivo'); ?></td>
                                    <td><span class="badge <?php echo historialBadge($turno['estado']); ?>"><?php echo htmlspecialchars($turno['estado']); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
