<?php
/**
 * @var GridView $grid
 * @var array $data
 * @var array<array{attribute: string, label: string, value?: callable(mixed): mixed}> $columns
 * @var string $pagination
 */

use core\helpers\GridView;

?>

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
            <?php foreach ($data as $index => $item): ?>
                <tr>
                    <?php foreach ($columns as $columnData): ?>
                        <?php if ($columnData['attribute'] === '{{actions}}') : ?>
                            <td><?= $grid->getActionsColumns($item['id']) ?></td>
                        <?php else: ?>
                            <td><?= isset($columnData['value']) ? $columnData['value']($item) : htmlspecialchars($item[$columnData['attribute']] ?? '') ?></td>
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
$isContainsActions = !empty(array_filter($columns, function ($column) {
    return isset($column['attribute']) && $column['attribute'] === '{{actions}}';
}));

if ($isContainsActions) : ?>
    <!-- Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmLabel">Подтверждение удаления</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Вы уверены, что хотите удалить этот элемент?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <a href="" class="btn btn-danger" id="deleteLink">Удалить</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).on('click', '[data-bs-target="#deleteConfirmModal"]', function (event) {
            event.preventDefault();
            const hrefValue = $(this).attr('href');
            $('#deleteLink').attr('href', hrefValue);
        });
    </script>
<?php endif; ?>