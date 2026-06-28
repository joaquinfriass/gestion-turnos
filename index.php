<?php

require_once __DIR__ . '/controllers/TurnoController.php';

$action = $_GET['action'] ?? 'dashboard';

switch ($action) {
    case 'dashboard':
        require_once __DIR__ . '/views/admin/dashboard.php';
        break;

    case 'turnos':
        (new TurnoController())->index();
        break;

    case 'turnos_crear':
        (new TurnoController())->crear();
        break;

    case 'turnos_editar':
        (new TurnoController())->editar();
        break;

    case 'turnos_eliminar':
        (new TurnoController())->eliminar();
        break;

    default:
        require_once __DIR__ . '/views/admin/dashboard.php';
        break;
}
