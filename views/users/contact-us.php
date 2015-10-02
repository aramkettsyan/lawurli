<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = 'Contact';
?>
<div id="about_us" class="titleWithBg aboutTitle">
    <h3>About Us</h3>
</div>
<div class="container mainContainer">
    <div class="aboutCont textFromEditor">
        <p><?= $aboutUsModel['about_us'] ?></p>
    </div>
    <div class="contactWrapp">
        <div class="pageTitle" id="contact_us">
            <h3>Contact us</h3>
        </div>
        <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

            <div class="alert alert-success">
                Thank you for contacting us. We will respond to you as soon as possible.
            </div>
        <?php endif; ?>
        <?php if ($aboutUsModel->contact_us_visibility == "hide") : ?>
            <div class="contactIntro textFromEditor">
                <p><?= $aboutUsModel->contact_us_text ?></p>
            </div>
        <?php else : ?>
            <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
            <div class="formRow">
                <?= $form->field($model, 'name')->textInput(['class' => 'formControl']) ?>
            </div>
            <div class="formRow">
                <?= $form->field($model, 'email')->textInput(['class' => 'formControl']) ?>
            </div>
            <div class="formRow">
                <?= $form->field($model, 'subject')->textInput(['class' => 'formControl']) ?>
            </div>
            <div class="formRow">
                <?= $form->field($model, 'body')->textArea(['rows' => 8, 'class' => 'formControl']) ?>
            </div><div class="formRow">
                <?=
                $form->field($model, 'reCaptcha')->widget(
                        \himiklab\yii2\recaptcha\ReCaptcha::className(), ['siteKey' => '6Lc2-g0TAAAAAOtCIS9NttY8eVDx9FW7oju3BnYH']
                )
                ?>
            </div>
            <div class="submitSect">
                <button type="submit" class="btn defBtn"><i class="icon-paper-plane"></i>Send</button>
            </div>
            <?php ActiveForm::end(); ?>
        <?php endif; ?>
    </div>
</div>


<script type="text/javascript">

    $(document).ready(function () { 
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
        
        var showRegistration = <?php echo Yii::$app->getSession()->readSession('showRegistration') ? 'true' : 'false' ?>;
<?php Yii::$app->getSession()->destroySession('showRegistration'); ?>
        if (showRegistration) {
            $.magnificPopup.open({
                items: {src: '#signup-popup'}, type: 'inline'
            }, 0);
        }

        var showLogin = <?php echo Yii::$app->getSession()->readSession('showLogin') ? 'true' : 'false' ?>;
<?php Yii::$app->getSession()->destroySession('showLogin'); ?>
        if (showLogin) {
            $.magnificPopup.open({
                items: {src: '#login-popup'}, type: 'inline'
            }, 0);
        }


        var newPassword = <?php echo Yii::$app->getSession()->readSession('newPassword') ? 'true' : 'false'                            ?>;
<?php Yii::$app->getSession()->destroySession('newPassword'); ?>
        if (newPassword) {
            $.magnificPopup.open({
                items: {src: '#forgpass-popup-2'}, type: 'inline'
            }, 0);
        }
        var resetPassword = <?php echo Yii::$app->getSession()->readSession('resetPassword') ? 'true' : 'false'                            ?>;
<?php Yii::$app->getSession()->destroySession('resetPassword'); ?>
        if (resetPassword) {
            $.magnificPopup.open({
                items: {src: '#forgpass-popup'}, type: 'inline'
            }, 0);
        }
    });
</script>