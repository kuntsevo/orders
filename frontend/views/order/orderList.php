<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

?>

<div class="site-success">
    <div class="container">
        <?php $client_name = $customer->first_name ? $customer->first_name : "уважаемый клиент"; ?>

        <h2> <?= "Добрый день, {$client_name}!" ?></h2>

        <?php if (!$orders) : ?>
            <?= Html::tag('p', Html::encode('Нет документов в работе')); ?>
        <?php else : ?>
            <?= Html::tag('h4', Html::encode('Активные работы:')) ?>

            <?php foreach ($orders as $order) :
                $vehicle = $order->vehicle; ?>

                <div class="border border-primary rounded p-2 mb-3">
                    <?= Html::tag('h5', Html::encode($order->dealer->name)) ?>
                    <?= $this->render('_vehicleInfo', compact('vehicle')) ?>

                    <hr>
                    <div class="row justify-content-md-left">
                        <?= Html::tag(
                            'div',
                            Html::tag('span', Html::activeLabel($order, 'number')),
                            ['class' => 'col-md-auto']
                        ) ?>
                        <div class="col-md-auto">
                            <?= Html::encode($order->number) ?>
                        </div>
                    </div>
                    <div class="row justify-content-md-left">
                        <div class="col-md-auto">
                            <?= Html::tag('span', Html::activeLabel($order, 'status')) ?>
                        </div>
                        <div class="col-md-auto">
                            <?= Html::encode($order->status) ?>
                        </div>
                    </div>
                    <div class="row justify-content-md-left">
                        <div class="col-md-auto">
                            <?= Html::tag('span', Html::activeLabel($order, 'issuance_date')) ?>
                        </div>
                        <div class="col-md-auto">
                            <?= Html::encode(Yii::$app->formatter->asDate($order->issuance_date)) ?>
                        </div>
                    </div>
                    <div class="row justify-content-md-left">
                        <div class="col-md-auto">
                            <?= Html::tag('span', Html::activeLabel($order, 'amount_payable')) ?>
                        </div>
                        <div class="col-md-auto">
                            <?= Html::encode(Yii::$app->formatter->asCurrency($order->amountPayable)) ?>
                        </div>
                    </div>
                    <div class="d-grid gap-2 pt-3">
                        <?= Html::button('Оплатить', ['class' => 'btn btn-primary', 'type' => 'submit']) ?>
                        <?= Html::a(
                            'Подробнее',
                            ['@orderItem', 'order_id' => $order->uid],
                            ['class' => 'btn btn-outline-primary', 'role' => 'button']
                        ) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>