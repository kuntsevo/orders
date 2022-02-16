<?php

use yii\helpers\Html;

?>

<div class="d-flex flex-row bd-highlight mb-3 align-items-center">
    <?= Html::encode($vehicle_data->model) ?>

    <div class="p-2 bd-highlight">
        <span class="border border-primary rounded p-1 bg-light"><?= Html::encode($vehicle->registration_number) ?></span>
    </div>
</div>