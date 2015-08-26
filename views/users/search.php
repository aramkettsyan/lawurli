<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'Search';
?>

<div class="container mainContainer">
    <div class="sidebarL searcSidebar ">
        <h6 class="boxTitle">Refine search</h6>
        <?php $query = explode(' ', $this->params['query'], 2); ?>
        <form id="first_last" method="GET" action="<?= \yii\helpers\Url::to(['users/search']) ?>">
            <div class="customScroll">
                <div class="formRow">
                    <input type="text" class="formControl sForm" value="<?= isset($query[0]) ? $query[0] : '' ?>" placeholder="First name">
                </div>
                <div class="formRow">
                    <input type="text" class="formControl sForm" value="<?= isset($query[1]) ? $query[1] : '' ?>" placeholder="Last name">
                </div>
                <input type="hidden" name="query" >
            </div>
            <div class="advSrchBtnWrap alignCenter">
                <input type="submit" class="btn defBtn sBtn blockBtn" value="Apply">
                <a href="#advanced-search" class="textBtn openAdvSrch popupBtn">Advanced Search</a>
            </div>
        </form>
    </div>
    <div class="profileR">
        <div class="tabsContent">
            <div class="peopleList">
                <ul>
                    <?php foreach ($search as $user) { ?>
                        <li>
                            <div class="peopleListL">
                                <img src="<?php echo \Yii::getAlias('@web') . '/images/users_images/' . $user['image']; ?>" alt="">
                            </div>
                            <div class="peopleListR">
                                <a href="<?= \yii\helpers\Url::to(['users/profile', 'id' => $user['id']]) ?>" class="plName"><?= $user['first_name'] ?> <?= $user['last_name'] ?></a>
                                <div class="plDets">
                                    <!--<p>Plumber at "Clean House" Ltd</p>-->
                                    <p class="plAddress"><i class="icon-location"></i><?= $user['location'] ?></p>
                                </div>
                                  <div class="plActions">
                                    <a href="<?= \yii\helpers\Url::to(['users/profile', 'id' => $user['id']]) ?>" class="btn lineDefBtn sBtn">View Profile</a>
                                    <?php if(isset($contacts[$user['id']])) : ?>
                                        <?php if($contacts[$user['id']]['user_to_id'] == $user['id'] && $contacts[$user['id']]['request_accepted']== 'N') : ?>
                                            <a href="<?= \yii\helpers\Url::to(['users/decline','id' => $user['id']]) ?>" class="btn greyBtn sBtn cdBtn"><i class="icon-cross-mark"></i>Request is sent</a>
                                        <?php elseif ($contacts[$user['id']]['user_to_id'] == $user['id'] && $contacts[$user['id']]['request_accepted']== 'Y') : ?>
                                            <a href="<?= \yii\helpers\Url::to(['users/decline', 'id' => $user['id']]) ?>" class="btn greyBtn sBtn cdBtn"><i class="icon-cross-mark"></i>Disconnect</a>
                                        <?php elseif ($contacts[$user['id']]['user_from_id'] == $user['id'] && $contacts[$user['id']]['request_accepted']== 'Y') : ?>
                                           <a href="<?= \yii\helpers\Url::to(['users/decline', 'id' => $user['id']]) ?>" class="btn greyBtn sBtn cdBtn"><i class="icon-cross-mark"></i>Disconnect</a>
                                        <?php elseif ($contacts[$user['id']]['user_from_id'] == $user['id'] && $contacts[$user['id']]['request_accepted']== 'N') : ?>
                                           <a href="<?= \yii\helpers\Url::to(['users/accetpt', 'id' => $user['id']]) ?>" class="btn defBtn sBtn cdBtn"><i class="icon-check-1"></i>Accept</a>
                                        <?php endif; ?>
                                    <?php else : ?>    
                                        <a href="<?= \yii\helpers\Url::to(['users/connect', 'id' => $user['id']]) ?>" class="btn lineDefBtn sBtn cdBtn">Connect</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </li>
                    <?php } ?>

                    <?php if (empty($search)) { ?>
                        <li>There are no result</li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <?php if (isset($pages) && !empty($pages)) { ?>
            <div class="pagin">
                <?php
                echo LinkPager::widget([
                    'pagination' => $pages,
                ]);
                ?>
            </div>
        <?php } ?>
    </div>
</div>

<!-- ###### -->
<!-- POPUPS -->
<!-- ###### -->

<!-- advanced search popup -->
<div id="advanced-search" class="popupWrap popupLarge mfp-hide">
    <div class="popupTitle">
        <h5>Advanced Search</h5>
        <button class="mfp-close"></button>
    </div>
    <div class="popupCont srchPopupCont">
        <?php
        $fm = ActiveForm::begin([
                    'id' => 'advanced-search-form',
                    'method' => 'GET',
                    'action' => \yii\helpers\Url::to(['users/search']),
                    'options' => ['class' => 'form-horizontal', 'novalidate' => '']
        ]);
        ?>
        <div class="customScroll">
            <div class="cols cols2">
                <?php $i = 0; ?>
                <div>
                    <input type="hidden" name="search" value="advanced" >
                    <input type="hidden" name="query" value="<?= isset($query[1]) ? $query[0] . ' ' . $query[1] : $query[0] ?>" >
                    <?php foreach ($advanced as $key => $input) { ?>
                        <?php if ($key != 0 && !($key % 2)) { ?>
                            <div class="formRow">
                                <?php if ($input->type === 'input') { ?>
                                    <label class="customLbSt" for="<?= $input->label . '_' . $i; ?>"> <?php echo $input->label; ?> </label>
                                    <?php if ($input->numeric == '0') { ?>
                                        <?php echo Html::input('text', 'advanced[' . $input->id . ']', isset($query_response[$input->id]) ? $query_response[$input->id] : '', ['id' => $input->label . '_' . $i, 'class' => 'formControl sForm', 'placeholder' => $input->placeholder]); ?>
                                    <?php } else { ?>
                                        <?php echo Html::input('text', 'advanced[' . $input->id . ']', isset($query_response[$input->id]) ? $query_response[$input->id] : '', ['id' => $input->label . '_' . $i, 'class' => 'formControl sForm', 'type' => 'number', 'placeholder' => $input->placeholder]); ?>
                                    <?php } ?>
                                <?php } ?>
                                <?php if ($input->type === 'textarea') { ?>
                                    <label class="customLbSt" for="<?= $input->label . '_' . $i; ?>"><?php echo $input->label; ?></label>
                                    <?php echo Html::textarea('advanced[' . $input->id . ']', isset($query_response[$input->id]) ? $query_response[$input->id] : '', ['id' => $input->label . '_' . $i, 'class' => 'formControl sForm', 'placeholder' => $input->placeholder]); ?>
                                <?php } ?>
                                <?php if ($input->type === 'select') { ?>
                                    <label class="customLbSt" for="<?= $input->label; ?>"><?php echo $input->label; ?></label> 
                                    <?php $options = explode('-,-', $input->options); ?>
                                    <?php $newOptions = [] ?>
                                    <?php foreach ($options as $option) { ?>
                                        <?php $newOptions[$option] = $option; ?>
                                    <?php } ?>
                                    <?php echo Html::dropDownList('advanced[' . $input->id . ']', isset($query_response[$input->id]) ? $query_response[$input->id] : '', $newOptions, ['prompt' => $input->placeholder ? $input->placeholder : 'Select', 'id' => $input->label, 'class' => 'formControl sForm']); ?>
                                <?php } ?>
                                <?php if ($input->type === 'checkbox') { ?>
                                    <label class="customLbSt"> <?php echo $input->label; ?></label>
                                    <div class="checkbox">
                                        <?php $options = explode('-,-', $input->options); ?>
                                        <?php
                                        echo Html::checkboxList('checkbox', null, $options, ['class' => 'checkRadioSec', 'item' => function($index, $label, $name, $checked, $value)use($input, $query_response) {
                                                $check = isset($query_response[$input->id][$index]) ? 'checked' : '';
                                                return '<label for="' . $value . '_' . $index . '"><input ' . $check . ' id="' . $value . '_' . $index . '" name="advanced[' . $input->id . '][' . $index . ']" type="checkbox"><span>' . $label . '</span></label> ';
                                            }]);
                                        ?>
                                    </div>
                                <?php } ?>
                                <?php if ($input->type === 'radio') { ?>
                                    <label><?php echo $input->label; ?></label>
                                    <?php $items = explode('-,-', $input->options); ?>
                                    <div class="radio">
                                        <?php
                                        echo Html::radioList('radio', isset($query_response[$input->id]) ? $query_response[$input->id] : '', $items, ['class' => 'checkRadioSec', 'item' => function($index, $label, $name, $checked, $value)use($input) {
                                                return '<label for="' . $value . '_' . $name . '"><input id="' . $value . '_' . $name . '" name="advanced[' . $input->id . ']" type="radio"><span>' . $label . '</span></label> ';
                                            }]);
                                        ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php $i++ ?>
                    <?php } ?>

                </div>
                <div>
                    <?php foreach ($advanced as $key => $input) { ?>
                        <?php if ($key == 0 || $key % 2) { ?>
                            <div class="formRow">
                                <?php if ($input->type === 'input') { ?>
                                    <label class="customLbSt" for="<?= $input->label . '_' . $i; ?>"> <?php echo $input->label; ?> </label>
                                    <?php if ($input->numeric == '0') { ?>
                                        <?php echo Html::input('text', 'advanced[' . $input->id . ']', isset($query_response[$input->id]) ? $query_response[$input->id] : '', ['id' => $input->label . '_' . $i, 'class' => 'formControl sForm', 'placeholder' => $input->placeholder]); ?>
                                    <?php } else { ?>
                                        <?php echo Html::input('text', 'advanced[' . $input->id . ']', isset($query_response[$input->id]) ? $query_response[$input->id] : '', ['id' => $input->label . '_' . $i, 'class' => 'formControl sForm', 'type' => 'number', 'placeholder' => $input->placeholder]); ?>
                                    <?php } ?>
                                <?php } ?>
                                <?php if ($input->type === 'textarea') { ?>
                                    <label class="customLbSt" for="<?= $input->label . '_' . $i; ?>"><?php echo $input->label; ?></label>
                                    <?php echo Html::textarea('advanced[' . $input->id . ']', isset($query_response[$input->id]) ? $query_response[$input->id] : '', ['id' => $input->label . '_' . $i, 'class' => 'formControl sForm', 'placeholder' => $input->placeholder]); ?>
                                <?php } ?>
                                <?php if ($input->type === 'select') { ?>
                                    <label class="customLbSt" for="<?= $input->label; ?>"><?php echo $input->label; ?></label> 
                                    <?php $options = explode('-,-', $input->options); ?>
                                    <?php echo Html::dropDownList('advanced[' . $input->id . ']', isset($query_response[$input->id]) ? $query_response[$input->id] : '', $options, ['prompt' => $input->placeholder ? $input->placeholder : 'Select', 'id' => $input->label, 'class' => 'formControl sForm']); ?>
                                <?php } ?>
                                <?php if ($input->type === 'checkbox') { ?>
                                    <label class="customLbSt"> <?php echo $input->label; ?></label>
                                    <div class="checkbox">
                                        <?php $options = explode('-,-', $input->options); ?>
                                        <?php
                                        echo Html::checkboxList('checkbox', isset($query_response[$input->id]) ? $query_response[$input->id] : '', $options, ['class' => 'checkRadioSec', 'item' => function($index, $label, $name, $checked, $value)use($input) {
                                                return '<label for="' . $value . '_' . $index . '"><input id="' . $value . '_' . $index . '" name="advanced[' . $input->id . '][]" type="checkbox"><span>' . $label . '</span></label> ';
                                            }]);
                                        ?>
                                    </div>
                                <?php } ?>
                                <?php if ($input->type === 'radio') { ?>
                                    <label><?php echo $input->label; ?></label>
                                    <?php $items = explode('-,-', $input->options); ?>
                                    <div class="radio">
                                        <?php
                                        echo Html::radioList('radio', isset($query_response[$input->id]) ? $query_response[$input->id] : '', $items, ['class' => 'checkRadioSec', 'item' => function($index, $label, $name, $checked, $value)use($input) {
                                                return '<label for="' . $value . '_' . $name . '"><input id="' . $value . '_' . $name . '" name="advanced[' . $input->id . ']" type="radio"><span>' . $label . '</span></label> ';
                                            }]);
                                        ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php $i++ ?>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="submitSect">
            <input class="btn defBtn" type="submit" value="Apply">
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>


<?php if (Yii::$app->user->isGuest) { ?>

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
    $('#first_last').submit(function () {
        var value = '';
        var i = 0;
        $(this).find('input[type="text"]').each(function () {
            if (i === 0) {
                value += $(this).val() + ' ';
            } else {
                value += $(this).val();
            }
            i++;
        });
        $(this).find('input[type="hidden"]').val(value);
    });
</script>



<script type="text/javascript">
    $(document).ready(function () {
        $('.disabledBtn').click(function (e) {
            e.preventDefault();
        });
        
        <?php if(Yii::$app->user->isGuest) : ?>
                $(".cdBtn").each(function(element){
                   $(this).attr("href", "#login-popup");
                   $(this).addClass("popupBtn");
                });
        <?php endif; ?>
            
        $('.popupBtn').magnificPopup();

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