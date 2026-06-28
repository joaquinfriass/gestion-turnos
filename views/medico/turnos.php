<?php
$estados = ['pendiente', 'confirmado', 'cancelado', 'atendido'];

function medicoTurnoBadge(string $estado): string
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
    <title><?php echo htmlspecialchars($titulo); ?> - Gestion de Turnos</title>
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
                    <a class="back-link" href="index.php?action=medico_dashboard"><i class="bi bi-arrow-left"></i><span>Dashboard</span></a>
                    <h1><?php echo htmlspecialchars($titulo); ?></h1>
                </div>
            </header>

            <?php if ($modo === 'historico'): ?>
                <section class="filter-bar">
                    <form action="index.php" method="GET" class="row g-3 align-items-end">
                        <input type="hidden" name="action" value="medico_turnos_historico">
                        <div class="col-12 col-md-3">
                            <label class="form-label" for="busqueda">Buscar</label>
                            <input class="form-control" type="search" id="busqueda" name="busqueda" value="<?php echo htmlspecialchars($filtros['busqueda'] ?? ''); ?>" placeholder="Paciente, DNI o motivo">
                        </div>
                        <div class="col-12 col-md-2">
                            <label class="form-label" for="desde">Desde</label>
                            <input class="form-control" type="date" id="desde" name="desde" value="<?php echo htmlspecialchars($filtros['desde'] ?? ''); ?>">
                        </div>
                        <div class="col-12 col-md-2">
                            <label class="form-label" for="hasta">Hasta</label>
                            <input class="form-control" type="date" id="hasta" name="hasta" value="<?php echo htmlspecialchars($filtros['hasta'] ?? ''); ?>">
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label" for="estado">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="">Todos</option>
                                <?php foreach ($estados as $estado): ?>
                                    <option value="<?php echo $estado; ?>" <?php echo (($filtros['estado'] ?? '') === $estado) ? 'selected' : ''; ?>><?php echo ucfirst($estado); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-2 d-grid">
                            <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i><span>Filtrar</span></button>
                        </div>
                    </form>
                </section>
            <?php endif; ?>

            <section class="data-panel">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Fecha y hora</th><th>Paciente</th><th>Contacto</th><th>Motivo</th><th>Estado</th><th>Acciones</th></tr></thead>
                        <tbody>
                            <?php if (empty($turnos)): ?><tr><td colspan="6" class="empty-state">No hay turnos para mostrar.</td></tr><?php endif; ?>
                            <?php foreach ($turnos as $turno): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars(date('d/m/Y', strtotime($turno['fecha_hora']))); ?></strong><span><?php echo htmlspecialchars(date('H:i', strtotime($turno['fecha_hora']))); ?></span></td>
                                    <td><strong><?php echo htmlspecialchars($turno['paciente']); ?></strong><span>DNI <?php echo htmlspecialchars($turno['paciente_dni']); ?></span></td>
                                    <td><?php echo htmlspecialchars($turno['paciente_telefono'] ?: 'Sin telefono'); ?></td>
                                    <td><?php echo htmlspecialchars($turno['motivo'] ?: 'Sin motivo'); ?></td>
                                    <td><span class="badge js-estado-turno <?php echo medicoTurnoBadge($turno['estado']); ?>"><?php echo htmlspecialchars($turno['estado']); ?></span></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a class="btn btn-sm btn-outline-primary" href="index.php?action=medico_historial_paciente&id_paciente=<?php echo (int) $turno['id_paciente']; ?>">Ver</a>
                                            <?php if ($turno['estado'] !== 'atendido'): ?>
                                                <button class="btn btn-sm btn-outline-success js-marcar-atendido" type="button" data-turno-id="<?php echo (int) $turno['id']; ?>">Atendido</button>
                                            <?php else: ?>
                                                <span class="text-secondary small align-self-center">Atendido</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/app.js"></script>
    <script src="public/js/medico.js"></script>
</body>
</html>
