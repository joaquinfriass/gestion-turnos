<?php

require_once __DIR__ . '/../config/conexion.php';

class Paciente
{
    private PDO $db;

    public function __construct()
    {
        $this->db = conexion::conexion();
    }

    public function listar(string $busqueda = ''): array
    {
        $sql = 'SELECT * FROM pacientes WHERE 1 = 1';
        $params = [];

        if ($busqueda !== '') {
            $sql .= ' AND (dni LIKE :busqueda OR nombre LIKE :busqueda OR apellido LIKE :busqueda OR telefono LIKE :busqueda)';
            $params[':busqueda'] = '%' . $busqueda . '%';
        }

        $sql .= ' ORDER BY apellido, nombre';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM pacientes WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $paciente = $stmt->fetch();

        return $paciente ?: null;
    }

    public function crear(array $datos): bool
    {
        $stmt = $this->db->prepare('
            INSERT INTO pacientes (dni, nombre, apellido, telefono)
            VALUES (:dni, :nombre, :apellido, :telefono)
        ');

        return $stmt->execute([
            ':dni' => $datos['dni'],
            ':nombre' => $datos['nombre'],
            ':apellido' => $datos['apellido'],
            ':telefono' => $datos['telefono'] ?: null,
        ]);
    }

    public function actualizar(int $id, array $datos): bool
    {
        $stmt = $this->db->prepare('
            UPDATE pacientes
            SET dni = :dni, nombre = :nombre, apellido = :apellido, telefono = :telefono
            WHERE id = :id
        ');

        return $stmt->execute([
            ':id' => $id,
            ':dni' => $datos['dni'],
            ':nombre' => $datos['nombre'],
            ':apellido' => $datos['apellido'],
            ':telefono' => $datos['telefono'] ?: null,
        ]);
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM pacientes WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function dniExiste(string $dni, ?int $idExcluir = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM pacientes WHERE dni = :dni';
        $params = [':dni' => $dni];

        if ($idExcluir !== null) {
            $sql .= ' AND id <> :id';
            $params[':id'] = $idExcluir;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }
}
