<?php

use yii\helpers\Html;
use app\assets\UserAsset;

/* @var $this \yii\web\View */
/* @var $content string */

UserAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
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
        <?php if (Yii::$app->controller->action->id == 'index') { ?>
            <section id="cd-intro">
                <div id="cd-intro-background"></div>
                <div id="cd-intro-tagline"><!-- insert your tagline here --></div>
            </section>
        <?php } ?>
        <div class="mainWrapper">
            <?php if (Yii::$app->controller->action->id != 'index') { ?>
                <header>
                    <div class="container">
                        <div class="headerLogo">
                            <a href="<?= \yii\helpers\Url::to(['users/index']) ?>"><img src="<?= \Yii::getAlias('@web') . '/images/'.$this->params['logo']; ?>" alt=""></a>
                        </div>
                        <div class="headerRight">
                            <div class="headerSrch">
                                <form method="GET" action="<?= \yii\helpers\Url::to(['users/search']) ?>">
                                    <input type="text" name="query" value="<?= isset($this->params['query'])?$this->params['query']:'' ?>" placeholder="Search...">
                                    <button type="submit">
                                        <i class="icon-search"></i>
                                    </button>
                                </form>
                            </div>
                            <nav class="headerMenu">
                                <ul class="clearAfter linkHover">
                                    <?php if (Yii::$app->user->isGuest) { ?>
                                        <li><a href="#login-popup" class="popupBtn" data-hover="Login">Login</a></li>
                                        <li><a href="#signup-popup" class="popupBtn" data-hover="Sign up">Sign up</a></li>
                                    <?php } else { ?>
                                        <li><a href="<?= \yii\helpers\Url::to(['users/index']) ?>" data-hover="Home">Home</a></li>
                                        <li><a href="/users/profile?colleaguesTab=open" data-hover="Colleagues">Colleagues</a></li>
                                    <?php } ?>
                                </ul>
                            </nav>
                            <?php if (!Yii::$app->user->isGuest) { ?>
                                    <?= $this->render('//notifications/notifications') ?>
                                <div class="headerDrDn dropDn">
                                    <a href="#" class="dropDnBtn">
                                        <div class="udImg">
                                            <img src="images/user-image.png" alt="">
                                            <span class="profileImage" style="background-image: url('<?php echo \Yii::getAlias('@web') . '/images/users_images/' . $this->params['user']->image; ?>')"></span>
                                        </div>

                                        <span class="udUserName"><?= $this->params['user']->first_name ?> <?= $this->params['user']->last_name ?></span>
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
            <?php } else if (!Yii::$app->user->isGuest) { ?>
                <header>
                    <div class="container">
                        <div class="headerLogo">
                            <a href="<?= \yii\helpers\Url::to(['users/index']) ?>"><img src="<?= \Yii::getAlias('@web') . '/images/'.$this->params['logo']; ?>" alt=""></a>
                        </div>
                        <div class="headerRight">
                            <nav class="headerMenu">
                                <ul class="clearAfter linkHover">
                                    <li><a href="<?= \yii\helpers\Url::to(['users/index']) ?>" data-hover="Home">Home</a></li>
                                    <li><a href="/users/profile?colleaguesTab=open" data-hover="Colleagues">Colleagues</a></li>
                                </ul>
                            </nav>
                            <?= $this->render('//notifications/notifications') ?>
                            <div class="headerDrDn dropDn">
                                <a href="#" class="dropDnBtn">
                                    <div class="udImg">
                                        <img src="images/user-image.png" alt="">
                                        <span class="profileImage" style="background-image: url('<?php echo \Yii::getAlias('@web') . '/images/users_images/' . $this->params['user']->image; ?>')"></span>
                                    </div>
                                    <span class="udUserName"><?= $this->params['user']->first_name ?> <?= $this->params['user']->last_name ?></span>
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
            <?php } ?>
            <?= $content ?>

        </div>
        <footer>
            <div class="container">
                <div class="clearAfter">
                    <div class="footerAbout">
                        <h4>About us</h4>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quam vero corporis, perspiciatis delectus dicta ullam et eaque consequuntur aliquid possimus facilis nesciunt voluptatem molestiae consectetur quibusdam temporibus voluptatum aliquam doloremque. Perspiciatis delectus dicta ullam et.</p>
                    </div>
                    <div class="footerContact">
                        <h4>Contact Us</h4>
                        <p><i class="icon-letter-mail-1"></i>blah@mail.com</p>
                        <p><i class="icon-call-phone-square"></i>341 987 44 63</p>
                        <div class="footerSocials">
                            <ul>
                                <li>
                                    <a href="#"><i class="icon-twitter39"></i></a>
                                    <a href="#"><i class="icon-youtube33"></i></a>
                                    <a href="#"><i class="icon-pinterest28"></i></a>
                                    <a href="#"><i class="icon-instagram14"></i></a>
                                    <a href="#"><i class="icon-linkedin22"></i></a>
                                    <a href="#"><i class="icon-facebook45"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="footerNav">
                    <ul class="clearAfter linkHover">
                        <li><a href="#" data-hover="Home">Home</a></li>
                        <li><a href="#" data-hover="Blah menu">Blah menu</a></li>
                        <li><a href="#" data-hover="Another blah">Another blah</a></li>
                        <li><a href="#" data-hover="Something else">Something else</a></li>
                    </ul>
                </div>
            </div>
        </footer>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
