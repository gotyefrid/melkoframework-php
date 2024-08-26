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
