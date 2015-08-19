<?php

use dosamigos\fileupload\FileUpload;
use yii\bootstrap\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
?>
<div class="container">
    <div id="profile_image"  style="display: inline-block;width: 300px;float:left">
        <?php $filename = \Yii::getAlias('@webroot') . '/images/users_images/' . $user->image; ?>
        <?php if (is_file($filename)) { ?>
            <img src="<?php echo \Yii::getAlias('@web') . '/images/users_images/' . $user->image; ?>"  style="height: 170px;width: 150px" alt="User image" >
        <?php } ?>
        <div id="uploadImage" class="qq-upload-button">Upload Image</div>
        <p style="color:green;display: none" id="imageUploadSuccess">Image uploaded successfully!</p>
        <p style="color:red;display: none" id="imageUploadError"></p>
    </div>
    <h4 style="color:green"><?= Yii::$app->getSession()->readSession('updateSuccess') ?></h4>
    <h4 style="color:red"><?= Yii::$app->getSession()->readSession('updateError') ?></h4>
    <?php Yii::$app->getSession()->destroySession('updateSuccess'); ?>
    <?php Yii::$app->getSession()->destroySession('updateError'); ?>
    <?php
    $fm = ActiveForm::begin([
                'id' => 'edit-form',
                'options' => ['class' => 'form-horizontal','novalidate'=>'']
    ]);
    ?>
    <div style="display: inline-block;width: 500px;float: left">

        <?= $fm->field($user, 'id')->hiddenInput()->label(false) ?>
        <?= $fm->field($user, 'first_name') ?>
        <?= $fm->field($user, 'last_name') ?>
        <?= $fm->field($user, 'email') ?>
        <?= $fm->field($user, 'password')->passwordInput(['value' => ''])->label('New password') ?>
        <?= $fm->field($user, 'confirm_password')->passwordInput(['value' => '']) ?>





    </div>

    <hr style="clear: both">
    <hr>
    <hr>
    <div id="sections" style="margin-bottom: 150px">
        <h2>Optional information</h2>
        <?php foreach ($sections as $sectionName => $section) { ?>
            <h3><?= $sectionName ?></h3>
            <?php foreach ($section as $subSectionName => $subSection) { ?>
                <div class="sub_section">
                    <h4><?= $subSectionName ?></h4>
                    <?php $subSectionId = $subSection['0']['subId']; ?>
                    <?php if (isset($user_forms[$subSectionId])) { ?>
                        <?php $sub_sections_count = count($user_forms[$subSectionId]); ?>
                    <?php } else { ?>
                        <?php $sub_sections_count = 1 ?>
                    <?php } ?>
                    <?php $i = 0; ?>
                    <?php for ($u = 0; $u < $sub_sections_count; $u++) { ?>
                        <div class="formSecRep">
                            <div class="deleteForm" style ="display:none;color:red;float:right;cursor:pointer" >Delete</div>
                            <?php foreach ($subSection as $key => $form) { ?>
                                <?php if ($key === 0) { ?>
                                    <?php continue; ?>
                                <?php } ?>
                                <?php $value = ''; ?>
                                <?php if (isset($user_forms[$subSectionId][$u][$form['formId']])) { ?>
                                    <?php $value = $user_forms[$subSectionId][$u][$form['formId']] ?>
                                <?php } ?>
                                <div class = 'item'>
                                    <?php if ($form['formLabel'] !== null) { ?>
                                        <label><?php echo $form['formLabel'] ?></label>
                                        <br>
                                    <?php } ?>
                                    <?php if ($form['formType'] === 'input') { ?>
                                        <?php $type = $form['formNumeric'] == 0 ? 'text' : 'number' ?>
                                        <input class='textInput' form-id="<?= $form['formId'] ?>" index="<?= $i ?>" value="<?= $value ?>" name="Users[custom_fields][<?= $form['formId'] ?>][]" placeholder="<?= $form['formPlaceholder'] ?>" type="<?= $type ?>" />
                                    <?php } ?>
                                    <?php if ($form['formType'] === 'textarea') { ?>
                                        <textarea class='inputTextarea' form-id="<?= $form['formId'] ?>" index="<?= $i ?>" name="Users[custom_fields][<?= $form['formId'] ?>][]" placeholder="<?= $form['formPlaceholder'] ?>"><?= $value ?></textarea>
                                    <?php } ?>
                                    <?php if ($form['formType'] === 'select') { ?>
                                        <?php $options = explode(',', $form['formOptions']); ?>
                                        <select form-id="<?= $form['formId'] ?>" index="<?= $i ?>" class='inputSelect' name="Users[custom_fields][<?= $form['formId'] ?>][]">
                                            <option value=''><?= $form['formPlaceholder'] ?></option>
                                            <?php foreach ($options as $option) { ?>
                                                <option <?= ($value === $option) ? 'selected="selected"' : '' ?> value="<?= $option ?>"><?= $option ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>
                                    <?php if ($form['formType'] === 'checkbox') { ?>
                                        <?php $options = explode(',', $form['formOptions']); ?>
                                        <?php foreach ($options as $option) { ?>
                                            <?php $checked = false; ?>
                                            <?php if (isset($user_forms[$subSectionId][$u][$form['formId']])) { ?>
                                                <?php if (is_array($user_forms[$subSectionId][$u][$form['formId']])) { ?>
                                                    <?php if (in_array($option, $user_forms[$subSectionId][$u][$form['formId']])) { ?>
                                                        <?php $checked = true; ?>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <?php if ($option === $user_forms[$subSectionId][$u][$form['formId']]) { ?>
                                                        <?php $checked = true; ?>
                                                    <?php } ?>   
                                                <?php } ?>
                                            <?php } ?>
                                            <label><input form-id="<?= $form['formId'] ?>" index="<?= $i ?>" <?= $checked === true ? 'checked' : '' ?> class='inputCheckbox' name="Users[custom_fields][<?= $form['formId'] ?>][<?= $i ?>][]" value="<?= $option ?>" type="<?= $form['formType'] ?>" /><?= $option ?></label>
                                        <?php } ?>
                                    <?php } ?>
                                    <?php if ($form['formType'] === 'radio') { ?>
                                        <?php $options = explode(',', $form['formOptions']); ?>
                                        <?php foreach ($options as $option) { ?>
                                            <label><input form-id="<?= $form['formId'] ?>" index="<?= $i ?>" <?= $option === $value ? 'checked' : '' ?> class='inputRadio' name="Users[custom_fields][<?= $form['formId'] ?>][<?= $i ?>]" value="<?= $option ?>" type="<?= $form['formType'] ?>" /><?= $option ?></label>

                                        <?php } ?>
                                    <?php } ?>
                                    <p class="message"></p>
                                </div>
                                <?php $value = ''; ?>
                            <?php } ?>
                        </div>
                        <?php $i++; ?>
                    <?php } ?>

                    <?php if ($subSection['0']['subMultiple'] === '1') { ?>
                        <div class="add-item text-left optionBtn" style="display: inline-block">
                            <a id="add_option_link" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-plus"></span>Add</a>
                        </div> 
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } ?>
        <?= yii\helpers\Html::submitButton('Save') ?>   
    </div>




</div>



<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">

    function generateName(formId, index, isRadio) {
        var i = isRadio === true ? '' : '[]';
        var newName = 'Users[custom_fields]' + '[' + formId + ']' + '[' + index + ']' + i;
        return newName;
    }

    function resetDelete() {
        $('.deleteForm').on('click', function () {
            if ($(this).parent().parent().find('.formSecRep').size() == 2)
            {
                $(this).parent().parent().find('.deleteForm').hide();
            }
            if ($(this).parent().parent().find('.formSecRep').size() > 1) {
                var thisIndex = $(this).parent('.formSecRep').index();
                $(this).parent().parent().find('.formSecRep').each(function () {
                    var nextIndex = $(this).index();
                    if (thisIndex < nextIndex) {
                        var checkboxFormId = parseInt($(this).find('.inputCheckbox').attr('form-id'));
                        var checkboxIndex = parseInt($(this).find('.inputCheckbox').attr('index')) - 1;
                        var newName = generateName(checkboxFormId, checkboxIndex);
                        $(this).find('.inputCheckbox').attr('name', newName);
                        $(this).find('.inputCheckbox').attr('form-id', checkboxFormId);
                        $(this).find('.inputCheckbox').attr('index', checkboxIndex);

                        var radioFormId = parseInt($(this).find('.inputRadio').attr('form-id'));
                        var radioIndex = parseInt($(this).find('.inputRadio').attr('index')) - 1;
                        var newName = generateName(radioFormId, radioIndex, true);
                        $(this).find('.inputRadio').attr('name', newName);
                        $(this).find('.inputRadio').attr('form-id', radioFormId);
                        $(this).find('.inputRadio').attr('index', radioIndex);
                    }
                });
                $(this).parent().remove();
            }
        });
    }

    $(document).ready(function () {
        $('#edit-form').submit(function (event) {
            $('.item input[type="text"],.item input[type="number"]').each(function () {
                if ($(this).val().length > 255) {
                    $(this).css('borderColor', '#a94442');
                    $(this).parent().find('.message').html('Maximum length is 255 character!').css('color', '#a94442');
                    event.preventDefault();
                    event.stopImmediatePropagation();
                } else if ($(this).attr('type') === 'number' && parseInt($(this).val()) != $(this).val()) {
                    $(this).css('borderColor', '#a94442');
                    $(this).parent().find('.message').html('This field must contain only integer values!').css('color', '#a94442');
                    event.preventDefault();
                    event.stopImmediatePropagation();
                }
            });
        });
        $('.item input[type="text"],.item input[type="number"]').on('blur',function () {
            if ($(this).val().length > 255) {
                $(this).css('borderColor', '#a94442');
                $(this).parent().find('.message').html('Maximum length is 255 character!').css('color', '#a94442');
                event.preventDefault();
                event.stopImmediatePropagation();
            } else if ($(this).attr('type') === 'number' && parseInt($(this).val()) != $(this).val()) {
                $(this).css('borderColor', '#a94442');
                $(this).parent().find('.message').html('This field must contain only integer values!').css('color', '#a94442');
                event.preventDefault();
                event.stopImmediatePropagation();
            }
        });
        $('.sub_section').each(function () {
            var formsCount = $(this).find('.formSecRep').size();
            if (formsCount > 1) {
                $(this).find('.deleteForm').show();
            }
        });
        $('.add-item').on('click', function () {
            $(this).parent().find('.deleteForm').show();
            var parent = $(this).parent();
            var item = parent.find('.formSecRep:last').clone();
            item.find('.textInput').val('');
            item.find('.inputTextarea').val('');
            item.find('.inputCheckbox').removeAttr('checked');
            item.find('.inputRadio').removeAttr('checked');
            item.find('.inputSelect').prop('selectedIndex', 0);

            var formId = parseInt(item.find('.inputCheckbox').attr('form-id'));
            var index = parseInt(item.find('.inputCheckbox').attr('index')) + 1;
            var newCheckbox = generateName(formId, index);
            item.find('.inputCheckbox').attr('name', newCheckbox);
            item.find('.inputCheckbox').attr('form-id', formId);
            item.find('.inputCheckbox').attr('index', index);


            var formId = parseInt(item.find('.inputRadio').attr('form-id'));
            var index = parseInt(item.find('.inputRadio').attr('index')) + 1;
            var newRadio = generateName(formId, index, true);
            console.log(newRadio);
            item.find('.inputRadio').attr('name', newRadio);
            item.find('.inputRadio').attr('form-id', formId);
            item.find('.inputRadio').attr('index', index);
            item.appendTo(parent);
            $(this).appendTo(parent);
            resetDelete();
//            parent.find('.formSecRep:last input').val('');
//            parent.find('.formSecRep:last input:checked').removeAttr('checked');
        });

        $('.deleteForm').on('click', function () {
            if ($(this).parent().parent().find('.formSecRep').size() == 2)
            {
                $(this).parent().parent().find('.deleteForm').hide();
            }
            if ($(this).parent().parent().find('.formSecRep').size() > 1) {
                var thisIndex = $(this).parent('.formSecRep').index();
                $(this).parent().parent().find('.formSecRep').each(function () {
                    var nextIndex = $(this).index();
                    if (thisIndex < nextIndex) {
                        var checkboxFormId = parseInt($(this).find('.inputCheckbox').attr('form-id'));
                        var checkboxIndex = parseInt($(this).find('.inputCheckbox').attr('index')) - 1;
                        var newName = generateName(checkboxFormId, checkboxIndex);
                        $(this).find('.inputCheckbox').attr('name', newName);
                        $(this).find('.inputCheckbox').attr('form-id', checkboxFormId);
                        $(this).find('.inputCheckbox').attr('index', checkboxIndex);

                        var radioFormId = parseInt($(this).find('.inputRadio').attr('form-id'));
                        var radioIndex = parseInt($(this).find('.inputRadio').attr('index')) - 1;
                        var newName = generateName(radioFormId, radioIndex, true);
                        $(this).find('.inputRadio').attr('name', newName);
                        $(this).find('.inputRadio').attr('form-id', radioFormId);
                        $(this).find('.inputRadio').attr('index', radioIndex);
                    }
                });
                $(this).parent().remove();
            }

        });

    });
</script>
<script type="text/javascript">

    new qq.FileUploaderBasic({
        button: document.getElementById('uploadImage'),
        action: '/users/upload-image',
        multiple: false,
        sizeLimit: 5242880,
        allowedExtensions: ['png', 'jpg', 'jpeg', 'gif'],
        onComplete: function (id, fileName, responseJSON) {

            if (responseJSON.success) {
                $('#profile_image').html("<img src='/images/users_images/" + responseJSON.fileName + "' style='height: 170px;width: 150px'>");
                $('#imageUploadSuccess').show();
                $('#imageUploadError').hide();
            }
            if (responseJSON.error) {
                $('#imageUploadSuccess').hide();
                $('#imageUploadError').html(responseJSON.error);
                $('#imageUploadError').show();

            }
        }
    });
</script>

