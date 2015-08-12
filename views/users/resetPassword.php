<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>

<div class="container">
    <p style="color:green">
        <?php
        echo \Yii::$app->getSession()->getFlash('success');
        ?>
    </p>
    <p style="color:red">
        <?php
        echo \Yii::$app->getSession()->getFlash('warning');
        ?>
    </p>
    <?php
    $form = ActiveForm::begin([
                'id' => 'password-reset-form',
                'options' => ['class' => '']
    ]);
    ?>
    <h1>Reset password</h1>
    <?php echo $form->field($model, 'email')->textInput(); ?>

    <div class="itemSecBot clearfix">
        <?= Html::submitButton('Reset password', ['id' => 'password-reset-form_submit', 'class' => 'btn btn-default pull-left']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>