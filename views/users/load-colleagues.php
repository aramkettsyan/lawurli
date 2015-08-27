<?php

use yii\widgets\LinkPager;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */
?>

<div class="peopleList">
    <?php if($colleagues) : ?>
    <ul>
        <?php foreach($colleagues as $colleague) : ?>
        <li>
            <div class="peopleListL">
                <img src="/images/default.png" alt="">
                <span style="background-image: url('/images/users_images/<?=$colleague['image']?>')"></span>
            </div>
            <div class="peopleListR">
                <a href="/users/profile/<?=$colleague['id']?>" class="plName"><?=$colleague['first_name'].' '.$colleague['last_name']?></a>
                <div class="plDets">
                   <?= ($colleague['location'] ?  '<p class="plAddress"><i class="icon-location"></i>'.$colleague['location'].'</p>'  : '' )?>
                </div>
                <div class="plActions">
                    <?php if($userId) : ?>
                        <?php if (isset($contacts[$colleague['id']])) : ?>
                            <?php if ($contacts[$colleague['id']]['user_to_id'] == $colleague['id'] && $contacts[$colleague['id']]['request_accepted'] == 'N') : ?>
                                <a href="<?= \yii\helpers\Url::to(['users/decline', 'id' => $colleague['id']]) ?>" class="btn greyBtn sBtn"><i class="icon-cross-mark"></i>Request is sent</a>
                            <?php elseif ($contacts[$colleague['id']]['user_to_id'] == $colleague['id'] && $contacts[$colleague['id']]['request_accepted'] == 'Y') : ?>
                                <a href="<?= \yii\helpers\Url::to(['users/decline', 'id' => $colleague['id']]) ?>" class="btn greyBtn sBtn"><i class="icon-cross-mark"></i>Disconnect</a>
                            <?php elseif ($contacts[$colleague['id']]['user_from_id'] == $colleague['id'] && $contacts[$colleague['id']]['request_accepted'] == 'Y') : ?>
                                <a href="<?= \yii\helpers\Url::to(['users/decline', 'id' => $user['id']]) ?>" class="btn greyBtn sBtn"><i class="icon-cross-mark"></i>Disconnect</a>
                            <?php elseif ($contacts[$colleague['id']]['user_from_id'] == $colleague['id'] && $contacts[$colleague['id']]['request_accepted'] == 'N') : ?>
                                <a href="<?= \yii\helpers\Url::to(['users/accetpt', 'id' => $colleague['id']]) ?>" class="btn defBtn sBtn"><i class="icon-check-1"></i>Accept</a>
                            <?php endif; ?>
                        <?php else : ?> 
                            <a href="/users/connect/<?=$colleague['id']?>" class="btn lineDefBtn sBtn">Connect</a>
                        <?php endif; ?>
                    <?php else : ?>
                            <a href="/users/decline/<?=$colleague['id']?>" class="btn lineDefBtn sBtn">Disconnect</a>
                    <?php endif; ?>        
                </div>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
   <?php else : ?>
<div> No Colleagues </div>
<?php endif; ?>
</div>
<?php if($colleagues) : ?>
<div class="pagin colleagPage">
    <?php
    // display pagination
    echo LinkPager::widget([
        'pagination' => $pages,
    ]);
    ?>
</div>
<?php endif; ?>