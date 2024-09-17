<?php

namespace core\helpers;

use core\Model;

class GridView
{
    private $columns = null;
    public $dataProvider = [];
    public $pagination = false;
    private $currentPage = 1;
    private $itemsPerPage = 10;
    private $enableItemsPerPageSelector = false;

    public function __construct(array $data = [])
    {
        $this->dataProvider = $data;
    }

    public function setPagination(bool $enabled, int $itemsPerPage = 10): self
    {
        $this->pagination = $enabled;
        $this->itemsPerPage = $itemsPerPage;

        return $this;
    }

    public function setCurrentPage(int $page): self
    {
        $this->currentPage = max(1, $page);

        return $this;
    }

    public function enableItemsPerPageSelector(bool $enabled = true): self
    {
        $this->enableItemsPerPageSelector = $enabled;
        return $this;
    }

    private function getItemsPerPage()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if ($this->enableItemsPerPageSelector) {
            if (isset($_GET['itemsPerPage'])) {
                $itemsPerPage = $_GET['itemsPerPage'];
                $_SESSION['gridview_itemsPerPage'] = $itemsPerPage;
            } elseif (isset($_SESSION['gridview_itemsPerPage'])) {
                $itemsPerPage = $_SESSION['gridview_itemsPerPage'];
            } else {
                $itemsPerPage = $this->itemsPerPage;
            }

            if ($itemsPerPage == 'all') {
                return 'all';
            } else {
                return (int)$itemsPerPage;
            }
        } else {
            return $this->itemsPerPage;
        }
    }

    public function render(): string
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $itemsPerPage = $this->getItemsPerPage();

        if (!$this->dataProvider) {
            return 'Нет записей';
        }

        $data = $this->pagination ? $this->getPaginatedData() : $this->dataProvider;

        return Renderer::render(__DIR__ . '/_grid.php', [
            'data' => $data,
            'columns' => $this->getColumns(),
            'pagination' => $this->pagination ? $this->getPaginationControls() : '',
            'grid' => $this,
            'itemsPerPage' => $itemsPerPage,
            'enableItemsPerPageSelector' => $this->enableItemsPerPageSelector
        ]);
    }

    /**
     * @param array<array{attribute: string, label: string, value: callable(mixed): mixed}> $columns
     *
     * @return $this
     */
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    public function getColumns(): array
    {
        if ($this->columns) {
            return $this->columns;
        }

        $first = $this->dataProvider[array_key_first($this->dataProvider)] ?? null;

        if (!$first) {
            return [];
        }

        if ($first instanceof Model) {
            return $this->buildDefaultColumns($first->attributes);
        }

        if (is_array($first)) {
            return $this->buildDefaultColumns(array_keys($first));
        }

        throw new \DomainException('Неизвестный объект в GridView');
    }

    private function buildDefaultColumns(array $attributes): array
    {
        $columns = [];

        foreach ($attributes as $attribute) {
            $columns[] = [
                'attribute' => $attribute,
                'label' => $attribute,
            ];
        }

        return $columns;
    }

    private function getPaginatedData(): array
    {
        $itemsPerPage = $this->getItemsPerPage();
        if ($itemsPerPage === 'all') {
            return $this->dataProvider;
        }
        $offset = ($this->currentPage - 1) * $itemsPerPage;
        return array_slice($this->dataProvider, $offset, $itemsPerPage);
    }

    private function getPaginationControls(): string
    {
        $itemsPerPage = $this->getItemsPerPage();
        if ($itemsPerPage === 'all') {
            return '';
        }

        $totalItems = count($this->dataProvider);
        $totalPages = ceil($totalItems / $itemsPerPage);
        $html = '<nav><ul class="pagination pagination-dark">';

        $range = 3;

        if ($this->currentPage > 1 + $range) {
            $html .= '<li class="page-item"><a class="page-link" href="' . $this->getPagingUrl(1) . '">1</a></li>';
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }

        for ($i = max(1, $this->currentPage - $range); $i <= min($totalPages, $this->currentPage + $range); $i++) {
            $active = ($i === $this->currentPage) ? ' active' : '';
            $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . $this->getPagingUrl($i) . '">' . $i . '</a></li>';
        }

        if ($this->currentPage < $totalPages - $range) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            $html .= '<li class="page-item"><a class="page-link" href="' . $this->getPagingUrl($totalPages) . '">' . $totalPages . '</a></li>';
        }

        $html .= '</ul></nav>';

        return $html;
    }

    private function getPagingUrl(int $page): string
    {
        $params = $_GET;
        unset($params['path']);
        $params['page'] = $page;

        if ($this->enableItemsPerPageSelector) {
            $itemsPerPageValue = isset($_GET['itemsPerPage']) ? $_GET['itemsPerPage'] : (isset($_SESSION['gridview_itemsPerPage']) ? $_SESSION['gridview_itemsPerPage'] : $this->itemsPerPage);
            $params['itemsPerPage'] = $itemsPerPageValue;
        }

        return Url::toRoute(Url::currentRoute(), $params);
    }

    public function getCurrentUrlWithoutParams(array $excludeParams = []): string
    {
        $params = $_GET;
        foreach ($excludeParams as $param) {
            unset($params[$param]);
        }
        unset($params['path']);

        return Url::toRoute(Url::currentRoute(), $params);
    }

    public function getActionsColumns(int $id): string
    {
        return '
        <div class="">
            <a href="' . Url::toRoute(Url::currentController() . '/update', ['id' => $id]) . '" class="btn btn-warning btn-sm me-2" title="Изменить">
                <i class="bi bi-pencil"></i>
            </a>
            <a href="' . Url::toRoute(Url::currentController() . '/delete', ['id' => $id]) . '" class="btn btn-danger btn-sm" title="Удалить" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">
                <i class="bi bi-trash"></i>
            </a>
        </div>
        ';
    }
}
