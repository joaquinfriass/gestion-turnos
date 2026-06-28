<?php

require_once __DIR__ . '/../models/Usuario.php';

class AuthController
{
    public function login(): void
    {
        $this->iniciarSesion();

        if (!empty($_SESSION['usuario_id']) && !empty($_SESSION['usuario_rol'])) {
            $this->redirigirPorRol($_SESSION['usuario_rol']);
        }

        $email = '';
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if ($email === '' || $password === '') {
                $error = 'Por favor, completa todos los campos.';
                require_once __DIR__ . '/../views/auth/login.php';
                return;
            }

            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->obtenerPorEmail($email);

            if (!$usuario || !password_verify($password, $usuario['password'])) {
                $error = 'Email o contraseña incorrectos.';
                require_once __DIR__ . '/../views/auth/login.php';
                return;
            }

            session_regenerate_id(true);
            $_SESSION['usuario_id'] = (int) $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_rol'] = $usuario['rol'];

            $this->redirigirPorRol($usuario['rol']);
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function logout(): void
    {
        $this->iniciarSesion();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }

    public static function requerirSesion(array $rolesPermitidos = []): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            self::configurarSesion();
            session_start();
        }

        if (empty($_SESSION['usuario_id']) || empty($_SESSION['usuario_rol'])) {
            header('Location: index.php?action=login');
            exit;
        }

        if (!empty($rolesPermitidos) && !in_array($_SESSION['usuario_rol'], $rolesPermitidos, true)) {
            self::redirigirDashboardPorRol($_SESSION['usuario_rol']);
        }
    }

    public static function redirigirDashboardPorRol(string $rol): void
    {
        $destinos = [
            'admin' => 'index.php?action=dashboard',
            'recepcionista' => 'index.php?action=recepcion_dashboard',
            'medico' => 'index.php?action=medico_dashboard',
        ];

        header('Location: ' . ($destinos[$rol] ?? 'index.php?action=login'));
        exit;
    }

    private function redirigirPorRol(string $rol): void
    {
        self::redirigirDashboardPorRol($rol);
    }

    private function iniciarSesion(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            self::configurarSesion();
            session_start();
        }
    }

    private static function configurarSesion(): void
    {
        $sessionPath = __DIR__ . '/../storage/sessions';

        if (!is_dir($sessionPath)) {
            mkdir($sessionPath, 0775, true);
        }

        session_save_path($sessionPath);
    }
}
