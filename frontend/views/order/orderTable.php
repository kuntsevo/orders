<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

?>

<div class="site-success">
    <div class="container">
        <?= $this->render('_backButton') ?>
        <table class="table">
            <thead>
                <tr>
                    <? foreach ($tableAttributes as $attribute_name => $alias) : ?>
                        <th scope="col"><?= Html::encode($alias) ?></th>
                    <? endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <? foreach ($order->{$table_name} as $row) : ?>
                    <tr>
                        <? foreach ($tableAttributes as $attribute_name => $alias) : ?>
                            <td><?= Html::encode($row[$attribute_name]) ?></td>
                        <? endforeach; ?>
                    </tr>
                <? endforeach; ?>
            </tbody>
        </table>
    </div>
</div>