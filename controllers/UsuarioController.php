<?php

require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController
{
    private Usuario $usuarioModel;
    private array $roles = ['admin', 'recepcionista', 'medico'];

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    public function index(): void
    {
        $busqueda = trim($_GET['busqueda'] ?? '');
        $rol = $_GET['rol'] ?? '';
        $usuarios = $this->usuarioModel->listar($busqueda, $rol ?: null);
        $mensaje = $_GET['mensaje'] ?? null;
        $error = $_GET['error'] ?? null;

        require_once __DIR__ . '/../views/usuarios/index.php';
    }

    public function crear(): void
    {
        $usuario = ['nombre' => '', 'email' => '', 'password' => '', 'rol' => 'recepcionista'];
        $errores = [];
        $roles = $this->roles;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = $this->normalizar($_POST);
            $errores = $this->validar($usuario, true);

            if (empty($errores) && $this->usuarioModel->emailExiste($usuario['email'])) {
                $errores[] = 'Ya existe un usuario con ese email.';
            }

            if (empty($errores)) {
                try {
                    $this->usuarioModel->crear($usuario);
                    $this->redirigir('index.php?action=usuarios&mensaje=Usuario creado correctamente');
                } catch (PDOException $e) {
                    error_log('Error al crear usuario: ' . $e->getMessage());
                    $errores[] = 'No se pudo crear el usuario.';
                }
            }
        }

        require_once __DIR__ . '/../views/usuarios/crear.php';
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $usuarioActual = $id > 0 ? $this->usuarioModel->obtenerPorId($id) : null;

        if (!$usuarioActual) {
            $this->redirigir('index.php?action=usuarios&error=Usuario no encontrado');
        }

        $usuario = $usuarioActual + ['password' => ''];
        $errores = [];
        $roles = $this->roles;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = array_merge($usuarioActual, $this->normalizar($_POST));
            $errores = $this->validar($usuario, false);

            if (empty($errores) && $this->usuarioModel->emailExiste($usuario['email'], $id)) {
                $errores[] = 'Ya existe otro usuario con ese email.';
            }

            if (empty($errores)) {
                try {
                    $this->usuarioModel->actualizar($id, $usuario);
                    $this->redirigir('index.php?action=usuarios&mensaje=Usuario actualizado correctamente');
                } catch (PDOException $e) {
                    error_log('Error al actualizar usuario: ' . $e->getMessage());
                    $errores[] = 'No se pudo actualizar el usuario.';
                }
            }
        }

        require_once __DIR__ . '/../views/usuarios/editar.php';
    }

    public function eliminar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('index.php?action=usuarios');
        }

        $id = (int) ($_POST['id'] ?? 0);

        try {
            $this->usuarioModel->eliminar($id);
            $this->redirigir('index.php?action=usuarios&mensaje=Usuario eliminado correctamente');
        } catch (PDOException $e) {
            error_log('Error al eliminar usuario: ' . $e->getMessage());
            $this->redirigir('index.php?action=usuarios&error=No se pudo eliminar el usuario. Puede tener turnos asociados.');
        }
    }

    private function normalizar(array $datos): array
    {
        return [
            'nombre' => trim($datos['nombre'] ?? ''),
            'email' => trim($datos['email'] ?? ''),
            'password' => trim($datos['password'] ?? ''),
            'rol' => $datos['rol'] ?? '',
        ];
    }

    private function validar(array $usuario, bool $requierePassword): array
    {
        $errores = [];

        if ($usuario['nombre'] === '') {
            $errores[] = 'Ingresa el nombre.';
        }

        if (!filter_var($usuario['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Ingresa un email valido.';
        }

        if (!in_array($usuario['rol'], $this->roles, true)) {
            $errores[] = 'Selecciona un rol valido.';
        }

        if ($requierePassword && $usuario['password'] === '') {
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
