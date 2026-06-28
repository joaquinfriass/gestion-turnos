<?php
$estados = ['pendiente', 'confirmado', 'cancelado', 'atendido'];

function estadoBadgeTurno(string $estado): string
{
    return match ($estado) {
        'confirmado' => 'text-bg-success',
        'cancelado' => 'text-bg-danger',
        'atendido' => 'text-bg-secondary',
        default => 'text-bg-warning',
    };
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnos - Gestion de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="public/css/turnos.css" rel="stylesheet">
</head>
<body>
    <div class="app-shell">
        <?php require __DIR__ . '/../layouts/sidebar.php'; ?>
    <main class="main-content page-shell">
        <header class="page-header">
            <div>
                <a class="back-link" href="index.php?action=dashboard">
                    <i class="bi bi-arrow-left"></i>
                    <span>Dashboard</span>
                </a>
                <h1>Turnos</h1>
            </div>
            <a class="btn btn-primary" href="index.php?action=turnos_crear">
                <i class="bi bi-plus-lg"></i>
                <span>Nuevo turno</span>
            </a>
        </header>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <section class="filter-bar">
            <form action="index.php" method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="action" value="turnos">

                <div class="col-12 col-md-4">
                    <label class="form-label" for="busqueda">Buscar</label>
                    <input
                        class="form-control"
                        type="search"
                        id="busqueda"
                        name="busqueda"
                        data-live-search="#tablaTurnos"
                        value="<?php echo htmlspecialchars($filtros['busqueda'] ?? ''); ?>"
                        placeholder="Paciente, DNI, medico o motivo"
                    >
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label" for="fecha">Fecha</label>
                    <input
                        class="form-control"
                        type="date"
                        id="fecha"
                        name="fecha"
                        value="<?php echo htmlspecialchars($filtros['fecha'] ?? ''); ?>"
                    >
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label" for="estado">Estado</label>
                    <select class="form-select" id="estado" name="estado">
                        <option value="">Todos</option>
                        <?php foreach ($estados as $estado): ?>
                            <option value="<?php echo $estado; ?>" <?php echo (($filtros['estado'] ?? '') === $estado) ? 'selected' : ''; ?>>
                                <?php echo ucfirst($estado); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-grid">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-search"></i>
                        <span>Filtrar</span>
                    </button>
                </div>
            </form>
        </section>

        <section class="data-panel">
            <div class="table-responsive">
                <table class="table align-middle" id="tablaTurnos">
                    <thead>
                        <tr>
                            <th>Fecha y hora</th>
                            <th>Paciente</th>
                            <th>Medico</th>
                            <th>Motivo</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($turnos)): ?>
                            <tr>
                                <td colspan="6" class="empty-state">No hay turnos para mostrar.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($turnos as $turno): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars(date('d/m/Y', strtotime($turno['fecha_hora']))); ?></strong>
                                    <span><?php echo htmlspecialchars(date('H:i', strtotime($turno['fecha_hora']))); ?></span>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($turno['paciente']); ?></strong>
                                    <span>DNI <?php echo htmlspecialchars($turno['paciente_dni']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($turno['medico']); ?></td>
                                <td><?php echo htmlspecialchars($turno['motivo'] ?: 'Sin motivo'); ?></td>
                                <td>
                                    <span class="badge <?php echo estadoBadgeTurno($turno['estado']); ?>">
                                        <?php echo htmlspecialchars($turno['estado']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a class="btn btn-sm btn-outline-secondary" href="index.php?action=turnos_editar&id=<?php echo (int) $turno['id']; ?>" title="Editar turno">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="index.php?action=turnos_eliminar" method="POST" class="js-delete-form">
                                            <input type="hidden" name="id" value="<?php echo (int) $turno['id']; ?>">
                                            <button class="btn btn-sm btn-outline-danger" type="submit" title="Eliminar turno">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/app.js"></script>
</body>
</html>
