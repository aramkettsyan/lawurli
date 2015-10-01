<?php

use yii\helpers\Html;
?>

<div class="sideBarR">
    <h6 class="boxTitle">People you may know</h6>
    <div class="peopleList">
        <ul id='notConnectedUsers'>
            <?php $ids = '' ?>
            <?php foreach ($this->params['notConnectedUsers'] as $us) { ?>
                <?php if (empty($ids)) { ?>
                    <?php $ids.=$us['id'] ?>
                <?php } else { ?>
                    <?php $ids.=',' . $us['id'] ?>
                <?php } ?>
                <li class='notConnectedUser'>
                    <div class="peopleListL">
                        <?php $filename = \Yii::getAlias('@webroot') . '/images/users_images/' . Html::encode($us['image']); ?>
                        <?php if (is_file($filename)) { ?>
                            <img src="/images/user-1.png" alt="">
                            <span style="background-image: url('<?php echo \Yii::getAlias('@web') . '/images/users_images/' . $us['image']; ?>')"></span>
                        <?php } else { ?>
                            <img src="/images/user-1.png" alt="">
                            <span style="background-image: url('<?php echo \Yii::getAlias('@web') . '/images/users_images/default.png'; ?>')"></span>
                        <?php } ?>
                    </div>
                    <div class="peopleListR">
                        <a href="/users/profile/<?= $us['id'] ?>?profileTab=open" class="plName"><?= Html::encode($us['first_name']) . ' ' . Html::encode($us['last_name']) ?></a>
                        <div class="plDets">
                            <p class="plAddress">
                                <?php if ($us['location']) { ?>
                                    <i class="icon-location"></i>
                                    <span><?= Html::encode($us['location']) ?></span>
                                <?php } ?>
                            </p>
                            <p class="usTitle">
                                <?php foreach ($this->params['usersTitles'] as $us_titles) { ?>
                                    <?php if ($us_titles['user_id'] === $us['id']) { ?>
                                        <span><?= $us_titles['value'] ?></span>
                                    <?php } ?>
                                <?php } ?>
                            </p>
                        </div>
                        <div class="plActions">
                            <a class="textBtn connect" id='<?= $us['id'] ?>'>Connect</a>
                        </div>
                    </div>
                    <button class="skip" id="<?= $us['id'] ?>"><i class="icon-remove"></i></button>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>