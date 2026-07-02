<?php

require_once __DIR__ . '/../config/conexion.php';

class Usuario
{
    private PDO $db;

    public function __construct()
    {
        $this->db = conexion::conexion();
    }

    public function obtenerPorEmail(string $email): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);

        return $stmt->fetch();
    }

    public function listar(string $busqueda = '', ?string $rol = null): array
    {
        $sql = 'SELECT id, nombre, email, rol, especialidad, matricula, created_at FROM usuarios WHERE 1 = 1';
        $params = [];

        if ($rol !== null && $rol !== '') {
            $sql .= ' AND rol = :rol';
            $params[':rol'] = $rol;
        }

        if ($busqueda !== '') {
            $sql .= ' AND (nombre LIKE :busqueda OR email LIKE :busqueda OR rol LIKE :busqueda OR especialidad LIKE :busqueda OR matricula LIKE :busqueda)';
            $params[':busqueda'] = '%' . $busqueda . '%';
        }

        $sql .= ' ORDER BY nombre';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, nombre, email, rol, especialidad, matricula, created_at FROM usuarios WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }

    public function crear(array $datos): bool
    {
        $stmt = $this->db->prepare('
            INSERT INTO usuarios (nombre, email, password, rol, especialidad, matricula)
            VALUES (:nombre, :email, :password, :rol, :especialidad, :matricula)
        ');

        return $stmt->execute([
            ':nombre' => $datos['nombre'],
            ':email' => $datos['email'],
            ':password' => password_hash($datos['password'], PASSWORD_DEFAULT),
            ':rol' => $datos['rol'],
            ':especialidad' => $datos['especialidad'] ?: null,
            ':matricula' => $datos['matricula'] ?: null,
        ]);
    }

    public function actualizar(int $id, array $datos): bool
    {
        $params = [
            ':id' => $id,
            ':nombre' => $datos['nombre'],
            ':email' => $datos['email'],
            ':rol' => $datos['rol'],
            ':especialidad' => $datos['especialidad'] ?: null,
            ':matricula' => $datos['matricula'] ?: null,
        ];

        $passwordSql = '';
        if (!empty($datos['password'])) {
            $passwordSql = ', password = :password';
            $params[':password'] = password_hash($datos['password'], PASSWORD_DEFAULT);
        }

        $stmt = $this->db->prepare("
            UPDATE usuarios
            SET nombre = :nombre, email = :email, rol = :rol, especialidad = :especialidad, matricula = :matricula {$passwordSql}
            WHERE id = :id
        ");

        return $stmt->execute($params);
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM usuarios WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function emailExiste(string $email, ?int $idExcluir = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM usuarios WHERE email = :email';
        $params = [':email' => $email];

        if ($idExcluir !== null) {
            $sql .= ' AND id <> :id';
            $params[':id'] = $idExcluir;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }
}
