<?php

/**
 * View Helper Service
 * Servicio para ayudas de renderizado en vistas usando DI
 */
class ViewHelperService {
    private $configService;
    private $sessionManager;
    
    public function __construct(ConfigService $configService, SessionManager $sessionManager) {
        $this->configService = $configService;
        $this->sessionManager = $sessionManager;
    }
    
    /**
     * Renderizar opciones de zona para selectores
     */
    public function renderZonaOptions(string $selected = ''): string {
        $zonas = [
            'A' => 'Zona A - Administrativa',
            'B' => 'Zona B - Académica', 
            'C' => 'Zona C - Deportiva',
            'D' => 'Zona D - Visitantes'
        ];
        
        $html = '<option value="">Seleccione una zona</option>';
        foreach ($zonas as $value => $label) {
            $selectedAttr = ($selected === $value) ? 'selected' : '';
            $html .= "<option value=\"{$value}\" {$selectedAttr}>{$label}</option>";
        }
        
        return $html;
    }
    
    /**
     * Obtener URL completa para un path
     */
    public function url(string $path = ''): string {
        $baseUrl = $this->configService->getUrl('base');
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
    
    /**
     * Obtener path completo para un directorio
     */
    public function path(string $type): string {
        return $this->configService->getPath($type);
    }
    
    /**
     * Verificar si el usuario está autenticado
     */
    public function isAuthenticated(): bool {
        return $this->sessionManager->isAuthenticated();
    }
    
    /**
     * Obtener usuario actual
     */
    public function getCurrentUser(): ?array {
        return $this->sessionManager->getCurrentUser();
    }
    
    /**
     * Generar token CSRF
     */
    public function csrfToken(): string {
        if (!$this->sessionManager->has('csrf_token')) {
            $token = bin2hex(random_bytes(32));
            $this->sessionManager->set('csrf_token', $token);
        }
        
        return $this->sessionManager->get('csrf_token');
    }
    
    /**
     * Renderizar campo CSRF hidden
     */
    public function csrfField(): string {
        return '<input type="hidden" name="csrf_token" value="' . $this->csrfToken() . '">';
    }
    
    /**
     * Formatear fecha para display
     */
    public function formatDate(string $date, string $format = 'd/m/Y'): string {
        try {
            $dateObj = new DateTime($date);
            return $dateObj->format($format);
        } catch (Exception $e) {
            return $date;
        }
    }
    
    /**
     * Formatear hora para display
     */
    public function formatTime(string $time, string $format = 'H:i'): string {
        try {
            $timeObj = new DateTime($time);
            return $timeObj->format($format);
        } catch (Exception $e) {
            return $time;
        }
    }
    
    /**
     * Obtener fecha mínima para inputs (hoy)
     */
    public function getMinDate(): string {
        return date('Y-m-d');
    }
    
    /**
     * Escapar HTML
     */
    public function escape(string $value): string {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Renderizar breadcrumb
     */
    public function renderBreadcrumb(array $items): string {
        if (empty($items)) {
            return '';
        }
        
        $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
        
        $lastIndex = count($items) - 1;
        foreach ($items as $index => $item) {
            if ($index === $lastIndex) {
                $html .= '<li class="breadcrumb-item active" aria-current="page">' . $this->escape($item['label']) . '</li>';
            } else {
                $url = isset($item['url']) ? $this->escape($item['url']) : '#';
                $html .= '<li class="breadcrumb-item"><a href="' . $url . '">' . $this->escape($item['label']) . '</a></li>';
            }
        }
        
        $html .= '</ol></nav>';
        return $html;
    }
    
    /**
     * Renderizar paginación
     */
    public function renderPagination(int $currentPage, int $totalPages, string $baseUrl): string {
        if ($totalPages <= 1) {
            return '';
        }
        
        $html = '<nav aria-label="Paginación"><ul class="pagination justify-content-center">';
        
        // Anterior
        if ($currentPage > 1) {
            $prevUrl = $baseUrl . '?page=' . ($currentPage - 1);
            $html .= '<li class="page-item"><a class="page-link" href="' . $prevUrl . '">Anterior</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Anterior</span></li>';
        }
        
        // Páginas
        for ($i = 1; $i <= $totalPages; $i++) {
            if ($i === $currentPage) {
                $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                $pageUrl = $baseUrl . '?page=' . $i;
                $html .= '<li class="page-item"><a class="page-link" href="' . $pageUrl . '">' . $i . '</a></li>';
            }
        }
        
        // Siguiente
        if ($currentPage < $totalPages) {
            $nextUrl = $baseUrl . '?page=' . ($currentPage + 1);
            $html .= '<li class="page-item"><a class="page-link" href="' . $nextUrl . '">Siguiente</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Siguiente</span></li>';
        }
        
        $html .= '</ul></nav>';
        return $html;
    }
}
