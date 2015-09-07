<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = 'Contact';

?>
<div class="titleWithBg aboutTitle">
    <h3>About Us</h3>
</div>
<div class="container mainContainer">
    <div class="aboutCont textFromEditor">
        <p><?=$aboutUsModel['about_us']?></p>
    </div>
    <div class="contactWrapp">
        <div class="pageTitle">
            <h3>Contact us</h3>
        </div>
        <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

        <div class="alert alert-success">
            Thank you for contacting us. We will respond to you as soon as possible.
        </div>
        <?php endif; ?>
        <?php if($aboutUsModel->contact_us_visibility == "hide") : ?>
            <div class="contactIntro textFromEditor">
                <p><?=$aboutUsModel->contact_us_text?></p>
            </div>
        <?php else : ?>
            <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
            <div class="formRow">
                <?= $form->field($model, 'name')->textInput(['class'=>'formControl']) ?>
            </div>
            <div class="formRow">
                <?= $form->field($model, 'email' )->textInput(['class'=>'formControl']) ?>
            </div>
            <div class="formRow">
                <?= $form->field($model, 'subject')->textInput(['class'=>'formControl']) ?>
            </div>
            <div class="formRow">
                <?= $form->field($model, 'body')->textArea(['rows' => 8,'class'=>'formControl']) ?>
            </div><div class="formRow">
                <?= $form->field($model, 'reCaptcha')->widget(
                    \himiklab\yii2\recaptcha\ReCaptcha::className(),
                    ['siteKey' => '6Lf3WQwTAAAAAGhg2foSOsAGOH2SkLYRelMiGpt6']
                ) ?>
            </div>
            <div class="submitSect">
                <button type="submit" class="btn defBtn"><i class="icon-paper-plane"></i>Send</button>
            </div>
            <?php ActiveForm::end(); ?>
        <?php endif; ?>
    </div>
</div>