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
        <?=
        $fm->field($this->params['user'], 'phone', [
            'options' => [
                'class' => 'formRow'
    ]])->textInput(['class' => 'formControl'])->label();
        ?>
        <?=
        $fm->field($this->params['user'], 'location', [
            'options' => [
                'class' => 'formRow',
    ]])->textInput(['class' => 'formControl',
            'id' => 'autocomplete'])->label();
        ?>
        <?=
        $fm->field($this->params['user'], 'latlng', [
            'options' => [
                'class' => 'formRow',
    ]])->hiddenInput(['class' => 'formControl', 'id' => 'latlng'])->label(false);
        ?>

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


<script>
    var autocomplete;

    function initialize() {
        autocomplete = new google.maps.places.Autocomplete(
                /** @type {HTMLInputElement} */(document.getElementById('autocomplete')),
                {types: ['geocode']});
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
            document.getElementById('autocomplete').value = place.name;
            document.getElementById('latlng').value = place.geometry.location.lat() + ',' + place.geometry.location.lng();
//            alert("This function is working!");
            alert(document.getElementById('latlng').value);
        });
    }
    initialize();
</script>