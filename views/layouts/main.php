<?php

use yii\helpers\Html;
use app\assets\UserAsset;
use Yii;

/* @var $this \yii\web\View */
/* @var $content string */

UserAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
        <?php $this->beginBody() ?>
        <div class="maincontainer">
            <nav class="navbar navbar-inverse navbar-static-top">
                <div class="container">
                    <div class="navbar-header">    
                        <?php echo Html::a('Network', \yii\helpers\Url::to(['admins/index']), ['class' => 'navbar-brand']) ?>
                    </div>
                    <div id="navbar" class="collapse navbar-collapse navbar-right">
                        <ul class="nav navbar-nav">

                            <?php if (Yii::$app->user->isGuest) { ?>
                                <li class="<?= Yii::$app->controller->action->id === 'login' ? 'active' : '' ?>"><?php echo Html::a('Login', \yii\helpers\Url::to(['users/login']), ['class' => '']); ?></li>
                                <li class="<?= Yii::$app->controller->action->id === 'registration' ? 'active' : '' ?>"><?php echo Html::a('Sign up', \yii\helpers\Url::to(['users/registration']), ['class' => '']); ?></li>
                            <?php } else { ?>
                                <li class="<?= Yii::$app->controller->action->id === 'index' ? 'active' : '' ?>"><?php echo Html::a('Home', \yii\helpers\Url::to(['users/index']), ['class' => '']); ?></li>
                                <li class="<?= Yii::$app->controller->action->id === 'edit' ? 'active' : '' ?>"><?php echo Html::a('Edit', \yii\helpers\Url::to(['users/edit']), ['class' => '']); ?></li>
                                <li ><?php echo Html::a('Logout(' . Yii::$app->user->identity->username . ')', \yii\helpers\Url::to(['users/logout']), ['class' => '']); ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </nav>

            <?= $content ?> 

        </div>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
