<?php

class AuthController {
    private $usuario;

    public function __construct() {
        $this->usuario = new Usuario();
    }

    public function login($email, $password) {
        $user = $this->usuario->obtenerPorEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            // Iniciar sesión
            return true;
        }
        return false;
    }
}
?php