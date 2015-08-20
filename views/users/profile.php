

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
                    <p>Clay County, Missouri, US</p>
                </li>
                <li>
                    <i class="icon-smart-phone-2"></i>
                    <p>+421 756 32 12</p>
                </li>
                <li>
                    <i class="icon-letter-mail-1"></i>
                    <p><?= $user->email ?></p>
                </li>
            </ul>
        </div>
        <div class="alignCenter">
            <a href="<?= \yii\helpers\Url::to(['users/edit']) ?>" class="btn defBtn">Edit profile</a>
        </div>
    </div>
    <div class="profileR">
        <div class="profileTabs">
            <ul class="clearAfter">
                <li class="active"><a href="<?= \yii\helpers\Url::to(['users/profile']) ?>"><i class="icon-card-user-2"></i>My Profile</a></li>
                <li><a href="#"><i class="icon-contacts"></i>Colleagues</a></li>
                <li><a href="#"><i class="icon-bell-two"></i>Notifications</a></li>
            </ul>
        </div>
        <div class="tabsContent">
            <?php foreach ($this->params['sections'] as $sectionName => $section) { ?>
                <div class="cvTimeline">
                    <h4><?= $sectionName ?></h4>

                    <?php foreach ($section as $subSectionName => $subSection) { ?>
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

                        </div>
                    <?php } ?>
                </div>
            <?php } ?>


        </div>
    </div>
</div>