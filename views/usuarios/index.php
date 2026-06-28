<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Gestion de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="public/css/turnos.css" rel="stylesheet">
</head>
<body>
    <div class="app-shell">
        <?php require __DIR__ . '/../layouts/sidebar.php'; ?>
    <main class="main-content page-shell">
        <header class="page-header">
            <div><a class="back-link" href="index.php?action=dashboard"><i class="bi bi-arrow-left"></i><span>Dashboard</span></a><h1>Usuarios</h1></div>
            <a class="btn btn-primary" href="index.php?action=usuarios_crear"><i class="bi bi-plus-lg"></i><span>Nuevo usuario</span></a>
        </header>
        <?php if (!empty($mensaje)): ?><div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div><?php endif; ?>
        <?php if (!empty($error)): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <section class="filter-bar">
            <form action="index.php" method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="action" value="usuarios">
                <div class="col-12 col-md-7"><label class="form-label" for="busqueda">Buscar</label><input class="form-control" type="search" id="busqueda" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Nombre, email o rol" data-live-search="#tablaUsuarios"></div>
                <div class="col-12 col-md-3">
                    <label class="form-label" for="rol">Rol</label>
                    <select class="form-select" id="rol" name="rol">
                        <option value="">Todos</option>
                        <?php foreach ($roles as $rolItem): ?><option value="<?php echo $rolItem; ?>" <?php echo ($rol === $rolItem) ? 'selected' : ''; ?>><?php echo ucfirst($rolItem); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-grid"><button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i><span>Filtrar</span></button></div>
            </form>
        </section>
        <section class="data-panel">
            <div class="table-responsive">
                <table class="table align-middle" id="tablaUsuarios">
                    <thead><tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Alta</th><th class="text-end">Acciones</th></tr></thead>
                    <tbody>
                        <?php if (empty($usuarios)): ?><tr><td colspan="5" class="empty-state">No hay usuarios para mostrar.</td></tr><?php endif; ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td><span class="badge text-bg-light"><?php echo htmlspecialchars($usuario['rol']); ?></span></td>
                                <td><?php echo htmlspecialchars($usuario['created_at'] ? date('d/m/Y', strtotime($usuario['created_at'])) : '-'); ?></td>
                                <td><div class="actions"><a class="btn btn-sm btn-outline-secondary" href="index.php?action=usuarios_editar&id=<?php echo (int) $usuario['id']; ?>" title="Editar usuario"><i class="bi bi-pencil"></i></a><form action="index.php?action=usuarios_eliminar" method="POST" class="js-delete-form"><input type="hidden" name="id" value="<?php echo (int) $usuario['id']; ?>"><button class="btn btn-sm btn-outline-danger" type="submit" title="Eliminar usuario"><i class="bi bi-trash"></i></button></form></div></td>
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
