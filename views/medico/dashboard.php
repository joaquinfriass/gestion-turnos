<?php
function medicoEstadoBadge(string $estado): string
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
    <title>Dashboard Medico - Gestion de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="public/css/admin-dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="app-shell">
        <?php require __DIR__ . '/../layouts/sidebar.php'; ?>

        <main class="main-content">
            <header class="topbar">
                <div>
                    <p class="eyebrow">Panel medico</p>
                    <h1>Hola, <?php echo htmlspecialchars($medico['nombre'] ?? 'Medico'); ?></h1>
                </div>
                <div class="topbar-actions">
                    <a href="index.php?action=medico_turnos_hoy" class="btn btn-primary">
                        <i class="bi bi-calendar-day"></i>
                        <span>Turnos de hoy</span>
                    </a>
                    <a href="index.php?action=medico_turnos_historico" class="btn btn-outline-primary">
                        <i class="bi bi-clock-history"></i>
                        <span>Historico</span>
                    </a>
                </div>
            </header>

            <?php if (!$medico): ?>
                <div class="alert alert-warning">No hay un medico disponible para mostrar este panel.</div>
            <?php endif; ?>

            <section class="stats-grid reception-stats">
                <article class="stat-card">
                    <div class="stat-icon calendar"><i class="bi bi-calendar2-week"></i></div>
                    <div><span>Turnos hoy</span><strong><?php echo $stats['hoy']; ?></strong></div>
                </article>
                <article class="stat-card">
                    <div class="stat-icon pending"><i class="bi bi-hourglass-split"></i></div>
                    <div><span>Pendientes hoy</span><strong><?php echo $stats['pendientes']; ?></strong></div>
                </article>
                <article class="stat-card">
                    <div class="stat-icon doctors"><i class="bi bi-check2-circle"></i></div>
                    <div><span>Atendidos recientes</span><strong><?php echo $stats['atendidos']; ?></strong></div>
                </article>
                <article class="stat-card">
                    <div class="stat-icon patients"><i class="bi bi-person-vcard"></i></div>
                    <div><span>Pacientes vistos</span><strong><?php echo $stats['pacientes']; ?></strong></div>
                </article>
            </section>

            <section class="content-grid">
                <article class="panel">
                    <div class="panel-header">
                        <div><p class="eyebrow">Agenda</p><h2>Turnos de hoy</h2></div>
                        <a href="index.php?action=medico_turnos_hoy" class="btn btn-sm btn-outline-primary">Ver agenda</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead><tr><th>Hora</th><th>Paciente</th><th>Motivo</th><th>Estado</th></tr></thead>
                            <tbody>
                                <?php if (empty($turnosHoy)): ?><tr><td colspan="4" class="empty-state">No hay turnos para hoy.</td></tr><?php endif; ?>
                                <?php foreach ($turnosHoy as $turno): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(date('H:i', strtotime($turno['fecha_hora']))); ?></td>
                                        <td><?php echo htmlspecialchars($turno['paciente']); ?></td>
                                        <td><?php echo htmlspecialchars($turno['motivo'] ?: 'Sin motivo'); ?></td>
                                        <td><span class="badge <?php echo medicoEstadoBadge($turno['estado']); ?>"><?php echo htmlspecialchars($turno['estado']); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </article>

                <article class="panel">
                    <div class="panel-header">
                        <div><p class="eyebrow">Pacientes</p><h2>Historial por paciente</h2></div>
                        <a href="index.php?action=medico_pacientes" class="btn btn-sm btn-outline-primary">Ver todos</a>
                    </div>
                    <div class="user-list">
                        <?php if (empty($pacientes)): ?><p class="empty-state">Todavia no hay pacientes asociados.</p><?php endif; ?>
                        <?php foreach (array_slice($pacientes, 0, 5) as $paciente): ?>
                            <a class="user-row text-decoration-none" href="index.php?action=medico_historial_paciente&id_paciente=<?php echo (int) $paciente['id']; ?>">
                                <div class="avatar"><?php echo htmlspecialchars(strtoupper(substr($paciente['nombre'], 0, 1))); ?></div>
                                <div>
                                    <strong><?php echo htmlspecialchars($paciente['apellido'] . ', ' . $paciente['nombre']); ?></strong>
                                    <span><?php echo (int) $paciente['total_turnos']; ?> turnos - ultimo <?php echo htmlspecialchars(date('d/m/Y', strtotime($paciente['ultimo_turno']))); ?></span>
                                </div>
                                <span class="role-pill">Ver</span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </article>
            </section>
        </main>
    </div>
</body>
</html>
