<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Html::encode($user->first_name) . ' ' . Html::encode($user->last_name);
?>

<?php Yii::$app->view->params['user'] = $user; ?>
<?php Yii::$app->view->params['sections'] = $sections; ?>
<?php Yii::$app->view->params['user_forms'] = $user_forms; ?>
<div class="container mainContainer withSideBar">
    <div class="profileL">
        <div class="userImage">
            <?php $filename = \Yii::getAlias('@webroot') . '/images/users_images/' . Html::encode($user->image); ?>
            <?php if (is_file($filename)) { ?>
                <img src="/images/user-1.png">
                <span class="profileImage" style="background-image: url('<?php echo \Yii::getAlias('@web') . '/images/users_images/' . $user->image; ?>')"></span>
            <?php } else { ?>
                <img src="/images/user-1.png">
                <span class="profileImage" style="background-image: url('<?php echo \Yii::getAlias('@web') . '/images/users_images/default.png'; ?>')"></span>
            <?php } ?>
        </div>
        <p style="color:green;display: none" id="imageUploadSuccess">Image uploaded successfully!</p>
        <p style="color:red;display: none" id="imageUploadError"></p>

        <div class="userDetails">
            <h3 class="userName"><?= Html::encode($user->first_name) ?> <?= Html::encode($user->last_name) ?></h3>
            <ul class="listWithIcons">
                <?php if ($user->location) { ?>
                    <li>
                        <i class="icon-location"></i>
                        <p><?= Html::encode($user->location) ?></p>
                    </li>
                <?php } ?>
                <?php if ($user->phone) { ?>
                    <li>
                        <i class="icon-smart-phone-2"></i>
                        <p><?= Html::encode($user->phone) ?></p>
                    </li>
                <?php } ?>
                <li>
                    <i class="icon-letter-mail-1"></i>
                    <p>
                        <?php if ((Yii::$app->controller->actionParams['id'] != Yii::$app->user->id && Yii::$app->controller->actionParams['id'])) { ?>
                            <a href="mailto:<?= Html::encode($user->email) ?>" target="_top" class="textBtn"><?= Html::encode($user->email) ?></a>
                        <?php }else{ ?>
                            <?= Html::encode($user->email) ?>
                        <?php } ?>
                    </p>
                </li>
            </ul>
            <?php if ((Yii::$app->controller->actionParams['id'] != Yii::$app->user->id && Yii::$app->controller->actionParams['id'])) { ?>
                <div class="alignCenter">
                    <?php if ($relation) : ?>
                        <?php if ($relation['request_accepted'] == "Y") : ?>
                            <a href="/users/decline/<?= Yii::$app->controller->actionParams['id'] ?>" class="btn defBtn">Disconnect</a>
                        <?php elseif ($relation['request_accepted'] == "N" && $relation['user_from_id'] == Yii::$app->user->identity->id) : ?>
                            <a href="/users/decline/<?= Yii::$app->controller->actionParams['id'] ?>" class="btn defBtn">Request is sent</a>
                        <?php elseif ($relation['request_accepted'] == "N" && $relation['user_to_id'] == Yii::$app->user->identity->id) : ?>
                            <a href="/users/accetpt/<?= Yii::$app->controller->actionParams['id'] ?>" class="btn defBtn">Accept</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="/users/connect/<?= Yii::$app->controller->actionParams['id'] ?>" class="btn defBtn">Connect</a>
                    <?php endif; ?>
                </div>
            <?php } ?>
        </div>
        <?php if (!Yii::$app->user->isGuest && (Yii::$app->controller->actionParams['id'] == Yii::$app->user->id || !Yii::$app->controller->actionParams['id'] )) { ?>
            <div class="alignCenter">
                <a href="<?= \yii\helpers\Url::to(['users/edit']) ?>" class="btn defBtn">Edit profile</a>
            </div>
        <?php } ?>
    </div>
    <div class="profileR">
        <h4 style="color:green"><?= Yii::$app->getSession()->readSession('updateSuccess') ?></h4>
        <?php Yii::$app->getSession()->destroySession('updateSuccess'); ?>
        <div class="profileTabs">
            <ul class="clearAfter">
                <?php if ((Yii::$app->controller->actionParams['id'] == Yii::$app->user->id || !Yii::$app->controller->actionParams['id'])) : ?>
                    <li><a href="#" id="profiletabNews"><i class="icon-newspaper"></i>News</a></li>
                <?php endif; ?>
                <li><a href="#" id="profiletab"><i class="icon-card-user-2"></i>Profile</a></li>
                <li><a href="#" id="colleag"><i class="icon-contacts"></i>Colleagues</a></li>
                <?php if ((Yii::$app->controller->actionParams['id'] == Yii::$app->user->id || !Yii::$app->controller->actionParams['id'])) : ?>
                    <li><a href="#" id="profiletabNot"><i class="icon-bell-two"></i>Notifications</a></li>
                    <li><a href="#" id="profiletabEdu"><i class="icon-bell-two"></i>CLEs</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="tabsContent">
            <div id="tabContent"></div>
            <div id="profileInfo" style="display:none">
                <?php $emptyProfile = false; ?>
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


                                            <?php if ($subSectionName === 'Links') { ?>
                                                <?php $i++ ?>
                                                <?php $link_name = ''; ?>
                                                <?php foreach ($subSection as $key => $form) { ?>

                                                    <?php if ($key === 0) { ?>
                                                        <?php continue; ?>
                                                    <?php } ?>


                                                    <?php $value = ''; ?>
                                                    <?php if (isset($this->params['user_forms'][$subSectionId][$u][$form['formId']]) && !is_array($this->params['user_forms'][$subSectionId][$u][$form['formId']])) { ?>
                                                        <?php $value = Html::encode($this->params['user_forms'][$subSectionId][$u][$form['formId']]) ?>
                                                    <?php } ?>
                                                    <?php if (!empty($value)) { ?>
                                                        <?php $emptyProfile = true; ?>
                                                        <?php $emptySectionToken = true; ?>
                                                        <?php $emptySubSectionToken = true; ?>
                                                    <?php } ?>
                                                    <?php if ($form['formLabel'] === 'Name') { ?>
                                                        <?php $link_name = $value; ?>
                                                        <?php continue; ?>
                                                    <?php } ?>
                                                    <li>
                                                        <?php if ($form['formType'] === 'input') { ?>
                                                            <?php $type = $form['formNumeric'] == 0 ? 'text' : 'number' ?>
                                                            <?php if (!empty($value)) { ?>
                                                                <div class="labelValue">
                                                                    <label><?= Html::encode($form['formLabel']) ?></label>
                                                                    <?php if ((substr($value, 0, 7) === "http://" || substr($value, 0, 8) === "https://" || substr($value, 0, 4) === "www.") && $form['formLabel'] === 'URL') { ?>
                                                                        <span class="<?= $key === 1 ? 'cvSingleTitle' : 'cvSingleDet' ?>" ><a href="<?= $value ?>" target="_blank" class="textBtn"><?= $link_name ? $link_name : $value ?></a></span>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php } ?>
                                                                                                                        <!--<input class='textInput formControl' form-id="<?= $form['formId'] ?>" index="<?= $i ?>" value="<?= $value ?>" name="Users[custom_fields][<?= $form['formId'] ?>][]" placeholder="<?= $form['formPlaceholder'] ?>" type="<?= $type ?>" />-->

                                                        <?php } ?>
                                                    </li>
                                                <?php } ?>
                                                <?php continue; ?>
                                            <?php } ?>
                                            <li>
                                                <?php foreach ($subSection as $key => $form) { ?>
                                                    <?php if ($key === 0) { ?>
                                                        <?php continue; ?>
                                                    <?php } ?>
                                                    <?php $value = ''; ?>
                                                    <?php if (isset($this->params['user_forms'][$subSectionId][$u][$form['formId']]) && !is_array($this->params['user_forms'][$subSectionId][$u][$form['formId']])) { ?>
                                                        <?php $value = Html::encode($this->params['user_forms'][$subSectionId][$u][$form['formId']]) ?>
                                                    <?php } ?>
                                                    <?php if (!empty($value)) { ?>
                                                        <?php $emptyProfile = true; ?>
                                                        <?php $emptySectionToken = true; ?>
                                                        <?php $emptySubSectionToken = true; ?>
                                                    <?php } ?>

                                                    <?php if ($form['formType'] === 'input') { ?>
                                                        <?php $type = $form['formNumeric'] == 0 ? 'text' : 'number' ?>
                                                        <?php if (!empty($value)) { ?>
                                                            <div class="labelValue">
                                                                <label><?= Html::encode($form['formLabel']) ?></label>
                                                                <?php if ((substr($value, 0, 7) === "http://" || substr($value, 0, 8) === "https://"  || substr($value, 0, 4) === "www.") && $form['formLabel'] === 'License URL') { ?>
                                                                    <span class="<?= $key === 1 ? 'cvSingleTitle' : 'cvSingleDet' ?>" ><a href="<?= $value ?>" class="textBtn" target="_blank">Click to View License</a></span>
                                                                <?php } else { ?>
                                                                    <span class="<?= $key === 1 ? 'cvSingleTitle' : 'cvSingleDet' ?>" ><?= $value ?></span>
                                                                <?php } ?>
                                                            </div>
                                                        <?php } ?>
                                                                                                                    <!--<input class='textInput formControl' form-id="<?= $form['formId'] ?>" index="<?= $i ?>" value="<?= $value ?>" name="Users[custom_fields][<?= $form['formId'] ?>][]" placeholder="<?= $form['formPlaceholder'] ?>" type="<?= $type ?>" />-->

                                                    <?php } ?>
                                                    <?php if ($form['formType'] === 'textarea') { ?>
                                                        <?php if (!empty($value)) { ?>
                                                            <div class="labelValue">
                                                                <label><?= Html::encode($form['formLabel']) ?></label>
                                                                <span class="<?= $key === 1 ? 'cvSingleTitle' : 'cvSingleDet' ?>" ><?= $value ?></span>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    <?php if ($form['formType'] === 'select') { ?>
                                                        <?php $options = Html::encode(str_replace('-,-', ',', $form['formOptions'])); ?>
                                                        <?php if (!empty($value)) { ?>
                                                            <div class="labelValue">
                                                                <label><?= Html::encode($form['formLabel']) ?></label>
                                                                <span><?= $value ?></span>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    <?php if ($form['formType'] === 'checkbox') { ?>
                                                        <?php $options = explode('-,-', $form['formOptions']); ?>
                                                        <?php $values = ''; ?>
                                                        <?php foreach ($options as $option) { ?>
                                                            <?php if (isset($this->params['user_forms'][$subSectionId][$u][$form['formId']])) { ?>
                                                                <?php if (is_array($this->params['user_forms'][$subSectionId][$u][$form['formId']])) { ?>
                                                                    <?php if (in_array($option, $this->params['user_forms'][$subSectionId][$u][$form['formId']])) { ?>
                                                                        <?php $values .= '<span>' . Html::encode($option) . '</span>' . ' '; ?>
                                                                    <?php } ?>
                                                                <?php } else { ?>
                                                                    <?php if ($option === $this->params['user_forms'][$subSectionId][$u][$form['formId']]) { ?>
                                                                        <?php $values = '<span>' . Html::encode($option) . '</span>'; ?>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            <?php } ?>

                                                        <?php } ?>
                                                        <?php if (!empty($values)) { ?>
                                                            <?php $emptyProfile = true; ?>
                                                            <?php $emptySectionToken = true; ?>
                                                            <?php $emptySubSectionToken = true; ?>
                                                            <div class="labelValue">
                                                                <label><?= Html::encode($form['formLabel']) ?></label>
                                                                <span><?= $values ?></span>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    <?php if ($form['formType'] === 'radio') { ?>
                                                        <?php if (!empty($value)) { ?>
                                                            <div class="labelValue">
                                                                <label><?= Html::encode($form['formLabel']) ?></label>
                                                                <span><?= $value ?></span>
                                                            </div>
                                                        <?php } ?>
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
                <?php if (!$emptyProfile) { ?>
                    <div class="cvTimeline">
                        No Information
                    </div>
                <?php } ?>
            </div>
        </div>
        <img src="/images/ajax-loader.gif" id="loader" style="display: none;position:absolute;top:250px;left:48%" >
    </div>
    <?php echo $this->render('//elements/sidebar') ?>
</div>



<script type="text/javascript">

    var imagesPath = '<?= \Yii::getAlias('@web') . '/images/users_images/' ?>';

    function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }
    var colleaguesTab = getParameterByName('colleaguesTab');
    var notificationsTab = getParameterByName('notificationsTab');
    var educationTab = getParameterByName('educationTab');
    var profileTab = getParameterByName('profileTab');


    $(document).ready(function () {
        if (colleaguesTab != 'open' && notificationsTab != 'open' && profileTab != 'open' && educationTab != 'open') {
            $('#newsContent').css('opacity', '0.2');
            $('#loader').show();

            $("#tabContent").hide();
            $("#profileInfo").hide();
            $(this).parent().addClass("active");
            $(this).parent().siblings().removeClass("active");
            $("#tabContent").load("/news/load-news", function () {
                $('#newsContent').css('opacity', '1');
                $('#loader').hide();
                $("#tabContent").show();
                $('#profileInfo').hide();
                $("#profiletabNews").parent().addClass("active");
                $("#profiletabNews").parent().siblings().removeClass("active");
            });
        }


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

        //        var showRegistration = <?php //echo Yii::$app->getSession()->readSession('showRegistration') ? 'true' : 'false'                                                          ?>;
        //<?php // Yii::$app->getSession()->destroySession('showRegistration');                                                          ?>
        //        if (showRegistration) {
        //            $.magnificPopup.open({
        //                items: {src: '#signup-popup'}, type: 'inline'
        //            }, 0);
        //        }
        //
        //        var showLogin = <?php //echo Yii::$app->getSession()->readSession('showLogin') ? 'true' : 'false'                                                          ?>;
        //<?php // Yii::$app->getSession()->destroySession('showLogin');                                                          ?>
        //        if (showLogin) {
        //            $.magnificPopup.open({
        //                items: {src: '#login-popup'}, type: 'inline'
        //            }, 0);
        //        }


        //        var newPassword = <?php //echo Yii::$app->getSession()->readSession('newPassword') ? 'true' : 'false'                                                          ?>;
        //<?php //Yii::$app->getSession()->destroySession('newPassword');                                                          ?>
        //        if (newPassword) {
        //            $.magnificPopup.open({
        //                items: {src: '#forgpass-popup-2'}, type: 'inline'
        //            }, 0);
        //        }
        //        var resetPassword = <?php //echo Yii::$app->getSession()->readSession('resetPassword') ? 'true' : 'false'                                                          ?>;
        //<?php //Yii::$app->getSession()->destroySession('resetPassword');                                                          ?>
        //        if (resetPassword) {
        //            $.magnificPopup.open({
        //                items: {src: '#forgpass-popup'}, type: 'inline'
        //            }, 0);
        //        }

        console.log(colleaguesTab);
        console.log(notificationsTab);

        if (colleaguesTab == 'open') {
            $("#profileInfo").hide();
            $("#colleag").parent().addClass("active");
            $("#colleag").parent().siblings().removeClass("active");
            $("#tabContent").load("/users/load-colleagues");
        }
        if (notificationsTab == 'open') {
            $("#profileInfo").hide();
            $("#profiletabNot").parent().addClass("active");
            $("#profiletabNot").parent().siblings().removeClass("active");
            $("#tabContent").load("/users/load-notifications");
        }
        if (educationTab == 'open') {
            $("#profileInfo").hide();
            $("#profiletabEdu").parent().addClass("active");
            $("#profiletabEdu").parent().siblings().removeClass("active");
            $("#tabContent").load("/users/load-education");
        }
        if (profileTab == 'open') {
            $("#tabContent").hide();
            $("#profiletab").parent().addClass("active");
            $("#profiletab").parent().siblings().removeClass("active");
            $("#profileInfo").show();
        }

        $(document).on("click", "#colleag", function (event) {
            event.preventDefault();
            $("#profileInfo").hide();
            $("#tabContent").show();
            $(this).parent().addClass("active");
            $(this).parent().siblings().removeClass("active");
            var userId = "<?= Yii::$app->controller->actionParams['id'] ?>";
            $("#tabContent").load("/users/load-colleagues" + "?userId=" + userId);
        });

        $(document).on("click", "#profiletab", function (event) {
            event.preventDefault();
            $("#profileInfo").show();
            $("#tabContent").html('');
            $(this).parent().addClass("active");
            $(this).parent().siblings().removeClass("active");
        });

        $(document).on("click", ".colleagPage li a", function (event) {
            event.preventDefault();
            var pageString = $(this).attr('data-page');
            var pageInt = parseInt(pageString) + 1;
            $("#profileInfo").hide();
            $("#tabContent").show();
            $(this).parent().addClass("active");
            $(this).parent().siblings().removeClass("active");
            var userId = "<?= Yii::$app->controller->actionParams['id'] ?>";
            $("#tabContent").load("/users/load-colleagues?page=" + pageInt + "&userId=" + userId);

        });

        $(document).on("click", "#profiletabNot", function (event) {
            event.preventDefault();
            $("#profileInfo").hide();
            $("#tabContent").show();
            $(this).parent().addClass("active");
            $(this).parent().siblings().removeClass("active");
            $("#tabContent").load("/users/load-notifications");
        });

        $(document).on("click", "#profiletabEdu", function (event) {
            event.preventDefault();
            $("#profileInfo").hide();
            $("#tabContent").show();
            $(this).parent().addClass("active");
            $(this).parent().siblings().removeClass("active");
            $("#tabContent").load("/users/load-education");
        });


        $(document).on("click", "#profiletabNews", function (event) {
            event.preventDefault();
            $('#newsContent').css('opacity', '0.2');
            $('#loader').show();

            $("#tabContent").hide();
            $("#profileInfo").hide();
            $(this).parent().addClass("active");
            $(this).parent().siblings().removeClass("active");
            $("#tabContent").load("/news/load-news", function () {
                $('#newsContent').css('opacity', '1');
                $('#loader').hide();
                $("#tabContent").show();
                $('#profileInfo').hide();
                $("#profiletabNews").parent().addClass("active");
                $("#profiletabNews").parent().siblings().removeClass("active");
            });
        });

        $(document).on("click", ".notifyPage li a", function (event) {
            event.preventDefault();
            var pageString = $(this).attr('data-page');
            var pageInt = parseInt(pageString) + 1;
            $("#profileInfo").hide();
            $(this).parent().addClass("active");
            $(this).parent().siblings().removeClass("active");
            $("#tabContent").load("/users/load-notifications?page=" + pageInt);

        });



    });
</script>