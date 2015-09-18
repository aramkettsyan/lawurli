<?php

use yii\helpers\Html;

?>

<header>
    <div class="container">
        <div class="headerLogo">
            <a href="<?= \yii\helpers\Url::to(['users/index']) ?>"><img src="<?= \Yii::getAlias('@web') . '/images/' . $this->params['logo']; ?>" alt=""></a>
        </div>
        <div class="headerRight">
            <nav class="headerMenu">
                <ul class="clearAfter linkHover">
                    <li><a href="<?= \yii\helpers\Url::to(['users/index']) ?>" data-hover="Home">Home</a></li>
                    <li><a href="<?= \yii\helpers\Url::to(['users/contact-us']) ?>" data-hover="Contact Us">Contact Us</a></li>
                    <li><a href="/users/profile?colleaguesTab=open" data-hover="Colleagues">Colleagues</a></li>
                </ul>
            </nav>
            <?= $this->render('//notifications/notifications') ?>
            <div class="headerDrDn dropDn">
                <a href="#" class="dropDnBtn">
                    <div class="udImg">
                        <img src="images/user-image.png" alt="">
                        <span class="profileImage" style="background-image: url('<?php echo Html::encode(\Yii::getAlias('@web') . '/images/users_images/' . $this->params['current_user']->image); ?>')"></span>
                    </div>
                    <span class="udUserName"><?= Html::encode($this->params['current_user']->first_name) ?> <?= Html::encode($this->params['current_user']->last_name) ?></span>
                    <i class="icon-caret-down-two"></i>
                    <span class="udArrow"></span>
                </a>
                <div class="dropDnSub">
                    <ul>
                        <li><a href="<?= \yii\helpers\Url::to(['users/profile']) ?>">My account</a></li>
                        <li><a href="<?= \yii\helpers\Url::to(['users/edit']) ?>">Edit</a></li>
                        <li><a href="<?= \yii\helpers\Url::to(['users/logout']) ?>">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>