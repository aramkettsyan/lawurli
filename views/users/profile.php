<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title = Html::encode($user->first_name).' '.Html::encode($user->last_name);
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
            <?php } ?>
        </div>
        <p style="color:green;display: none" id="imageUploadSuccess">Image uploaded successfully!</p>
        <p style="color:red;display: none" id="imageUploadError"></p>

        <div class="userDetails">
            <h3 class="userName"><?= Html::encode($user->first_name) ?> <?= Html::encode($user->last_name) ?></h3>
            <!--            <div class="proffInfo">
                            <span class="userProff">Bandit</span> 
                        </div>-->
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
                    <p><?= Html::encode($user->email) ?></p>
                </li>
            </ul>
            <?php if ((Yii::$app->controller->actionParams['id'] != Yii::$app->user->id && Yii::$app->controller->actionParams['id'] )) { ?>
                <div class="alignCenter">
                    <?php if($relation) : ?>
                        <?php if($relation['request_accepted'] == "Y") :  ?>
                            <a href="/users/decline/<?=Yii::$app->controller->actionParams['id'] ?>" class="btn defBtn">Disconnect</a>
                        <?php elseif($relation['request_accepted'] == "N" && $relation['user_from_id'] == Yii::$app->user->identity->id) :  ?>
                            <a href="/users/decline/<?=Yii::$app->controller->actionParams['id'] ?>" class="btn defBtn">Request is sent</a>
                        <?php elseif($relation['request_accepted'] == "N" && $relation['user_to_id'] == Yii::$app->user->identity->id) :  ?>
                            <a href="/users/accetpt/<?=Yii::$app->controller->actionParams['id'] ?>" class="btn defBtn">Accept</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="/users/connect/<?=Yii::$app->controller->actionParams['id'] ?>" class="btn defBtn">Connect</a>
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
        <h4 style="color:red"><?= Yii::$app->getSession()->readSession('updateError') ?></h4>
        <?php Yii::$app->getSession()->destroySession('updateSuccess'); ?>
        <?php Yii::$app->getSession()->destroySession('updateError'); ?>
        <div class="profileTabs">
            <ul class="clearAfter">
                <li class="active"><a href="#" id="profiletab"><i class="icon-card-user-2"></i>Profile</a></li>
                <li><a href="#" id="colleag"><i class="icon-contacts"></i>Colleagues</a></li>
                <?php if((Yii::$app->controller->actionParams['id'] == Yii::$app->user->id || !Yii::$app->controller->actionParams['id'] )) : ?>
                <li><a href="#" id="profiletabNot"><i class="icon-bell-two"></i>Notifications</a></li>
                <li><a href="#" id=""><i class="icon-bell-two"></i>News</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="tabsContent">
            <div id="tabContent"></div>
            <div id="profileInfo">
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
                                            <li>
                                                <?php foreach ($subSection as $key => $form) { ?>
                                                    <?php if ($key === 0) { ?>
                                                        <?php continue; ?>
                                                    <?php } ?>
                                                    <?php $value = ''; ?>
                                                    <?php if (isset($this->params['user_forms'][$subSectionId][$u][$form['formId']])) { ?>
                                                        <?php $value = Html::encode($this->params['user_forms'][$subSectionId][$u][$form['formId']]) ?>
                                                    <?php } ?>
                                                    <?php if (!empty($value)) { ?>
                                                        <?php $emptyProfile = true; ?>
                                                        <?php $emptySectionToken = true; ?>
                                                        <?php $emptySubSectionToken = true; ?>
                                                    <?php } ?>

                                                    <?php if ($form['formType'] === 'input') { ?>
                                                        <?php $type = $form['formNumeric'] == 0 ? 'text' : 'number' ?>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <!--<input class='textInput formControl' form-id="<?= $form['formId'] ?>" index="<?= $i ?>" value="<?= $value ?>" name="Users[custom_fields][<?= $form['formId'] ?>][]" placeholder="<?= $form['formPlaceholder'] ?>" type="<?= $type ?>" />-->
                                                        <p class="<?= $key === 1 ? 'cvSingleTitle' : 'cvSingleDet' ?>" ><?= $value ?></p>
                                                    <?php } ?>
                                                    <?php if ($form['formType'] === 'textarea') { ?>
                                                        <p class="<?= $key === 1 ? 'cvSingleTitle' : 'cvSingleDet' ?>" ><?= $value ?></p>
                                                    <?php } ?>
                                                    <?php if ($form['formType'] === 'select') { ?>
                                                        <?php $options = Html::encode(str_replace('-,-', ',', $form['formOptions'])); ?>
                                                        <div class="labelValue">
                                                            <label><?= Html::encode($form['formLabel']) ?></label>
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
                                                            <label><?= Html::encode($form['formLabel']) ?></label>
                                                            <span><?= Html::encode($values) ?></span>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if ($form['formType'] === 'radio') { ?>
                                                        <div class="labelValue">
                                                            <label><?= Html::encode($form['formLabel']) ?></label>
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
                <?php if (!$emptyProfile) { ?>
                    <div class="cvTimeline">
                        No Information
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="sideBarR">
        <h6 class="boxTitle">People you may know</h6>
        <div class="peopleList">
            <ul>
                <li>
                    <div class="peopleListL">
                        <img src="/images/user-1.png" alt="">
                        <span style="background-image: url('/images/users_images/default.jpg')"></span>
                    </div>
                    <div class="peopleListR">
                        <a href="/users/profile/256" class="plName">Ann Morrison</a>
                        <div class="plDets">
                            <p class="plAddress"><i class="icon-location"></i>15 Jackson Park</p>
                        </div>
                        <div class="plActions">
                            <a href="/users/connect/256" class="textBtn">Connect</a>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="peopleListL">
                        <img src="/images/user-1.png" alt="">
                        <span style="background-image: url('/images/users_images/default.jpg')"></span>
                    </div>
                    <div class="peopleListR">
                        <a href="/users/profile/256" class="plName">Ann Morrison</a>
                        <div class="plDets">
                            <p class="plAddress"><i class="icon-location"></i>15 Jackson Park</p>
                        </div>
                        <div class="plActions">
                            <a href="/users/connect/256" class="textBtn">Connect</a>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="peopleListL">
                        <img src="/images/user-1.png" alt="">
                        <span style="background-image: url('/images/users_images/default.jpg')"></span>
                    </div>
                    <div class="peopleListR">
                        <a href="/users/profile/256" class="plName">Ann Morrison</a>
                        <div class="plDets">
                            <p class="plAddress"><i class="icon-location"></i>15 Jackson Park</p>
                        </div>
                        <div class="plActions">
                            <a href="/users/connect/256" class="textBtn">Connect</a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>



<script type="text/javascript">
    function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }
    var colleaguesTab = getParameterByName('colleaguesTab');
    var notificationsTab = getParameterByName('notificationsTab');

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

//        var showRegistration = <?php //echo Yii::$app->getSession()->readSession('showRegistration') ? 'true' : 'false' ?>;
//<?php // Yii::$app->getSession()->destroySession('showRegistration'); ?>
//        if (showRegistration) {
//            $.magnificPopup.open({
//                items: {src: '#signup-popup'}, type: 'inline'
//            }, 0);
//        }
//
//        var showLogin = <?php //echo Yii::$app->getSession()->readSession('showLogin') ? 'true' : 'false' ?>;
//<?php // Yii::$app->getSession()->destroySession('showLogin'); ?>
//        if (showLogin) {
//            $.magnificPopup.open({
//                items: {src: '#login-popup'}, type: 'inline'
//            }, 0);
//        }


//        var newPassword = <?php //echo Yii::$app->getSession()->readSession('newPassword') ? 'true' : 'false' ?>;
//<?php //Yii::$app->getSession()->destroySession('newPassword'); ?>
//        if (newPassword) {
//            $.magnificPopup.open({
//                items: {src: '#forgpass-popup-2'}, type: 'inline'
//            }, 0);
//        }
//        var resetPassword = <?php //echo Yii::$app->getSession()->readSession('resetPassword') ? 'true' : 'false' ?>;
//<?php //Yii::$app->getSession()->destroySession('resetPassword'); ?>
//        if (resetPassword) {
//            $.magnificPopup.open({
//                items: {src: '#forgpass-popup'}, type: 'inline'
//            }, 0);
//        }

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

        $(document).on("click", "#colleag", function (event) {
            event.preventDefault();
            $("#profileInfo").hide();
            $(this).parent().addClass("active");
            $(this).parent().siblings().removeClass("active");
            var userId = "<?=Yii::$app->controller->actionParams['id'] ?>";
            $("#tabContent").load("/users/load-colleagues" + "?userId="+ userId);
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
            $(this).parent().addClass("active");
            $(this).parent().siblings().removeClass("active");
            var userId = "<?=Yii::$app->controller->actionParams['id'] ?>";
            $("#tabContent").load("/users/load-colleagues?page=" + pageInt + "&userId="+ userId);

        });

        $(document).on("click", "#profiletabNot", function (event) {
            event.preventDefault();
            $("#profileInfo").hide();
            $(this).parent().addClass("active");
            $(this).parent().siblings().removeClass("active");
            $("#tabContent").load("/users/load-notifications");
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