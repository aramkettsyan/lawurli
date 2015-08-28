<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Users */
/* @var $form ActiveForm */
?>
<?php
$this->title = 'Home'
?>
<div class="homeTop <?= !Yii::$app->user->isGuest ? 'homeTopUser' : '' ?> clearAfter">
    <div class="container">
        <div class="homeTopText">
            <h4 class="cd-headline zoom">
                <span class="cd-words-wrapper">
                    <b class="is-visible">Lawurli.</b>              
                </span><br>
                <span>Simple Solutions </span><br>
                <span>To Complex Issues.</span><br>
                <?php if (!Yii::$app->user->isGuest) { ?>
                    <div class="homeSearch">
                        <form method="GET" action="<?= \yii\helpers\Url::to(['users/search']) ?>">
                            <input type="text" name="query" placeholder="Search for a colleague...">
                            <button type="submit"><i class="icon-search"></i></button>
                        </form>
                    </div>
                <?php } ?>
            </h4>
        </div>
        <?php if (Yii::$app->user->isGuest) { ?>
            <div class="homeSignInUp tabs">
                <ul>
                    <li><a href="#signupTab">Signup</a></li>
                    <li><a href="#loginTab">Login</a></li>
                </ul>
                <div>
                    <div id="signupTab">
                        <?php
                        echo \Yii::$app->getSession()->getFlash('registrationSuccess');
                        echo \Yii::$app->getSession()->getFlash('registrationWarning');
                        ?>
                        <?php
                        $f = ActiveForm::begin([
                                    'id' => 'registration-form',
                                    'action' => \yii\helpers\Url::to(['users/index'])
                        ]);
                        ?>


                        <?=
                        $f->field($registrationModel, 'first_name', [
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
                        $f->field($registrationModel, 'last_name', [
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
                        $f->field($registrationModel, 'email', [
                            'template' => "{input} <i class='icon-email-streamline'></i>
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
                            'options' => [
                                'class' => 'formRow frIconLeft'
                    ]])->textInput(['class' => 'formControl', 'placeholder' => 'Email'])->label(false);
                        ?>
                        <!-- disables autocomplete --><input type="text" style="display:none">
                        <?=
                        $f->field($registrationModel, 'password', [
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
                        $f->field($registrationModel, 'confirm_password', [
                            'template' => "{input} <i class='icon-lock-streamline'></i>
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
                            'options' => [
                                'class' => 'formRow frIconLeft'
                    ]])->passwordInput(['class' => 'formControl', 'placeholder' => 'Retype password'])->label(false);
                        ?>
                        <p>By clicking Register, you agree to our Terms and Conditions.</p>

                        <?= Html::submitButton('Register', ['class' => 'btn defBtn']) ?>


                        <?php ActiveForm::end(); ?>
                    </div>
                    <div id="loginTab">
                        <?php
                        echo \Yii::$app->getSession()->getFlash('success');
                        echo \Yii::$app->getSession()->getFlash('warning');
                        ?>

                        <?php
                        $form = ActiveForm::begin([
                                    'id' => 'login-form',
                                    'action' => \yii\helpers\Url::to(['users/index'])
                        ]);
                        ?>



                        <?=
                        $form->field($model, 'email', [
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
                        $form->field($model, 'password', [
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
                            $form->field($model, 'rememberMe', ['options' => [
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
            </div>
        <?php } ?>
    </div>
</div>
<section class="homeSection">
    <div class="container">
        <div class="homeFeatures">
            <!-- <p class="alignCenter mb30">Autem qui consequatur facere impedit excepturi tempora hic, sint commodi sed nostrum corporis sapiente.</p> -->
            <ul class="clearAfter">
                <li>
                    <i class="icon-portfolio"></i>
                    <p>Similique recusandae corporis, in numquam doloribus itaque eveniet inventore dicta aut.</p>
                </li>
                <li>
                    <i class="icon-anchor"></i>
                    <p>Iusto quaerat, hic doloribus vel. Voluptatibus harum dolore sunt autem vero nesciunt.</p>
                </li>
                <li>
                    <i class="icon-light-bulb"></i>
                    <p>Dicta officiis, vero fugit dolore repellendus, placeat at. Eos perferendis voluptatem voluptatibus.</p>
                </li>
            </ul>
        </div>
    </div>
</section>


<script type="text/javascript">
    $(document).ready(function () {

        $('#signupTab input').on('keyup', function () {
            $(this).parent().find('.help-block').html('');
            $(this).parent().find('.inputError').hide();
        });
        $('#loginTab input').on('keyup', function () {
            $(this).parent().find('.help-block').html('');
            $(this).parent().find('.inputError').hide();
        });

        $('.inputError').each(function () {
            if ($(this).find('.help-block').html().length > 0) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        $('.inputError').bind("DOMSubtreeModified", function () {
            if ($(this).find('.help-block').html().length > 0) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        $('.checkbox').on('change', function () {
            $(this).find('.inputError').hide();
        });


        var showLogin = <?= Yii::$app->getSession()->readSession('showLogin') ? 'true' : 'false' ?>;
<?php Yii::$app->getSession()->destroySession('showLogin'); ?>
        if (showLogin) {
            $('a[href="#loginTab"]').trigger('click');
        }


        var newPassword = <?= Yii::$app->getSession()->readSession('newPassword') ? 'true' : 'false' ?>;
<?php Yii::$app->getSession()->destroySession('newPassword'); ?>
        if (newPassword) {
            $.magnificPopup.open({
                items: {src: '#forgpass-popup-2'}, type: 'inline'
            }, 0);
        }
        var resetPassword = <?= Yii::$app->getSession()->readSession('resetPassword') ? 'true' : 'false' ?>;
<?php Yii::$app->getSession()->destroySession('resetPassword'); ?>
        if (resetPassword) {
            $.magnificPopup.open({
                items: {src: '#forgpass-popup'}, type: 'inline'
            }, 0);
        }
    });
</script>

