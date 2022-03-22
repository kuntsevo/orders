<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="site-success">
    <div class="container">
        <?= $this->render(
            '../partials/_backButton',
            ['route' => Url::to([
                '@orderItem',
                'customer' => $order->customer->uid,
                'order' => $order->uid,
            ])]
        ) ?>

        <?php foreach ($payment_types as $type => $alias) : ?>
            <div class="row justify-content-md-left">
                <?= Html::a(
                    $alias,
                    ['payment/pay', 'customer' => $order->customer->uid, 'order' => $order->uid, 'component' => $type,],
                    ['class' => 'btn btn-outline-primary', 'role' => 'button']
                ) ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
if (isset($external_url)) {
    $js = "window.open('$external_url')";
    $this->registerJs($js, $position = static::POS_READY);
}
