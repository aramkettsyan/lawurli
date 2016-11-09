<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $this->params['educationModel'] app\models\Education */
/* @var $form ActiveForm */
?>

<div id="add-cle" class="popupWrap mfp-hide">
    <div class="popupTitle">
        <h5 id="header_text"><?php echo $this->params['educationModel']->id?'Edit':'Add' ?></h5>
        <button class="mfp-close"></button>
    </div>
    <div class="popupCont srchPopupCont">
        <?php
        $form = ActiveForm::begin(['options' => [
                        'enctype' => 'multipart/form-data'
                    ],
                    'method' => 'post',
                    'action' => ['users/profile']
        ]);
        ?>
        <?php
        $date = date('Y-m-d', strtotime($this->params['educationModel']->date));
        $this->params['educationModel']->date = $date;
        ?>
        <?= $form->field($this->params['educationModel'], 'id')->hiddenInput(['value' => $this->params['educationModel']->id])->label(false); ?>

        <?=
        $form->field($this->params['educationModel'], 'organization', [
            'template' => "{label}{input}
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
            'options' => [
                'class' => 'formRow'
    ]])->textInput(['class' => 'formControl']);
        ?>
        <?=
        $form->field($this->params['educationModel'], 'number_of_units', [
            'template' => "{label}{input}
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
            'options' => [
                'class' => 'formRow'
    ]])->textInput(['class' => 'formControl']);
        ?>
        <?=
        $form->field($this->params['educationModel'], 'date', [
            'template' => "{label}{input}
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
            'options' => [
                'class' => 'formRow'
    ]])->textInput(['class' => 'formControl datepicker']);
        ?>
        <?=
        $form->field($this->params['educationModel'], 'ethics', [
            'template' => "{label}{input}
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
            'options' => [
                'class' => 'formRow'
    ]])->textInput(['class' => 'formControl ethics']);
        ?>
        <?=
        $form->field($this->params['educationModel'], 'certificate', [
            'template' => "{label}{input}
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
            'options' => [
                'class' => 'formRow'
    ]])->fileInput(['class' => 'formControl']);
        ?>
        <div class="submitSect">
            <?= Html::submitButton('Save', ['class' => 'btn defBtn']) ?>
        </div>

        <?php ActiveForm::end(); ?>


    </div>
</div>


<script>
    $(function () {
        $(".datepicker").datepicker({dateFormat: 'yy-mm-dd'});
    });
</script>



