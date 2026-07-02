<?php

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/TurnoController.php';
require_once __DIR__ . '/controllers/PacienteController.php';
require_once __DIR__ . '/controllers/MedicoController.php';
require_once __DIR__ . '/controllers/UsuarioController.php';
require_once __DIR__ . '/controllers/MedicoDashboardController.php';

$action = $_GET['action'] ?? 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    AuthController::verificarCsrf();
}

switch ($action) {
    case 'login':
        (new AuthController())->login();
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    case 'dashboard':
        AuthController::requerirSesion(['admin']);
        require_once __DIR__ . '/views/admin/dashboard.php';
        break;

    case 'recepcion_dashboard':
        AuthController::requerirSesion(['recepcionista']);
        require_once __DIR__ . '/views/recepcion/dashboard.php';
        break;

    case 'recepcion_turnos_crear':
        AuthController::requerirSesion(['recepcionista']);
        (new TurnoController())->crear();
        break;

    case 'ajax_turno_horario':
        AuthController::requerirSesion(['admin', 'recepcionista']);
        (new TurnoController())->verificarHorario();
        break;

    case 'recepcion_pacientes':
        AuthController::requerirSesion(['recepcionista']);
        (new PacienteController())->index();
        break;

    case 'recepcion_pacientes_crear':
        AuthController::requerirSesion(['recepcionista']);
        (new PacienteController())->crear();
        break;

    case 'recepcion_medicos':
        AuthController::requerirSesion(['recepcionista']);
        (new MedicoController())->index();
        break;

    case 'medico_dashboard':
        AuthController::requerirSesion(['medico']);
        (new MedicoDashboardController())->dashboard();
        break;

    case 'medico_turnos_hoy':
        AuthController::requerirSesion(['medico']);
        (new MedicoDashboardController())->turnosHoy();
        break;

    case 'medico_turnos_historico':
        AuthController::requerirSesion(['medico']);
        (new MedicoDashboardController())->historico();
        break;

    case 'medico_pacientes':
        AuthController::requerirSesion(['medico']);
        (new MedicoDashboardController())->pacientes();
        break;

    case 'medico_historial_paciente':
        AuthController::requerirSesion(['medico']);
        (new MedicoDashboardController())->historialPaciente();
        break;

    case 'medico_marcar_atendido':
        AuthController::requerirSesion(['medico']);
        (new MedicoDashboardController())->marcarAtendido();
        break;

    case 'turnos':
        AuthController::requerirSesion(['admin']);
        (new TurnoController())->index();
        break;

    case 'turnos_crear':
        AuthController::requerirSesion(['admin']);
        (new TurnoController())->crear();
        break;

    case 'turnos_editar':
        AuthController::requerirSesion(['admin']);
        (new TurnoController())->editar();
        break;

    case 'turnos_eliminar':
        AuthController::requerirSesion(['admin']);
        (new TurnoController())->eliminar();
        break;

    case 'pacientes':
        AuthController::requerirSesion(['admin']);
        (new PacienteController())->index();
        break;

    case 'pacientes_crear':
        AuthController::requerirSesion(['admin']);
        (new PacienteController())->crear();
        break;

    case 'pacientes_editar':
        AuthController::requerirSesion(['admin']);
        (new PacienteController())->editar();
        break;

    case 'pacientes_eliminar':
        AuthController::requerirSesion(['admin']);
        (new PacienteController())->eliminar();
        break;

    case 'medicos':
        AuthController::requerirSesion(['admin']);
        (new MedicoController())->index();
        break;

    case 'medicos_crear':
        AuthController::requerirSesion(['admin']);
        (new MedicoController())->crear();
        break;

    case 'medicos_editar':
        AuthController::requerirSesion(['admin']);
        (new MedicoController())->editar();
        break;

    case 'medicos_eliminar':
        AuthController::requerirSesion(['admin']);
        (new MedicoController())->eliminar();
        break;

    case 'usuarios':
        AuthController::requerirSesion(['admin']);
        (new UsuarioController())->index();
        break;

    case 'usuarios_crear':
        AuthController::requerirSesion(['admin']);
        (new UsuarioController())->crear();
        break;

    case 'usuarios_editar':
        AuthController::requerirSesion(['admin']);
        (new UsuarioController())->editar();
        break;

    case 'usuarios_eliminar':
        AuthController::requerirSesion(['admin']);
        (new UsuarioController())->eliminar();
        break;

    default:
        header('Location: index.php?action=login');
        exit;
        break;
}
