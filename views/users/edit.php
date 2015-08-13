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
    <div style="display: inline-block;width: 500px;float: left">
        <?php
        $form = ActiveForm::begin([
                    'id' => 'edit-form',
                    'options' => ['class' => 'form-horizontal']
        ]);
        ?>

        <?= $form->field($user, 'first_name') ?>
        <?= $form->field($user, 'last_name') ?>
        <?= $form->field($user, 'username') ?>
        <?= $form->field($user, 'email') ?>
        <?= $form->field($user, 'password')->passwordInput(['value' => ''])->label('New password') ?>
        <?= $form->field($user, 'confirm_password')->passwordInput() ?>





        <?php ActiveForm::end(); ?>
    </div>

    <hr style="clear: both">
    <hr>
    <hr>
    <div style="margin-bottom: 150px">
        <h2>Optional information</h2>
        <?php
        $custom_form = ActiveForm::begin([
                    'id' => 'custom-form',
                    'options' => ['class' => 'form-horizontal']
        ]);
        ?>
        <?php foreach ($sections as $sectionName => $section) { ?>
            <h3><?= $sectionName ?></h3>
            <?php foreach ($section as $subSectionName => $subSection) { ?>
                <h4><?= $subSectionName ?></h4>
                <?php if ($subSection['0']['subMultiple'] === '1') { ?>
                    <?php
                    DynamicFormWidget::begin([
                        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                        'widgetBody' => '.formSecRep', // required: css class selector
                        'widgetItem' => '.item', // required: css class
                        'limit' => 100, // the maximum times, an element can be cloned (default 999)
                        'min' => 1, // 0 or 1 (default 1)
                        'insertButton' => '.add-item', // css class
                        'deleteButton' => '.remove-item', // css class
                        'element_n' => 0, // css class
                        'model' => $model_forms[0],
                        'formId' => 'sub-section-form',
                        'formFields' => [
                            '5',
                            '6'
                        ],
                    ]);
                    ?>
                    <div class="formSecRep">
                        <?php foreach ($model_forms as $i => $modelAddress): ?>
                            <div class='item'>
                                <?php foreach ($subSection as $key => $form) { ?>
                                    <?php if ($key === 0) { ?>
                                        <?php continue; ?>
                                    <?php } ?>
                                
                                    <input name="<?php echo $form['id'] ?>" type="<?php echo $form['formType'] ?>" />

                                <?php } ?>
                            </div>
                            <div class="text-left optionBtn">
                                <a id="add_option_link" class="add-item btn btn-primary btn-xs"><span class="glyphicon glyphicon-plus"></span> Add option</a>
                            </div> 
                        <?php endforeach; ?>
                    </div>
                    <?php DynamicFormWidget::end(); ?>
                <?php } else { ?>

                <?php } ?>

            <?php } ?>

        <?php } ?>




        <?php ActiveForm::end(); ?>
    </div>



</div>

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

