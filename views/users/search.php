<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
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
                                    <a href="#" class="btn lineDefBtn sBtn">Connect</a>
                                </div>
                            </div>
                        </li>
                    <?php } ?>

                    <?php if (empty($search)) { ?>
                        <li>There are no result</li>
                    <?php } ?>
                    <!--                    <li>
                                            <div class="peopleListL">
                                                <img src="img/user-2.png" alt="">
                                            </div>
                                            <div class="peopleListR">
                                                <a href="#" class="plName">Charlotte Douglas</a>
                                                <div class="plDets">
                                                    <p>CEO at "Clean House" Ltd</p>
                                                    <p class="plAddress"><i class="icon-location"></i>Southaven, Mississippi</p>
                                                </div>
                                                <div class="plActions">
                                                    <a href="#" class="btn lineDefBtn sBtn">View Profile</a>
                                                    <a href="#" class="btn lineDefBtn sBtn">Connect</a>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="peopleListL">
                                                <img src="img/user-3.png" alt="">
                                            </div>
                                            <div class="peopleListR">
                                                <a href="#" class="plName">Charlotte Douglas</a>
                                                <div class="plDets">
                                                    <p>Programmer at "Clean House" Ltd</p>
                                                    <p class="plAddress"><i class="icon-location"></i>Southaven, Mississippi</p>
                                                </div>
                                                <div class="plActions">
                                                    <a href="#" class="btn lineDefBtn sBtn">View Profile</a>
                                                    <a href="#" class="btn lineDefBtn sBtn">Connect</a>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="peopleListL">
                                                <img src="img/user-4.png" alt="">
                                            </div>
                                            <div class="peopleListR">
                                                <a href="#" class="plName">Charlotte Douglas</a>
                                                <div class="plDets">
                                                    <p>Sale Manager at "Clean House" Ltd</p>
                                                    <p class="plAddress"><i class="icon-location"></i>Southaven, Mississippi</p>
                                                </div>
                                                <div class="plActions">
                                                    <a href="#" class="btn lineDefBtn sBtn">View Profile</a>
                                                    <a href="#" class="btn lineDefBtn sBtn">Connect</a>
                                                </div>
                                            </div>
                                        </li>-->
                </ul>
            </div>
        </div>
    </div>
