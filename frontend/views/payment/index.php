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
                <button id=<?= $type ?> class="btn btn-primary" type="button" onclick="getInternetAcquiringUrl(this.id)">
                    <span id=<?= "spinner_{$type}" ?> class="" style="width: 2rem; height: 2rem;" role="status"></span>
                    <span><?= $alias ?></span>
                </button>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
$url = Url::to(['payment/pay', 'customer' => $order->customer->uid, 'order' => $order->uid, 'component' => $type], true);
$js = <<< JS
async function getInternetAcquiringUrl(id){
    let elem = document.getElementById('spinner_' + id);
    let spinnerElem = 'spinner-border';
    elem.classList.toggle(spinnerElem);
    let response = await fetch('{$url}');
    if (response.ok) {       
        let json = await response.json();
        window.open(json.url);
        elem.classList.toggle(spinnerElem);
    } else {
        console.error("Ошибка HTTP: " + response.status);
    }
}
JS;

$this->registerJs($js, $position = static::POS_BEGIN, $key = null);
