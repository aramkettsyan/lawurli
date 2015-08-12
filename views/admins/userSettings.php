<?php

use \yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;
?>
<div class="container">
    <div class="adminCont">
        <div class="page-header topDbleMenu clearfix">
            <h1 class="paddingtop10 pull-left">Dynamic Inputs</h1>
            <div class="pull-right">
                <?php if (!$isUpdate) { ?>
                    <button class="btn btn-primary marginbot10 pull-left" data-toggle="button" id='addSection'><span class="glyphicon glyphicon-plus"></span> Add Section</button>
                    <button class="btn btn-primary marginbot10 pull-left" data-toggle="button" id='addForm'><span class="glyphicon glyphicon-plus"></span> Add Form</button>
                <?php } else { ?>
                    <?php echo Html::a('<span class="glyphicon glyphicon-plus"></span>Add Section', \yii\helpers\Url::to(['admins/redirect-and-set-flash', 'data' => 'section']), ['class' => 'btn btn-primary marginbot10 pull-left', "data-toggle" => "button"]) ?>
                    <?php echo Html::a('<span class="glyphicon glyphicon-plus"></span>Add Form', \yii\helpers\Url::to(['admins/redirect-and-set-flash', 'data' => 'sub-section']), ['class' => 'btn btn-primary marginbot10 pull-left', "data-toggle" => "button"]) ?>
                <?php } ?>
            </div>
        </div>
        <div class="row">  
            <div class="col-md-6 padtop34">
                <ol class="dd_fields customFormCont">
                    <div style="color: green"><?php echo \Yii::$app->getSession()->getFlash('deleteSuccess'); ?></div>
                    <div style="color: green"><?php echo \Yii::$app->getSession()->getFlash('sub_section_success')[0]; ?></div>
                    <div style="color: green"><?php echo \Yii::$app->getSession()->getFlash('section_success')[0]; ?></div>

                    <?php if (empty($sections)) { ?>
                        <p>There are no sections!</p>
                    <?php } ?>
                    <?php foreach ($sections as $section) { ?>
                        <li>
                            <div class="customFormItem"> 
                                <div class="mainSecCont clearfix">
                                    <span class="mainHeadSt pull-left"><i class="glyphicon glyphicon-menu-right"></i><?php echo $section->name; ?></span>
                                    <div class="controlNav pull-right">
                                        <!--<a class="btn btn-primary btn-xs pull-left" href="/admins/delete-section/1"><span class="glyphicon glyphicon-trash"></span></a>-->
                                        <?php echo Html::a('<span class="glyphicon glyphicon-trash"></span>', \yii\helpers\Url::to(['admins/delete-section', 'id' => $section->id]), ['class' => 'btn btn-primary btn-xs pull-left']) ?>
                                        <?php echo Html::a('<span class="glyphicon glyphicon-pencil"></span>', \yii\helpers\Url::to(['admins/user-settings', 'section' => 'section', 'id' => $section->id]), ['class' => 'btn btn-primary btn-xs pull-left']) ?>
                                    </div>
                                </div>
                                <?php foreach ($sub_sections as $sub_section) { ?>
                                    <div class="subSecCont">
                                        <?php if ($sub_section->section_id === $section->id) { ?>
                                            <div class="subSecTop clearfix">
                                                <span class="subHeading pull-left"><i class="glyphicon glyphicon-menu-right"></i><?php echo $sub_section->name; ?></span>
                                                <div class="controlNav pull-right">
                                                    <?php echo Html::a('<span class="glyphicon glyphicon-trash"></span>', \yii\helpers\Url::to(['admins/delete-sub-section', 'id' => $sub_section->id]), ['class' => 'btn btn-primary btn-xs pull-left']) ?>
                                                    <?php echo Html::a('<span class="glyphicon glyphicon-pencil"></span>', \yii\helpers\Url::to(['admins/user-settings', 'section' => 'sub-section', 'id' => $sub_section->id]), ['class' => 'btn btn-primary btn-xs pull-left']) ?>
                                                </div>                                            
                                            </div>
                                            <?php foreach ($forms as $sub) { ?>   
                                                <div class="form-group">
                                                    <?php if ($sub->type === 'select' && $sub->sub_section_id === $sub_section->id) { ?>

                                                        <label class="customLbSt" for="<?= $sub->label; ?>"><?php echo $sub->label; ?></label> 
                                                        <?php $options = explode(',', $sub->options); ?>
                                                        <?php echo Html::dropDownList($sub->label, '', $options, ['prompt' => $sub->placeholder, 'id' => $sub->label, 'class' => 'form-control']); ?>

                                                    <?php } ?>
                                                    <?php if ($sub->type === 'input' && $sub->sub_section_id === $sub_section->id) { ?>
                                                        <label class="customLbSt" for="<?= $sub->label; ?>"> <?php echo $sub->label; ?> </label>
                                                        <?php if ($sub->numeric == '0') { ?>
                                                            <?php echo Html::input('text', $sub->label, NULL, ['id' => $sub->label, 'class' => 'form-control', 'placeholder' => $sub->placeholder]); ?>
                                                        <?php } else { ?>
                                                            <?php echo Html::input('text', $sub->label, NULL, ['id' => $sub->label, 'class' => 'form-control', 'type' => 'number', 'placeholder' => $sub->placeholder]); ?>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    <?php if ($sub->type === 'checkbox' && $sub->sub_section_id === $sub_section->id) { ?>
                                                        <label class="customLbSt" for=""> <?php echo $sub->label; ?></label>
                                                        <?php $options = explode(',', $sub->options); ?>
                                                        <?php
                                                        echo Html::checkboxList('checkbox', null, $options, ['class' => 'checkRadioSec', 'item' => function($index, $label, $name, $checked, $value) {
                                                                return '<label for="' . $value . '"><input id="' . $value . '" type="checkbox"><span>' . $label . '</span></label> ';
                                                            }]);
                                                        ?>
                                                        <div class="clearFull"></div>
                                                    <?php } ?>
                                                    <?php if ($sub->type === 'textarea' && $sub->sub_section_id === $sub_section->id) { ?>
                                                        <label class="customLbSt" for="<?= $sub->label; ?>"><?php echo $sub->label; ?></label>
                                                        <?php echo Html::textarea('textarea', '', ['id' => $sub->label, 'class' => 'form-control', 'placeholder' => $sub->placeholder]); ?>
                                                    <?php } ?>
                                                    <?php if ($sub->type === 'radio' && $sub->sub_section_id === $sub_section->id) { ?>
                                                        <span class="customLbSt"><?php echo $sub->label; ?></span>
                                                        <?php $items = explode(',', $sub->options); ?>
                                                        <?php ?>
                                                        <?php
                                                        echo Html::radioList('radio', NULL, $items, ['class' => 'checkRadioSec', 'item' => function($index, $label, $name, $checked, $value) {
                                                                return '<label for="' . $value . '"><input id="' . $value . '" type="radio"><span>' . $label . '</span></label> ';
                                                            }]);
                                                        ?>
                                                        <div class="clearFull"></div>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>  
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </li>
                    <?php } ?>
                </ol>
            </div>




            <div class="col-md-6">          
                <div id="dd_create_section" style="display: none" class="clear grey_form">
                    <?php
                    $form = ActiveForm::begin([
                                'id' => 'section-form',
                                'options' => ['class' => '']
                    ]);
                    ?>
                    <div class="fromTopStat">
                        <h4>Create Section</h4>
                        <p class="note">Fields with <span class="required">*</span> are required.</p>
                        <label for="DemographicDataKeys_label">Section Title</label>			
                        <?= $form->field($section_model, 'name')->textInput(['class' => 'form-control', "size" => "60", "maxlength" => "255", "id" => "DemographicDataKeys_label"])->label(false) ?>
                    </div>
                    <div class="itemSecBot clearfix">
                        <?= Html::submitButton('Create', ['id' => 'section_form_submit', 'class' => 'btn btn-default pull-left']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
                <div id="dd_create_form" style="display: none" class="clear grey_form">
                    <?php
                    $form = ActiveForm::begin([
                                'id' => 'sub-section-form',
                                'options' => [],
                                'enableClientValidation' => false
                    ]);
                    ?>
                    <div class="fromTopStat">
                        <h4>Create Form Data</h4>
                        <p class="note">Fields with <span class="required">*</span> are required.</p>
                        <label for="DemographicDataKeys_label">Main SubSection Title</label>			
                        <!--<input size="60" maxlength="255" name="DemographicDataKeys[label]" id="DemographicDataKeys_label" type="text" class="form-control">-->
                        <?= $form->field($sub_section_model, 'name')->textInput(['class' => 'form-control', "size" => "60", "maxlength" => "255", "id" => "DemographicDataKeys_label"])->label(false) ?>


                        <label for="sel_sec_form" class="required">Choose a Section</label>
                        <?= $form->field($sub_section_model, 'section_id')->dropDownList($sections_array, ['prompt' => 'Select Section'])->label(false); ?>
<!--                                <select name="sel_sec_form" id="sel_sec_form" class="form-control">
                            <option value="text" selected="selected">Select Section</option>
                            <option value="text">Education</option>
                            <option value="textarea">Experience</option>
                        </select>-->

                        <?=
                        $form->field($sub_section_model, 'multiple', ['options' =>
                            [
                                'tag' => 'div',
                                'class' => 'form-group'
                    ]])->checkbox()->label('Allow multiple on user side')
                        ?> 
                    </div>




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
                        'model' => $multiple_form_model[0],
                        'formId' => 'sub-section-form',
                        'formFields' => [
                            'label',
                            'numeric',
                            'type',
                            'options',
                            'placeholder'
                        ],
                    ]);
                    ?>

                    <div class="formSecRep"> 
                        <?php foreach ($multiple_form_model as $i => $modelAddress): ?>
                            <div class='item'>
                                <?php echo $form->field($modelAddress, "[{$i}]sub_section_id")->hiddenInput()->label(false); ?>
                                <?php echo $form->field($modelAddress, "[{$i}]id")->hiddenInput()->label(false); ?>
                                <?=
                                $form->field($modelAddress, "[{$i}]label")->textInput(['maxlength' => '255', 'size' => 60, 'class' => 'form-control input_label'])->label('label')
                                ?>
                                <?=
                                $form->field($modelAddress, "[{$i}]numeric", ['options' =>
                                    [
                                        'tag' => 'div',
                                        'class' => 'form-group numeric'
                                    ]
                                ])->checkbox()->label('Only integer values')
                                ?> 
                                <?php //$form->field($modelAddress, "[{$i}]numeric")->checkbox()->label('Only integer values') ?>
                                <?php // $form->field($modelAddress, "[{$i}]type")->dropDownList(['select' => 'Drop down list', 'input' => 'Input', 'checkbox' => 'Checkbox', 'radio' => 'Radio', 'textarea' => 'Textarea'], ['prompt' => 'Choose']) ?>
                                <label for="sel_sec_form" class="required">Type</label>
                                <?= $form->field($modelAddress, "[{$i}]type")->dropDownList(['select' => 'Drop down list', 'input' => 'Input', 'checkbox' => 'Checkbox', 'radio' => 'Radio', 'textarea' => 'Textarea'], ['prompt' => 'Select Section', 'class' => 'drop_down_list form-control'])->label(false); ?>
                                
                                <?php
                                DynamicFormWidget::begin([
                                    'widgetContainer' => 'dynamicform_inner', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                                    'widgetBody' => '.container-loads', // required: css class selector
                                    'widgetItem' => '.load-item', // required: css class
                                    'insertButton' => '.add-load', // css class
                                    'element_n' => $i, // css class
                                    'deleteButton' => '.del-load', // css class
                                    'model' => $modelsFormsForm[$i][0],
                                    'formId' => 'sub-section-form',
                                    'formFields' => [
                                        'options'
                                    ],
                                ]);
                                ?>

                                <div id="option_list" class="option_lst container-loads">
                                    <?php if (isset($modelAddress->getErrors('options')[0])) { ?>
                                        <label style="color:#a94442" for="DemographicDataOptions_Option">Option</label>
                                    <?php } ?>
                                    <input size="60" maxlength="255" name="label[]" style="display: none" type="text" disabled="disabled" class="form-control hiddenInput">
                                    <?php foreach ($modelsFormsForm[$i] as $ix => $modelPaymentLoads): ?>
                                        <div class='load-item mBot10'>
                                            <?php
                                            echo $form->field($modelPaymentLoads, "[{$i}][{$ix}]options", [
                                                'template' => "{input} <a class='delOptionBtn del-load'></a>",
                                                'options' => [
                                                    'class' => 'optionDelSec options'
                                        ]])->textInput(['maxlength' => '255', 'size' => 60, 'class' => 'added_option form-control'])->label(false);
                                            ?>
                                        </div>
                                    <?php endforeach; // end of loads loop    ?>

                                </div>
                                <?php foreach ($modelAddress->getErrors('options') as $error) { ?>
                                    <p style="color:#a94442" id="options_error"><?php echo $error; ?></p>
                                <?php } ?>
                                <div class="text-left optionBtn">
                                    <a id="add_option_link" class="add-load btn btn-primary btn-xs"><span class="glyphicon glyphicon-plus"></span> Add option</a>
                                </div>    
                                <?php DynamicFormWidget::end(); ?>
                                <?= $form->field($modelAddress, "[{$i}]placeholder")->textInput(['class' => 'placeholder form-control']) ?>
                            </div>
                            <hr>
                            <hr>
                        <?php endforeach; ?>

                    </div>
                    <!--                    <div style="margin-bottom: 25px">
                                            <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                            <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                        </div>-->

                    <div class="itemSecBot clearfix">
                        <?php if ($isUpdate) { ?>
                            <?= Html::submitButton('Update', ['id' => 'section_form_submit', 'class' => 'btn btn-default pull-left']) ?>
                        <?php } else { ?>
                            <?= Html::submitButton('Create', ['id' => 'section_form_submit', 'class' => 'btn btn-default pull-left']) ?>
                        <?php } ?>
                        <button type='button' class="add-item btn btn-default pull-right"><span class="glyphicon glyphicon-plus"></span> Add Another Input</button>
                    </div> 
                    <?php DynamicFormWidget::end(); ?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>                    
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        var showSection = <?= (Yii::$app->session->getHasSessionId('showSection') && Yii::$app->session->readSession('showSection')) ? Yii::$app->session->readSession('showSection') : 'false' ?>;
        var showSubSection = <?= (Yii::$app->session->getHasSessionId('showSubSection') && Yii::$app->session->readSession('showSubSection')) ? Yii::$app->session->readSession('showSubSection') : 'false' ?>;
<?php
\Yii::$app->session->destroySession('showSection');
\Yii::$app->session->destroySession('showSubSection');
?>
        if (showSection) {
            $('#dd_create_section').show();
        } else if (showSubSection) {
            $('#dd_create_form').show();
        }
        $('#addSection').on('click', function () {
            $('#dd_create_form').hide();
            $('#dd_create_section').show();
        });
        $('#addForm').on('click', function () {
            $('#dd_create_section').hide();
            $('#dd_create_form').show();
        });
        var i = 0;
        $('.container-loads').each(function () {
            $(this).find('.options:first').removeClass();
            $(this).find('.added_option:first').removeClass().addClass('form-control');
            $(this).find('.del-load:first').css('display', 'none');
            i++;
        });

        $('.drop_down_list').each(function () {
            if ($(this).val() === 'select') {
                $(this).parent().parent().find('.load-item,.add-load').show();
                $(this).parent().parent().find('.hiddenInput').hide();
                $(this).parent().parent().find('.placeholder').prop('disabled', false);
            }
            if ($(this).val() === 'input') {
                $(this).parent().parent().find('.load-item,.add-load').hide();
                $(this).parent().parent().find('.hiddenInput').show();
                $(this).parent().parent().find('.placeholder').prop('disabled', false);
            }
            if ($(this).val() === 'radio') {
                $(this).parent().parent().find('.load-item,.add-load').show();
                $(this).parent().parent().find('.hiddenInput').hide();
                $(this).parent().parent().find('.placeholder').prop('disabled', true);
            }
            if ($(this).val() === 'checkbox') {
                $(this).parent().parent().find('.load-item,.add-load').show();
                $(this).parent().parent().find('.hiddenInput').hide();
                $(this).parent().parent().find('.placeholder').prop('disabled', true);
            }
            if ($(this).val() === 'textarea') {
                $(this).parent().parent().find('.load-item,.add-load').hide();
                $(this).parent().parent().find('.hiddenInput').show();
                $(this).parent().parent().find('.placeholder').prop('disabled', false);
            }
        });

        $('.drop_down_list').on('change', function () {
            if ($(this).val() === 'select') {
                $(this).parent().parent().find('.load-item,.add-load').show();
                $(this).parent().parent().find('.hiddenInput').hide();
                $(this).parent().parent().find('.placeholder').prop('disabled', false);
            }
            if ($(this).val() === 'input') {
                $(this).parent().parent().find('.load-item,.add-load').hide();
                $(this).parent().parent().find('#options_error').hide();
                $(this).parent().parent().find('.hiddenInput').show();
                $(this).parent().parent().find('.placeholder').prop('disabled', false);
            }
            if ($(this).val() === 'radio') {
                $(this).parent().parent().find('.load-item,.add-load').show();
                $(this).parent().parent().find('.hiddenInput').hide();
                $(this).parent().parent().find('.placeholder').prop('disabled', true);
            }
            if ($(this).val() === 'checkbox') {
                $(this).parent().parent().find('.load-item,.add-load').show();
                $(this).parent().parent().find('.hiddenInput').hide();
                $(this).parent().parent().find('.placeholder').prop('disabled', true);
            }
            if ($(this).val() === 'textarea') {
                $(this).parent().parent().find('.load-item,.add-load').hide();
                $(this).parent().parent().find('#options_error').hide();
                $(this).parent().parent().find('.hiddenInput').show();
                $(this).parent().parent().find('.placeholder').prop('disabled', false);
            }
        });
        
        var j = 1;
        
        $('.add-item').on('click',function(){
            j = 0;
        });
        
        
        $('.item').bind("DOMSubtreeModified", function () {
            if (j === 0) {
                $('.drop_down_list').on('change', function () {
                    if ($(this).val() === 'select') {
                        $(this).parent().parent().find('.load-item,.add-load').show();
                        $(this).parent().parent().find('.hiddenInput').hide();
                        $(this).parent().parent().find('.placeholder').prop('disabled', false);
                    }
                    if ($(this).val() === 'input') {
                        $(this).parent().parent().find('.load-item,.add-load').hide();
                        $(this).parent().parent().find('#options_error').hide();
                        $(this).parent().parent().find('.hiddenInput').show();
                        $(this).parent().parent().find('.placeholder').prop('disabled', false);
                    }
                    if ($(this).val() === 'radio') {
                        $(this).parent().parent().find('.load-item,.add-load').show();
                        $(this).parent().parent().find('.hiddenInput').hide();
                        $(this).parent().parent().find('.placeholder').prop('disabled', true);
                    }
                    if ($(this).val() === 'checkbox') {
                        $(this).parent().parent().find('.load-item,.add-load').show();
                        $(this).parent().parent().find('.hiddenInput').hide();
                        $(this).parent().parent().find('.placeholder').prop('disabled', true);
                    }
                    if ($(this).val() === 'textarea') {
                        $(this).parent().parent().find('.load-item,.add-load').hide();
                        $(this).parent().parent().find('#options_error').hide();
                        $(this).parent().parent().find('.hiddenInput').show();
                        $(this).parent().parent().find('.placeholder').prop('disabled', false);
                    }

                });
            }
            j++;
        });
    });


</script>