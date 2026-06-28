<?php

require_once __DIR__ . '/../models/Turno.php';

class MedicoDashboardController
{
    private Turno $turnoModel;

    public function __construct()
    {
        $this->turnoModel = new Turno();
    }

    public function dashboard(): void
    {
        $contexto = $this->resolverMedico();
        $idMedico = $contexto['id_medico'];
        $medico = $contexto['medico'];
        $sidebarRole = 'medico';

        $turnosHoy = $idMedico ? $this->turnoModel->listarPorMedico($idMedico, ['hoy' => true, 'limite' => 8]) : [];
        $historicos = $idMedico ? $this->turnoModel->listarPorMedico($idMedico, ['limite' => 8]) : [];
        $pacientes = $idMedico ? $this->turnoModel->resumenPacientesPorMedico($idMedico) : [];

        $stats = [
            'hoy' => count($turnosHoy),
            'pendientes' => $this->contarEstado($turnosHoy, 'pendiente'),
            'atendidos' => $this->contarEstado($historicos, 'atendido'),
            'pacientes' => count($pacientes),
        ];

        require_once __DIR__ . '/../views/medico/dashboard.php';
    }

    public function turnosHoy(): void
    {
        $contexto = $this->resolverMedico();
        $idMedico = $contexto['id_medico'];
        $medico = $contexto['medico'];
        $sidebarRole = 'medico';
        $turnos = $idMedico ? $this->turnoModel->listarPorMedico($idMedico, ['hoy' => true]) : [];
        $titulo = 'Turnos del dia';
        $modo = 'hoy';

        require_once __DIR__ . '/../views/medico/turnos.php';
    }

    public function historico(): void
    {
        $contexto = $this->resolverMedico();
        $idMedico = $contexto['id_medico'];
        $medico = $contexto['medico'];
        $sidebarRole = 'medico';
        $filtros = [
            'desde' => $_GET['desde'] ?? '',
            'hasta' => $_GET['hasta'] ?? '',
            'estado' => $_GET['estado'] ?? '',
            'busqueda' => trim($_GET['busqueda'] ?? ''),
        ];
        $turnos = $idMedico ? $this->turnoModel->listarPorMedico($idMedico, $filtros) : [];
        $titulo = 'Turnos historicos';
        $modo = 'historico';

        require_once __DIR__ . '/../views/medico/turnos.php';
    }

    public function pacientes(): void
    {
        $contexto = $this->resolverMedico();
        $idMedico = $contexto['id_medico'];
        $medico = $contexto['medico'];
        $sidebarRole = 'medico';
        $busqueda = trim($_GET['busqueda'] ?? '');
        $pacientes = $idMedico ? $this->turnoModel->resumenPacientesPorMedico($idMedico, $busqueda) : [];

        require_once __DIR__ . '/../views/medico/pacientes.php';
    }

    public function historialPaciente(): void
    {
        $contexto = $this->resolverMedico();
        $idMedico = $contexto['id_medico'];
        $medico = $contexto['medico'];
        $sidebarRole = 'medico';
        $idPaciente = (int) ($_GET['id_paciente'] ?? 0);
        $turnos = ($idMedico && $idPaciente > 0) ? $this->turnoModel->historialPacientePorMedico($idMedico, $idPaciente) : [];
        $paciente = $turnos[0] ?? null;

        require_once __DIR__ . '/../views/medico/historial-paciente.php';
    }

    public function marcarAtendido(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['ok' => false, 'message' => 'Metodo no permitido.']);
            return;
        }

        $contexto = $this->resolverMedico();
        $idMedico = $contexto['id_medico'];
        $idTurno = (int) ($_POST['id_turno'] ?? 0);

        if ($idMedico <= 0 || $idTurno <= 0) {
            echo json_encode(['ok' => false, 'message' => 'Turno no encontrado.']);
            return;
        }

        $actualizado = $this->turnoModel->marcarAtendido($idTurno, $idMedico);

        echo json_encode([
            'ok' => $actualizado,
            'message' => $actualizado ? 'Turno marcado como atendido.' : 'No se pudo actualizar el turno.',
        ]);
    }

    private function resolverMedico(): array
    {
        $idSesion = (int) ($_SESSION['usuario_id'] ?? 0);
        $medico = $idSesion > 0 ? $this->turnoModel->obtenerMedicoPorId($idSesion) : null;

        if (!$medico) {
            $medico = $this->turnoModel->obtenerPrimerMedico();
        }

        return [
            'id_medico' => $medico ? (int) $medico['id'] : 0,
            'medico' => $medico,
        ];
    }

    private function contarEstado(array $turnos, string $estado): int
    {
        return count(array_filter($turnos, fn (array $turno): bool => $turno['estado'] === $estado));
    }
}
