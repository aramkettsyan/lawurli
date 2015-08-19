<?php

use yii\bootstrap\ActiveForm;
?>
<?php
$fm = ActiveForm::begin([
            'id' => 'edit-form',
            'options' => ['class' => 'form-horizontal', 'novalidate' => '']
        ]);
?>
<div class="cols cols2">
    <div>
        <?=
        $fm->field($this->params['user'], 'first_name', [
            'options' => [
                'class' => 'formRow'
    ]])->textInput(['class' => 'formControl'])->label();
        ?>
        <?=
        $fm->field($this->params['user'], 'last_name', [
            'options' => [
                'class' => 'formRow'
    ]])->textInput(['class' => 'formControl'])->label();
        ?>
        <?=
        $fm->field($this->params['user'], 'email', [
            'options' => [
                'class' => 'formRow'
    ]])->textInput(['class' => 'formControl'])->label();
        ?>
        <div class="formRow">
            <label>Phone</label>
            <input type="text" class="formControl">
        </div>
        <div class="formRow">
            <label>Address</label>
            <input type="text" class="formControl">
        </div>

    </div>



    <div>
        <?php $this->params['user']->password = ''; ?>
        <?php $this->params['user']->confirm_password = ''; ?>
        
        <?=
        $fm->field($this->params['user'], 'password', [
            'options' => [
                'class' => 'formRow'
    ]])->passwordInput(['class' => 'formControl'])->label('New password');
        ?>
        <?=
        $fm->field($this->params['user'], 'confirm_password', [
            'options' => [
                'class' => 'formRow'
    ]])->passwordInput(['class' => 'formControl'])->label('Confirm new password');
        ?>

    </div>
</div>
<div class="submitSect">
    <input type="submit" class="btn defBtn" value="Save">
</div>

<?php ActiveForm::end(); ?>