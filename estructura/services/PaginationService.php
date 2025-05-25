<?php

require_once dirname(__DIR__) . '/interfaces/IPaginationService.php';

class PaginationService implements IPaginationService {
    
    const DEFAULT_LIMIT = 10;
    const MAX_LIMIT = 100;
    const MIN_LIMIT = 1;

    /**
     * Paginar datos
     */
    public function paginate(array $data, int $page, int $limit): array {
        $validation = $this->validatePaginationParams($page, $limit);
        if ($validation['status'] === 'error') {
            return $validation;
        }

        $offset = ($page - 1) * $limit;
        $paginatedData = array_slice($data, $offset, $limit);

        return [
            'status' => 'success',
            'data' => $paginatedData,
            'pagination' => $this->getPaginationInfo(count($data), $page, $limit)
        ];
    }

    /**
     * Obtener información de paginación
     */
    public function getPaginationInfo(int $totalItems, int $currentPage, int $limit): array {
        $totalPages = ceil($totalItems / $limit);
        $hasNextPage = $currentPage < $totalPages;
        $hasPreviousPage = $currentPage > 1;

        return [
            'current_page' => $currentPage,
            'per_page' => $limit,
            'total_items' => $totalItems,
            'total_pages' => $totalPages,
            'has_next_page' => $hasNextPage,
            'has_previous_page' => $hasPreviousPage,
            'next_page' => $hasNextPage ? $currentPage + 1 : null,
            'previous_page' => $hasPreviousPage ? $currentPage - 1 : null
        ];
    }

    /**
     * Validar parámetros de paginación
     */
    public function validatePaginationParams(int $page, int $limit): array {
        if ($page < 1) {
            return ['status' => 'error', 'message' => 'El número de página debe ser mayor a 0'];
        }

        if ($limit < self::MIN_LIMIT) {
            return ['status' => 'error', 'message' => 'El límite debe ser mayor a 0'];
        }

        if ($limit > self::MAX_LIMIT) {
            return ['status' => 'error', 'message' => 'El límite no puede ser mayor a ' . self::MAX_LIMIT];
        }

        return ['status' => 'success'];
    }

    /**
     * Calcular offset para la base de datos
     */
    public function calculateOffset(int $page, int $limit): int {
        return ($page - 1) * $limit;
    }

    /**
     * Sanitizar parámetros de paginación con valores por defecto
     */
    public function sanitizePaginationParams(?int $page, ?int $limit): array {
        $sanitizedPage = max(1, $page ?? 1);
        $sanitizedLimit = min(self::MAX_LIMIT, max(self::MIN_LIMIT, $limit ?? self::DEFAULT_LIMIT));

        return [
            'page' => $sanitizedPage,
            'limit' => $sanitizedLimit
        ];
    }
}

?>