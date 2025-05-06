<?php
require_once dirname(__DIR__) . '/config/config.php';

/**
 * Función para redirigir a una página
 */
function redirect($path) {
    header("Location: " . $path);
    exit();
}

/**
 * Función para validar una patente
 */
function validarPatente($patente) {
    return preg_match('/^[A-Z]{2}[A-Z0-9]{2}[0-9]{2}$/', $patente);
}

/**
 * Función para formatear una fecha
 */
function formatearFecha($fecha) {
    return date('d/m/Y', strtotime($fecha));
}

/**
 * Función para validar una sesión
 */
function validarSesion() {
    if (!isset($_SESSION['user_id'])) {
        redirect(VIEWS_PATH . '/inicio.php');
    }
}

/**
 * Función para sanitizar entrada de usuario
 */
function sanitizar($input) {
    return htmlspecialchars(strip_tags(trim($input)));
} 