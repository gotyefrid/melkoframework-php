<?php
/** @var GridView $grid */
/** @var array $data */
/** @var array $columns */
/** @var string $pagination */

use core\helpers\GridView;

?>

<table class="table">
    <thead>
    <tr>
        <?php foreach ($columns as $column => $label): ?>
            <th scope="col"><?= htmlspecialchars(ucfirst($label)) ?></th>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($data as $index => $item): ?>
        <tr>
            <?php foreach ($columns as $column => $label): ?>
                <?php if ($column === '{{actions}}') : ?>
                    <td><?= $grid->getActionsColumns() ?></td>
                <?php else: ?>
                    <td><?= htmlspecialchars($item[$column] ?? '') ?></td>
                <?php endif; ?>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="d-flex justify-content-center">
    <?= $pagination ?>
</div>
