<?php

use yii\widgets\LinkPager;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */
?>
<div class="notifList">
    <?php if($notifications) : ?>
    <ul>
        <?php foreach($notifications as $notification) : ?>
        <li>
            <div class="notifL" style="background-image: url('/images/users_images/<?=Html::encode($notification['image'])?>')"></div>
            <div class="notifC">
                <p><?=Html::encode($notification['first_name']).' '.Html::encode($notification['last_name'])?> sent you a connect request.</p>
                <time><?=Html::encode($notification['request_created'])?></time>
            </div>
            <div class="notifR">
                <a href="<?= \yii\helpers\Url::to(['users/accetpt', 'id' => $notification['id']]) ?>" class="btn defBtn sBtn"><i class="icon-check-1"></i>Accept</a>
                <a href="<?= \yii\helpers\Url::to(['users/decline', 'id' => $notification['id']]) ?>" class="btn greyBtn sBtn"><i class="icon-cross-mark"></i>Decline</a>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php else: ?>
            <div> No Requests </div>
    <?php endif; ?>
</div>
<?php if($notifications) : ?>
<div class="pagin notifyPage">
    <?php
    // display pagination
    echo LinkPager::widget([
        'pagination' => $pages,
    ]);
    ?>
</div>
<?php endif; ?>