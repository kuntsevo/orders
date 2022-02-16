<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

?>

<div class="site-success">
    <div class="container">
        <?php
        $order_attributes = $order->dataAttributes();

        $vehicle = $order->vehicle;
        $vehicle_data = $vehicle->dataAttributes();

        ?>

        <div class="border border-primary rounded p-2 mb-3">
            <h5><?= Html::encode($order->dealer->name) ?></h5>
            <?= $this->render('_vehicleInfo.php', compact('vehicle', 'vehicle_data')) ?>

            <div class="row justify-content-md-left">
                <div class="col-md-auto">
                    VIN:
                </div>
                <div class="col-md-auto">
                    <?= Html::encode($vehicle->vin) ?>
                </div>
            </div>

            <div class="row justify-content-md-left">
                <div class="col-md-auto">
                    Вид ремонта:
                </div>
                <div class="col-md-auto">
                    <?= Html::encode($order_attributes->repair_kind) ?>
                </div>
            </div>
            <div class="row justify-content-md-left">
                <div class="col-md-auto">
                    Мастер-консультант:
                </div>
                <div class="col-md-auto">
                    <?  //Html::encode($staff->employee_id) 
                    ?>
                    <? //Html::encode($staff->staffInfo->work_phone) 
                    ?>
                </div>
            </div>
            <div class="row justify-content-md-left">
                <div class="col-md-auto">
                    Дата выдачи:
                </div>
                <div class="col-md-auto">
                    <?= Html::encode(Yii::$app->formatter->asDate($order_attributes->issuance_date)) ?>
                </div>
            </div>
            <div class="row justify-content-md-left">
                <div class="col-md-auto">
                    Сумма к оплате:
                </div>
                <div class="col-md-auto">
                    <?= Html::encode(Yii::$app->formatter->asCurrency(($order_attributes->amount - $order_attributes->payment_amount))) ?>
                </div>
            </div>
            <div class="d-grid gap-2">
                <button class="btn btn-primary" type="submit">Оплатить</button>
                <a class="btn btn-outline-primary" href="#" role="button">Подробнее</a>
            </div>
        </div>
    </div>
</div>