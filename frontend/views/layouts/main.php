<?php

use frontend\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>

<head>
    <?php $this->registerCsrfMetaTags() ?>
    <?php $this->head() ?>
    <?php if (YII_ENV == 'prod') $this->render('_counters') ?>
</head>

<?php $this->beginBody() ?>
<?= $content ?>

<?php $this->endBody() ?>
</body>

<?php $this->endPage();
