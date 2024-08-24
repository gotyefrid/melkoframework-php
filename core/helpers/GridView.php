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

    /**
     * @var array
     */
    private $actionColumns = [];

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
        $this->currentPage = max(1, $page); // Ensure the page is at least 1

        return $this;
    }

    public function render()
    {
        if (!$this->dataProvider) {
            return 'Нет записей';
        }

        $data = $this->pagination ? $this->getPaginatedData() : $this->dataProvider;

        return Renderer::render(__DIR__ . '/_grid.php', [
            'data' => $data,
            'columns' => $this->getColumns(),
            'pagination' => $this->pagination ? $this->getPaginationControls() : '',
            'grid' => $this
        ]);
    }

    public function setColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    public function getColumns(): array
    {
        if ($this->columns) {
            return $this->columns;
        } else {
            if ($first = $this->dataProvider[array_key_first($this->dataProvider)]) {
                if (is_object($first) && $first instanceof Model) {
                    return array_combine($first->attributes, $first->attributes);
                } elseif (is_array($first)) {
                    return array_combine(array_keys($first), array_keys($first));
                }

                throw new \DomainException('Неизвестный объект в GridView');
            }
        }

        return [];
    }

    private function getPaginatedData(): array
    {
        $offset = ($this->currentPage - 1) * $this->itemsPerPage;
        return array_slice($this->dataProvider, $offset, $this->itemsPerPage);
    }

    private function getPaginationControls(): string
    {
        $totalItems = count($this->dataProvider);
        $totalPages = ceil($totalItems / $this->itemsPerPage);

        $html = '<nav><ul class="pagination pagination-dark">';

        //if ($this->currentPage > 1) {
        //    $html .= '<li class="page-item"><a class="page-link" href="' . $this->getPagingUrl($this->currentPage - 1) . '">Previous</a></li>';
        //}

        for ($i = 1; $i <= $totalPages; $i++) {
            $active = ($i === $this->currentPage) ? ' active' : '';
            $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . $this->getPagingUrl($i) . '">' . $i . '</a></li>';
        }

        //if ($this->currentPage < $totalPages) {
        //    $html .= '<li class="page-item"><a class="page-link" href="' . $this->getPagingUrl($this->currentPage + 1) . '">Next</a></li>';
        //}

        $html .= '</ul></nav>';

        return $html;
    }

    private function getPagingUrl(int $page): string
    {
        $params = $_GET;
        unset($params['path']);
        $params['page'] = $page;

        return Url::toRoute(Url::currentRoute(), $params);
    }

    public function getActionsColumns(int $id): string
    {
        return '
    <div class="d-flex justify-content-center">
        <a href="' . Url::toRoute(Url::currentController() . '/update', ['id' => $id]) . '" class="btn btn-warning btn-sm me-2" title="Изменить">
            <i class="bi bi-pencil"></i>
        </a>
        <a href="" class="btn btn-danger btn-sm" title="Удалить" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal' . $id . '">
            <i class="bi bi-trash"></i>
        </a>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="deleteConfirmModal' . $id . '" tabindex="-1" aria-labelledby="deleteConfirmLabel' . $id . '" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmLabel' . $id . '">Подтверждение удаления</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Вы уверены, что хотите удалить этот элемент?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <a href="' . Url::toRoute(Url::currentController() . '/delete', ['id' => $id]) . '" class="btn btn-danger">Удалить</a>
                </div>
            </div>
        </div>
    </div>
    ';
    }

    public function setActionColumns(array $columns): self
    {
        $this->actionColumns = $columns;
        return $this;
    }
}
