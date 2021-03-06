<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'Search';
?>

<div class="container mainContainer">
    <div class="sidebarL searcSidebar ">
        <h6 class="boxTitle">Refine search</h6>
        <?php if (!isset($this->params['first_name']) && !isset($this->params['last_name'])) { ?>
            <?php $query = explode(' ', $this->params['query'], 2); ?>
        <?php } else { ?>
            <?php if (isset($this->params['first_name'])) { ?>
                <?php $query[0] = $this->params['first_name'] ?>
            <?php } ?>
            <?php if (isset($this->params['last_name'])) { ?>
                <?php $query[1] = $this->params['last_name'] ?>
            <?php } ?>
        <?php } ?>
        <form id="first_last" method="GET" action="<?= \yii\helpers\Url::to(['users/search']) ?>">
            <div class="customScroll">
                <div class="formRow">
                    <input type="hidden" id="first_last" name="first_last" value="true" >
                    <input type="text" class="formControl sForm" name="first_name" value="<?= isset($query[0]) && (isset($query[1])) ? Html::encode($query[0]) : '' ?>" placeholder="First name">
                </div>
                <div class="formRow">
                    <input type="text" class="formControl sForm" name="last_name" value="<?= isset($query[1]) ? Html::encode($query[1]) : '' ?>" placeholder="Last name">
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
                                <?php $filename = \Yii::getAlias('@webroot') . '/images/users_images/' . Html::encode($user['image']); ?>
                                <?php if (is_file($filename)) { ?>
                                    <img src="/images/user-1.png" alt="">
                                    <span style="background-image: url('<?php echo \Yii::getAlias('@web') . '/images/users_images/' . $user['image']; ?>')"></span>
                                <?php } else { ?>
                                    <img src="/images/user-1.png" alt="">
                                    <span style="background-image: url('<?php echo \Yii::getAlias('@web') . '/images/users_images/default.png'; ?>')"></span>
                                <?php } ?>
                            </div>
                            <div class="peopleListR">
                                <a href="<?= \yii\helpers\Url::to(['users/profile', 'id' => $user['id'], 'profileTab' => 'open']) ?>" class="plName"><?= Html::encode($user['first_name']) ?> <?= Html::encode($user['last_name']) ?></a>
                                <div class="plDets">
                                    <!--<p>Plumber at "Clean House" Ltd</p>-->
                                    <?php if ($user['location']) { ?>
                                        <p class="plAddress"><i class="icon-location"></i><?= Html::encode($user['location']) ?></p>
                                    <?php } ?>
                                </div>
                                <div class="plActions">
                                    <a href="<?= \yii\helpers\Url::to(['users/profile', 'id' => $user['id'], 'profileTab' => 'open']) ?>" class="btn lineDefBtn sBtn">View Profile</a>
                                    <?php if (!Yii::$app->user->isGuest) : ?>
                                        <?php if (isset($contacts[$user['id']])) : ?>
                                            <?php if ($contacts[$user['id']]['user_to_id'] == $user['id'] && $contacts[$user['id']]['request_accepted'] == 'N') : ?>
                                                <a href="<?= \yii\helpers\Url::to(['users/decline', 'id' => $user['id']]) ?>" class="btn greyBtn sBtn"><i class="icon-cross-mark"></i>Request is sent</a>
                                            <?php elseif ($contacts[$user['id']]['user_to_id'] == $user['id'] && $contacts[$user['id']]['request_accepted'] == 'Y') : ?>
                                                <a href="<?= \yii\helpers\Url::to(['users/decline', 'id' => $user['id']]) ?>" class="btn greyBtn sBtn"><i class="icon-cross-mark"></i>Disconnect</a>
                                            <?php elseif ($contacts[$user['id']]['user_from_id'] == $user['id'] && $contacts[$user['id']]['request_accepted'] == 'Y') : ?>
                                                <a href="<?= \yii\helpers\Url::to(['users/decline', 'id' => $user['id']]) ?>" class="btn greyBtn sBtn"><i class="icon-cross-mark"></i>Disconnect</a>
                                            <?php elseif ($contacts[$user['id']]['user_from_id'] == $user['id'] && $contacts[$user['id']]['request_accepted'] == 'N') : ?>
                                                <a href="<?= \yii\helpers\Url::to(['users/accetpt', 'id' => $user['id']]) ?>" class="btn defBtn sBtn"><i class="icon-check-1"></i>Accept</a>
                                            <?php endif; ?>
                                        <?php else : ?>    
                                            <a href="<?= \yii\helpers\Url::to(['users/connect', 'id' => $user['id']]) ?>" class="btn lineDefBtn sBtn">Connect</a>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <a class="popupBtn btn lineDefBtn sBtn"  href="#login-popup">Connect</a>
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
        <?php if (isset($pages) && !empty($pages) && $pages->totalCount > 10) { ?>
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
                    <input type="hidden" id="query" name="query" value="" >
                    <input type="hidden" id="first_last" name="first_last" value="true" >
                    <?php $l = 0; ?>
                    <?php foreach ($advanced as $key => $input) { ?>
                        <?php if ($l == 0) { ?>
                            <div class="formRow">
                                <label class="customLbSt" for="first_name"> First name </label>
                                <?php echo Html::input('text', 'first_name', isset($query[0]) && (isset($query[1])) ? Html::encode($query[0]) : '', ['id' => 'first_name', 'class' => 'formControl sForm', 'placeholder' => 'First name']); ?>
                            </div>
                            <?php $l++; ?>
                        <?php } ?>
                        <?php if ($input->show_in_search) { ?>
                            <?php if ($key != 0 && !($key % 2)) { ?>
                                <div class="formRow">
                                    <?php if ($input->type === 'input') { ?>
                                        <label class="customLbSt" for="<?= Html::encode($input->label) . '_' . $i; ?>"> <?php echo Html::encode($input->label); ?> </label>
                                        <?php if ($input->numeric == '0') { ?>
                                            <?php echo Html::input('text', 'advanced[' . Html::encode($input->id) . ']', isset($query_response[$input->id]) ? Html::encode($query_response[$input->id]) : '', ['id' => Html::encode($input->label) . '_' . $i, 'class' => 'formControl sForm', 'placeholder' => Html::encode($input->placeholder)]); ?>
                                        <?php } else { ?>
                                            <?php echo Html::input('text', 'advanced[' . Html::encode($input->id) . ']', isset($query_response[$input->id]) ? Html::encode($query_response[$input->id]) : '', ['id' => Html::encode($input->label) . '_' . $i, 'class' => 'formControl sForm', 'type' => 'number', 'placeholder' => Html::encode($input->placeholder)]); ?>
                                        <?php } ?>
                                    <?php } ?>
                                    <?php if ($input->type === 'textarea') { ?>
                                        <label class="customLbSt" for="<?= Html::encode($input->label) . '_' . $i; ?>"><?php echo Html::encode($input->label); ?></label>
                                        <?php echo Html::textarea('advanced[' . Html::encode($input->id) . ']', isset($query_response[$input->id]) ? Html::encode($query_response[$input->id]) : '', ['id' => Html::encode($input->label) . '_' . $i, 'class' => 'formControl sForm', 'placeholder' => Html::encode($input->placeholder)]); ?>
                                    <?php } ?>
                                    <?php if ($input->type === 'select') { ?>
                                        <label class="customLbSt" for="<?= Html::encode($input->label); ?>"><?php echo Html::encode($input->label); ?></label> 
                                        <?php $options = explode('-,-', $input->options); ?>
                                        <?php $newOptions = [] ?>
                                        <?php foreach ($options as $option) { ?>
                                            <?php $newOptions[$option] = Html::encode($option); ?>
                                        <?php } ?>
                                        <?php echo Html::dropDownList('advanced[' . Html::encode($input->id) . ']', isset($query_response[$input->id]) ? Html::encode($query_response[$input->id]) : '', $newOptions, ['prompt' => $input->placeholder ? Html::encode($input->placeholder) : 'Select', 'id' => Html::encode($input->label), 'class' => 'formControl sForm']); ?>
                                    <?php } ?>
                                    <?php if ($input->type === 'checkbox') { ?>
                                        <label class="customLbSt"> <?php echo Html::encode($input->label); ?></label>
                                        <div class="checkbox">
                                            <?php $options = explode('-,-', $input->options); ?>
                                            <?php
                                            echo Html::checkboxList('checkbox', '', $options, ['class' => 'checkRadioSec', 'item' => function($index, $label, $name, $checked, $value)use($input, $query_response) {
                                                    $check = isset($query_response[$input->id][$index]) ? 'checked' : '';
                                                    return '<label for="' . Html::encode($input->id) . '_' . $index . '"><input ' . $check . ' id="' . Html::encode($input->id) . '_' . $index . '" name="advanced[' . Html::encode($input->id) . '][' . $index . ']" value="' . $label . '" type="checkbox"><span>' . Html::encode($label) . '</span></label> ';
                                                }]);
                                            ?>
                                        </div>
                                    <?php } ?>
                                    <?php if ($input->type === 'radio') { ?>
                                        <label><?php echo Html::encode($input->label); ?></label>
                                        <?php $items = explode('-,-', $input->options); ?>
                                        <div class="radio">
                                            <?php
                                            echo Html::radioList('radio', '', $items, ['class' => 'checkRadioSec', 'item' => function($index, $label, $name, $checked, $value)use($input, $query_response) {
                                                    $check = isset($query_response[$input->id])&&$label==$query_response[$input->id] ? 'checked' : '';
                                                    return '<label for="' . Html::encode($input->id) . '_' . Html::encode($value) . '_' . $name . '"><input '.$check.' id="' . Html::encode($input->id) . '_' . Html::encode($value) . '_' . $name . '" name="advanced[' . Html::encode($input->id) . ']" type="radio" value="' . Html::encode($label) . '"><span>' . Html::encode($label) . '</span></label> ';
                                                }]);
                                            ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php $i++ ?>
                        <?php } ?>
                    <?php } ?>

                </div>
                <div>
                    <?php foreach ($advanced as $key => $input) { ?>
                        <?php if ($l == 1) { ?>
                            <div class="formRow">
                                <label class="customLbSt" for="last_name"> Last name </label>
                                <?php echo Html::input('text', 'last_name', isset($query[1]) ? Html::encode($query[1]) : '', ['id' => 'last_name', 'class' => 'formControl sForm', 'placeholder' => 'Last name']); ?>
                            </div>
                            <?php $l++; ?>
                        <?php } ?>
                        <?php if ($input->show_in_search) { ?>
                            <?php if ($key == 0 || $key % 2) { ?>
                                <div class="formRow">
                                    <?php if ($input->type === 'input') { ?>
                                        <label class="customLbSt" for="<?= Html::encode($input->label) . '_' . $i; ?>"> <?php echo Html::encode($input->label); ?> </label>
                                        <?php if ($input->numeric == '0') { ?>
                                            <?php echo Html::input('text', 'advanced[' . Html::encode($input->id) . ']', isset($query_response[$input->id]) ? Html::encode($query_response[$input->id]) : '', ['id' => Html::encode($input->label) . '_' . $i, 'class' => 'formControl sForm', 'placeholder' => Html::encode($input->placeholder)]); ?>
                                        <?php } else { ?>
                                            <?php echo Html::input('text', 'advanced[' . Html::encode($input->id) . ']', isset($query_response[$input->id]) ? Html::encode($query_response[$input->id]) : '', ['id' => Html::encode($input->label) . '_' . $i, 'class' => 'formControl sForm', 'type' => 'number', 'placeholder' => Html::encode($input->placeholder)]); ?>
                                        <?php } ?>
                                    <?php } ?>
                                    <?php if ($input->type === 'textarea') { ?>
                                        <label class="customLbSt" for="<?= Html::encode($input->label) . '_' . $i; ?>"><?php echo Html::encode($input->label); ?></label>
                                        <?php echo Html::textarea('advanced[' . Html::encode($input->id) . ']', isset($query_response[$input->id]) ? Html::encode($query_response[$input->id]) : '', ['id' => Html::encode($input->label) . '_' . $i, 'class' => 'formControl sForm', 'placeholder' => Html::encode($input->placeholder)]); ?>
                                    <?php } ?>
                                    <?php if ($input->type === 'select') { ?>
                                        <label class="customLbSt" for="<?= Html::encode($input->label); ?>"><?php echo Html::encode($input->label); ?></label> 
                                        <?php $options = explode('-,-', $input->options); ?>
                                        <?php $newOptions = [] ?>
                                        <?php foreach ($options as $option) { ?>
                                            <?php $newOptions[$option] = Html::encode($option); ?>
                                        <?php } ?>
                                        <?php echo Html::dropDownList('advanced[' . Html::encode($input->id) . ']', isset($query_response[$input->id]) ? Html::encode($query_response[$input->id]) : '', $newOptions, ['prompt' => $input->placeholder ? Html::encode($input->placeholder) : 'Select', 'id' => Html::encode($input->label), 'class' => 'formControl sForm']); ?>
                                    <?php } ?>
                                    <?php if ($input->type === 'checkbox') { ?>
                                        <label class="customLbSt"> <?php echo Html::encode($input->label); ?></label>
                                        <div class="checkbox">
                                            <?php $options = explode('-,-', $input->options); ?>
                                            <?php
                                            echo Html::checkboxList('checkbox', '', $options, ['class' => 'checkRadioSec', 'item' => function($index, $label, $name, $checked, $value)use($input, $query_response) {
                                                    $check = isset($query_response[$input->id][$index]) ? 'checked' : '';
                                                    return '<label for="' . Html::encode($input->id) . '_' . $index . '"><input ' . $check . ' id="' . Html::encode($input->id) . '_' . $index . '" name="advanced[' . Html::encode($input->id) . '][' . $index . ']" value="' . $label . '" type="checkbox"><span>' . Html::encode($label) . '</span></label> ';
                                                }]);
                                            ?>
                                        </div>
                                    <?php } ?>
                                    <?php if ($input->type === 'radio') { ?>
                                        <label><?php echo Html::encode($input->label); ?></label>
                                        <?php $items = explode('-,-', $input->options); ?>
                                        <div class="radio">
                                            <?php
                                            echo Html::radioList('radio', '', $items, ['class' => 'checkRadioSec', 'item' => function($index, $label, $name, $checked, $value)use($input, $query_response) {
                                                    $check = isset($query_response[$input->id])&&$label==$query_response[$input->id] ? 'checked' : '';
                                                    return '<label for="' . Html::encode($input->id) . '_' . Html::encode($value) . '_' . $name . '"><input '.$check.' id="' . Html::encode($input->id) . '_' . Html::encode($value) . '_' . $name . '" name="advanced[' . Html::encode($input->id) . ']" type="radio" value="' . Html::encode($label) . '"><span>' . Html::encode($label) . '</span></label> ';
                                                }]);
                                            ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php $i++ ?>
                        <?php } ?>
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

    $('#advanced-search-form').submit(function () {
        var firstName = $('#first_name').val();
        var lastName = $('#last_name').val();
        $('#query').val(firstName + ' ' + lastName);
    });


    function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }
</script>



<script type="text/javascript">
    $(document).ready(function () {
        $('.disabledBtn').click(function (e) {
            e.preventDefault();
        });

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
        // open advanced Search popup
        var advancedSearch = getParameterByName('advancedSearch');
        if (advancedSearch == 'open') {
            $('.openAdvSrch').click();
        }

//        var showRegistration = <?php //echo Yii::$app->getSession()->readSession('showRegistration') ? 'true' : 'false'                        ?>;
//<?php //Yii::$app->getSession()->destroySession('showRegistration');                        ?>
//        if (showRegistration) {
//            $.magnificPopup.open({
//                items: {src: '#signup-popup'}, type: 'inline'
//            }, 0);
//        }
//
//        var showLogin = <?php //echo Yii::$app->getSession()->readSession('showLogin') ? 'true' : 'false'                        ?>;
//<?php //Yii::$app->getSession()->destroySession('showLogin');                        ?>
//        if (showLogin) {
//            $.magnificPopup.open({
//                items: {src: '#login-popup'}, type: 'inline'
//            }, 0);
//        }
//
//
//        var newPassword = <?php //echo Yii::$app->getSession()->readSession('newPassword') ? 'true' : 'false'                        ?>;
//<?php // Yii::$app->getSession()->destroySession('newPassword');                        ?>
//        if (newPassword) {
//            $.magnificPopup.open({
//                items: {src: '#forgpass-popup-2'}, type: 'inline'
//            }, 0);
//        }
//        var resetPassword = <?php //echo Yii::$app->getSession()->readSession('resetPassword') ? 'true' : 'false'                        ?>;
//<?php // Yii::$app->getSession()->destroySession('resetPassword');                        ?>
//        if (resetPassword) {
//            $.magnificPopup.open({
//                items: {src: '#forgpass-popup'}, type: 'inline'
//            }, 0);
//        }

    });
</script>