<?php

use yii\helpers\Html;
?>

<div class="headerDrDn dropDn notifDrDn">
    <a href="#" class="dropDnBtn">
        <i class="icon-bell-two"></i>
<?= ($this->params['notifyCount'] ? '<span class="notifCount">' . $this->params['notifyCount'] . '</span>' : '') ?>
    </a>
    <div class="dropDnSub">
        <ul>
<?php foreach ($this->params['notify'] as $notification) : ?>
                <li>
                    <a href="/users/profile?notificationsTab=open">
                        <div class="hdrNotifL">
                            <!--<img src="/images/users_images/<?= Html::encode($notification['image']) ?>" alt="">-->
                            <span style="background-image: url('/images/users_images/<?= Html::encode($notification['image']) ?>')"></span>
                        </div>
                        <div class="hdrNotifC">
                            <p><?= Html::encode($notification['first_name']) . ' ' . Html::encode($notification['last_name']) ?></p>
                            <p><?= Html::encode($notification['location']) ?></p>
                        </div>
                    </a>
                    <div class="hdrNotifR">
                        <a href="<?= \yii\helpers\Url::to(['users/accetpt', 'id' => $notification['id']]) ?>"><i class="icon-check-1"></i></a>
                        <a href="<?= \yii\helpers\Url::to(['users/decline', 'id' => $notification['id']]) ?>"><i class="icon-cross-mark"></i></a>
                    </div>
                </li>
<?php endforeach; ?>
            <li class="notifSeeAll">
                <a href="/users/profile?notificationsTab=open">See All</a>
            </li>
        </ul>
    </div>
</div>