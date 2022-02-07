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
	
	<?= var_dump($order); ?>

</div>
