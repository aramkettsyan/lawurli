<div class="headerDrDn dropDn notifDrDn">
    <a href="#" class="dropDnBtn">
        <i class="icon-bell-two"></i>
        <span class="notifCount">3</span>
    </a>
    <div class="dropDnSub">
        <ul>
            <?php foreach ($this->params['notify'] as $notification) : ?>
                <li>
                    <div class="hdrNotifL">
                        <!--<img src="/images/users_images/<?=$notification['image']?>" alt="">-->
                        <span style="background-image: url('/images/users_images/<?=$notification['image']?>')"></span>
                    </div>
                    <div class="hdrNotifC">
                       <p>Request from <?=$notification['first_name'].' '.$notification['last_name']?></p>
                    </div>
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