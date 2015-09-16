<?php
use yii\helpers\Html;
 
/* @var $this yii\web\View */
/* @var $user app\modules\user\models\User */
 
$confirmLink = Yii::$app->urlManager->createAbsoluteUrl([$action,'action'=>'reset', 'id' => $user->id, 'key' => $user->password_reset_token]).'#forgpass-popup-2';
?>

<img src="http://network.codebnb.me/images/logo.png">
Dear <?= Html::encode($user->first_name.' '.$user->last_name) ?>,<br>
For resetting <?= \Yii::getAlias('@webroot') ?> your password please follow this <?= Html::a(Html::encode($confirmLink), $confirmLink) ?> <br>
Best regards.