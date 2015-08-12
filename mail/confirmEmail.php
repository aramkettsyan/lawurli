<?php
use yii\helpers\Html;
 
/* @var $this yii\web\View */
/* @var $user app\modules\user\models\User */
 
$confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['users/confirm', 'id' => $user->id, 'key' => $user->activation_token]);
?>

Dear <?= Html::encode($user->username) ?>,<br>
Thanks for signing up. Please confirm your email address by clicking this link: <?= Html::a(Html::encode($confirmLink), $confirmLink) ?> 
<br>
Best regards. 