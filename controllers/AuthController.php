<?php
// controllers/AuthController.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    
    public function login() {
        // Verificar si se enviaron datos por POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            if (empty($email) || empty($password)) {
                $error = "Por favor, completa todos los campos.";
                require_once __DIR__ . '/../views/auth/login.php';
                return;
            }

            $modelUsuario = new Usuario();
            $usuario = $modelUsuario->obtenerPorEmail($email);

            // Validar si el usuario existe y la contraseña coincide con el hash
            if ($usuario && password_verify($password, $usuario['password'])) {
                
                // Crear variables de sesión de forma segura
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol'] = $usuario['rol'];

                // Redirección según el ROL (Tu requerimiento principal)
                switch ($usuario['rol']) {
                    case 'admin':
                        header('Location: views/admin/dashboard.php');
                        break;
                    case 'recep1aer': // Corregido según tu ENUM previo o 'recepcionista'
                    case 'recepcionista':
                        header('Location: views/recepcion/dashboard.php');
                        break;
                    case 'medico':
                        header('Location: views/medico/dashboard.php');
                        break;
                }
                exit;
            } else {
                $error = "Email o contraseña incorrectos.";
                require_once __DIR__ . '/../views/auth/login.php';
            }
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: index.php');
        exit;
    }
}

