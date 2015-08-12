<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Users */
/* @var $form ActiveForm */
?>
<div class="container">
    <div class="adminCont">
        <div style="margin-top:50px">

            <?php
            $form = ActiveForm::begin([
                        'id' => 'admin-login-form'
            ]);
            ?>

<?= $form->field($model, 'username') ?>
                <?= $form->field($model, 'password')->passwordInput() ?>

            <div>
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
            </div>
<?php ActiveForm::end(); ?>

        </div>
    </div>
</div>