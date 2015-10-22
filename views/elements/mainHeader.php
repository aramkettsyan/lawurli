<?php

use yii\helpers\Html;

?>

<header>
    <div class="container">
        <div class="headerLogo">
            <a href="<?= \yii\helpers\Url::to(['users/index']) ?>">
                <img src="/images/logo.png" alt=""/>
            </a>
        </div>
        <div class="headerRight">
            <div class="headerSrch">
                <form method="GET" action="<?= \yii\helpers\Url::to(['users/search']) ?>">
                    <input type="text" name="query" value="<?= isset($this->params['query']) ? $this->params['query'] : '' ?>" placeholder="Search...">
                    <button type="submit">
                        <i class="icon-search"></i>
                    </button>
                </form>
                <a href="/users/search?advancedSearch=open" class="advSrch">Advanced</a>
            </div>
            <nav class="headerMenu">
                <ul class="clearAfter linkHover">
                    <?php if (Yii::$app->user->isGuest) { ?>
                        <li><a href="#login-popup" class="popupBtn" data-hover="Login">Login</a></li>
                        <li><a href="#signup-popup" class="popupBtn" data-hover="Sign up">Sign up</a></li>
                    <?php } else { ?>
                        <li><a href="<?= \yii\helpers\Url::to(['users/index']) ?>" data-hover="Home">Home</a></li>
                        <li><a href="<?= \yii\helpers\Url::to(['users/contact-us#contact_us']) ?>" data-hover="Contact Us">Contact Us</a></li>
                        <li><a href="/users/profile?colleaguesTab=open" data-hover="Colleagues">Colleagues</a></li>
                    <?php } ?>
                </ul>
            </nav>
            <?php if (!Yii::$app->user->isGuest) { ?>
                <?= $this->render('//notifications/notifications') ?>
                <div class="headerDrDn dropDn">
                    <a href="#" class="dropDnBtn">
                        <div class="udImg">
                            <img src="/images/user-image.png" alt="">
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

            <?php } ?>
        </div>
    </div>
</header>