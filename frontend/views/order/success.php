<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;

$customer_data = json_decode($customer->data)->attributes;
?>

<div class="site-success">
    <div class="container">

        <?php
        if ($customer_data->first_name) :
            $client_name = Html::encode($customer_data->first_name);
        else :
            $client_name =  "уважаемый клиент";
        endif;
        ?>

        <h2> <?= "Добрый день, " . $client_name . "!" ?></h2>

        <?php if (!$orders) :
            echo Html::encode('Нет документов в работе');
        ?>
        <?php else : ?>
            <h4><?= "Активные работы:" ?></h4>

            <?php foreach ($orders as $order) :
                $order_attributes = json_decode($order->data)->attributes;

                $vehicle = $order->vehicle;
                $vehicle_data = json_decode($vehicle->data)->attributes; ?>

                <div class="border border-primary rounded p-2 mb-3">
                    <h5><?= Html::encode($order->dealer->name) ?></h5>
                    <div class="d-flex flex-row bd-highlight mb-3 align-items-center">
                        <?= Html::encode($vehicle_data->model) ?>

                        <div class="p-2 bd-highlight">
                            <span class="border border-primary rounded p-1"><?= Html::encode($vehicle->registration_number) ?></span>
                        </div>
                    </div>

                    <hr>
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
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>