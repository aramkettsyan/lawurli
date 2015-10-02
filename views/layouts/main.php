<?php

use yii\helpers\Html;
use app\assets\UserAsset;
use yii\widgets\ActiveForm;

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
                <?= $this->render('//elements/mainHeader') ?>
            <?php } else if (!Yii::$app->user->isGuest) { ?>
                <?= $this->render('//elements/indexHeader') ?>
            <?php } ?>
            <?= $content ?>

            <?php if (Yii::$app->user->isGuest) { ?>
                <!-- ###### -->
                <!-- POPUPS -->
                <!-- ###### -->

                <?= $this->render('//elements/login') ?>
                <?= $this->render('//elements/signUp') ?>
                <?= $this->render('//elements/forgotPassword_1') ?>
                <?= $this->render('//elements/forgotPassword_2') ?>

            <?php } ?>
        </div>
        <footer>
            <div class="container">
                <div class="clearAfter">
                    <div class="footerAbout">
                        <h4>About us</h4>
                        <p><?php echo $this->params['about_us'] ?></p>
                    </div>
                    <div class="footerContact">
                        <h4>Contact Us</h4>
                        <p><i class="icon-letter-mail-1"></i><?php echo $this->params['contact_email'] ?></p>
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
                        <li><a href="<?= \yii\helpers\Url::to(['users/index']) ?>" data-hover="Home">Home</a></li>
                        <li><a href="<?= \yii\helpers\Url::to(['users/contact-us#about_us']) ?>" data-hover="About us">About us</a></li>
                        <li><a href="<?= \yii\helpers\Url::to(['users/contact-us#contact_us']) ?>" data-hover="Contact us">Contact us</a></li>
                    </ul>
                </div>
                <p class="poweredBy">© 2015 <?= (date('Y', time()) == '2015' ? '' : '- ' . date('Y', time())) ?> Lawurli | Created by <a href="http://st-dev.com" target="_blank">STDev</a></p>
            </div>
        </footer>

        <?php if (Yii::$app->controller->action->id == 'profile') { ?>
            <script>
                function notConnectedUsers() {
                    var ids = '';
                    $('.connect').each(function () {
                        if (ids === '') {
                            ids += $(this).attr('id');
                        } else {
                            ids += ',' + $(this).attr('id');
                        }
                    });
                    $('.skip,.connect').off();
                    $('.connect').on('click', function () {
                        var imagesPath = '<?php echo \Yii::getAlias('@web') . '/images/users_images/'; ?>';
                        console.log(imagesPath);
                        
                        $.ajax({
                            method: "POST",
                            url: "/users/get-not-connected-users",
                            data: {add: true, allIds: ids, id: $(this).attr('id')},
                            dataType: "json"
                        }).done(function (msg) {
                            console.log(msg);
                            var userTitle = msg.users_titles;
                            console.log(userTitle[0]);
                            var msg = msg.users;
                            for (var i = 0; i < Object.keys(msg).length; i++) {
                                if (msg[0] !== undefined) {
                                    var user = $('.notConnectedUser:first').clone();
                                    if (msg[0].location) {
                                        user.find('.plAddress').html('<i class="icon-location">' + msg[0].location);
                                    } else {
                                        user.find('.plAddress').html('');
                                    }
                                    if (userTitle[0] !== undefined) {
                                        user.find('.usTitle').html(userTitle[0].value);
                                    } else {
                                        user.find('.usTitle').html('');
                                    }
                                    user.find('.plName').html(msg[0].first_name + ' ' + msg[0].last_name);
                                    user.find('.plName').attr('href','/users/profile/'+msg[0].id+'?profileTab=open');
                                    user.find('.peopleListL span').css('background-image', 'url(' + imagesPath + msg[0].image + ')');
                                    user.find('.skip').attr('id', msg[0].id);
                                    user.find('.connect').attr('id', msg[0].id);
                                    user.appendTo('#notConnectedUsers');
                                    notConnectedUsers();
                                }
                            }

                        }).fail(function (msg) {
                            console.log(msg.responseText);
                        });
                        $(this).parent().parent().parent().remove();
                    });
                    $('.skip').on('click', function () {

                        var imagesPath = '<?php echo \Yii::getAlias('@web') . '/images/users_images/'; ?>';
                        console.log(imagesPath);

                        $.ajax({
                            method: "POST",
                            url: "/users/get-not-connected-users",
                            data: {allIds: ids, id: $(this).attr('id')},
                            dataType: "json"
                        }).done(function (msg, textStatus, xhr) {
                            console.log(xhr);
                            console.log(textStatus);
                            console.log(msg);
                            var userTitle = msg.users_titles;
                            var msg = msg.users;
                            for (var i = 0; i < Object.keys(msg).length; i++) {
                                if (msg[0] !== undefined) {
                                    var user = $('.notConnectedUser:first').clone();
                                    if (msg[0].location) {
                                        user.find('.plAddress').html('<i class="icon-location">' + msg[0].location);
                                    } else {
                                        user.find('.plAddress').html('');
                                    }
                                    if (userTitle[0] !== undefined) {
                                        user.find('.usTitle').html(userTitle[0].value);
                                    } else {
                                        user.find('.usTitle').html('');
                                    }
                                    user.find('.plName').html(msg[0].first_name + ' ' + msg[0].last_name);
                                    user.find('.plName').attr('href','/users/profile/'+msg[0].id+'?profileTab=open');
                                    user.find('.peopleListL span').css('background-image', 'url(' + imagesPath + msg[0].image + ')');
                                    user.find('.skip').attr('id', msg[0].id);
                                    user.find('.connect').attr('id', msg[0].id);
                                    user.appendTo('#notConnectedUsers');
                                    notConnectedUsers();
                                }
                            }

                        }).fail(function (msg) {
                            console.log(msg.responseText);
                        });
                        $(this).parent().remove();
                    });

                }

                $(document).ready(function () {
                    notConnectedUsers();
                });
            </script>
        <?php } ?>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
