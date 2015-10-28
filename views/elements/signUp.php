<?php

use yii\helpers\Html;
use app\assets\UserAsset;
use yii\widgets\ActiveForm;
?>

<div id="signup-popup" class="popupWrap popupSmall mfp-hide">
    <div class="popupTitle">
        <h5>Sign up</h5>
        <button class="mfp-close"></button>
    </div>
    <div class="popupCont">
        <?php
//        echo \Yii::$app->getSession()->getFlash('registrationSuccess');
//        echo \Yii::$app->getSession()->getFlash('registrationWarning');
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
        <p>By clicking Register, you agree to our Terms and Conditions.</p>

        <?= Html::submitButton('Register', ['class' => 'btn defBtn']) ?>


        <?php ActiveForm::end(); ?>
    </div>
</div>