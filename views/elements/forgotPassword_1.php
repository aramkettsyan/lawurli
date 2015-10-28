<?php

use yii\helpers\Html;
use app\assets\UserAsset;
use yii\widgets\ActiveForm;
?>

<!-- forgot password -->
<div id="forgpass-popup" class="popupWrap popupSmall mfp-hide">


    <div class="popupTitle">
        <h5>Reset password</h5>
        <button class="mfp-close"></button>
    </div>

    <?php
    $resetPassForm = ActiveForm::begin([
                'id' => 'password-reset-form',
                'action' => \yii\helpers\Url::to(['users/' . Yii::$app->controller->action->id, 'action' => 'reset_password', 'id' => $this->params['id']]),
                'options' => ['class' => '']
    ]);
    ?>

    <div class="popupCont">
        <?=
        $resetPassForm->field($this->params['resetModel'], 'email', [
            'template' => "{input}<i class='icon-email-streamline'></i><span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
            'options' => [
                'class' => 'formRow frIconLeft'
    ]])->textInput(['class' => 'formControl', 'placeholder' => 'Email']);
        ?>
<!--        <p style="color:red">
            <?php
//            echo \Yii::$app->getSession()->getFlash('resetWarning');
            ?>
        </p>
        <p style="color:green">
            <?php
//            echo \Yii::$app->getSession()->getFlash('resetSuccess');
            ?>
        </p>-->

        <?= Html::submitButton('Send Email', ['id' => 'password-reset-form_submit', 'class' => 'btn defBtn']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>