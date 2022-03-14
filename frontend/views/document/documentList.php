<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="site-success">
    <div class="container">
        <?= $this->render(
            '..\partials\_backButton',
            ['route' => Url::to([
                '@orderItem',
                'customer' => $order->customer->uid,
                'order' => $order->uid,
            ])]
        ) ?>

        <h4>Документы</h4>
        <hr>

        <?php if (empty($documentList)) : ?>
            <h4><?= Html::encode('Здесь пока ничего нет...') ?></h4>
        <?php else : ?>
            <?php foreach ($documentList as $document_key => $label) : ?>
                <?php $label = empty($label) ? 'Документ' : $label ?>
                <div class="row justify-content-md-left">
                    <?= Html::a(
                        "$label &rarr;",
                        [
                            '@document',
                            'customer' => $order->customer->uid,
                            'order' => $order->uid,
                            'component' => $document_key,
                        ],
                        ['class' => 'btn btn-link', 'role' => 'button']
                    ) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>