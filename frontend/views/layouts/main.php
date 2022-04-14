<?php

use frontend\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>

<head>
    <?php $this->registerCsrfMetaTags() ?>
    <?php $this->head() ?>
</head>

<?php $this->beginBody() ?>
<?= $content ?>

<?php $this->endBody() ?>
</body>

<?php $this->endPage();
