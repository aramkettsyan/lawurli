<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>


<div class="container">
    <?php
    $form = ActiveForm::begin([
                'id' => 'password-reset-form',
                'options' => ['class' => '']
    ]);
    ?>
    <h1>Reset password</h1>
    <?php echo $form->field($model, 'password')->passwordInput()->label('New password'); ?>
    <?php echo $form->field($model, 'confirm_password')->passwordInput(); ?>

    <div class="itemSecBot clearfix">
        <?= Html::submitButton('Save', ['id' => 'password-reset-form_submit', 'class' => 'btn btn-default pull-left']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>