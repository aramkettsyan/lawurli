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
