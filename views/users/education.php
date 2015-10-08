<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Education */
/* @var $form ActiveForm */
?>
<div class="users-education">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'organization') ?>
        <?= $form->field($model, 'number_of_units') ?>
        <?= $form->field($model, 'date') ?>
        <?= $form->field($model, 'ethics') ?>
        <?= $form->field($model, 'certificate') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- users-education -->
