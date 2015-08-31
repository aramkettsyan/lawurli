<?php

use yii\widgets\ActiveForm;
?>

<?php
$fm = ActiveForm::begin([
            'id' => 'edit-form',
            'options' => ['class' => 'form-horizontal', 'novalidate' => '']
        ]);
?>

<?php foreach ($this->params['sections'] as $sectionName => $section) { ?>
    <div class="cvTimeline">
        <h4><?= $sectionName ?></h4>

        <?php foreach ($section as $subSectionName => $subSection) { ?>
            <div class="cvSub sub_section">
                <div class="cvSubLabel">
                    <h5><?= $subSectionName ?></h5>
                </div>
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
                            <li class="formSecRep">
                                <button class="delCvItem deleteForm singleForm"><i class="icon-cross-mark"></i></button>
                                <?php foreach ($subSection as $key => $form) { ?>
                                    <?php if ($key === 0) { ?>
                                        <?php continue; ?>
                                    <?php } ?>
                                    <?php $value = ''; ?>
                                    <?php if (isset($this->params['user_forms'][$subSectionId][$u][$form['formId']])) { ?>
                                        <?php $value = Html::encode($this->params['user_forms'][$subSectionId][$u][$form['formId']]) ?>
                                    <?php } ?>
                                    <div class = 'item formRow'>
                                        <?php if ($form['formLabel'] !== null) { ?>
                                            <label><?php echo $form['formLabel'] ?></label>
                                        <?php } ?>
                                        <?php if ($form['formType'] === 'input') { ?>
                                            <?php $type = $form['formNumeric'] == 0 ? 'text' : 'number' ?>
                                            <input class='textInput formControl' form-id="<?= $form['formId'] ?>" index="<?= $i ?>" value="<?= $value ?>" name="Users[custom_fields][<?= $form['formId'] ?>][]" placeholder="<?= $form['formPlaceholder'] ?>" type="<?= $type ?>" />
                                        <?php } ?>
                                        <?php if ($form['formType'] === 'textarea') { ?>
                                            <textarea class='inputTextarea formControl' form-id="<?= $form['formId'] ?>" index="<?= $i ?>" name="Users[custom_fields][<?= $form['formId'] ?>][]" placeholder="<?= $form['formPlaceholder'] ?>"><?= $value ?></textarea>
                                        <?php } ?>
                                        <?php if ($form['formType'] === 'select') { ?>
                                            <?php $options = explode('-,-', $form['formOptions']); ?>
                                            <select form-id="<?= $form['formId'] ?>" index="<?= $i ?>" class='inputSelect formControl' name="Users[custom_fields][<?= $form['formId'] ?>][]">
                                                <option value=''><?= $form['formLabel'] ?></option>
                                                <?php foreach ($options as $option) { ?>
                                                    <option <?= ($value === $option) ? 'selected="selected"' : '' ?> value="<?= $option ?>"><?= $option ?></option>
                                                <?php } ?>
                                            </select>
                                        <?php } ?>
                                        <?php if ($form['formType'] === 'checkbox') { ?>
                                            <?php $options = explode('-,-', $form['formOptions']); ?>
                                            <?php foreach ($options as $option) { ?>
                                                <?php $checked = false; ?>
                                                <?php if (isset($this->params['user_forms'][$subSectionId][$u][$form['formId']])) { ?>
                                                    <?php if (is_array($this->params['user_forms'][$subSectionId][$u][$form['formId']])) { ?>
                                                        <?php if (in_array($option, $this->params['user_forms'][$subSectionId][$u][$form['formId']])) { ?>
                                                            <?php $checked = true; ?>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <?php if ($option === $this->params['user_forms'][$subSectionId][$u][$form['formId']]) { ?>
                                                            <?php $checked = true; ?>
                                                        <?php } ?>   
                                                    <?php } ?>
                                                <?php } ?>
                                                <div class="checkbox checkboxInl">
                                                    <label><input form-id="<?= $form['formId'] ?>" index="<?= $i ?>" <?= $checked === true ? 'checked' : '' ?> class='inputCheckbox' name="Users[custom_fields][<?= $form['formId'] ?>][<?= $i ?>][]" value="<?= $option ?>" type="<?= $form['formType'] ?>" /><?= $option ?></label>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                        <?php if ($form['formType'] === 'radio') { ?>
                                            <?php $options = explode('-,-', $form['formOptions']); ?>
                                            <?php foreach ($options as $option) { ?>
                                                <div class="radio radioInl">
                                                    <label><input form-id="<?= $form['formId'] ?>" index="<?= $i ?>" <?= $option === $value ? 'checked' : '' ?> class='inputRadio' name="Users[custom_fields][<?= $form['formId'] ?>][<?= $i ?>]" value="<?= $option ?>" type="<?= $form['formType'] ?>" /><?= $option ?></label>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                        <p class="message"></p>
                                    </div>
                                    <?php $value = ''; ?>
                                <?php } ?>
                            </li>

                            <?php $i++; ?>
                        <?php } ?>
                        <?php if ($subSection['0']['subMultiple'] === '1') { ?>
                            <div class="addMWrapper">
                                <div class="add-item addMoreBtn">
                                    <button type="button" ><i class="icon-plus"></i></button>
                                </div>
                            </div>
                        <?php } ?>
                    </ul>

                </div>

            </div>
        <?php } ?>
    </div>
<?php } ?>

<div class="submitSect">
    <input type="submit" class="btn defBtn" value="Save">
</div>

<?php ActiveForm::end(); ?>


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
                $(this).css('borderColor', '#cccccc');
                $(this).parent().find('.message').html('');
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
            $(this).css('borderColor', '#cccccc');
            $(this).parent().find('.message').html('');
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
            $(this).parent().parent().find('.deleteForm').removeClass('singleForm');
            var parent = $(this).parent().parent();
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
            $(this).parent().appendTo(parent);
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