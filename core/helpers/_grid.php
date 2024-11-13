<?php
declare(strict_types=1);

/**
 * @var GridView $grid
 * @var array $data
 * @var array<array{attribute: string, label: string, value?: callable(mixed): mixed}> $columns
 * @var string $pagination
 * @var int|string $itemsPerPage
 * @var bool $itemsPerPageSelectorEnabled
 */

use core\helpers\GridView;

?>

<?php if (isset($itemsPerPageSelectorEnabled) && $itemsPerPageSelectorEnabled): ?>
    <div class="d-flex justify-content-end mb-2">
        <form method="get" id="itemsPerPageForm" class="form-inline"
              action="<?= htmlspecialchars($grid->getCurrentUrlWithoutParams(['itemsPerPage', 'page'])) ?>">
            <label for="itemsPerPage" class="me-2">Показать по:</label>
            <select name="itemsPerPage" id="itemsPerPage" class="form-control form-control-sm">
                <?php
                $options = [10, 50, 100, 200, 500, 'all'];
                foreach ($options as $option) {
                    $isSelected = ($itemsPerPage == $option) ? 'selected' : '';
                    /** @var int|string $option */
                    $optionLabel = ($option === 'all') ? 'Все' : $option;
                    echo "<option value=\"$option\" $isSelected>$optionLabel</option>";
                }
                ?>
            </select>
            <?php
            foreach ($_GET as $key => $value) {
                if (!in_array($key, ['itemsPerPage', 'page'])) {
                    $keyEscaped = htmlspecialchars($key);
                    $valueEscaped = htmlspecialchars($value);
                    echo "<input type=\"hidden\" name=\"$keyEscaped\" value=\"$valueEscaped\">";
                }
            }
            ?>
        </form>
    </div>
    <script>
        document.getElementById('itemsPerPage').addEventListener('change', function () {
            document.getElementById('itemsPerPageForm').submit();
        });
    </script>
<?php endif; ?>

<div class="table-responsive">
    <table class="table">
        <thead>
        <tr>
            <?php foreach ($columns as $columnData): ?>
                <th scope="col"><?= htmlspecialchars(ucfirst($columnData['label'] ?? $columnData['attribute'])) ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $item): ?>
            <tr>
                <?php foreach ($columns as $columnData): ?>
                    <?php if ($columnData['attribute'] === '{{actions}}'): ?>
                        <td><?= $grid->getActionsColumnHtml($item['id']) ?></td>
                    <?php else: ?>
                        <td>
                            <?php
                            $value = $columnData['value'] ?? function ($item) use ($columnData) {
                                return $item[$columnData['attribute']] ?? '';
                            };
                            echo htmlspecialchars((string)$value($item));
                            ?>
                        </td>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center">
    <?= $pagination ?>
</div>

<?php
$isActionsColumnPresent = array_filter(
    $columns,
    function ($column) {
        return $column['attribute'] === '{{actions}}';
    });

if ($isActionsColumnPresent): ?>
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="deleteConfirmLabel" class="modal-title">Подтверждение удаления</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    Вы уверены, что хотите удалить этот элемент?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <a href="#" class="btn btn-danger" id="confirmDeleteButton">Удалить</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('[data-bs-target="#deleteConfirmModal"]').forEach(function (element) {
            element.addEventListener('click', function (event) {
                event.preventDefault();
                const deleteUrl = this.getAttribute('href');
                document.getElementById('confirmDeleteButton').setAttribute('href', deleteUrl);
            });
        });
    </script>
<?php endif; ?>
