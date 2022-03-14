<?php

use yii\helpers\Html;

$route = isset($route) ? $route : Yii::$app->request->referrer;
?>

<?= Html::a(
    '&larr;',
    $route,
    ['class' => 'btn btn-primary btn-sm rounded-pill px-4 mb-3', 'role' => 'button']
)  ?>