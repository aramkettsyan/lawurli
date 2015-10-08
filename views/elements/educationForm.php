<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $this->params['educationModel'] app\models\Education */
/* @var $form ActiveForm */
?>


<div id="education-popup" class="popupWrap popupSmall mfp-hide">
    <div class="popupTitle">
        <h5>Add CLEs</h5>
        <button class="mfp-close"></button>
    </div>
    <div class="popupCont">
        <div class="users-education">

            <?php
            $form = ActiveForm::begin(['options' => [
                            'enctype' => 'multipart/form-data'
                        ],
                        'method' => 'post',
                        'action' => ['users/profile']
            ]);
            ?>

            <?= $form->field($this->params['educationModel'], 'organization') ?>
            <?= $form->field($this->params['educationModel'], 'number_of_units') ?>
            <?= $form->field($this->params['educationModel'], 'date') ?>
            <?= $form->field($this->params['educationModel'], 'ethics')->radioList(['1' => 'y', '0' => 'n']) ?>
            <?= $form->field($this->params['educationModel'], 'certificate')->fileInput() ?>

            <div class="form-group">
                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>

        </div><!-- users-education -->
    </div>
</div>




<script>
    $(function () {
        $("#educationform-date").datepicker({dateFormat: 'yy-mm-dd'});
    });
</script>



