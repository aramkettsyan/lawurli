<?php
use yii\widgets\LinkPager;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model app\models\User */
?>

<div class="peopleList">
    <ul>
        <?php if($colleagues) : ?>
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
                    <a href="/users/decline/<?=$colleague['id']?>" class="btn lineDefBtn sBtn">Disconnect</a>
                </div>
            </div>
        </li>
        <?php endforeach; ?>
        
        <?php else : ?>
        <div> No Colleagues </div>
        <?php endif; ?>
    </ul>
   
</div>
 <div class="pagin colleagPage">
    <?php // display pagination
           echo LinkPager::widget([
               'pagination' => $pages,
           ]);
    ?>
</div>