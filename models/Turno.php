<?php

require_once __DIR__ . '/../config/conexion.php';

class Turno
{
    private PDO $db;

    public function __construct()
    {
        $this->db = conexion::conexion();
    }

    public function listar(array $filtros = []): array
    {
        $sql = "
            SELECT
                t.id,
                t.id_medico,
                t.id_paciente,
                t.fecha_hora,
                t.motivo,
                t.estado,
                CONCAT(p.nombre, ' ', p.apellido) AS paciente,
                p.dni AS paciente_dni,
                u.nombre AS medico
            FROM turnos t
            INNER JOIN pacientes p ON p.id = t.id_paciente
            INNER JOIN usuarios u ON u.id = t.id_medico
            WHERE 1 = 1
        ";
        $params = [];

        if (!empty($filtros['estado'])) {
            $sql .= ' AND t.estado = :estado';
            $params[':estado'] = $filtros['estado'];
        }

        if (!empty($filtros['fecha'])) {
            $sql .= ' AND DATE(t.fecha_hora) = :fecha';
            $params[':fecha'] = $filtros['fecha'];
        }

        if (!empty($filtros['busqueda'])) {
            $sql .= " AND (
                p.nombre LIKE :busqueda
                OR p.apellido LIKE :busqueda
                OR p.dni LIKE :busqueda
                OR u.nombre LIKE :busqueda
                OR t.motivo LIKE :busqueda
            )";
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }

        $sql .= ' ORDER BY t.fecha_hora DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT
                t.*,
                CONCAT(p.nombre, ' ', p.apellido) AS paciente,
                u.nombre AS medico
            FROM turnos t
            INNER JOIN pacientes p ON p.id = t.id_paciente
            INNER JOIN usuarios u ON u.id = t.id_medico
            WHERE t.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $turno = $stmt->fetch();

        return $turno ?: null;
    }

    public function crear(array $datos): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO turnos (id_medico, id_paciente, fecha_hora, motivo, estado)
            VALUES (:id_medico, :id_paciente, :fecha_hora, :motivo, :estado)
        ");

        return $stmt->execute([
            ':id_medico' => $datos['id_medico'],
            ':id_paciente' => $datos['id_paciente'],
            ':fecha_hora' => $datos['fecha_hora'],
            ':motivo' => $datos['motivo'] ?: null,
            ':estado' => $datos['estado'],
        ]);
    }

    public function actualizar(int $id, array $datos): bool
    {
        $stmt = $this->db->prepare("
            UPDATE turnos
            SET
                id_medico = :id_medico,
                id_paciente = :id_paciente,
                fecha_hora = :fecha_hora,
                motivo = :motivo,
                estado = :estado
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id' => $id,
            ':id_medico' => $datos['id_medico'],
            ':id_paciente' => $datos['id_paciente'],
            ':fecha_hora' => $datos['fecha_hora'],
            ':motivo' => $datos['motivo'] ?: null,
            ':estado' => $datos['estado'],
        ]);
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM turnos WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function horarioOcupado(int $idMedico, string $fechaHora, ?int $idExcluir = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM turnos WHERE id_medico = :id_medico AND fecha_hora = :fecha_hora';
        $params = [
            ':id_medico' => $idMedico,
            ':fecha_hora' => $fechaHora,
        ];

        if ($idExcluir !== null) {
            $sql .= ' AND id <> :id_excluir';
            $params[':id_excluir'] = $idExcluir;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function listarPacientes(): array
    {
        $stmt = $this->db->query("
            SELECT id, dni, nombre, apellido
            FROM pacientes
            ORDER BY apellido, nombre
        ");

        return $stmt->fetchAll();
    }

    public function listarMedicos(): array
    {
        $stmt = $this->db->query("
            SELECT id, nombre, email
            FROM usuarios
            WHERE rol = 'medico'
            ORDER BY nombre
        ");

        return $stmt->fetchAll();
    }
}
