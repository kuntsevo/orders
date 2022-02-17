<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

?>

<div class="site-success">
    <div class="container">

        <? $client_name = $customer->first_name ? $customer->first_name : "уважаемый клиент"; ?>

        <h2> <?= "Добрый день, {$client_name}!" ?></h2>

        <? if (!$orders) :
            echo Html::encode('Нет документов в работе');
        ?>
        <? else : ?>
            <h4><?= "Активные работы:" ?></h4>

            <? foreach ($orders as $order) :
                $vehicle = $order->vehicle; ?>

                <div class="border border-primary rounded p-2 mb-3">
                    <h5><?= Html::encode($order->dealer->name) ?></h5>
                    <?= $this->render('_vehicleInfo', compact('vehicle')) ?>

                    <hr>
                    <div class="row justify-content-md-left">
                        <div class="col-md-auto">
                            <?= Html::encode($order->attributeLabels()['number']) ?>

                        </div>
                        <div class="col-md-auto">
                            <?= Html::encode($order->number) ?>
                        </div>
                    </div>
                    <div class="row justify-content-md-left">
                        <div class="col-md-auto">
                            Статус:
                        </div>
                        <div class="col-md-auto">
                            <?= Html::encode($order->status) ?>
                        </div>
                    </div>
                    <div class="row justify-content-md-left">
                        <div class="col-md-auto">
                            Дата выдачи:
                        </div>
                        <div class="col-md-auto">
                            <?= Html::encode(Yii::$app->formatter->asDate($order->issuance_date)) ?>
                        </div>
                    </div>
                    <div class="row justify-content-md-left">
                        <div class="col-md-auto">
                            Сумма к оплате:
                        </div>
                        <div class="col-md-auto">
                            <?= Html::encode(Yii::$app->formatter->asCurrency(($order->amount - $order->payment_amount))) ?>
                        </div>
                    </div>
                    <div class="d-grid gap-2 pt-3">
                        <button class="btn btn-primary" type="submit">Оплатить</button>
                        <?= Html::a(
                            'Подробнее',
                            ['@orderItem', 'order_id' => $order->uid],
                            ['class' => 'btn btn-outline-primary', 'role' => 'button']
                        )  ?>
                    </div>
                </div>
            <? endforeach; ?>
        <? endif; ?>
    </div>
</div>