<?php

use yii\helpers\Html;

?>

<?= Html::a(
    '&larr;',
    Yii::$app->request->referrer,
    ['class' => 'btn btn-primary btn-sm rounded-pill px-4 mb-3', 'role' => 'button']
)  ?>