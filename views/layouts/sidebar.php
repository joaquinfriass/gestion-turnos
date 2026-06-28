<?php
$currentAction = $_GET['action'] ?? 'dashboard';
$sidebarRole = $sidebarRole ?? (strpos($currentAction, 'recepcion') === 0 ? 'recepcionista' : (strpos($currentAction, 'medico_') === 0 ? 'medico' : 'admin'));

function menuActivo(string $grupo, string $currentAction): string
{
    return strpos($currentAction, $grupo) === 0 ? 'active' : '';
}
?>
<aside class="sidebar">
    <div class="brand">
        <span class="brand-mark"><i class="bi bi-calendar2-pulse"></i></span>
        <span>Gestion Turnos</span>
    </div>

    <nav class="nav flex-column gap-1">
        <?php if ($sidebarRole === 'recepcionista'): ?>
            <a class="nav-link <?php echo $currentAction === 'recepcion_dashboard' ? 'active' : ''; ?>" href="index.php?action=recepcion_dashboard">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a class="nav-link <?php echo menuActivo('recepcion_turnos', $currentAction); ?>" href="index.php?action=recepcion_turnos_crear">
                <i class="bi bi-calendar-plus"></i>
                <span>Nuevo turno</span>
            </a>
            <a class="nav-link <?php echo menuActivo('recepcion_pacientes', $currentAction); ?>" href="index.php?action=recepcion_pacientes">
                <i class="bi bi-people"></i>
                <span>Pacientes</span>
            </a>
            <a class="nav-link <?php echo menuActivo('recepcion_medicos', $currentAction); ?>" href="index.php?action=recepcion_medicos">
                <i class="bi bi-person-badge"></i>
                <span>Medicos</span>
            </a>
        <?php elseif ($sidebarRole === 'medico'): ?>
            <a class="nav-link <?php echo $currentAction === 'medico_dashboard' ? 'active' : ''; ?>" href="index.php?action=medico_dashboard">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a class="nav-link <?php echo menuActivo('medico_turnos_hoy', $currentAction); ?>" href="index.php?action=medico_turnos_hoy">
                <i class="bi bi-calendar-day"></i>
                <span>Turnos de hoy</span>
            </a>
            <a class="nav-link <?php echo menuActivo('medico_turnos_historico', $currentAction); ?>" href="index.php?action=medico_turnos_historico">
                <i class="bi bi-clock-history"></i>
                <span>Historico</span>
            </a>
            <a class="nav-link <?php echo (menuActivo('medico_pacientes', $currentAction) === 'active' || menuActivo('medico_historial_paciente', $currentAction) === 'active') ? 'active' : ''; ?>" href="index.php?action=medico_pacientes">
                <i class="bi bi-person-vcard"></i>
                <span>Pacientes</span>
            </a>
        <?php else: ?>
            <a class="nav-link <?php echo $currentAction === 'dashboard' ? 'active' : ''; ?>" href="index.php?action=dashboard">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a class="nav-link <?php echo menuActivo('turnos', $currentAction); ?>" href="index.php?action=turnos">
                <i class="bi bi-calendar-check"></i>
                <span>Turnos</span>
            </a>
            <a class="nav-link <?php echo menuActivo('pacientes', $currentAction); ?>" href="index.php?action=pacientes">
                <i class="bi bi-people"></i>
                <span>Pacientes</span>
            </a>
            <a class="nav-link <?php echo menuActivo('medicos', $currentAction); ?>" href="index.php?action=medicos">
                <i class="bi bi-person-badge"></i>
                <span>Medicos</span>
            </a>
            <a class="nav-link <?php echo menuActivo('usuarios', $currentAction); ?>" href="index.php?action=usuarios">
                <i class="bi bi-person-gear"></i>
                <span>Usuarios</span>
            </a>
        <?php endif; ?>

        <a class="nav-link mt-3" href="index.php?action=logout">
            <i class="bi bi-box-arrow-right"></i>
            <span>Cerrar sesion</span>
        </a>
    </nav>
</aside>
