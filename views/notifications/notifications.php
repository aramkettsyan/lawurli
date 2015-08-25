<div class="headerDrDn dropDn notifDrDn">
    <a href="#" class="dropDnBtn">
        <i class="icon-bell-two"></i>
    </a>
    <div class="dropDnSub">
        <ul>
            <?php foreach ($this->params['notify'] as $notification) : ?>
                <li>
                    <div class="hdrNotifL">
                        <img src="/images/users_images/<?=$notification['image']?>" alt="">
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
            
            

<!--            <li>
                <div class="hdrNotifL">
                    <img src="<?= \Yii::getAlias('@web') . '/images/user-2.png'; ?>" alt="">
                </div>
                <div class="hdrNotifC">
                    Blah blah text
                </div>
                <div class="hdrNotifR">
                    <button><i class="icon-check-1"></i></button>
                    <button><i class="icon-cross-mark"></i></button>
                </div>
            </li>
            <li>
                <div class="hdrNotifL">
                    <img src="<?= \Yii::getAlias('@web') . '/images/user-1.png'; ?>" alt="">
                </div>
                <div class="hdrNotifC">
                    lorem ipsum dolor sit
                </div>
                <div class="hdrNotifR">
                    <button><i class="icon-check-1"></i></button>
                    <button><i class="icon-cross-mark"></i></button>
                </div>
            </li>-->
        </ul>
    </div>
</div>