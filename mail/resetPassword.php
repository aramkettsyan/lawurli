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
Dear <?= Html::encode($user->first_name.' '.$user->last_name) ?>,
<br>
<br>
For resetting your password please follow this <?= Html::a(Html::encode($confirmLink), $confirmLink) ?>
<br>
<br>
Best regards.