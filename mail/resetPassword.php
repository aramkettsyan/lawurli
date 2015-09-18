<?php
use yii\helpers\Html;
 
/* @var $this yii\web\View */
/* @var $user app\modules\user\models\User */
 
$confirmLink = Yii::$app->urlManager->createAbsoluteUrl([$action,'action'=>'reset', 'id' => $user->id, 'key' => $user->password_reset_token]).'#forgpass-popup-2';
?>
<br>
<a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['']) ?>" style="display: inline-block; text-align: left; text-decoration:none;">
    <img src="<?= Yii::$app->urlManager->createAbsoluteUrl(['']) ?>images/logo.png" style="width: 130px; border:0 none; outline:0 none; margin:0; padding:0" alt="" />
</a>
<br>
<br>
<p style="color:#222; margin: 0; padding: 0;">Dear <?= Html::encode($user->first_name.' '.$user->last_name) ?>,</p>
<br>
<br>
<p style="color:#222; margin: 0; padding: 0;">You have requested to reset your password. Please proceed with the following button.</p>
<div style="width:100%; clear:both; text-align: center;">
    <br>
    <br>
    <a href="<?= $confirmLink ?>" style="display: inline-block; text-align: center; background: #DA8213; color: #fff; padding: 9px 18px; font-size: 14px; border: none; margin: 0; text-decoration:none;">Reset Password</a>
    <br>
    <br>
</div>
<p style="color:#222; margin: 0; padding: 0;">Best regards.</p>
