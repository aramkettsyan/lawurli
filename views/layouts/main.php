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
                <header>
                    <div class="container">
                        <div class="headerLogo">
                            <a href="<?= \yii\helpers\Url::to(['users/index']) ?>"><img src="<?= \Yii::getAlias('@web') . '/images/' . $this->params['logo']; ?>" alt=""></a>
                        </div>
                        <div class="headerRight">
                            <div class="headerSrch">
                                <form method="GET" action="<?= \yii\helpers\Url::to(['users/search']) ?>">
                                    <input type="text" name="query" value="<?= isset($this->params['query']) ? $this->params['query'] : '' ?>" placeholder="Search...">
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
                                            <img src="/images/user-image.png" alt="">
                                            <span class="profileImage" style="background-image: url('<?php echo \Yii::getAlias('@web') . '/images/users_images/' . $this->params['current_user']->image; ?>')"></span>
                                        </div>

                                        <span class="udUserName"><?= $this->params['current_user']->first_name ?> <?= $this->params['current_user']->last_name ?></span>
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
                            <a href="<?= \yii\helpers\Url::to(['users/index']) ?>"><img src="<?= \Yii::getAlias('@web') . '/images/' . $this->params['logo']; ?>" alt=""></a>
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
                                        <span class="profileImage" style="background-image: url('<?php echo \Yii::getAlias('@web') . '/images/users_images/' . $this->params['current_user']->image; ?>')"></span>
                                    </div>
                                    <span class="udUserName"><?= $this->params['current_user']->first_name ?> <?= $this->params['current_user']->last_name ?></span>
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

            <?php if (Yii::$app->user->isGuest) { ?>
                <!-- ###### -->
                <!-- POPUPS -->
                <!-- ###### -->

                <!-- login popup -->
                <div id="login-popup" class="popupWrap popupSmall mfp-hide">
                    <div class="popupTitle">
                        <h5>Login</h5>
                        <button class="mfp-close"></button>
                    </div>
                    <div class="popupCont">
                        <?php
                        echo \Yii::$app->getSession()->getFlash('success');
                        echo \Yii::$app->getSession()->getFlash('warning');
                        ?>
                        <?php
                        $form = ActiveForm::begin([
                                    'id' => 'login-form'
                        ]);
                        ?>



                        <?=
                        $form->field($this->params['model'], 'email', [
                            'template' => "{input} <i class='icon-email-streamline'></i>
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
                            'options' => [
                                'class' => 'formRow frIconLeft'
                    ]])->textInput(['class' => 'formControl', 'placeholder' => 'Email']);
                        ?>

                        <?=
                        $form->field($this->params['model'], 'password', [
                            'template' => "{input} <i class='icon-lock-streamline'></i>
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
                            'options' => [
                                'class' => 'formRow frIconLeft'
                    ]])->passwordInput(['class' => 'formControl', 'placeholder' => 'Password']);
                        ?>

                        <div class="remMeForgPass clearAfter">
                            <?=
                            $form->field($this->params['model'], 'rememberMe', ['options' => [
                                    'class' => 'checkbox'
                        ]])->checkbox(['label' => 'Remember me!'])
                            ?>
                            <div class="forgPass">
                                <a class="textBtn popupBtn" href="#forgpass-popup" >Forgot password?</a>
                            </div>
                        </div>
                        <?= Html::submitButton('Login', ['class' => 'btn defBtn']) ?>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>

                <!-- sign up popup -->
                <div id="signup-popup" class="popupWrap popupSmall mfp-hide">
                    <div class="popupTitle">
                        <h5>Sign up</h5>
                        <button class="mfp-close"></button>
                    </div>
                    <div class="popupCont">
                        <?php
                        echo \Yii::$app->getSession()->getFlash('registrationSuccess');
                        echo \Yii::$app->getSession()->getFlash('registrationWarning');
                        ?>
                        <?php
                        $f = ActiveForm::begin([
                                    'id' => 'registration-form'
                        ]);
                        ?>


                        <?=
                        $f->field($this->params['registrationModel'], 'first_name', [
                            'template' => "{input}<i class='icon-man-streamline-user'></i>
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
                            'options' => [
                                'class' => 'formRow frIconLeft'
                    ]])->textInput(['class' => 'formControl', 'placeholder' => 'Name'])->label(false);
                        ?>
                        <?=
                        $f->field($this->params['registrationModel'], 'last_name', [
                            'template' => "{input} <i class='icon-man-streamline-user'></i>
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
                            'options' => [
                                'class' => 'formRow frIconLeft'
                    ]])->textInput(['class' => 'formControl', 'placeholder' => 'Last name'])->label(false);
                        ?>
                        <?=
                        $f->field($this->params['registrationModel'], 'email', [
                            'template' => "{input} <i class='icon-email-streamline'></i>
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
                            'options' => [
                                'class' => 'formRow frIconLeft'
                    ]])->textInput(['class' => 'formControl', 'placeholder' => 'Email'])->label(false);
                        ?>
                        <?=
                        $f->field($this->params['registrationModel'], 'password', [
                            'template' => "{input} <i class='icon-lock-streamline'></i>
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
                            'options' => [
                                'class' => 'formRow frIconLeft'
                    ]])->passwordInput(['class' => 'formControl', 'placeholder' => 'Password'])->label(false);
                        ?>
                        <?=
                        $f->field($this->params['registrationModel'], 'confirm_password', [
                            'template' => "{input} <i class='icon-lock-streamline'></i>
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
                            'options' => [
                                'class' => 'formRow frIconLeft'
                    ]])->passwordInput(['class' => 'formControl', 'placeholder' => 'Retype password'])->label(false);
                        ?>
                        <?=
                        $f->field($this->params['registrationModel'], 'conditions', ['options' => [
                                'class' => 'checkbox'
                    ]])->checkbox(['label' => 'Terms and conditions'])
                        ?>

                        <?= Html::submitButton('Register', ['class' => 'btn defBtn']) ?>


                        <?php ActiveForm::end(); ?>
                    </div>
                </div>


                <!-- forgot password -->
                <div id="forgpass-popup" class="popupWrap popupSmall mfp-hide">


                    <div class="popupTitle">
                        <h5>Reset password</h5>
                        <button class="mfp-close"></button>
                    </div>

                    <?php
                    $resetPassForm = ActiveForm::begin([
                                'id' => 'password-reset-form',
                                'action' => \yii\helpers\Url::to(['users/'.Yii::$app->controller->action->id, 'action' => 'reset_password', 'id' => $this->params['id']]),
                                'options' => ['class' => '']
                    ]);
                    ?>

                    <div class="popupCont">
                        <?=
                        $resetPassForm->field($this->params['resetModel'], 'email', [
                            'template' => "{input}{error} <i class='icon-email-streamline'></i>",
                            'options' => [
                                'class' => 'formRow frIconLeft'
                    ]])->textInput(['class' => 'formControl', 'placeholder' => 'Email']);
                        ?>
                        <p style="color:red">
                            <?php
                            echo \Yii::$app->getSession()->getFlash('resetWarning');
                            ?>
                        </p>
                        <p style="color:green">
                            <?php
                            echo \Yii::$app->getSession()->getFlash('resetSuccess');
                            ?>
                        </p>

                        <?= Html::submitButton('Send Email', ['id' => 'password-reset-form_submit', 'class' => 'btn defBtn']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>

                <!-- forgot password 2 -->
                <div id="forgpass-popup-2" class="popupWrap popupSmall mfp-hide">
                    <div class="popupTitle">
                        <h5>Reset password</h5>
                        <button class="mfp-close"></button>
                    </div>
                    <?php
                    $resetForm = ActiveForm::begin([
                                'id' => 'password-reset-form',
                    ]);
                    ?>


                    <div class="popupCont">
                        <?=
                        $resetForm->field($this->params['user_reset'], 'password', [
                            'template' => "{input} <i class='icon-lock-streamline'></i>{error}",
                            'options' => [
                                'class' => 'formRow frIconLeft'
                    ]])->passwordInput(['class' => 'formControl', 'placeholder' => 'New password']);
                        ?>
                        <?=
                        $resetForm->field($this->params['user_reset'], 'confirm_password', [
                            'template' => "{input} <i class='icon-lock-streamline'></i>{error}",
                            'options' => [
                                'class' => 'formRow frIconLeft'
                    ]])->passwordInput(['class' => 'formControl', 'placeholder' => 'Confirm password']);
                        ?>
                        <?= Html::submitButton('Confirm', ['id' => 'password-reset-form_submit', 'class' => 'btn defBtn']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            <?php } ?>
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
                <p class="poweredBy">© Lawurli 2015 <?= (date('Y',time()) == '2015' ? '' : '- '.date('Y',time())) ?> | Created by <a href="http://st-dev.com" target="_blank">STDev</a></p>
            </div>
        </footer>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
