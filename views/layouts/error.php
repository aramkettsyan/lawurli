<?php

use yii\helpers\Html;
use app\assets\UserAsset;

/* @var $this \yii\web\View */
/* @var $content string */

UserAsset::register($this);
?>
<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <script>
            WebFont.load({
                custom: {
                    families: ['Open Sans']
                }
            });
        </script>
    </head>
    <body>
        <?php $this->beginBody() ?>
        <?= $content ?>
        <?php $this->endBody() ?>
    </body>
</html>

<?php $this->endPage() ?>

