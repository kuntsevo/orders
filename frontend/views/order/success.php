<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;

?>
<div class="site-success">

    <h2><?= Html::encode($this->title) ?></h2>

    <div class="alert alert-success">
        <i class="glyphicon glyphicon-ok"></i>
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <h3><?= Html::encode($order->dealer->name) ?></h3>
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
    </div>
</div>
</div>