<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicos - Gestion de Turnos</title>
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
                <a class="back-link" href="index.php?action=<?php echo htmlspecialchars($backAction ?? 'dashboard'); ?>"><i class="bi bi-arrow-left"></i><span>Dashboard</span></a>
                <h1>Medicos</h1>
            </div>
            <?php if (empty($soloLectura)): ?>
                <a class="btn btn-primary" href="index.php?action=medicos_crear"><i class="bi bi-plus-lg"></i><span>Nuevo medico</span></a>
            <?php endif; ?>
        </header>

        <?php if (!empty($mensaje)): ?><div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div><?php endif; ?>
        <?php if (!empty($error)): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

        <section class="filter-bar">
            <form action="index.php" method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="action" value="<?php echo htmlspecialchars($listAction ?? 'medicos'); ?>">
                <div class="col-12 col-md-4">
                    <label class="form-label" for="busqueda">Buscar</label>
                    <input class="form-control" type="search" id="busqueda" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Nombre o email" data-live-search="#tablaMedicos">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label" for="especialidad">Especialidad</label>
                    <input class="form-control" id="especialidad" name="especialidad" value="<?php echo htmlspecialchars($especialidad ?? ''); ?>" placeholder="Clinica, pediatria...">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label" for="matricula">Matricula</label>
                    <input class="form-control" id="matricula" name="matricula" value="<?php echo htmlspecialchars($matricula ?? ''); ?>" placeholder="MN 12345">
                </div>
                <div class="col-12 col-md-2 d-grid">
                    <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i><span>Filtrar</span></button>
                </div>
            </form>
        </section>

        <section class="data-panel">
            <div class="table-responsive">
                <table class="table align-middle" id="tablaMedicos">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Especialidad</th>
                            <th>Matricula</th>
                            <th>Alta</th>
                            <?php if (empty($soloLectura)): ?>
                                <th class="text-end">Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($medicos)): ?><tr><td colspan="<?php echo empty($soloLectura) ? '6' : '5'; ?>" class="empty-state">No hay medicos para mostrar.</td></tr><?php endif; ?>
                        <?php foreach ($medicos as $medico): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($medico['nombre']); ?></strong></td>
                                <td><?php echo htmlspecialchars($medico['email']); ?></td>
                                <td><?php echo htmlspecialchars($medico['especialidad'] ?: 'Sin especialidad'); ?></td>
                                <td><?php echo htmlspecialchars($medico['matricula'] ?: 'Sin matricula'); ?></td>
                                <td><?php echo htmlspecialchars($medico['created_at'] ? date('d/m/Y', strtotime($medico['created_at'])) : '-'); ?></td>
                                <?php if (empty($soloLectura)): ?>
                                    <td>
                                        <div class="actions">
                                            <a class="btn btn-sm btn-outline-secondary" href="index.php?action=medicos_editar&id=<?php echo (int) $medico['id']; ?>" title="Editar medico"><i class="bi bi-pencil"></i></a>
                                            <form action="index.php?action=medicos_eliminar" method="POST" class="js-delete-form">
                                                <?php echo AuthController::csrfInput(); ?>
                                                <input type="hidden" name="id" value="<?php echo (int) $medico['id']; ?>">
                                                <button class="btn btn-sm btn-outline-danger" type="submit" title="Eliminar medico"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                <?php endif; ?>
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
