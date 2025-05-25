<?php

interface IPaginationService {
    public function paginate(array $data, int $page, int $limit): array;
    public function getPaginationInfo(int $totalItems, int $currentPage, int $limit): array;
    public function validatePaginationParams(int $page, int $limit): array;
}

?>