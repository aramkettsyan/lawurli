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
    <?php
    $fm = ActiveForm::begin([
                'id' => 'edit-form',
                'options' => ['class' => 'form-horizontal']
    ]);
    ?>
    <div style="display: inline-block;width: 500px;float: left">

        <?= $fm->field($user, 'id')->hiddenInput()->label(false) ?>
        <?= $fm->field($user, 'first_name') ?>
        <?= $fm->field($user, 'last_name') ?>
        <?= $fm->field($user, 'username') ?>
        <?= $fm->field($user, 'email') ?>
        <?= $fm->field($user, 'password')->passwordInput(['value' => ''])->label('New password') ?>
        <?= $fm->field($user, 'confirm_password')->passwordInput(['value' => '']) ?>





    </div>

    <hr style="clear: both">
    <hr>
    <hr>
    <div id="sections" style="margin-bottom: 150px">
        <h2>Optional information</h2>
        <form method="POST">
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
                                <?php foreach ($subSection as $key => $form) { ?>
                                    <?php if ($key === 0) { ?>
                                        <?php continue; ?>
                                    <?php } ?>

                                    <?php if (isset($user_forms[$subSectionId][$u][$form['formId']])) { ?>
                                        <?php $value = $user_forms[$subSectionId][$u][$form['formId']] ?>
                                    <?php } ?>
                                    <div class = 'item'>
                                        <?php if ($form['formLabel'] !== null) { ?>
                                            <label><?php echo $form['formLabel'] ?></label>
                                            <br>
                                        <?php } ?>
                                        <?php if ($form['formType'] === 'input') { ?>
                                            <input class='textInput' value="<?= $value ?>" name="Users[custom_fields][<?= $form['formId'] ?>][]" placeholder="<?= $form['formPlaceholder'] ?>" type="<?= $form['formType'] ?>" />
                                        <?php } ?>
                                        <?php if ($form['formType'] === 'textarea') { ?>
                                            <textarea class='inputTextarea' value="<?= $value ?>" name="Users[custom_fields][<?= $form['formId'] ?>][]" placeholder="<?= $form['formPlaceholder'] ?>"></textarea>
                                        <?php } ?>
                                        <?php if ($form['formType'] === 'select') { ?>
                                            <?php $options = explode(',', $form['formOptions']); ?>
                                            <select class='inputSelect' name="Users[custom_fields][<?= $form['formId'] ?>][]">
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
                                                <label><input <?= $checked === true ? 'checked' : '' ?> class='inputCheckbox' name="Users[custom_fields][<?= $form['formId'] ?>][<?= $i ?>][]" value="<?= $option ?>" type="<?= $form['formType'] ?>" /><?= $option ?></label>
                                            <?php } ?>
                                        <?php } ?>
                                        <?php if ($form['formType'] === 'radio') { ?>
                                            <?php $options = explode(',', $form['formOptions']); ?>
                                            <?php foreach ($options as $option) { ?>
                                                <label><input <?= $option === $value ? 'checked' : '' ?> class='inputRadio' name="Users[custom_fields][<?= $form['formId'] ?>][<?= $i ?>]" value="<?= $option ?>" type="<?= $form['formType'] ?>" /><?= $option ?></label>

                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                    <?php $value = ''; ?>
                                <?php } ?>
                            </div>
                            <?php $i++; ?>
                        <?php } ?>

                        <?php if ($subSection['0']['subMultiple'] === '1') { ?>
                            <div class="add-item text-left optionBtn">
                                <a id="add_option_link" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-plus"></span>Add</a>
                            </div> 
                        <?php } ?>
                    </div>
                <?php } ?>
            <?php } ?>
            <input type="submit" value="Save" name="submit">
        </form>
    </div>




</div>



<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">

    function increaseIndex(name, nameCopy) {
        for (var j = 0; j < 2; j++) {
            var index = name.indexOf('[');
            var numb = index;
            var c = name.charAt(numb);
            name = name.substr(0, numb) + name.substr(numb + 1);
        }

        for (var k = 0; k < 2; k++) {
            var index = name.indexOf(']');
            var numb = index;
            var c = name.charAt(numb);
            name = name.substr(0, numb) + name.substr(numb + 1);
        }
        var index1 = name.indexOf('[');
        var index2 = name.indexOf(']');
        var n = index2 - index1;
        var oldIndex = name.substr(index1 + 1, n - 1);
        var newIndex = parseInt(oldIndex) + 1;
        var last = nameCopy.lastIndexOf(oldIndex);
        var newName = nameCopy.substr(0, last) + newIndex + nameCopy.substr(last + oldIndex.length, nameCopy.length);
        return newName;
    }

    $(document).ready(function () {
        $('.add-item').on('click', function () {
            var parent = $(this).parent();
            var item = parent.find('.formSecRep:last').clone();
            item.find('.textInput').val('');
            item.find('.inputTextarea').val('');
            item.find('.inputCheckbox').removeAttr('checked');
            item.find('.inputRadio').removeAttr('checked');
            item.find('.inputSelect').prop('selectedIndex', 0);
            var checkbox = item.find('.inputCheckbox').attr('name');
            var checkboxCopy = checkbox;
            if (checkbox !== undefined) {
                var newCheckbox = increaseIndex(checkbox, checkboxCopy);
                item.find('.inputCheckbox').attr('name', newCheckbox);
            }
            var radio = item.find('.inputRadio').attr('name');
            var radioCopy = radio;
            if (radio !== undefined) {
                var newRadio = increaseIndex(radio, radioCopy);
                item.find('.inputRadio').attr('name', newRadio);
            }
            item.appendTo(parent);
            $(this).appendTo(parent);
//            parent.find('.formSecRep:last input').val('');
//            parent.find('.formSecRep:last input:checked').removeAttr('checked');
            console.log(parent.html());
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

