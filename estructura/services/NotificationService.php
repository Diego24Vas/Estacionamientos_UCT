<?php

/**
 * Notification Service
 * Servicio para manejo de notificaciones usando DI
 */
class NotificationService {
    private $sessionManager;
    
    public function __construct(SessionManager $sessionManager = null) {
        $this->sessionManager = $sessionManager ?: new SessionManager();
    }
    
    /**
     * Agregar notificación de éxito
     */
    public function success(string $message): void {
        $this->addNotification('success', $message);
    }
    
    /**
     * Agregar notificación de error
     */
    public function error(string $message): void {
        $this->addNotification('error', $message);
    }
    
    /**
     * Agregar notificación de advertencia
     */
    public function warning(string $message): void {
        $this->addNotification('warning', $message);
    }
    
    /**
     * Agregar notificación de información
     */
    public function info(string $message): void {
        $this->addNotification('info', $message);
    }
    
    /**
     * Agregar notificación al sistema
     */
    private function addNotification(string $type, string $message): void {
        $this->sessionManager->flash('notification', [
            'type' => $type,
            'message' => $message,
            'timestamp' => time()
        ]);
    }
    
    /**
     * Obtener todas las notificaciones
     */
    public function getNotifications(): array {
        $notification = $this->sessionManager->getFlash('notification');
        return $notification ? [$notification] : [];
    }
    
    /**
     * Verificar si hay notificaciones
     */
    public function hasNotifications(): bool {
        return $this->sessionManager->hasFlash('notification');
    }
    
    /**
     * Renderizar notificaciones como HTML para Bootstrap
     */
    public function renderNotifications(): string {
        $notifications = $this->getNotifications();
        $html = '';
        
        foreach ($notifications as $notification) {
            $alertClass = $this->getAlertClass($notification['type']);
            $icon = $this->getIcon($notification['type']);
            
            $html .= '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">';
            $html .= '<i class="' . $icon . '"></i> ';
            $html .= htmlspecialchars($notification['message']);
            $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            $html .= '</div>';
        }
        
        return $html;
    }
    
    /**
     * Obtener clase CSS para el tipo de alerta
     */
    private function getAlertClass(string $type): string {
        $classes = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ];
        
        return $classes[$type] ?? 'alert-info';
    }
    
    /**
     * Obtener icono para el tipo de notificación
     */
    private function getIcon(string $type): string {
        $icons = [
            'success' => 'fas fa-check-circle',
            'error' => 'fas fa-exclamation-circle',
            'warning' => 'fas fa-exclamation-triangle',
            'info' => 'fas fa-info-circle'
        ];
        
        return $icons[$type] ?? 'fas fa-info-circle';
    }
    
    /**
     * Obtener notificaciones en formato JSON para AJAX
     */
    public function getNotificationsJson(): string {
        return json_encode($this->getNotifications());
    }
    
    /**
     * Limpiar todas las notificaciones
     */
    public function clearNotifications(): void {
        $this->sessionManager->remove('_flash.notification');
    }
}
