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

        $staff_data = $orderWithStaff->staff_data;

        ?>

        <div>
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
            </div>

            <div class="row justify-content-md-left">
                <div class="d-flex flex-row bd-highlight mb-3 align-items-center">
                    <div class="bd-highlight border border-primary rounded">
                        <div class="col-md-auto">
                            <?= Html::encode($staff_data->name) ?>
                        </div>
                        <div class="col-md-auto">
                            <?= Html::encode($orderWithStaff->work_phone) ?>
                        </div>
                    </div>
                </div>
            </div>

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
                    <?= Html::encode($order_attributes->status) ?>
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
                    Сумма по работам:
                </div>
                <div class="col-md-auto">
                    <?= Html::encode(Yii::$app->formatter->asCurrency(($order_attributes->works_cost))) ?>
                </div>
            </div>

            <div class="row justify-content-md-left">
                <div class="col-md-auto">
                    Сумма по товарам:
                </div>
                <div class="col-md-auto">
                    <?= Html::encode(Yii::$app->formatter->asCurrency(($order_attributes->goods_cost))) ?>
                </div>
            </div>

            <div class="row justify-content-md-left">
                <div class="col-md-auto">
                    Сумма без скидки:
                </div>
                <div class="col-md-auto">
                    <?= Html::encode(Yii::$app->formatter->asCurrency(($order_attributes->net_price))) ?>
                </div>
            </div>

            <div class="row justify-content-md-left">
                <div class="col-md-auto">
                    Сумма скидки:
                </div>
                <div class="col-md-auto">
                    <?= Html::encode(Yii::$app->formatter->asCurrency(($order_attributes->discount))) ?>
                </div>
            </div>

            <hr>

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
            </div>
        </div>
    </div>
</div>