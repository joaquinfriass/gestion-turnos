<?php

require_once __DIR__ . '/../models/Paciente.php';

class PacienteController
{
    private Paciente $pacienteModel;

    public function __construct()
    {
        $this->pacienteModel = new Paciente();
    }

    public function index(): void
    {
        $esRecepcion = ($_GET['action'] ?? '') === 'recepcion_pacientes';
        $sidebarRole = $esRecepcion ? 'recepcionista' : 'admin';
        $soloLectura = $esRecepcion;
        $listAction = $esRecepcion ? 'recepcion_pacientes' : 'pacientes';
        $backAction = $esRecepcion ? 'recepcion_dashboard' : 'dashboard';
        $busqueda = trim($_GET['busqueda'] ?? '');
        $pacientes = $this->pacienteModel->listar($busqueda);
        $mensaje = $_GET['mensaje'] ?? null;
        $error = $_GET['error'] ?? null;

        require_once __DIR__ . '/../views/pacientes/index.php';
    }

    public function crear(): void
    {
        $esRecepcion = ($_GET['action'] ?? '') === 'recepcion_pacientes_crear';
        $formAction = $esRecepcion ? 'recepcion_pacientes_crear' : 'pacientes_crear';
        $cancelAction = $esRecepcion ? 'recepcion_dashboard' : 'pacientes';
        $sidebarRole = $esRecepcion ? 'recepcionista' : 'admin';
        $paciente = ['dni' => '', 'nombre' => '', 'apellido' => '', 'telefono' => ''];
        $errores = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $paciente = $this->normalizar($_POST);
            $errores = $this->validar($paciente);

            if (empty($errores) && $this->pacienteModel->dniExiste($paciente['dni'])) {
                $errores[] = 'Ya existe un paciente con ese DNI.';
            }

            if (empty($errores)) {
                try {
                    $this->pacienteModel->crear($paciente);
                    if ($esRecepcion) {
                        $this->redirigir('index.php?action=recepcion_dashboard&mensaje=Paciente creado correctamente');
                    }
                    $this->redirigir('index.php?action=pacientes&mensaje=Paciente creado correctamente');
                } catch (PDOException $e) {
                    error_log('Error al crear paciente: ' . $e->getMessage());
                    $errores[] = 'No se pudo crear el paciente.';
                }
            }
        }

        require_once __DIR__ . '/../views/pacientes/crear.php';
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $pacienteActual = $id > 0 ? $this->pacienteModel->obtenerPorId($id) : null;

        if (!$pacienteActual) {
            $this->redirigir('index.php?action=pacientes&error=Paciente no encontrado');
        }

        $paciente = $pacienteActual;
        $errores = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $paciente = array_merge($pacienteActual, $this->normalizar($_POST));
            $errores = $this->validar($paciente);

            if (empty($errores) && $this->pacienteModel->dniExiste($paciente['dni'], $id)) {
                $errores[] = 'Ya existe otro paciente con ese DNI.';
            }

            if (empty($errores)) {
                try {
                    $this->pacienteModel->actualizar($id, $paciente);
                    $this->redirigir('index.php?action=pacientes&mensaje=Paciente actualizado correctamente');
                } catch (PDOException $e) {
                    error_log('Error al actualizar paciente: ' . $e->getMessage());
                    $errores[] = 'No se pudo actualizar el paciente.';
                }
            }
        }

        require_once __DIR__ . '/../views/pacientes/editar.php';
    }

    public function eliminar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('index.php?action=pacientes');
        }

        $id = (int) ($_POST['id'] ?? 0);

        try {
            $this->pacienteModel->eliminar($id);
            $this->redirigir('index.php?action=pacientes&mensaje=Paciente eliminado correctamente');
        } catch (PDOException $e) {
            error_log('Error al eliminar paciente: ' . $e->getMessage());
            $this->redirigir('index.php?action=pacientes&error=No se pudo eliminar el paciente. Puede tener turnos asociados.');
        }
    }

    private function normalizar(array $datos): array
    {
        return [
            'dni' => trim($datos['dni'] ?? ''),
            'nombre' => trim($datos['nombre'] ?? ''),
            'apellido' => trim($datos['apellido'] ?? ''),
            'telefono' => trim($datos['telefono'] ?? ''),
        ];
    }

    private function validar(array $paciente): array
    {
        $errores = [];

        if ($paciente['dni'] === '') {
            $errores[] = 'Ingresa el DNI.';
        }

        if ($paciente['nombre'] === '') {
            $errores[] = 'Ingresa el nombre.';
        }

        if ($paciente['apellido'] === '') {
            $errores[] = 'Ingresa el apellido.';
        }

        return $errores;
    }

    private function redirigir(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
