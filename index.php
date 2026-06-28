<?php
// index.php
require_once 'controllers/AuthController.php';

$authController = new AuthController();

// Leer la acción de la URL (ej: index.php?action=login)
$action = isset($_GET['action']) ? $_GET['action'] : 'view';

if ($action === 'login') {
    $authController->login();
} else {
    // Si no hay acción o es "view", mostramos la pantalla de login por defecto
    require_once 'views/login.php';
}