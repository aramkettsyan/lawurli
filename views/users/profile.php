<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<?php Yii::$app->view->params['user'] = $user; ?>
<?php Yii::$app->view->params['sections'] = $sections; ?>
<?php Yii::$app->view->params['user_forms'] = $user_forms; ?>
<div class="container mainContainer">
    <div class="profileL">
        <div class="userImage">
            <?php $filename = \Yii::getAlias('@webroot') . '/images/users_images/' . $user->image; ?>
            <?php if (is_file($filename)) { ?>
                <img src="/images/user-1.png">
                <span class="profileImage" style="background-image: url('<?php echo \Yii::getAlias('@web') . '/images/users_images/' . $user->image; ?>')"></span>
            <?php } ?>
        </div>
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
                    <p><?= $user->phone?$user->phone:'No phone number' ?></p>
                </li>
                <li>
                    <i class="icon-letter-mail-1"></i>
                    <p><?= $user->email ?></p>
                </li>
            </ul>
        </div>
        <?php if (!Yii::$app->user->isGuest) { ?>
            <div class="alignCenter">
                <a href="<?= \yii\helpers\Url::to(['users/edit']) ?>" class="btn defBtn">Edit profile</a>
            </div>
        <?php } ?>
    </div>
    <div class="profileR">
        <h4 style="color:green"><?= Yii::$app->getSession()->readSession('updateSuccess') ?></h4>
        <h4 style="color:red"><?= Yii::$app->getSession()->readSession('updateError') ?></h4>
        <?php Yii::$app->getSession()->destroySession('updateSuccess'); ?>
        <?php Yii::$app->getSession()->destroySession('updateError'); ?>
        <div class="profileTabs">
            <ul class="clearAfter">
                <li class="active"><a href="<?= \yii\helpers\Url::to(['users/profile']) ?>"><i class="icon-card-user-2"></i>Profile</a></li>
                <li><a href="#" id="colleag"><i class="icon-contacts"></i>Colleagues</a></li>
                <li><a href="#"><i class="icon-bell-two"></i>Notifications</a></li>
            </ul>
        </div>
        <div class="tabsContent">
            <?php foreach ($this->params['sections'] as $sectionName => $section) { ?>
                <?php $emptySectionToken = false; ?>
                <div class="cvTimeline">
                    <h4><?= $sectionName ?></h4>

                    <?php foreach ($section as $subSectionName => $subSection) { ?>
                        <?php $emptySubSectionToken = false; ?>
                        <div class="cvSub sub_section">
                            <?php if ($subSectionName) { ?>
                                <div class="cvSubLabel">
                                    <h5><?= $subSectionName ?></h5>
                                </div>
                            <?php } ?>
                            <div class="cvSubCont">
                                <ul>
                                    <?php $subSectionId = $subSection['0']['subId']; ?>
                                    <?php if (isset($this->params['user_forms'][$subSectionId])) { ?>
                                        <?php $sub_sections_count = count($this->params['user_forms'][$subSectionId]); ?>
                                    <?php } else { ?>
                                        <?php $sub_sections_count = 1 ?>
                                    <?php } ?>
                                    <?php $i = 0; ?>
                                    <?php for ($u = 0; $u < $sub_sections_count; $u++) { ?>
                                        <li>
                                            <?php foreach ($subSection as $key => $form) { ?>
                                                <?php if ($key === 0) { ?>
                                                    <?php continue; ?>
                                                <?php } ?>
                                                <?php $value = ''; ?>
                                                <?php if (isset($this->params['user_forms'][$subSectionId][$u][$form['formId']])) { ?>
                                                    <?php $value = $this->params['user_forms'][$subSectionId][$u][$form['formId']] ?>
                                                <?php } ?>
                                                <?php if (!empty($value)) { ?>
                                                    <?php $emptySectionToken = true; ?>
                                                    <?php $emptySubSectionToken = true; ?>
                                                <?php } ?>

                                                <?php if ($form['formType'] === 'input') { ?>
                                                    <?php $type = $form['formNumeric'] == 0 ? 'text' : 'number' ?>
                                                                                                                                                                                                                                                                                                                                                                                                            <!--<input class='textInput formControl' form-id="<?= $form['formId'] ?>" index="<?= $i ?>" value="<?= $value ?>" name="Users[custom_fields][<?= $form['formId'] ?>][]" placeholder="<?= $form['formPlaceholder'] ?>" type="<?= $type ?>" />-->
                                                    <p class="<?= $key === 1 ? 'cvSingleTitle' : 'cvSingleDet' ?>" ><?= $value ?></p>
                                                <?php } ?>
                                                <?php if ($form['formType'] === 'textarea') { ?>
                                                    <!--<textarea class='inputTextarea formControl' form-id="<?= $form['formId'] ?>" index="<?= $i ?>" name="Users[custom_fields][<?= $form['formId'] ?>][]" placeholder="<?= $form['formPlaceholder'] ?>"><?= $value ?></textarea>-->
                                                    <p class="<?= $key === 1 ? 'cvSingleTitle' : 'cvSingleDet' ?>" ><?= $value ?></p>
                                                <?php } ?>
                                                <?php if ($form['formType'] === 'select') { ?>
                                                    <?php $options = str_replace('-,-', ',', $form['formOptions']); ?>
                                                    <div class="labelValue">
                                                        <label><?= $form['formLabel'] ?></label>
                                                        <span><?= $value ?></span>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($form['formType'] === 'checkbox') { ?>
                                                    <?php $options = explode('-,-', $form['formOptions']); ?>
                                                    <?php $values = ''; ?>
                                                    <?php foreach ($options as $option) { ?>
                                                        <?php if (isset($this->params['user_forms'][$subSectionId][$u][$form['formId']])) { ?>
                                                            <?php if (is_array($this->params['user_forms'][$subSectionId][$u][$form['formId']])) { ?>
                                                                <?php if (in_array($option, $this->params['user_forms'][$subSectionId][$u][$form['formId']])) { ?>
                                                                    <?php $values .= $option . ' '; ?>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                <?php if ($option === $this->params['user_forms'][$subSectionId][$u][$form['formId']]) { ?>
                                                                    <?php $values = $option; ?>
                                                                <?php } ?>   
                                                            <?php } ?>
                                                        <?php } ?>

                                                    <?php } ?>
                                                    <div class="labelValue">
                                                        <label><?= $form['formLabel'] ?></label>
                                                        <span><?= $values ?></span>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($form['formType'] === 'radio') { ?>
                                                    <div class="labelValue">
                                                        <label><?= $form['formLabel'] ?></label>
                                                        <span><?= $value ?></span>
                                                    </div>
                                                <?php } ?>
                                                <p class="message"></p> 
                                                <?php $value = ''; ?>
                                            <?php } ?>
                                        </li>

                                        <?php $i++; ?>
                                    <?php } ?>
                                </ul>

                            </div>
                            <?php if (!$emptySubSectionToken) { ?>
                                <span class="hideSubSection"></span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php if (!$emptySectionToken) { ?>
                        <span class="hideSection"></span>
                    <?php } ?>
                </div>
            <?php } ?>

        </div>
    </div>
</div>

<?php if (Yii::$app->user->isGuest) { ?>
    <!-- ###### -->
    <!-- POPUPS -->
    <!-- ###### -->

    <!-- login popup -->
    <div id="login-popup" class="popupWrap popupSmall mfp-hide">
        <div class="popupTitle">
            <h5>Login</h5>
            <button class="mfp-close"></button>
        </div>
        <div class="popupCont">
            <?php
            echo \Yii::$app->getSession()->getFlash('success');
            echo \Yii::$app->getSession()->getFlash('warning');
            ?>
            <?php
            $form = ActiveForm::begin([
                        'id' => 'login-form'
            ]);
            ?>



            <?=
            $form->field($model, 'email', [
                'template' => "{input} <i class='icon-email-streamline'></i>
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
                'options' => [
                    'class' => 'formRow frIconLeft'
        ]])->textInput(['class' => 'formControl', 'placeholder' => 'Email']);
            ?>

            <?=
            $form->field($model, 'password', [
                'template' => "{input} <i class='icon-lock-streamline'></i>
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
                'options' => [
                    'class' => 'formRow frIconLeft'
        ]])->passwordInput(['class' => 'formControl', 'placeholder' => 'Password']);
            ?>

            <div class="remMeForgPass clearAfter">
                <?=
                $form->field($model, 'rememberMe', ['options' => [
                        'class' => 'checkbox'
            ]])->checkbox(['label' => 'Remember me!'])
                ?>
                <div class="forgPass">
                    <a class="textBtn popupBtn" href="#forgpass-popup" >Forgot password?</a>
                </div>
            </div>
            <?= Html::submitButton('Login', ['class' => 'btn defBtn']) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <!-- sign up popup -->
    <div id="signup-popup" class="popupWrap popupSmall mfp-hide">
        <div class="popupTitle">
            <h5>Sign up</h5>
            <button class="mfp-close"></button>
        </div>
        <div class="popupCont">
            <?php
            echo \Yii::$app->getSession()->getFlash('registrationSuccess');
            echo \Yii::$app->getSession()->getFlash('registrationWarning');
            ?>
            <?php
            $f = ActiveForm::begin([
                        'id' => 'registration-form'
            ]);
            ?>


            <?=
            $f->field($registrationModel, 'first_name', [
                'template' => "{input}<i class='icon-man-streamline-user'></i>
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
                'options' => [
                    'class' => 'formRow frIconLeft'
        ]])->textInput(['class' => 'formControl', 'placeholder' => 'Name'])->label(false);
            ?>
            <?=
            $f->field($registrationModel, 'last_name', [
                'template' => "{input} <i class='icon-man-streamline-user'></i>
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
                'options' => [
                    'class' => 'formRow frIconLeft'
        ]])->textInput(['class' => 'formControl', 'placeholder' => 'Last name'])->label(false);
            ?>
            <?=
            $f->field($registrationModel, 'email', [
                'template' => "{input} <i class='icon-email-streamline'></i>
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
                'options' => [
                    'class' => 'formRow frIconLeft'
        ]])->textInput(['class' => 'formControl', 'placeholder' => 'Email'])->label(false);
            ?>
            <?=
            $f->field($registrationModel, 'password', [
                'template' => "{input} <i class='icon-lock-streamline'></i>
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
                'options' => [
                    'class' => 'formRow frIconLeft'
        ]])->passwordInput(['class' => 'formControl', 'placeholder' => 'Password'])->label(false);
            ?>
            <?=
            $f->field($registrationModel, 'confirm_password', [
                'template' => "{input} <i class='icon-lock-streamline'></i>
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
                'options' => [
                    'class' => 'formRow frIconLeft'
        ]])->passwordInput(['class' => 'formControl', 'placeholder' => 'Retype password'])->label(false);
            ?>
            <?=
            $f->field($registrationModel, 'conditions', ['options' => [
                    'class' => 'checkbox'
        ]])->checkbox(['label' => 'Terms and conditions'])
            ?>

            <?= Html::submitButton('Register', ['class' => 'btn defBtn']) ?>


            <?php ActiveForm::end(); ?>
        </div>
    </div>


    <!-- forgot password -->
    <div id="forgpass-popup" class="popupWrap popupSmall mfp-hide">


        <div class="popupTitle">
            <h5>Reset password</h5>
            <button class="mfp-close"></button>
        </div>

        <?php
        $resetPassForm = ActiveForm::begin([
                    'id' => 'password-reset-form',
                    'action' => \yii\helpers\Url::to(['users/profile', 'action' => 'reset_password', 'id' => $id]),
                    'options' => ['class' => '']
        ]);
        ?>

        <div class="popupCont">
            <?=
            $resetPassForm->field($resetModel, 'email', [
                'template' => "{input}{error} <i class='icon-email-streamline'></i>",
                'options' => [
                    'class' => 'formRow frIconLeft'
        ]])->textInput(['class' => 'formControl', 'placeholder' => 'Email']);
            ?>
            <p style="color:red">
                <?php
                echo \Yii::$app->getSession()->getFlash('resetWarning');
                ?>
            </p>
            <p style="color:green">
                <?php
                echo \Yii::$app->getSession()->getFlash('resetSuccess');
                ?>
            </p>

            <?= Html::submitButton('Send Email', ['id' => 'password-reset-form_submit', 'class' => 'btn defBtn']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <!-- forgot password 2 -->
    <div id="forgpass-popup-2" class="popupWrap popupSmall mfp-hide">
        <div class="popupTitle">
            <h5>Reset password</h5>
            <button class="mfp-close"></button>
        </div>
        <?php
        $resetForm = ActiveForm::begin([
                    'id' => 'password-reset-form',
        ]);
        ?>


        <div class="popupCont">
            <?=
            $resetForm->field($user_reset, 'password', [
                'template' => "{input}{error} <i class='icon-lock-streamline'></i>",
                'options' => [
                    'class' => 'formRow frIconLeft'
        ]])->passwordInput(['class' => 'formControl', 'placeholder' => 'New password']);
            ?>
            <?=
            $resetForm->field($user_reset, 'confirm_password', [
                'template' => "{input}{error} <i class='icon-lock-streamline'></i>",
                'options' => [
                    'class' => 'formRow frIconLeft'
        ]])->passwordInput(['class' => 'formControl', 'placeholder' => 'Confirm password']);
            ?>
            <?= Html::submitButton('Confirm', ['id' => 'password-reset-form_submit', 'class' => 'btn defBtn']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
<?php } ?>


<script type="text/javascript">
    $(document).ready(function () {

        $('.hideSection').parent().hide();
        $('.hideSubSection').parent().hide();


        $('.inputError').each(function () {
            if ($(this).find('.help-block').html().length > 0) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        $('.inputError').bind("DOMSubtreeModified", function () {
            if ($(this).find('.help-block').html().length > 0) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        $('.checkbox').on('change', function () {
            $(this).find('.inputError').hide();
        });

        var showRegistration = <?= Yii::$app->getSession()->readSession('showRegistration') ? 'true' : 'false' ?>;
<?php Yii::$app->getSession()->destroySession('showRegistration'); ?>
        if (showRegistration) {
            $.magnificPopup.open({
                items: {src: '#signup-popup'}, type: 'inline'
            }, 0);
        }

        var showLogin = <?= Yii::$app->getSession()->readSession('showLogin') ? 'true' : 'false' ?>;
<?php Yii::$app->getSession()->destroySession('showLogin'); ?>
        if (showLogin) {
            $.magnificPopup.open({
                items: {src: '#login-popup'}, type: 'inline'
            }, 0);
        }


        var newPassword = <?= Yii::$app->getSession()->readSession('newPassword') ? 'true' : 'false' ?>;
<?php Yii::$app->getSession()->destroySession('newPassword'); ?>
        if (newPassword) {
            $.magnificPopup.open({
                items: {src: '#forgpass-popup-2'}, type: 'inline'
            }, 0);
        }
        var resetPassword = <?= Yii::$app->getSession()->readSession('resetPassword') ? 'true' : 'false' ?>;
<?php Yii::$app->getSession()->destroySession('resetPassword'); ?>
        if (resetPassword) {
            $.magnificPopup.open({
                items: {src: '#forgpass-popup'}, type: 'inline'
            }, 0);
        }

    });
</script>