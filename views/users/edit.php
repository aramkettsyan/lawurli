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

        <?= $fm->field($user, 'first_name') ?>
        <?= $fm->field($user, 'last_name') ?>
        <?= $fm->field($user, 'username') ?>
        <?= $fm->field($user, 'email') ?>
        <?= $fm->field($user, 'password')->passwordInput(['value' => ''])->label('New password') ?>
        <?= $fm->field($user, 'confirm_password')->passwordInput() ?>





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
                        <?php if ($subSection['0']['subMultiple'] === '1') { ?>
                            <div class="formSecRep">
                                <?php $i = 0; ?>
                                <?php foreach ($subSection as $key => $form) { ?>
                                    <?php if ($key === 0) { ?>
                                        <?php continue; ?>
                                    <?php } ?>
                                    <div class = 'item'>
                                        <?php if ($form['formLabel'] !== null) { ?>
                                            <label><?php echo $form['formLabel'] ?></label>
                                            <br>
                                        <?php } ?>
                                        <?php if ($form['formType'] === 'input') { ?>
                                            <input class='textInput' name="Users[custom][<?= $form['formId'] ?>][]" placeholder="<?= $form['formPlaceholder'] ?>" type="<?= $form['formType'] ?>" />
                                        <?php } ?>
                                        <?php if ($form['formType'] === 'textarea') { ?>
                                            <textarea class='inputTextarea' name="Users[custom][<?= $form['formId'] ?>][]" placeholder="<?= $form['formPlaceholder'] ?>"></textarea>
                                        <?php } ?>
                                        <?php if ($form['formType'] === 'select') { ?>
                                            <?php $options = explode(',', $form['formOptions']); ?>
                                            <select class='inputSelect' name="Users[custom][<?= $form['formId'] ?>][]">
                                                <option value=''><?= $form['formPlaceholder'] ?></option>
                                                <?php foreach ($options as $option) { ?>
                                                    <option value="<?= $option ?>"><?= $option ?></option>
                                                <?php } ?>
                                            </select>

                                            <?php $i++; ?>
                                        <?php } ?>
                                        <?php if ($form['formType'] === 'checkbox') { ?>
                                            <?php $options = explode(',', $form['formOptions']); ?>
                                            <?php foreach ($options as $option) { ?>
                                                <label><input  class='inputCheckbox' name="Users[custom][<?= $form['formId'] ?>][<?= $i ?>][]" value="<?= $option ?>" type="<?= $form['formType'] ?>" /><?= $option ?></label>

                                            <?php } ?>
                                        <?php } ?>
                                        <?php if ($form['formType'] === 'radio') { ?>
                                            <?php $options = explode(',', $form['formOptions']); ?>
                                            <?php foreach ($options as $option) { ?>
                                                <label><input class='inputRadio' name="Users[custom][<?= $form['formId'] ?>][]" value="<?= $option ?>" type="<?= $form['formType'] ?>" /><?= $option ?></label>

                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="add-item text-left optionBtn">
                                <a id="add_option_link" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-plus"></span>Add</a>
                            </div> 
                        <?php } else { ?>

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

    $(document).ready(function () {
        $('.add-item').on('click', function () {
            var parent = $(this).parent();
            var item = parent.find('.formSecRep:first').clone();
            item.find('.textInput').val('');
            item.find('.inputTextarea').val('');
            item.find('.inputCheckbox').removeAttr('checked');
            item.find('.inputRadio').removeAttr('checked');
            item.find('.inputSelect').prop('selectedIndex', 0);
            var name = item.find('.inputSelect').prop('name');
//            for (var j = 0; j < 2; j++) {
//                name.indexOf('[');
//            }
//            var index = name
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

