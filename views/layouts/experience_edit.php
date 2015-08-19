<?php

use yii\bootstrap\ActiveForm;
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
                                        <?php $value = $this->params['user_forms'][$subSectionId][$u][$form['formId']] ?>
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
                                                <option value=''><?= $form['formPlaceholder'] ?></option>
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
                            <div class="add-item addMoreBtn">
                                <button type="button" ><i class="icon-plus"></i></button>
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