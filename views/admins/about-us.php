<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;
/* @var $this yii\web\View */
/* @var $model app\models\AboutUs */
/* @var $form ActiveForm */
?>
<div class="admin-about-us">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'about_us')->widget(CKEditor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'basic'
    ]) ?>
    <?= $form->field($model, 'contact_us_text')->widget(CKEditor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'basic'
    ]) ?>
    <?= $form->field($model, 'contact_us_email') ?>



    <?= $form->field($model, 'contact_us_visibility')->radioList(['show'=>'show','hide'=>'hide']); ?>



    <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- admin-about-us -->
