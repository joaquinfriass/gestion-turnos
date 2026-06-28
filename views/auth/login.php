<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesion - Gestion de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">
    <main class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-6 col-lg-4">
                <section class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-2 mb-3" style="width: 44px; height: 44px;">
                                <i class="bi bi-calendar2-pulse fs-4"></i>
                            </div>
                            <h1 class="h4 mb-1">Gestion de Turnos</h1>
                            <p class="text-secondary mb-0">Ingreso al sistema</p>
                        </div>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <form action="index.php?action=login" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo electronico</label>
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($email ?? ''); ?>"
                                    autocomplete="email"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control"
                                    autocomplete="current-password"
                                    required
                                >
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-box-arrow-in-right"></i>
                                <span>Ingresar</span>
                            </button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
