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

    public function marcarAtendido(int $idTurno, int $idMedico): bool
    {
        $stmt = $this->db->prepare("
            UPDATE turnos
            SET estado = 'atendido'
            WHERE id = :id AND id_medico = :id_medico
        ");

        $stmt->execute([
            ':id' => $idTurno,
            ':id_medico' => $idMedico,
        ]);

        return $stmt->rowCount() > 0;
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
            SELECT id, nombre, email, especialidad, matricula
            FROM usuarios
            WHERE rol = 'medico'
            ORDER BY nombre
        ");

        return $stmt->fetchAll();
    }

    public function obtenerPrimerMedico(): ?array
    {
        $stmt = $this->db->query("
            SELECT id, nombre, email
            FROM usuarios
            WHERE rol = 'medico'
            ORDER BY id
            LIMIT 1
        ");
        $medico = $stmt->fetch();

        return $medico ?: null;
    }

    public function obtenerMedicoPorId(int $idMedico): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, nombre, email
            FROM usuarios
            WHERE id = :id AND rol = 'medico'
            LIMIT 1
        ");
        $stmt->execute([':id' => $idMedico]);
        $medico = $stmt->fetch();

        return $medico ?: null;
    }

    public function listarPorMedico(int $idMedico, array $filtros = []): array
    {
        $sql = "
            SELECT
                t.id,
                t.id_paciente,
                t.fecha_hora,
                t.motivo,
                t.estado,
                CONCAT(p.nombre, ' ', p.apellido) AS paciente,
                p.dni AS paciente_dni,
                p.telefono AS paciente_telefono
            FROM turnos t
            INNER JOIN pacientes p ON p.id = t.id_paciente
            WHERE t.id_medico = :id_medico
        ";
        $params = [':id_medico' => $idMedico];

        if (!empty($filtros['hoy'])) {
            $sql .= ' AND DATE(t.fecha_hora) = CURDATE()';
        }

        if (!empty($filtros['desde'])) {
            $sql .= ' AND DATE(t.fecha_hora) >= :desde';
            $params[':desde'] = $filtros['desde'];
        }

        if (!empty($filtros['hasta'])) {
            $sql .= ' AND DATE(t.fecha_hora) <= :hasta';
            $params[':hasta'] = $filtros['hasta'];
        }

        if (!empty($filtros['estado'])) {
            $sql .= ' AND t.estado = :estado';
            $params[':estado'] = $filtros['estado'];
        }

        if (!empty($filtros['busqueda'])) {
            $sql .= " AND (
                p.nombre LIKE :busqueda
                OR p.apellido LIKE :busqueda
                OR p.dni LIKE :busqueda
                OR t.motivo LIKE :busqueda
            )";
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }

        $sql .= !empty($filtros['hoy']) ? ' ORDER BY t.fecha_hora ASC' : ' ORDER BY t.fecha_hora DESC';

        if (!empty($filtros['limite'])) {
            $sql .= ' LIMIT ' . (int) $filtros['limite'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function resumenPacientesPorMedico(int $idMedico, string $busqueda = ''): array
    {
        $sql = "
            SELECT
                p.id,
                p.dni,
                p.nombre,
                p.apellido,
                p.telefono,
                COUNT(t.id) AS total_turnos,
                MAX(t.fecha_hora) AS ultimo_turno
            FROM pacientes p
            INNER JOIN turnos t ON t.id_paciente = p.id
            WHERE t.id_medico = :id_medico
        ";
        $params = [':id_medico' => $idMedico];

        if ($busqueda !== '') {
            $sql .= ' AND (p.nombre LIKE :busqueda OR p.apellido LIKE :busqueda OR p.dni LIKE :busqueda)';
            $params[':busqueda'] = '%' . $busqueda . '%';
        }

        $sql .= '
            GROUP BY p.id, p.dni, p.nombre, p.apellido, p.telefono
            ORDER BY ultimo_turno DESC
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function historialPacientePorMedico(int $idMedico, int $idPaciente): array
    {
        $stmt = $this->db->prepare("
            SELECT
                t.id,
                t.fecha_hora,
                t.motivo,
                t.estado,
                CONCAT(p.nombre, ' ', p.apellido) AS paciente,
                p.dni AS paciente_dni,
                p.telefono AS paciente_telefono
            FROM turnos t
            INNER JOIN pacientes p ON p.id = t.id_paciente
            WHERE t.id_medico = :id_medico AND t.id_paciente = :id_paciente
            ORDER BY t.fecha_hora DESC
        ");
        $stmt->execute([
            ':id_medico' => $idMedico,
            ':id_paciente' => $idPaciente,
        ]);

        return $stmt->fetchAll();
    }
}