</div>
</div> <!-- end of mainWrapper -->

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
        <div class="customScroll">
            <?php
            $fm = ActiveForm::begin([
                        'id' => 'advanced-search-form',
                        'options' => ['class' => 'form-horizontal', 'novalidate' => '']
            ]);
            ?>
            <div class="cols cols2">
                <?php $i = 0; ?>
                <div>
                    <?php foreach ($advanced as $key=>$input) { ?>
                        <?php if (!($key % 2)) { ?>
                            <div class="formRow">
                                <?php if ($input->type === 'input') { ?>
                                    <label class="customLbSt" for="<?= $input->label . '_' . $i; ?>"> <?php echo $input->label; ?> </label>
                                    <?php if ($input->numeric == '0') { ?>
                                        <?php echo Html::input('text', $input->label, NULL, ['id' => $input->label . '_' . $i, 'class' => 'formControl sForm', 'placeholder' => $input->placeholder]); ?>
                                    <?php } else { ?>
                                        <?php echo Html::input('text', $input->label, NULL, ['id' => $input->label . '_' . $i, 'class' => 'formControl sForm', 'type' => 'number', 'placeholder' => $input->placeholder]); ?>
                                    <?php } ?>
                                <?php } ?>
                                <?php if ($input->type === 'textarea') { ?>
                                    <label class="customLbSt" for="<?= $input->label . '_' . $i; ?>"><?php echo $input->label; ?></label>
                                    <?php echo Html::textarea('textarea', '', ['id' => $input->label . '_' . $i, 'class' => 'formControl sForm', 'placeholder' => $input->placeholder]); ?>
                                <?php } ?>
                                <?php if ($input->type === 'select') { ?>
                                    <label class="customLbSt" for="<?= $input->label; ?>"><?php echo $input->label; ?></label> 
                                    <?php $options = explode('-,-', $input->options); ?>
                                    <?php echo Html::dropDownList($input->label, '', $options, ['prompt' => $input->placeholder ? $input->placeholder : 'Select', 'id' => $input->label, 'class' => 'formControl sForm']); ?>
                                <?php } ?>
                                <?php if ($input->type === 'checkbox') { ?>
                                    <label class="customLbSt"> <?php echo $input->label; ?></label>
                                    <div class="checkbox">
                                        <?php $options = explode('-,-', $input->options); ?>
                                        <?php
                                        echo Html::checkboxList('checkbox', null, $options, ['class' => 'checkRadioSec', 'item' => function($index, $label, $name, $checked, $value) {
                                                return '<label for="' . $value . '_' . $index . '"><input id="' . $value . '_' . $index . '" name="' . $value . '" type="checkbox"><span>' . $label . '</span></label> ';
                                            }]);
                                        ?>
                                    </div>
                                <?php } ?>
                                <?php if ($input->type === 'checkbox') { ?>
                                    <label><?php echo $input->label; ?></label>
                                    <?php $items = explode('-,-', $input->options); ?>
                                    <div class="radio">
                                        <?php
                                        echo Html::radioList('radio', NULL, $items, ['class' => 'checkRadioSec', 'item' => function($index, $label, $name, $checked, $value) {
                                                return '<label for="' . $value . '_' . $name . '"><input id="' . $value . '_' . $name . '" name="' . $name . '" type="radio"><span>' . $label . '</span></label> ';
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
                    <?php foreach ($advanced as $key=>$input) { ?>
                        <?php if ($i % 2) { ?>
                            <div class="formRow">
                                <?php if ($input->type === 'input') { ?>
                                    <label class="customLbSt" for="<?= $input->label . '_' . $i; ?>"> <?php echo $input->label; ?> </label>
                                    <?php if ($input->numeric == '0') { ?>
                                        <?php echo Html::input('text', $input->label, NULL, ['id' => $input->label . '_' . $i, 'class' => 'formControl sForm', 'placeholder' => $input->placeholder]); ?>
                                    <?php } else { ?>
                                        <?php echo Html::input('text', $input->label, NULL, ['id' => $input->label . '_' . $i, 'class' => 'formControl sForm', 'type' => 'number', 'placeholder' => $input->placeholder]); ?>
                                    <?php } ?>
                                <?php } ?>
                                <?php if ($input->type === 'textarea') { ?>
                                    <label class="customLbSt" for="<?= $input->label . '_' . $i; ?>"><?php echo $input->label; ?></label>
                                    <?php echo Html::textarea('textarea', '', ['id' => $input->label . '_' . $i, 'class' => 'formControl sForm', 'placeholder' => $input->placeholder]); ?>
                                <?php } ?>
                                <?php if ($input->type === 'select') { ?>
                                    <label class="customLbSt" for="<?= $input->label; ?>"><?php echo $input->label; ?></label> 
                                    <?php $options = explode('-,-', $input->options); ?>
                                    <?php echo Html::dropDownList($input->label, '', $options, ['prompt' => $input->placeholder ? $input->placeholder : 'Select', 'id' => $input->label, 'class' => 'formControl sForm']); ?>
                                <?php } ?>
                                <?php if ($input->type === 'checkbox') { ?>
                                    <label class="customLbSt"> <?php echo $input->label; ?></label>
                                    <div class="checkbox">
                                        <?php $options = explode('-,-', $input->options); ?>
                                        <?php
                                        echo Html::checkboxList('checkbox', null, $options, ['class' => 'checkRadioSec', 'item' => function($index, $label, $name, $checked, $value) {
                                                return '<label for="' . $value . '_' . $index . '"><input id="' . $value . '_' . $index . '" name="' . $value . '" type="checkbox"><span>' . $label . '</span></label> ';
                                            }]);
                                        ?>
                                    </div>
                                <?php } ?>
                                <?php if ($input->type === 'checkbox') { ?>
                                    <label><?php echo $input->label; ?></label>
                                    <?php $items = explode('-,-', $input->options); ?>
                                    <div class="radio">
                                        <?php
                                        echo Html::radioList('radio', NULL, $items, ['class' => 'checkRadioSec', 'item' => function($index, $label, $name, $checked, $value) {
                                                return '<label for="' . $value . '_' . $name . '"><input id="' . $value . '_' . $name . '" name="' . $name . '" type="radio"><span>' . $label . '</span></label> ';
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
            <?php ActiveForm::end(); ?>
        </div>
        <div class="submitSect">
            <input class="btn defBtn" type="submit" value="Apply">
        </div>
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
</script>