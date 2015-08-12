<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Users */
/* @var $form ActiveForm */
?>
<div class="container">

    <?php
    echo \Yii::$app->getSession()->getFlash('success');
    echo \Yii::$app->getSession()->getFlash('warning');
    ?>

    <?php
    $form = ActiveForm::begin([
                'id' => 'login-form',
                'options' => ['class' => 'form-horizontal']
    ]);
    ?>

    <?= $form->field($model, 'username') ?>
<?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'rememberMe')->checkbox() ?>

    <div class="form-group">
    <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>
<?= Html::a('Create a new account', ['users/registration']) ?>
    <br>
<?= Html::a('Reset password', ['users/reset-password']) ?>
<?php ActiveForm::end(); ?>

</div><!-- users-login -->
