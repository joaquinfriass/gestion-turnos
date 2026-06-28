<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/conexion.php';

$nombreUsuario = $_SESSION['usuario_nombre'] ?? 'Administrador';
$stats = [
    'usuarios' => 0,
    'pacientes' => 0,
    'medicos' => 0,
    'turnos_hoy' => 0,
    'turnos_pendientes' => 0,
];
$turnosRecientes = [];
$usuariosRecientes = [];
$errorDashboard = null;

try {
    $db = conexion::conexion();

    $stats['usuarios'] = (int) $db->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
    $stats['pacientes'] = (int) $db->query('SELECT COUNT(*) FROM pacientes')->fetchColumn();
    $stats['medicos'] = (int) $db->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'medico'")->fetchColumn();
    $stats['turnos_hoy'] = (int) $db->query('SELECT COUNT(*) FROM turnos WHERE DATE(fecha_hora) = CURDATE()')->fetchColumn();
    $stats['turnos_pendientes'] = (int) $db->query("SELECT COUNT(*) FROM turnos WHERE estado = 'pendiente'")->fetchColumn();

    $stmtTurnos = $db->query("
        SELECT
            t.fecha_hora,
            t.estado,
            t.motivo,
            CONCAT(p.nombre, ' ', p.apellido) AS paciente,
            COALESCE(u.nombre, 'Sin medico asignado') AS medico
        FROM turnos t
        LEFT JOIN pacientes p ON p.id = t.id_paciente
        LEFT JOIN usuarios u ON u.id = t.id_medico
        ORDER BY t.fecha_hora DESC
        LIMIT 5
    ");
    $turnosRecientes = $stmtTurnos->fetchAll();

    $stmtUsuarios = $db->query("
        SELECT nombre, email, rol, created_at
        FROM usuarios
        ORDER BY created_at DESC, id DESC
        LIMIT 5
    ");
    $usuariosRecientes = $stmtUsuarios->fetchAll();
} catch (Throwable $e) {
    error_log('Error en dashboard admin: ' . $e->getMessage());
    $errorDashboard = 'No se pudieron cargar los datos del dashboard.';
}

function badgeEstado(string $estado): string
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
    <title>Dashboard Admin - Gestion de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="public/css/admin-dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="brand">
                <span class="brand-mark"><i class="bi bi-calendar2-pulse"></i></span>
                <span>Gestion Turnos</span>
            </div>

            <nav class="nav flex-column gap-1">
                <a class="nav-link active" href="index.php?action=dashboard">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <a class="nav-link" href="index.php?action=turnos">
                    <i class="bi bi-calendar-check"></i>
                    <span>Turnos</span>
                </a>
                <a class="nav-link" href="#">
                    <i class="bi bi-people"></i>
                    <span>Pacientes</span>
                </a>
                <a class="nav-link" href="#">
                    <i class="bi bi-person-badge"></i>
                    <span>Medicos</span>
                </a>
                <a class="nav-link" href="#">
                    <i class="bi bi-person-gear"></i>
                    <span>Usuarios</span>
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div>
                    <p class="eyebrow">Panel administrativo</p>
                    <h1>Hola, <?php echo htmlspecialchars($nombreUsuario); ?></h1>
                </div>
                <div class="topbar-actions">
                    <a href="index.php?action=turnos_crear" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i>
                        <span>Nuevo turno</span>
                    </a>
                    <a href="index.php?action=logout" class="btn btn-outline-secondary icon-btn" title="Cerrar sesion">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                </div>
            </header>

            <?php if ($errorDashboard): ?>
                <div class="alert alert-warning"><?php echo htmlspecialchars($errorDashboard); ?></div>
            <?php endif; ?>

            <section class="stats-grid">
                <article class="stat-card">
                    <div class="stat-icon users"><i class="bi bi-person-lines-fill"></i></div>
                    <div>
                        <span>Usuarios</span>
                        <strong><?php echo $stats['usuarios']; ?></strong>
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
                    <div class="stat-icon doctors"><i class="bi bi-heart-pulse"></i></div>
                    <div>
                        <span>Medicos</span>
                        <strong><?php echo $stats['medicos']; ?></strong>
                    </div>
                </article>
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
                        <strong><?php echo $stats['turnos_pendientes']; ?></strong>
                    </div>
                </article>
            </section>

            <section class="content-grid">
                <article class="panel">
                    <div class="panel-header">
                        <div>
                            <p class="eyebrow">Agenda</p>
                            <h2>Turnos recientes</h2>
                        </div>
                        <a href="index.php?action=turnos" class="btn btn-sm btn-outline-primary">Ver todos</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Paciente</th>
                                    <th>Medico</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($turnosRecientes)): ?>
                                    <tr>
                                        <td colspan="4" class="empty-state">Todavia no hay turnos cargados.</td>
                                    </tr>
                                <?php endif; ?>

                                <?php foreach ($turnosRecientes as $turno): ?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($turno['fecha_hora']))); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($turno['paciente'] ?? 'Sin paciente'); ?></td>
                                        <td><?php echo htmlspecialchars($turno['medico']); ?></td>
                                        <td>
                                            <span class="badge <?php echo badgeEstado($turno['estado']); ?>">
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
                            <p class="eyebrow">Equipo</p>
                            <h2>Usuarios recientes</h2>
                        </div>
                    </div>

                    <div class="user-list">
                        <?php if (empty($usuariosRecientes)): ?>
                            <p class="empty-state">Todavia no hay usuarios cargados.</p>
                        <?php endif; ?>

                        <?php foreach ($usuariosRecientes as $usuario): ?>
                            <div class="user-row">
                                <div class="avatar">
                                    <?php echo htmlspecialchars(strtoupper(substr($usuario['nombre'], 0, 1))); ?>
                                </div>
                                <div>
                                    <strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong>
                                    <span><?php echo htmlspecialchars($usuario['email']); ?></span>
                                </div>
                                <span class="role-pill"><?php echo htmlspecialchars($usuario['rol']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </article>
            </section>
        </main>
    </div>
</body>
</html>
