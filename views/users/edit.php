<?php 

    $this->title = 'Edit profile' 

?>
<div class="container mainContainer">
    <div class="profileL">
        <div class="userImage">
            <?php $filename = \Yii::getAlias('@webroot') . '/images/users_images/' . $user->image; ?>
            <?php if (is_file($filename)) { ?>
                <img src="/images/user-1.png">
                <span class="profileImage" style="background-image: url('<?php echo \Yii::getAlias('@web') . '/images/users_images/' . $user->image; ?>')"></span>
                <div class="imgEditBtns">
                    <button id="uploadImage"><i class="icon-pencil-square"></i></button>
                </div>
            </div>

        <!--<img src="<?php echo \Yii::getAlias('@web') . '/images/users_images/' . $user->image; ?>"  alt="User image" >-->
        <?php } ?>
        <p style="color:green;display: none" id="imageUploadSuccess">Image uploaded successfully!</p>
        <p style="color:red;display: none" id="imageUploadError"></p>

        <div class="userDetails">
            <h3 class="userName"><?= $user->first_name ?> <?= $user->last_name ?></h3>
            <!--            <div class="proffInfo">
                            <span class="userProff">Bandit</span> 
                        </div>-->
            <ul class="listWithIcons">
                <li>
                    <i class="icon-location"></i>
                    <p><?= $user->location ? $user->location : 'Location undefined' ?></p>
                </li>
                <li>
                    <i class="icon-smart-phone-2"></i>
                    <p><?= $user->phone ?></p>
                </li>
                <li>
                    <i class="icon-letter-mail-1"></i>
                    <p><?= $user->email ?></p>
                </li>
            </ul>
        </div>
    </div>
    <div class="profileR">

        <?php $action = Yii::$app->getRequest()->getQueryParam('action'); ?>

        <?php if (empty($action)) { ?>
            <?php $action = 'general' ?>
        <?php } ?>
        <?php if ($action === 'detailed') { ?>
            <?php $layoutName = 'experience_edit.php'; ?>
        <?php } else { ?>
            <?php $action = 'general' ?>
            <?php $layoutName = 'general_edit.php' ?>
        <?php } ?>

        <div class="profileTabs">
            <ul class="clearAfter">
                <li class="<?= $action === 'general' ? 'active' : '' ?>"><a href="<?= \yii\helpers\Url::to(['users/edit', 'action' => 'general']) ?>"><i class="icon-user"></i>General Information</a></li>
                <li class="<?= $action === 'detailed' ? 'active' : '' ?>"><a href="<?= \yii\helpers\Url::to(['users/edit', 'action' => 'detailed']) ?>"><i class="icon-card-user-2"></i>Detailed Information</a></li>
            </ul>
        </div>



        <div class="tabsContent">
            <?php if ($action === 'detailed') { ?>
                <?php Yii::$app->view->params['sections'] = $sections; ?>
                <?php Yii::$app->view->params['user_forms'] = $user_forms; ?>
            <?php } ?>
            <?php Yii::$app->view->params['user'] = $user; ?>
            <?php $this->beginContent('@app/views/layouts/' . $layoutName); ?>

            <?php $this->endContent(); ?>
        </div>

    </div>
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
                $(this).parent().parent().find('.deleteForm').addClass('singleForm');
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
                } else if ($(this).val().length > 0 && $(this).attr('type') === 'number' && parseInt($(this).val()) != $(this).val()) {
                    $(this).css('borderColor', '#a94442');
                    $(this).parent().find('.message').html('This field must contain only integer values!').css('color', '#a94442');
                    event.preventDefault();
                    event.stopImmediatePropagation();
                }
            });
        });
        $('.item input[type="text"],.item input[type="number"]').on('blur', function () {
            if ($(this).val().length > 255) {
                $(this).css('borderColor', '#a94442');
                $(this).parent().find('.message').html('Maximum length is 255 character!').css('color', '#a94442');
                event.preventDefault();
                event.stopImmediatePropagation();
            } else if ($(this).val().length > 0 && $(this).attr('type') === 'number' && parseInt($(this).val()) != $(this).val()) {
                $(this).css('borderColor', '#a94442');
                $(this).parent().find('.message').html('This field must contain only integer values!').css('color', '#a94442');
                event.preventDefault();
                event.stopImmediatePropagation();
            }
        });
        $('.sub_section').each(function () {
            var formsCount = $(this).find('.formSecRep').size();
            console.log(formsCount);
            if (formsCount > 1) {
                $(this).find('.deleteForm').removeClass('singleForm');
            }
        });
        $('.add-item').on('click', function () {
            $(this).parent().find('.deleteForm').removeClass('singleForm');
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
                $(this).parent().parent().find('.deleteForm').addClass('singleForm');
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
                $('.profileImage').css('background-image', 'url(/images/users_images/' + responseJSON.fileName + ')');
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



