<?php

use yii\helpers\Html;
use app\assets\UserAsset;
use yii\widgets\ActiveForm;
?>

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
