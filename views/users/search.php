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
                                <img src="/images/user-1.png" alt="">
                                <img src="<?php // echo \Yii::getAlias('@web') . '/images/users_images/' . $user['image']; ?>" alt="">
                                <span style='background-image: url("<?php echo \Yii::getAlias('@web') . '/images/users_images/' . $user['image']; ?>")'></span>
                            </div>
                            <div class="peopleListR">
                                <a href="<?= \yii\helpers\Url::to(['users/profile', 'id' => $user['id']]) ?>" class="plName"><?= $user['first_name'] ?> <?= $user['last_name'] ?></a>
                                <div class="plDets">
                                    <!--<p>Plumber at "Clean House" Ltd</p>-->
                                    <p class="plAddress"><i class="icon-location"></i><?= $user['location'] ?></p>
                                </div>
                                <div class="plActions">
                                    <a href="<?= \yii\helpers\Url::to(['users/profile', 'id' => $user['id']]) ?>" class="btn lineDefBtn sBtn">View Profile</a>
                                    <?php if (isset($contacts[$user['id']])) : ?>
                                        <?php if ($contacts[$user['id']]['user_to_id'] == $user['id'] && $contacts[$user['id']]['request_accepted'] == 'N') : ?>
                                            <a href="<?= \yii\helpers\Url::to(['users/decline', 'id' => $user['id']]) ?>" class="btn greyBtn sBtn cdBtn"><i class="icon-cross-mark"></i>Request is sent</a>
                                        <?php elseif ($contacts[$user['id']]['user_to_id'] == $user['id'] && $contacts[$user['id']]['request_accepted'] == 'Y') : ?>
                                            <a href="<?= \yii\helpers\Url::to(['users/decline', 'id' => $user['id']]) ?>" class="btn greyBtn sBtn cdBtn"><i class="icon-cross-mark"></i>Disconnect</a>
                                        <?php elseif ($contacts[$user['id']]['user_from_id'] == $user['id'] && $contacts[$user['id']]['request_accepted'] == 'Y') : ?>
                                            <a href="<?= \yii\helpers\Url::to(['users/decline', 'id' => $user['id']]) ?>" class="btn greyBtn sBtn cdBtn"><i class="icon-cross-mark"></i>Disconnect</a>
                                        <?php elseif ($contacts[$user['id']]['user_from_id'] == $user['id'] && $contacts[$user['id']]['request_accepted'] == 'N') : ?>
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

<?php if (Yii::$app->user->isGuest) : ?>
            $(".cdBtn").each(function (element) {
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