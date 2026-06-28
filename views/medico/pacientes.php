<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes atendidos - Gestion de Turnos</title>
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
                    <a class="back-link" href="index.php?action=medico_dashboard"><i class="bi bi-arrow-left"></i><span>Dashboard</span></a>
                    <h1>Pacientes atendidos</h1>
                </div>
            </header>

            <section class="filter-bar">
                <form action="index.php" method="GET" class="row g-3 align-items-end">
                    <input type="hidden" name="action" value="medico_pacientes">
                    <div class="col-12 col-md-10">
                        <label class="form-label" for="busqueda">Buscar</label>
                        <input class="form-control" type="search" id="busqueda" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Nombre, apellido o DNI" data-live-search="#tablaMedicoPacientes">
                    </div>
                    <div class="col-12 col-md-2 d-grid">
                        <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i><span>Filtrar</span></button>
                    </div>
                </form>
            </section>

            <section class="data-panel">
                <div class="table-responsive">
                    <table class="table align-middle" id="tablaMedicoPacientes">
                        <thead><tr><th>Paciente</th><th>DNI</th><th>Telefono</th><th>Total turnos</th><th>Ultimo turno</th><th>Historial</th></tr></thead>
                        <tbody>
                            <?php if (empty($pacientes)): ?><tr><td colspan="6" class="empty-state">No hay pacientes asociados a tus turnos.</td></tr><?php endif; ?>
                            <?php foreach ($pacientes as $paciente): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($paciente['apellido'] . ', ' . $paciente['nombre']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($paciente['dni']); ?></td>
                                    <td><?php echo htmlspecialchars($paciente['telefono'] ?: 'Sin telefono'); ?></td>
                                    <td><?php echo (int) $paciente['total_turnos']; ?></td>
                                    <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($paciente['ultimo_turno']))); ?></td>
                                    <td><a class="btn btn-sm btn-outline-primary" href="index.php?action=medico_historial_paciente&id_paciente=<?php echo (int) $paciente['id']; ?>">Ver historial</a></td>
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
