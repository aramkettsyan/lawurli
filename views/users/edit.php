<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>
<?php
$this->title = 'Edit profile'
?>
<div class="container mainContainer">
    <div class="profileL">
        <div class="userImage">
            <?php $filename = \Yii::getAlias('@webroot') . '/images/users_images/' . $this->params['current_user']->image; ?>
            <?php if (is_file($filename)) { ?>
                <img src="/images/user-1.png">
                <span class="profileImage" style="background-image: url('<?php echo \Yii::getAlias('@web') . '/images/users_images/' . $this->params['current_user']->image; ?>')"></span>
                <div class="imgEditBtns">
                    <!--<button id="uploadImage" type="button"><i class="icon-pencil-square"></i></button>-->
                    <div id="uploadImage"><i class="icon-pencil-square"></i></div>
                </div>
            </div>

                                <!--<img src="<?php echo \Yii::getAlias('@web') . '/images/users_images/' . $this->params['current_user']->image; ?>"  alt="User image" >-->
        <?php } ?>
        <p style="color:red;display: none" id="imageUploadError"></p>

        <div class="userDetails">
            <h3 class="userName"><?= Html::encode($this->params['current_user']->first_name) ?> <?= Html::encode($this->params['current_user']->last_name) ?></h3>
            <!--            <div class="proffInfo">
                            <span class="userProff">Bandit</span> 
                        </div>-->
            <ul class="listWithIcons">
                <?php if ($this->params['current_user']->location) { ?>
                    <li>
                        <i class="icon-location"></i>
                        <p><?= Html::encode($this->params['current_user']->location) ?></p>
                    </li>
                <?php } ?>
                <?php if ($this->params['current_user']->phone) { ?>
                    <li>
                        <i class="icon-smart-phone-2"></i>
                        <p><?= Html::encode($this->params['current_user']->phone) ?></p>
                    </li>
                <?php } ?>
                <li>
                    <i class="icon-letter-mail-1"></i>
                    <p><?= Html::encode($this->params['current_user']->email) ?></p>
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
            <?php $layoutName = 'load-detailed.php'; ?>
        <?php } else { ?>
            <?php $action = 'general' ?>
            <?php $layoutName = 'load-general.php' ?>
        <?php } ?>

        <div class="profileTabs">
            <ul class="clearAfter">
                <li class="<?= $action === 'general' ? 'active' : '' ?>" id="generalInfo"><a><i class="icon-user"></i>General Information</a></li>
                <li class="<?= $action === 'detailed' ? 'active' : '' ?>" id ="detailedInfo"><a><i class="icon-card-user-2"></i>Detailed Information</a></li>
            </ul>
        </div>



        <div class="tabsContent">
            <?php if ($action === 'detailed') { ?>
                <?php Yii::$app->view->params['sections'] = $sections; ?>
                <?php Yii::$app->view->params['user_forms'] = $user_forms; ?>
            <?php } ?>
            <?php $this->beginContent('@app/views/users/' . $layoutName); ?>

            <?php $this->endContent(); ?>
        </div>

    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        new qq.FileUploaderBasic({
            button: document.getElementById('uploadImage'),
            action: '/users/upload-image',
            multiple: false,
            sizeLimit: 5242880,
            allowedExtensions: ['png', 'jpg', 'jpeg'],
            onComplete: function (id, fileName, responseJSON) {

                if (responseJSON.success) {
                    $('.profileImage').css('background-image', 'url(/images/users_images/' + responseJSON.fileName + ')');
                    $('#imageUploadError').hide();
                }
                if (responseJSON.error) {
                    $('#imageUploadError').html(responseJSON.error);
                    $('#imageUploadError').show();
                }
            }
        });
        $('#generalInfo').on('click', function () {
            $('#detailedInfo').removeClass('active');
            $(this).addClass('active');
            $('.tabsContent').load('/users/edit?action=general');
        });
        $('#detailedInfo').on('click', function () {
            $('#generalInfo').removeClass('active');
            $(this).addClass('active');
            $('.tabsContent').load('/users/edit?action=detailed');
        });

    });
</script>



