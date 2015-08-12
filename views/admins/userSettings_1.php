<?php

use \yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;
?>
<div id='aaaa'>
    <?php foreach ($sections as $section) { ?>
        <div style='margin-top: 40px;'>
            <h2 style="font-weight: bolder">
                <?php echo $section->name; ?>
            </h2>
            <a href="edit.php"></a>
            <?php foreach ($sub_sections as $sub_section) { ?>
                <div style='margin-top: 25px;'>
                    <?php if ($sub_section->section_id === $section->id) { ?>
                        <h3><?php echo $sub_section->name; ?></h3>
                        <?php foreach ($forms as $sub) { ?>    
                            <?php if ($sub->type === 'select' && $sub->sub_section_id === $sub_section->id) { ?>
                                <h5><?php echo $sub->label; ?></h5>
                                <?php $options = explode(',', $sub->options); ?>
                                <?php echo Html::dropDownList($sub->label, '', $options); ?>
                            <?php } ?>
                            <?php if ($sub->type === 'input' && $sub->sub_section_id === $sub_section->id) { ?>
                                <h5><?php echo $sub->label; ?></h5>
                                <?php echo Html::input('text', $sub->label); ?>
                            <?php } ?>
                            <?php if ($sub->type === 'checkbox' && $sub->sub_section_id === $sub_section->id) { ?>
                                <h5><?php echo $sub->label; ?></h5>
                                <?php $options = explode(',', $sub->options); ?>
                                <?php echo Html::checkboxList('checkbox', null, $options); ?>
                            <?php } ?>
                            <?php if ($sub->type === 'textarea' && $sub->sub_section_id === $sub_section->id) { ?>
                                <h5><?php echo $sub->label; ?></h5>
                                <?php echo Html::textarea('textarea'); ?>
                            <?php } ?>
                            <?php if ($sub->type === 'radio' && $sub->sub_section_id === $sub_section->id) { ?>
                                <h5><?php echo $sub->label; ?></h5>
                                <?php $options = explode(',', $sub->options); ?>
                                <?php echo Html::radioList('radio', NULL, $options); ?>
                            <?php } ?>
                        <?php } ?>  
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>
<br>
<br>
<br>
<hr>
<hr>
<div>
    <?php
    $form = ActiveForm::begin([
                'id' => 'section-form',
                'options' => ['class' => 'form-horizontal']
    ]);
    ?>

    <?= $form->field($section_model, 'name', ['inputOptions' => ['id' => 'section_name']]) ?>

    <div class="form-group">
        <?= Html::submitButton('Add section', ['id' => 'section_form_submit', 'class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
    <div><?php echo Yii::$app->getSession()->getFlash('section_success')[0]; ?></div>
</div>

<br>
<br>
<br>
<br>
<hr>
<hr>
<hr>
<div>
    <?php
    $form = ActiveForm::begin([
                'id' => 'sub-section-form',
                'options' => ['class' => 'form-horizontal'],
                'enableClientValidation' => false
    ]);
    ?>

    <?= $form->field($sub_section_model, 'name') ?>
    <?= $form->field($sub_section_model, 'section_id')->dropDownList($sections_array, ['prompt' => 'Choose'])->label('Sections'); ?>
    <?= $form->field($sub_section_model, 'multiple')->checkbox() ?>
    <br>
    <br>

    <?php
    DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.container-items', // required: css class selector
        'widgetItem' => '.item', // required: css class
        'limit' => 10, // the maximum times, an element can be cloned (default 999)
        'min' => 1, // 0 or 1 (default 1)
        'insertButton' => '.add-item', // css class
        'deleteButton' => '.remove-item', // css class
        'model' => $multiple_form_model[0],
        'formId' => 'sub-section-form',
        'formFields' => [
            'label',
            'numeric',
            'type',
            'options',
            'label_checkbox_radio',
            'placeholder',
        ],
    ]);
    ?>

    <div class="container-items" style="margin-top: 20px"><!-- widgetContainer -->
        <?php foreach ($multiple_form_model as $i => $modelAddress): ?>
            <div class="item" style="margin-top:20px"><!-- widgetBody -->
                <hr>
                <?= $form->field($modelAddress, "[{$i}]label") ?>
                <?= $form->field($modelAddress, "[{$i}]numeric")->checkbox()->label('Only integer values') ?>
                <?= $form->field($modelAddress, "[{$i}]type")->dropDownList(['select' => 'Drop down list', 'input' => 'Input', 'checkbox' => 'Checkbox', 'radio' => 'Radio', 'textarea' => 'Textarea'], ['prompt' => 'Choose']) ?>
                <?php
                DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_inner', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-loads', // required: css class selector
                    'widgetItem' => '.load-item', // required: css class
                    'insertButton' => '.add-load', // css class
                    'deleteButton' => '.del-load', // css class
                    'model' => $modelsPaymentLoads[$i][0],
                    'formId' => 'sub-section-form',
                    'formFields' => [
                        'options'
                    ],
                ]);
                ?>

                <div class="container-loads"><!-- widgetContainer -->
                    <?php foreach ($modelsPaymentLoads[$i] as $ix => $modelPaymentLoads): ?>
                        <div class="load-item"><!-- widgetBody -->
                            <?php echo $form->field($modelPaymentLoads, "[{$i}][{$ix}]options"); ?>
                        </div>
                    <?php endforeach; // end of loads loop  ?>
                </div>
                <button type="button" class="add-load btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                <?php DynamicFormWidget::end(); ?>
                <?= $form->field($modelAddress, "[{$i}]label_checkbox_radio") ?>
                <?= $form->field($modelAddress, "[{$i}]placeholder") ?>
            </div>
        <?php endforeach; ?>

    </div>
    <div style="margin-bottom: 25px">
        <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
        <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
    </div>
    <?php DynamicFormWidget::end(); ?>

    <div class="form-group">
        <?= Html::submitButton('Add section', ['id' => 'section_form_submit', 'class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
    <div><?php echo Yii::$app->getSession()->getFlash('sub_section_success')[0]; ?></div>
</div>



<div class="container">
    <div class="adminCont">
        <div class="page-header topDbleMenu clearfix">
            <h1 class="paddingtop10 pull-left">Dynamic Inputs</h1>
            <div class="pull-right">
                <button class="btn btn-primary marginbot10 pull-left" data-toggle="button"><span class="glyphicon glyphicon-plus"></span> Add Section</button>
                <button class="btn btn-primary marginbot10 pull-left" data-toggle="button"><span class="glyphicon glyphicon-plus"></span> Add Form</button>
            </div>
        </div>
        <div class="row">  
            <div class="col-md-6 padtop34">
                <ol class="dd_fields customFormCont">
                    <?php foreach ($sections as $section) { ?>
                        <div style='margin-top: 40px;'>
                            <h2 style="font-weight: bolder">
                                <?php echo $section->name; ?>
                            </h2>
                            <a href="edit.php"></a>
                            <?php foreach ($sub_sections as $sub_section) { ?>
                                <div style='margin-top: 25px;'>
                                    <?php if ($sub_section->section_id === $section->id) { ?>
                                        <h3><?php echo $sub_section->name; ?></h3>
                                        <?php foreach ($forms as $sub) { ?>    
                                            <?php if ($sub->type === 'select' && $sub->sub_section_id === $sub_section->id) { ?>
                                                <h5><?php echo $sub->label; ?></h5>
                                                <?php $options = explode(',', $sub->options); ?>
                                                <?php echo Html::dropDownList($sub->label, '', $options); ?>
                                            <?php } ?>
                                            <?php if ($sub->type === 'input' && $sub->sub_section_id === $sub_section->id) { ?>
                                                <h5><?php echo $sub->label; ?></h5>
                                                <?php echo Html::input('text', $sub->label); ?>
                                            <?php } ?>
                                            <?php if ($sub->type === 'checkbox' && $sub->sub_section_id === $sub_section->id) { ?>
                                                <h5><?php echo $sub->label; ?></h5>
                                                <?php $options = explode(',', $sub->options); ?>
                                                <?php echo Html::checkboxList('checkbox', null, $options); ?>
                                            <?php } ?>
                                            <?php if ($sub->type === 'textarea' && $sub->sub_section_id === $sub_section->id) { ?>
                                                <h5><?php echo $sub->label; ?></h5>
                                                <?php echo Html::textarea('textarea'); ?>
                                            <?php } ?>
                                            <?php if ($sub->type === 'radio' && $sub->sub_section_id === $sub_section->id) { ?>
                                                <h5><?php echo $sub->label; ?></h5>
                                                <?php $options = explode(',', $sub->options); ?>
                                                <?php echo Html::radioList('radio', NULL, $options); ?>
                                            <?php } ?>
                                        <?php } ?>  
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <li>
                        <div class="customFormItem">                                        
                            <div class="mainSecCont clearfix">
                                <span class="mainHeadSt pull-left"><i class="glyphicon glyphicon-menu-right"></i> Section Name</span>
                                <div class="controlNav pull-right">
                                    <a class="btn btn-primary btn-xs pull-left" href="#"><span class="glyphicon glyphicon-trash"></span></a>
                                    <a data-toggle="button" class="btn btn-primary btn-xs pull-left" href="#"><span class="glyphicon glyphicon-pencil"></span></a>
                                </div>
                            </div>
                            <div class="subSecCont">
                                <div class="subSecTop clearfix">
                                    <span class="subHeading pull-left"><i class="glyphicon glyphicon-menu-right"></i> Subsection Name</span>
                                    <div class="controlNav pull-right">
                                        <a class="btn btn-primary btn-xs pull-left" href="#"><span class="glyphicon glyphicon-trash"></span></a>
                                        <a data-toggle="button" class="btn btn-primary btn-xs pull-left" href="#"><span class="glyphicon glyphicon-pencil"></span></a>
                                    </div>                                            
                                </div>
                                <div class="form-group">
                                    <label class="customLbSt" for="lorem_ipsum"> Lorem Ipsum </label>
                                    <input class="form-control" value="" name="Demographic[Head of Beef]" id="lorem_ipsum" type="text" placeholder="Lorem Ipsum">
                                </div>
                                <div class="form-group">
                                    <label class="customLbSt" for="lorem_ipsum"> Lorem Ipsum </label>
                                    <div class="checkRadioSec">
                                        <label for="Y"><input id="Y" type="checkbox" name="" value="Y"><span> Y </span></label>
                                        <label for="werwqer"><input id="werwqer" type="checkbox" name="" value="werwqer"><span> werwqer </span></label>
                                        <label for="wqerwqr"><input id="wqerwqr" type="checkbox" name="" value="wqerwqr"><span> wqerwqr </span></label>
                                        <label for="wqerwqer"><input id="wqerwqer" type="checkbox" name="" value="wqerwqer"><span> wqerwqer </span></label>
                                        <label for="rrrrrr"><input id="rrrrrr" type="checkbox" name="" value="rrrrrr"><span> rrrrrr </span></label>
                                        <div class="clearFull"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="customLbSt" for="lorem_ipsum2">Lorem Ipsum 3</label> 
                                    <select class="form-control" name="">
                                        <option selected="" value=""> select value </option>
                                        <option value="A - AGCO"> A - AGCO </option>
                                        <option value="B - Case / IH"> B - Case / IH </option>
                                        <option value="C - Challenger"> C - Challenger </option>
                                        <option value="D - John Deere"> D - John Deere </option>
                                        <option value="E - Massey Ferguson"> E - Massey Ferguson </option>
                                        <option value="F - New Holland"> F - New Holland </option>
                                        <option value="G - Other"> G - Other </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <span class="customLbSt"> Lorem Ipsum 4 </span>
                                    <div class="checkRadioSec">
                                        <label for="ssss"><input id="ssss" type="radio" name="Demographic[GFGFDDD]" value="ssss"><span>ssss</span></label>
                                        <label for="sssss"><input id="sssss" type="radio" name="Demographic[GFGFDDD]" value="sssss"><span>sssss</span></label>
                                        <label for="sssss"><input id="sssss" type="radio" name="Demographic[GFGFDDD]" value="sssss"><span>sssss</span></label>
                                        <label for="ss"><input id="ss" type="radio" name="Demographic[GFGFDDD]" value="ss"><span>ss</span></label>
                                        <label for="sss"><input id="sss" type="radio" name="Demographic[GFGFDDD]" value="sss"><span>sss</span></label>
                                        <label for="ssss"><input id="ssss" type="radio" name="Demographic[GFGFDDD]" value="ssss"><span>ssss</span></label>
                                        <label for="sssss"><input id="sssss" type="radio" name="Demographic[GFGFDDD]" value="sssss"><span>sssss</span></label>
                                        <div class="clearFull"></div>                                                    
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="customLbSt" for="aaaa"> Lorem Ipsum 5 </label>
                                    <textarea class="form-control" name="Demographic[aaaa]" id="aaaa" placeholder=""></textarea>
                                </div>
                            </div>                                        
                            <div class="subSecCont">
                                <div class="subSecTop clearfix">
                                    <span class="subHeading pull-left"><i class="glyphicon glyphicon-menu-right"></i> Subsection Name2</span>
                                    <div class="controlNav pull-right">
                                        <a class="btn btn-primary btn-xs pull-left" href="#"><span class="glyphicon glyphicon-trash"></span></a>
                                        <a data-toggle="button" class="btn btn-primary btn-xs pull-left" href="#"><span class="glyphicon glyphicon-pencil"></span></a>
                                    </div>                                            
                                </div>
                                <div class="form-group">
                                    <label class="customLbSt" for="lorem_ipsum"> Lorem Ipsum </label>
                                    <div class="checkRadioSec">
                                        <label for="Y"><input id="Y" type="checkbox" name="" value="Y"><span> Y </span></label>
                                        <label for="werwqer"><input id="werwqer" type="checkbox" name="" value="werwqer"><span> werwqer </span></label>
                                        <label for="wqerwqr"><input id="wqerwqr" type="checkbox" name="" value="wqerwqr"><span> wqerwqr </span></label>
                                        <label for="wqerwqer"><input id="wqerwqer" type="checkbox" name="" value="wqerwqer"><span> wqerwqer </span></label>
                                        <label for="rrrrrr"><input id="rrrrrr" type="checkbox" name="" value="rrrrrr"><span> rrrrrr </span></label>
                                        <div class="clearFull"></div>
                                    </div>
                                </div>
                            </div>                                        
                            <div class="subSecCont">
                                <div class="subSecTop clearfix">
                                    <span class="subHeading pull-left"><i class="glyphicon glyphicon-menu-right"></i> Subsection Name3</span>
                                    <div class="controlNav pull-right">
                                        <a class="btn btn-primary btn-xs pull-left" href="#"><span class="glyphicon glyphicon-trash"></span></a>
                                        <a data-toggle="button" class="btn btn-primary btn-xs pull-left" href="#"><span class="glyphicon glyphicon-pencil"></span></a>
                                    </div>                                            
                                </div>
                                <div class="form-group">
                                    <label class="customLbSt" for="lorem_ipsum2">Lorem Ipsum 3</label> 
                                    <select class="form-control" name="">
                                        <option selected="" value=""> select value </option>
                                        <option value="A - AGCO"> A - AGCO </option>
                                        <option value="B - Case / IH"> B - Case / IH </option>
                                        <option value="C - Challenger"> C - Challenger </option>
                                        <option value="D - John Deere"> D - John Deere </option>
                                        <option value="E - Massey Ferguson"> E - Massey Ferguson </option>
                                        <option value="F - New Holland"> F - New Holland </option>
                                        <option value="G - Other"> G - Other </option>
                                    </select>
                                </div>
                            </div>                                        
                        </div>
                        <div style="display: none;" class="grey_form data_update_form">
                            <h4>Update Lorem Ipsum</h4>
                            <form action="" method="post">
                                <p class="note">Fields with <span class="required">*</span> are required.</p>
                                <div class="form-group">
                                    <label for="demographicDataKeys_label" class="control-label">Name</label>			
                                    <input size="60" maxlength="255" value="Head of Beef" name="DemographicDataKeys[label]" id="demographicDataKeys_label" class="form-control" type="text">
                                </div>
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label for="DemographicDataKeys_type" class="required">
                                            <input type="checkbox" class="is_int_checkbox" name="DemographicDataKeys[is_int]" value="1">
                                            Only integer values
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="DemographicDataKeys_placeholder" class="control-label">Placeholder</label>
                                    <input size="60" maxlength="255" name="DemographicDataKeys[placeholder]" id="demographicDataKeys_placeholder" class="form-control" type="text" value="">
                                </div>
                                <button type="submit" class="btn btn-default" id="demo_update_form_save">Save</button>
                            </form>
                        </div>
                    </li>
                    <li>
                        <div class="customFormItem">                                        
                            <div class="mainSecCont clearfix">
                                <span class="mainHeadSt pull-left"><i class="glyphicon glyphicon-menu-right"></i> Section Name</span>
                                <div class="controlNav pull-right">
                                    <a class="btn btn-primary btn-xs pull-left" href="#"><span class="glyphicon glyphicon-trash"></span></a>
                                    <a data-toggle="button" class="btn btn-primary btn-xs pull-left" href="#"><span class="glyphicon glyphicon-pencil"></span></a>
                                </div>
                            </div>
                            <div class="subSecCont">
                                <div class="subSecTop clearfix">
                                    <span class="subHeading pull-left"><i class="glyphicon glyphicon-menu-right"></i> Subsection Name</span>
                                    <div class="controlNav pull-right">
                                        <a class="btn btn-primary btn-xs pull-left" href="#"><span class="glyphicon glyphicon-trash"></span></a>
                                        <a data-toggle="button" class="btn btn-primary btn-xs pull-left" href="#"><span class="glyphicon glyphicon-pencil"></span></a>
                                    </div>                                            
                                </div>
                                <div class="form-group">
                                    <label class="customLbSt" for="lorem_ipsum"> Lorem Ipsum </label>
                                    <div class="checkRadioSec">
                                        <label for="Y"><input id="Y" type="checkbox" name="" value="Y"><span> Y </span></label>
                                        <label for="werwqer"><input id="werwqer" type="checkbox" name="" value="werwqer"><span> werwqer </span></label>
                                        <label for="wqerwqr"><input id="wqerwqr" type="checkbox" name="" value="wqerwqr"><span> wqerwqr </span></label>
                                        <label for="wqerwqer"><input id="wqerwqer" type="checkbox" name="" value="wqerwqer"><span> wqerwqer </span></label>
                                        <label for="rrrrrr"><input id="rrrrrr" type="checkbox" name="" value="rrrrrr"><span> rrrrrr </span></label>
                                        <div class="clearFull"></div>
                                    </div>
                                </div>
                            </div>                                        
                        </div>
                        <div style="display: none;" class="grey_form data_update_form">
                            <h4>Update Lorem Ipsum2</h4>
                            <form action="/demographicData/update/24" method="post">
                                <p class="note">Fields with <span class="required">*</span> are required.</p>
                                <div class="form-group">
                                    <label for="demographicDataKeys_label" class="control-label">Name</label>			
                                    <input size="60" maxlength="255" value="Irrigation Ind" name="DemographicDataKeys[label]" id="demographicDataKeys_label" class="form-control" type="text">
                                </div>
                                <div class="form-group dd_checkrad">
                                    <label class="control-label">Option</label>

                                    <div class="optionDelSec"><input size="60" maxlength="255" name="label[]" class="form-control" type="text" id="24" value="Y"><a class="delOptionBtn"></a></div>

                                    <div class="optionDelSec"><input size="60" maxlength="255" name="label[]" class="form-control" type="text" id="24" value="werwqer"><a class="delOptionBtn"></a></div>

                                    <div class="optionDelSec"><input size="60" maxlength="255" name="label[]" class="form-control" type="text" id="24" value="wqerwqr"><a class="delOptionBtn"></a></div>

                                    <div class="optionDelSec"><input size="60" maxlength="255" name="label[]" class="form-control" type="text" id="24" value="wqerwqer"><a class="delOptionBtn"></a></div>

                                    <div class="optionDelSec"><input size="60" maxlength="255" name="label[]" class="form-control" type="text" id="24" value="rrrrrr"><a class="delOptionBtn"></a></div>
                                    <a class="btn btn-primary btn-xs add_option_link_update" href="#"><span class="glyphicon glyphicon-plus"></span> Add option</a>
                                </div>
                                <button type="submit" class="btn btn-default" id="demo_update_form_save">Save</button>
                            </form>
                        </div>
                    </li>
                </ol>
            </div>

            <div class="col-md-6">                            
                <!--Create Demographic Data Keys-->
                <div id="dd_create_form" style="" class="clear grey_form">
                    <form action="" method="post">
                        <div class="fromTopStat">
                            <h4>Create Form Data</h4>
                            <p class="note">Fields with <span class="required">*</span> are required.</p>
                            <div class="form-group">
                                <label for="DemographicDataKeys_label">Main SubSection Title</label>			
                                <input size="60" maxlength="255" name="DemographicDataKeys[label]" id="DemographicDataKeys_label" type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="sel_sec_form" class="required">Choose a Section</label>
                                <select name="sel_sec_form" id="sel_sec_form" class="form-control">
                                    <option value="text" selected="selected">Select Section</option>
                                    <option value="text">Education</option>
                                    <option value="textarea">Experience</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <div class="checkbox">
                                    <label for="DemographicDataKeys_type" class="required"><input type="checkbox" id="is_int_checkbox" name="DemographicDataKeys[is_int]" value="1"> Allow multiple on user side</label>  
                                </div>
                            </div>
                        </div>
                        <div class="formSecRep">
                            <div class="form-group">
                                <label for="DemographicDataKeys_label">Label</label>			
                                <input size="60" maxlength="255" name="DemographicDataKeys[label]" id="DemographicDataKeys_label" type="text" class="form-control">
                            </div>                                        
                            <div class="form-group">
                                <div class="checkbox">
                                    <label for="DemographicDataKeys_type" class="required"><input type="checkbox" id="is_int_checkbox" name="DemographicDataKeys[is_int]" value="1"> Only integer values</label>  
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="DemographicDataKeys_type" class="required">Type <span class="required">*</span></label>
                                <select name="DemographicDataKeys[type]" id="DemographicDataKeys_type" class="form-control">
                                    <option value="text" selected="selected">text</option>
                                    <option value="textarea">textarea</option>
                                    <option value="select">select</option>
                                    <option value="checkbox">checkbox</option>
                                    <option value="radio">radio</option>
                                </select>
                            </div>
                            <div id="option_list" class="option_lst">
                                <div class="form-group">
                                    <label for="DemographicDataOptions_Option">Option</label>
                                    <input size="60" maxlength="255" name="label[]" id="DemographicDataOptions_label" type="text" disabled="disabled" class="form-control">
                                    <div class="optionDelSec"><input type="text" class="added_option form-control" id="label" name="label[]" maxlength="255" size="60"><a class="delOptionBtn"></a></div>
                                </div>
                                <div class="text-left optionBtn">
                                    <a id="add_option_link" href="#" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-plus"></span> Add option</a>
                                </div>                                            
                            </div>
                            <div class="form-group">
                                <label for="DemographicDataKeys_placeholder">Label for Checkbox/Radio</label>
                                <input size="60" maxlength="255" name="DemographicDataKeys[placeholder]" id="DemographicDataKeys_placeholder" type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="DemographicDataKeys_placeholder">Placeholder</label>
                                <input size="60" maxlength="255" name="DemographicDataKeys[placeholder]" id="DemographicDataKeys_placeholder" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="itemSecBot clearfix">
                            <button type="submit" class="btn btn-default pull-left">Create</button>
                            <button type="submit" class="btn btn-default pull-right"><span class="glyphicon glyphicon-plus"></span> Add Another Input</button>
                        </div>                                    
                    </form>
                </div>
            </div>
        </div>                    
    </div>
</div>
