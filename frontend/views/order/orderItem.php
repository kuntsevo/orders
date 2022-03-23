<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="site-success">
    <div class="container">
        <?php

        $vehicle = $order->vehicle;
        $staff = $order->staff;
        $staffInfo = $order->staffInfo;

        ?>

        <div>
            <?= $this->render(
                '../partials/_backButton',
                ['route' => Url::to(['@orders', 'customer' => $order->customer->uid])]
            ) ?>

            <?= Html::tag('h5', Html::encode($order->dealer->name)) ?>

            <?= $this->render('../partials/_vehicleInfo', compact('vehicle')) ?>

            <div class="row justify-content-md-left">
                <div class="col-md-auto">
                    <?= Html::tag('span', Html::activeLabel($vehicle, 'vin')) ?>
                </div>
                <div class="col-md-auto">
                    <?= Html::encode($vehicle->vin) ?>
                </div>
            </div>

            <div class="row justify-content-md-left">
                <div class="col-md-auto">
                    <?= Html::tag('span', Html::activeLabel($order, 'repair_kind')) ?>
                </div>
                <div class="col-md-auto">
                    <?= Html::encode($order->repair_kind) ?>
                </div>
            </div>

            <div class="row justify-content-md-left">
                <div class="col-md-auto">
                    <?= Html::tag('span', Html::label('Мастер-консультант')) ?>
                </div>
            </div>

            <div class="card my-2 py-1 border border-primary rounded" style="max-width: 540px;">
                <div class="row g-0">
                    <div class="col-md-4">
                        <?= Html::img(
                            $staff->photo,
                            ['alt' => Html::encode($staff->name), 'class' => 'img-fluid rounded-start']
                        ) ?>
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title"><?= Html::encode($staff->name) ?></h5>
                            <?= Html::encode($staffInfo->work_phone) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-md-left">
                <div class="col-md-auto">
                    <?= Html::tag('span', Html::activeLabel($order, 'number')) ?>
                </div>
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
                    <?= Html::tag('span', Html::activeLabel($order, 'works_cost')) ?>
                </div>
                <div class="col-md-auto">
                    <?= Html::encode(Yii::$app->formatter->asCurrency(($order->works_cost))) ?>
                </div>
            </div>

            <div class="row justify-content-md-left">
                <div class="col-md-auto">
                    <?= Html::tag('span', Html::activeLabel($order, 'goods_cost')) ?>
                </div>
                <div class="col-md-auto">
                    <?= Html::encode(Yii::$app->formatter->asCurrency(($order->goods_cost))) ?>
                </div>
            </div>

            <div class="row justify-content-md-left">
                <div class="col-md-auto">
                    <?= Html::tag('span', Html::activeLabel($order, 'net_price')) ?>
                </div>
                <div class="col-md-auto">
                    <?= Html::encode(Yii::$app->formatter->asCurrency(($order->net_price))) ?>
                </div>
            </div>

            <div class="row justify-content-md-left">
                <div class="col-md-auto">
                    <?= Html::tag('span', Html::activeLabel($order, 'discount')) ?>
                </div>
                <div class="col-md-auto">
                    <?= Html::encode(Yii::$app->formatter->asCurrency(($order->discount))) ?>
                </div>
            </div>

            <div class="row justify-content-md-left">
                <?= Html::a(
                    'Работы &rarr;',
                    [
                        '@orderTable', 'customer' => $order->customer->uid,
                        'order' => $order->uid, 'component' => 'works'
                    ],
                    ['class' => 'btn btn-link', 'role' => 'button']
                ) ?>
            </div>

            <div class="row justify-content-md-left">
                <?= Html::a(
                    'Запчасти &rarr;',
                    [
                        '@orderTable',
                        'customer' => $order->customer->uid,
                        'order' => $order->uid,
                        'component' => 'goods',
                    ],
                    ['class' => 'btn btn-link', 'role' => 'button']
                ) ?>
            </div>

            <div class="row justify-content-md-left">
                <?= Html::a(
                    'Рекомендации &rarr;',
                    [
                        '@orderTable',
                        'customer' => $order->customer->uid,
                        'order' => $order->uid,
                        'component' => 'recommendations',
                    ],
                    ['class' => 'btn btn-link', 'role' => 'button']
                ) ?>
            </div>

            <div class="row justify-content-md-left">
                <?= Html::a(
                    'Документы &rarr;',
                    [
                        '@documents',
                        'customer' => $order->customer->uid,
                        'order' => $order->uid,
                    ],
                    ['class' => 'btn btn-link', 'role' => 'button']
                ) ?>
            </div>

            <hr>

            <div class="row justify-content-md-left">
                <div class="col-md-auto">
                    <?= Html::tag('span', Html::activeLabel($order, 'amount_payable')) ?>
                </div>
                <div class="col-md-auto">
                    <?= Html::encode(Yii::$app->formatter->asCurrency($order->amountPayable)) ?>
                </div>
            </div>

            <div class="d-grid gap-2 pt-3">
                <?= Html::a(
                    'Оплатить',
                    ['@payments', 'customer' => $order->customer->uid, 'order' => $order->uid],
                    ['class' => 'btn btn-primary', 'role' => 'button']
                ) ?>
            </div>
        </div>
    </div>
</div>