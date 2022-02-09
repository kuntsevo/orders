<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;

$vehicle = $order->vehicle;
$vehicle_data = json_decode($vehicle->data)->attributes;

?>
<div class="site-success">

    <h2><?= Html::encode($this->title) ?></h2>

    <div class="alert alert-success">
        <i class="glyphicon glyphicon-ok"></i>
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <h3><?= Html::encode($order->dealer->name) ?></h3>

    <div class="d-flex flex-row bd-highlight mb-3">
        <div class="p-2 bd-highlight"><?= Html::encode($vehicle_data->brand) ?>
            <?= Html::encode($vehicle_data->model) ?></div>
        <div class="p-2 bd-highlight"><span class="border border-primary rounded"><?= Html::encode($vehicle->registration_number) ?></span></div>
    </div>


    <div class="container">
        <div class="row justify-content-md-left">
            <div class="col-md-auto">
                № заказ-наряда:
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
                <?= Html::encode($data_attributes->status) ?>
            </div>
        </div>
        <div class="row justify-content-md-left">
            <div class="col-md-auto">
                Дата выдачи:
            </div>
            <div class="col-md-auto">
                <?= Html::encode(Yii::$app->formatter->asDate($data_attributes->issuance_date)) ?>
            </div>
        </div>
        <div class="row justify-content-md-left">
            <div class="col-md-auto">
                Сумма к оплате:
            </div>
            <div class="col-md-auto">
                <?= Html::encode(Yii::$app->formatter->asCurrency(($data_attributes->amount - $data_attributes->payment_amount))) ?>
            </div>
        </div>
        <div class="d-grid gap-2">
            <button class="btn btn-primary" type="submit">Оплатить</button>
            <a class="btn btn-outline-primary" href="#" role="button">Подробнее</a>
        </div>
    </div>
</div>