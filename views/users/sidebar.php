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
                        <img src="/images/user-1.png" alt="">
                        <span style="background-image: url(<?= \Yii::getAlias('@web') . '/images/users_images/' . $us['image'] ?>)"></span>
                    </div>
                    <div class="peopleListR">
                        <a href="/users/profile/256" class="plName"><?= $us['first_name'] . ' ' . $us['last_name'] ?></a>
                        <div class="plDets">
                            <p class="plAddress"><i class="icon-location"></i><span><?= $us['location'] ?></span></p>
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