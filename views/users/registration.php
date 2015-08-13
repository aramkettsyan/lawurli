<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Users */
/* @var $form ActiveForm */
?>
<div class="container">

    <?php $form = ActiveForm::begin([
        'id' => 'registration-form',
        'options' => ['class' => 'form-horizontal']
    ]); ?>

        <?= $form->field($model, 'username') ?>
        <?= $form->field($model, 'first_name') ?>
        <?= $form->field($model, 'last_name') ?>
        <?= $form->field($model, 'email') ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'confirm_password')->passwordInput() ?>
        <?= $form->field($model, 'conditions')->checkbox(['label'=>'I agree to <a href="/users/terms-and-conditions" >terms and conditions</a>']) ?>
    
        <div class="form-group">
            <?= Html::submitButton('Sign Up', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Login',['users/login']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- users-login -->
