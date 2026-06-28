<?php

require_once __DIR__ . '/Usuario.php';

class Medico
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    public function listar(string $busqueda = ''): array
    {
        return $this->usuarioModel->listar($busqueda, 'medico');
    }

    public function obtenerPorId(int $id): ?array
    {
        $usuario = $this->usuarioModel->obtenerPorId($id);

        if (!$usuario || $usuario['rol'] !== 'medico') {
            return null;
        }

        return $usuario;
    }

    public function crear(array $datos): bool
    {
        $datos['rol'] = 'medico';
        return $this->usuarioModel->crear($datos);
    }

    public function actualizar(int $id, array $datos): bool
    {
        $datos['rol'] = 'medico';
        return $this->usuarioModel->actualizar($id, $datos);
    }

    public function eliminar(int $id): bool
    {
        return $this->usuarioModel->eliminar($id);
    }

    public function emailExiste(string $email, ?int $idExcluir = null): bool
    {
        return $this->usuarioModel->emailExiste($email, $idExcluir);
    }
}
