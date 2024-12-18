<?php
declare(strict_types=1);

namespace Gotyefrid\MelkoframeworkCore\helpers;

use Gotyefrid\MelkoframeworkCore\App;
use Gotyefrid\MelkoframeworkCore\Model;
use DomainException;
use Throwable;

class GridView
{
    private ?array $columns = null;
    private array $dataProvider;
    private bool $paginationEnabled = false;
    private int $currentPage = 1;
    private int $defaultItemsPerPage = 10;
    private bool $perPageSelector = true;

    public function __construct(array $data = [])
    {
        $this->dataProvider = $data;
    }

    public function enablePagination(int $itemsPerPage = 10): self
    {
        $this->paginationEnabled = true;

        if ($itemsPerPage) {
            $this->defaultItemsPerPage = $itemsPerPage;
        }

        return $this;
    }

    public function setCurrentPage(int $page): self
    {
        $this->currentPage = max(1, $page);

        return $this;
    }

    public function setPerPageSelector(bool $enabled = true): self
    {
        $this->perPageSelector = $enabled;

        return $this;
    }

    /**
     * @param array $columns
     * ```php
     * [
     *  [
     *      'attribute' => "string of {{actions}}"
     *      'label' => "string",
     *      'format' => "'raw' or null",
     *      'value' => "value or closure($item)"
     *  ]
     * ]
     * ```
     *
     * @return $this
     */
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @return string
     * @throws Throwable
     */
    public function render(): string
    {
        $this->startSession();

        if (empty($this->dataProvider)) {
            return 'Нет записей';
        }

        $itemsPerPage = $this->getItemsPerPage();
        $data = $this->paginationEnabled ? $this->getPaginatedData($itemsPerPage) : $this->dataProvider;
        $paginationControls = $this->paginationEnabled ? $this->getPaginationControls($itemsPerPage) : '';

        return Renderer::render(__DIR__ . '/_grid.php', [
            'data' => $data,
            'columns' => $this->getColumns(),
            'pagination' => $paginationControls,
            'grid' => $this,
            'itemsPerPage' => $itemsPerPage,
            'perPageSelector' => $this->perPageSelector,
        ]);
    }

    private function getColumns(): array
    {
        if ($this->columns !== null) {
            return $this->columns;
        }

        $firstItem = reset($this->dataProvider);

        if ($firstItem instanceof Model) {
            return $this->buildDefaultColumns($firstItem->attributes);
        }

        if (is_array($firstItem)) {
            return $this->buildDefaultColumns(array_keys($firstItem));
        }

        throw new DomainException('Неизвестный объект в GridView');
    }

    private function buildDefaultColumns(array $attributes): array
    {
        return array_map(function ($attribute) {
            return ['attribute' => $attribute, 'label' => $attribute];
        }, $attributes);
    }

    private function getItemsPerPage()
    {
        if (!$this->perPageSelector) {
            return $this->defaultItemsPerPage;
        }

        $this->startSession();

        if (isset($_GET['itemsPerPage'])) {
            $itemsPerPage = $_GET['itemsPerPage'];
            $_SESSION['gridview_itemsPerPage'] = $itemsPerPage;
        } elseif (isset($_SESSION['gridview_itemsPerPage'])) {
            $itemsPerPage = $_SESSION['gridview_itemsPerPage'];
        } else {
            $itemsPerPage = $this->defaultItemsPerPage;
        }

        return $itemsPerPage === 'all' ? 'all' : (int)$itemsPerPage;
    }

    private function getPaginatedData($itemsPerPage): array
    {
        if ($itemsPerPage === 'all') {
            return $this->dataProvider;
        }

        $offset = ($this->currentPage - 1) * $itemsPerPage;

        return array_slice($this->dataProvider, $offset, $itemsPerPage);
    }

    private function getPaginationControls($itemsPerPage): string
    {
        if ($itemsPerPage === 'all') {
            return '';
        }

        $totalItems = count($this->dataProvider);
        $totalPages = (int)ceil($totalItems / $itemsPerPage);
        $range = 3;
        $html = '<nav><ul class="pagination pagination-dark">';

        if ($this->currentPage > 1 + $range) {
            $html .= $this->buildPageLink(1);
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }

        for ($i = max(1, $this->currentPage - $range); $i <= min($totalPages, $this->currentPage + $range); $i++) {
            $html .= $this->buildPageLink($i, $i === $this->currentPage);
        }

        if ($this->currentPage < $totalPages - $range) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            $html .= $this->buildPageLink($totalPages);
        }

        $html .= '</ul></nav>';

        return $html;
    }

    private function buildPageLink(int $page, bool $isActive = false): string
    {
        $activeClass = $isActive ? ' active' : '';
        $url = $this->getPagingUrl($page);

        return "<li class=\"page-item$activeClass\"><a class=\"page-link\" href=\"$url\">$page</a></li>";
    }

    private function getPagingUrl(int $page): string
    {
        $params = $_GET;
        $params['page'] = $page;
        unset($params[App::get()->getRequest()->routeParameterName]);

        if ($this->perPageSelector) {
            $itemsPerPage = $this->getItemsPerPage();
            $params['itemsPerPage'] = $itemsPerPage;
        }

        return Url::toRoute(App::get()->getRequest()->getRoute(), $params);
    }

    public function getCurrentUrlWithoutParams(array $excludeParams = []): string
    {
        $params = $_GET;
        foreach ($excludeParams as $param) {
            unset($params[$param]);
        }
        unset($params[App::get()->getRequest()->routeParameterName]);

        return Url::toRoute(App::get()->getRequest()->getRoute(), $params);
    }

    public function getActionsColumnHtml(int $id): string
    {
        $updateUrl = Url::toRoute(App::get()->getRequest()->getController() . '/update', ['id' => $id]);
        $deleteUrl = Url::toRoute(App::get()->getRequest()->getController() . '/delete', ['id' => $id]);

        return <<<HTML
        <div class="action-buttons">
            <a href="$updateUrl" class="btn btn-warning btn-sm me-2" title="Изменить">
                <i class="bi bi-pencil"></i>
            </a>
            <a href="$deleteUrl" class="btn btn-danger btn-sm" title="Удалить" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">
                <i class="bi bi-trash"></i>
            </a>
        </div>
        HTML;
    }

    private function startSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
}
