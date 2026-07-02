<?php

require_once __DIR__ . '/../models/Medico.php';

class MedicoController
{
    private Medico $medicoModel;

    public function __construct()
    {
        $this->medicoModel = new Medico();
    }

    public function index(): void
    {
        $esRecepcion = ($_GET['action'] ?? '') === 'recepcion_medicos';
        $sidebarRole = $esRecepcion ? 'recepcionista' : 'admin';
        $soloLectura = $esRecepcion;
        $listAction = $esRecepcion ? 'recepcion_medicos' : 'medicos';
        $backAction = $esRecepcion ? 'recepcion_dashboard' : 'dashboard';
        $busqueda = trim($_GET['busqueda'] ?? '');
        $especialidad = trim($_GET['especialidad'] ?? '');
        $matricula = trim($_GET['matricula'] ?? '');
        $medicos = $this->medicoModel->listarFiltrado($busqueda, $especialidad, $matricula);
        $mensaje = $_GET['mensaje'] ?? null;
        $error = $_GET['error'] ?? null;

        require_once __DIR__ . '/../views/medicos/index.php';
    }

    public function crear(): void
    {
        $medico = ['nombre' => '', 'email' => '', 'password' => '', 'especialidad' => '', 'matricula' => ''];
        $errores = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $medico = $this->normalizar($_POST);
            $errores = $this->validar($medico, true);

            if (empty($errores) && $this->medicoModel->emailExiste($medico['email'])) {
                $errores[] = 'Ya existe un usuario con ese email.';
            }

            if (empty($errores)) {
                try {
                    $this->medicoModel->crear($medico);
                    $this->redirigir('index.php?action=medicos&mensaje=Medico creado correctamente');
                } catch (PDOException $e) {
                    error_log('Error al crear medico: ' . $e->getMessage());
                    $errores[] = 'No se pudo crear el medico.';
                }
            }
        }

        require_once __DIR__ . '/../views/medicos/crear.php';
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $medicoActual = $id > 0 ? $this->medicoModel->obtenerPorId($id) : null;

        if (!$medicoActual) {
            $this->redirigir('index.php?action=medicos&error=Medico no encontrado');
        }

        $medico = $medicoActual + ['password' => ''];
        $errores = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $medico = array_merge($medicoActual, $this->normalizar($_POST));
            $errores = $this->validar($medico, false);

            if (empty($errores) && $this->medicoModel->emailExiste($medico['email'], $id)) {
                $errores[] = 'Ya existe otro usuario con ese email.';
            }

            if (empty($errores)) {
                try {
                    $this->medicoModel->actualizar($id, $medico);
                    $this->redirigir('index.php?action=medicos&mensaje=Medico actualizado correctamente');
                } catch (PDOException $e) {
                    error_log('Error al actualizar medico: ' . $e->getMessage());
                    $errores[] = 'No se pudo actualizar el medico.';
                }
            }
        }

        require_once __DIR__ . '/../views/medicos/editar.php';
    }

    public function eliminar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('index.php?action=medicos');
        }

        $id = (int) ($_POST['id'] ?? 0);

        try {
            $this->medicoModel->eliminar($id);
            $this->redirigir('index.php?action=medicos&mensaje=Medico eliminado correctamente');
        } catch (PDOException $e) {
            error_log('Error al eliminar medico: ' . $e->getMessage());
            $this->redirigir('index.php?action=medicos&error=No se pudo eliminar el medico. Puede tener turnos asociados.');
        }
    }

    private function normalizar(array $datos): array
    {
        $nombre = trim($datos['nombre'] ?? '');

        return [
            'nombre' => $this->nombreMedico($nombre),
            'email' => trim($datos['email'] ?? ''),
            'password' => trim($datos['password'] ?? ''),
            'especialidad' => trim($datos['especialidad'] ?? ''),
            'matricula' => trim($datos['matricula'] ?? ''),
            'rol' => 'medico',
        ];
    }

    private function nombreMedico(string $nombre): string
    {
        $nombre = trim(preg_replace('/^dra?\.?\s+/i', '', $nombre));
        return 'Dr. ' . $nombre;
    }

    private function validar(array $medico, bool $requierePassword): array
    {
        $errores = [];

        if ($medico['nombre'] === '') {
            $errores[] = 'Ingresa el nombre.';
        }

        if (!filter_var($medico['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Ingresa un email valido.';
        }

        if ($medico['especialidad'] === '') {
            $errores[] = 'Ingresa la especialidad.';
        }

        if ($medico['matricula'] === '') {
            $errores[] = 'Ingresa la matricula.';
        }

        if ($requierePassword && $medico['password'] === '') {
            $errores[] = 'Ingresa una contraseña.';
        }

        return $errores;
    }

    private function redirigir(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
