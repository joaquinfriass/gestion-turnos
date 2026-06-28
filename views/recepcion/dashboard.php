<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/conexion.php';

$sidebarRole = 'recepcionista';
$nombreUsuario = $_SESSION['usuario_nombre'] ?? 'Recepcionista';
$mensaje = $_GET['mensaje'] ?? null;
$stats = [
    'turnos_hoy' => 0,
    'pendientes' => 0,
    'confirmados' => 0,
    'pacientes' => 0,
    'medicos' => 0,
];
$turnosHoy = [];
$pacientesRecientes = [];
$errorDashboard = null;

try {
    $db = conexion::conexion();

    $stats['turnos_hoy'] = (int) $db->query('SELECT COUNT(*) FROM turnos WHERE DATE(fecha_hora) = CURDATE()')->fetchColumn();
    $stats['pendientes'] = (int) $db->query("SELECT COUNT(*) FROM turnos WHERE estado = 'pendiente'")->fetchColumn();
    $stats['confirmados'] = (int) $db->query("SELECT COUNT(*) FROM turnos WHERE estado = 'confirmado'")->fetchColumn();
    $stats['pacientes'] = (int) $db->query('SELECT COUNT(*) FROM pacientes')->fetchColumn();
    $stats['medicos'] = (int) $db->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'medico'")->fetchColumn();

    $stmtTurnos = $db->query("
        SELECT
            t.fecha_hora,
            t.estado,
            t.motivo,
            CONCAT(p.nombre, ' ', p.apellido) AS paciente,
            u.nombre AS medico
        FROM turnos t
        INNER JOIN pacientes p ON p.id = t.id_paciente
        INNER JOIN usuarios u ON u.id = t.id_medico
        WHERE DATE(t.fecha_hora) = CURDATE()
        ORDER BY t.fecha_hora ASC
        LIMIT 8
    ");
    $turnosHoy = $stmtTurnos->fetchAll();

    $stmtPacientes = $db->query("
        SELECT dni, nombre, apellido, telefono, created_at
        FROM pacientes
        ORDER BY created_at DESC, id DESC
        LIMIT 6
    ");
    $pacientesRecientes = $stmtPacientes->fetchAll();
} catch (Throwable $e) {
    error_log('Error en dashboard recepcion: ' . $e->getMessage());
    $errorDashboard = 'No se pudieron cargar los datos del dashboard.';
}

function estadoBadgeRecepcion(string $estado): string
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
    <title>Dashboard Recepcion - Gestion de Turnos</title>
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
                    <p class="eyebrow">Panel de recepcion</p>
                    <h1>Hola, <?php echo htmlspecialchars($nombreUsuario); ?></h1>
                </div>
                <div class="topbar-actions">
                    <a href="index.php?action=recepcion_turnos_crear" class="btn btn-primary">
                        <i class="bi bi-calendar-plus"></i>
                        <span>Nuevo turno</span>
                    </a>
                    <a href="index.php?action=recepcion_pacientes" class="btn btn-outline-primary">
                        <i class="bi bi-people"></i>
                        <span>Pacientes</span>
                    </a>
                    <a href="index.php?action=recepcion_medicos" class="btn btn-outline-primary">
                        <i class="bi bi-person-badge"></i>
                        <span>Medicos</span>
                    </a>
                </div>
            </header>

            <?php if ($mensaje): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
            <?php endif; ?>

            <?php if ($errorDashboard): ?>
                <div class="alert alert-warning"><?php echo htmlspecialchars($errorDashboard); ?></div>
            <?php endif; ?>

            <section class="stats-grid reception-stats">
                <article class="stat-card">
                    <div class="stat-icon calendar"><i class="bi bi-calendar2-week"></i></div>
                    <div>
                        <span>Turnos hoy</span>
                        <strong><?php echo $stats['turnos_hoy']; ?></strong>
                    </div>
                </article>
                <article class="stat-card">
                    <div class="stat-icon pending"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <span>Pendientes</span>
                        <strong><?php echo $stats['pendientes']; ?></strong>
                    </div>
                </article>
                <article class="stat-card">
                    <div class="stat-icon doctors"><i class="bi bi-check2-circle"></i></div>
                    <div>
                        <span>Confirmados</span>
                        <strong><?php echo $stats['confirmados']; ?></strong>
                    </div>
                </article>
                <article class="stat-card">
                    <div class="stat-icon patients"><i class="bi bi-person-vcard"></i></div>
                    <div>
                        <span>Pacientes</span>
                        <strong><?php echo $stats['pacientes']; ?></strong>
                    </div>
                </article>
                <article class="stat-card">
                    <div class="stat-icon users"><i class="bi bi-person-badge"></i></div>
                    <div>
                        <span>Medicos disponibles</span>
                        <strong><?php echo $stats['medicos']; ?></strong>
                    </div>
                </article>
            </section>

            <section class="content-grid">
                <article class="panel">
                    <div class="panel-header">
                        <div>
                            <p class="eyebrow">Agenda</p>
                            <h2>Turnos de hoy</h2>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Hora</th>
                                    <th>Paciente</th>
                                    <th>Medico</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($turnosHoy)): ?>
                                    <tr>
                                        <td colspan="4" class="empty-state">No hay turnos cargados para hoy.</td>
                                    </tr>
                                <?php endif; ?>

                                <?php foreach ($turnosHoy as $turno): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(date('H:i', strtotime($turno['fecha_hora']))); ?></td>
                                        <td><?php echo htmlspecialchars($turno['paciente']); ?></td>
                                        <td><?php echo htmlspecialchars($turno['medico']); ?></td>
                                        <td>
                                            <span class="badge <?php echo estadoBadgeRecepcion($turno['estado']); ?>">
                                                <?php echo htmlspecialchars($turno['estado']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </article>

                <article class="panel">
                    <div class="panel-header">
                        <div>
                            <p class="eyebrow">Pacientes</p>
                            <h2>Altas recientes</h2>
                        </div>
                    </div>

                    <div class="user-list">
                        <?php if (empty($pacientesRecientes)): ?>
                            <p class="empty-state">Todavia no hay pacientes cargados.</p>
                        <?php endif; ?>

                        <?php foreach ($pacientesRecientes as $paciente): ?>
                            <div class="user-row">
                                <div class="avatar">
                                    <?php echo htmlspecialchars(strtoupper(substr($paciente['nombre'], 0, 1))); ?>
                                </div>
                                <div>
                                    <strong><?php echo htmlspecialchars($paciente['apellido'] . ', ' . $paciente['nombre']); ?></strong>
                                    <span>DNI <?php echo htmlspecialchars($paciente['dni']); ?> · <?php echo htmlspecialchars($paciente['telefono'] ?: 'Sin telefono'); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </article>
            </section>
        </main>
    </div>
</body>
</html>
