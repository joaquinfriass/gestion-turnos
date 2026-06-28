<?php

require_once __DIR__ . '/../models/Turno.php';

class TurnoController
{
    private Turno $turnoModel;

    public function __construct()
    {
        $this->turnoModel = new Turno();
    }

    public function index(): void
    {
        $filtros = [
            'estado' => $_GET['estado'] ?? '',
            'fecha' => $_GET['fecha'] ?? '',
            'busqueda' => trim($_GET['busqueda'] ?? ''),
        ];

        $turnos = $this->turnoModel->listar($filtros);
        $mensaje = $_GET['mensaje'] ?? null;
        $error = $_GET['error'] ?? null;

        require_once __DIR__ . '/../views/turnos/index.php';
    }

    public function crear(): void
    {
        $esRecepcion = ($_GET['action'] ?? '') === 'recepcion_turnos_crear';
        $formAction = $esRecepcion ? 'recepcion_turnos_crear' : 'turnos_crear';
        $cancelAction = $esRecepcion ? 'recepcion_dashboard' : 'turnos';
        $sidebarRole = $esRecepcion ? 'recepcionista' : 'admin';
        $pacientes = $this->turnoModel->listarPacientes();
        $medicos = $this->turnoModel->listarMedicos();
        $turno = [
            'id_medico' => '',
            'id_paciente' => '',
            'fecha_hora' => '',
            'motivo' => '',
            'estado' => 'pendiente',
        ];
        $errores = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $turno = $this->normalizarDatos($_POST);
            $errores = $this->validar($turno);

            if (empty($errores) && $this->turnoModel->horarioOcupado((int) $turno['id_medico'], $turno['fecha_hora'])) {
                $errores[] = 'El medico ya tiene un turno en esa fecha y hora.';
            }

            if (empty($errores)) {
                try {
                    $this->turnoModel->crear($turno);
                    if ($esRecepcion) {
                        $this->redirigir('index.php?action=recepcion_dashboard&mensaje=Turno creado correctamente');
                    }
                    $this->redirigir('index.php?action=turnos&mensaje=Turno creado correctamente');
                } catch (PDOException $e) {
                    error_log('Error al crear turno: ' . $e->getMessage());
                    $errores[] = 'No se pudo crear el turno.';
                }
            }
        }

        $modo = 'crear';
        require_once __DIR__ . '/../views/turnos/crear.php';
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

        if ($id <= 0) {
            $this->redirigir('index.php?action=turnos&error=Turno no encontrado');
        }

        $turnoActual = $this->turnoModel->obtenerPorId($id);

        if (!$turnoActual) {
            $this->redirigir('index.php?action=turnos&error=Turno no encontrado');
        }

        $pacientes = $this->turnoModel->listarPacientes();
        $medicos = $this->turnoModel->listarMedicos();
        $turno = $turnoActual;
        $errores = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $turno = array_merge($turnoActual, $this->normalizarDatos($_POST));
            $errores = $this->validar($turno);

            if (empty($errores) && $this->turnoModel->horarioOcupado((int) $turno['id_medico'], $turno['fecha_hora'], $id)) {
                $errores[] = 'El medico ya tiene un turno en esa fecha y hora.';
            }

            if (empty($errores)) {
                try {
                    $this->turnoModel->actualizar($id, $turno);
                    $this->redirigir('index.php?action=turnos&mensaje=Turno actualizado correctamente');
                } catch (PDOException $e) {
                    error_log('Error al actualizar turno: ' . $e->getMessage());
                    $errores[] = 'No se pudo actualizar el turno.';
                }
            }
        }

        $modo = 'editar';
        require_once __DIR__ . '/../views/turnos/editar.php';
    }

    public function eliminar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('index.php?action=turnos');
        }

        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $this->redirigir('index.php?action=turnos&error=Turno no encontrado');
        }

        try {
            $this->turnoModel->eliminar($id);
            $this->redirigir('index.php?action=turnos&mensaje=Turno eliminado correctamente');
        } catch (PDOException $e) {
            error_log('Error al eliminar turno: ' . $e->getMessage());
            $this->redirigir('index.php?action=turnos&error=No se pudo eliminar el turno');
        }
    }

    public function verificarHorario(): void
    {
        header('Content-Type: application/json');

        $idMedico = (int) ($_GET['id_medico'] ?? 0);
        $fechaHora = trim($_GET['fecha_hora'] ?? '');
        $idExcluir = isset($_GET['id_excluir']) ? (int) $_GET['id_excluir'] : null;

        if ($fechaHora !== '') {
            $fechaHora = str_replace('T', ' ', $fechaHora);
            if (strlen($fechaHora) === 16) {
                $fechaHora .= ':00';
            }
        }

        if ($idMedico <= 0 || $fechaHora === '') {
            echo json_encode(['ok' => false, 'ocupado' => false, 'message' => 'Datos incompletos.']);
            return;
        }

        $ocupado = $this->turnoModel->horarioOcupado($idMedico, $fechaHora, $idExcluir ?: null);

        echo json_encode([
            'ok' => true,
            'ocupado' => $ocupado,
            'message' => $ocupado ? 'El medico ya tiene un turno en ese horario.' : 'Horario disponible.',
        ]);
    }

    private function normalizarDatos(array $datos): array
    {
        $fechaHora = trim($datos['fecha_hora'] ?? '');

        if ($fechaHora !== '') {
            $fechaHora = str_replace('T', ' ', $fechaHora);
            if (strlen($fechaHora) === 16) {
                $fechaHora .= ':00';
            }
        }

        return [
            'id_medico' => (int) ($datos['id_medico'] ?? 0),
            'id_paciente' => (int) ($datos['id_paciente'] ?? 0),
            'fecha_hora' => $fechaHora,
            'motivo' => trim($datos['motivo'] ?? ''),
            'estado' => $datos['estado'] ?? 'pendiente',
        ];
    }

    private function validar(array $turno): array
    {
        $errores = [];
        $estadosValidos = ['pendiente', 'confirmado', 'cancelado', 'atendido'];

        if ((int) $turno['id_medico'] <= 0) {
            $errores[] = 'Selecciona un medico.';
        }

        if ((int) $turno['id_paciente'] <= 0) {
            $errores[] = 'Selecciona un paciente.';
        }

        if (empty($turno['fecha_hora'])) {
            $errores[] = 'Selecciona fecha y hora.';
        }

        if (!in_array($turno['estado'], $estadosValidos, true)) {
            $errores[] = 'Selecciona un estado valido.';
        }

        return $errores;
    }

    private function redirigir(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
