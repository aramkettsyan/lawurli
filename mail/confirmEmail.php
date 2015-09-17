<?php
use yii\helpers\Html;
 
/* @var $this yii\web\View */
/* @var $user app\modules\user\models\User */
 
$confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['users/confirm', 'id' => $user->id, 'key' => $user->activation_token]);
?>
<br>
<a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['users/index']) ?>" style="display: inline-block; text-align: left; text-decoration:none;">
    <img src="<?= Yii::$app->urlManager->createAbsoluteUrl(['']) ?>images/logo.png" style="width: 130px; border:0 none; outline:0 none; margin:0; padding:0" alt="" />
</a>
<br>
<br>
Dear <?= Html::encode($user->first_name.' '.$user->last_name) ?>,
<br>
<br>
Thank you for becoming a member of our growing legal community! Please confirm your email address. 

<div style="width:100%; clear:both; text-align: center;">
    <br>
    <br>
    <a href="<?= $confirmLink ?>" style="display: inline-block; text-align: center; background: #DA8213; color: #fff; padding: 9px 18px; font-size: 14px; border: none; margin: 0; text-decoration:none;">Confirm email address</a>
    <br>
    <br>
</div>
- The Lawurli Team