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
            '../partials/_backButton',
            ['route' => Url::to([
                '@orderItem',
                'customer' => $order->customer->uid, 'order' => $order->uid
            ])]
        ) ?>

        <?php if (empty($order->{$table_name})) : ?>
            <h4><?= Html::encode('Здесь пока ничего нет...') ?></h4>
        <?php else : ?>
            <table class="table">
                <thead>
                    <tr>
                        <?php foreach ($tableAttributes as $attribute_name => $alias) : ?>
                            <th scope="col"><?= Html::encode($alias) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order->{$table_name} as $row) : ?>
                        <tr>
                            <?php foreach ($tableAttributes as $attribute_name => $alias) : ?>
                                <td><?= Html::encode($row[$attribute_name]) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>