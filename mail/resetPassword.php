<?php
use yii\helpers\Html;
 
/* @var $this yii\web\View */
/* @var $user app\modules\user\models\User */
 
$confirmLink = Yii::$app->urlManager->createAbsoluteUrl([$action,'action'=>'reset', 'id' => $user->id, 'key' => $user->password_reset_token]).'#forgpass-popup-2';
?>

Dear <?= Html::encode($user->first_name.' '.$user->last_name) ?>,<br>
<img src="<?= \Yii::getAlias('@webroot') ?>/images/logo.png">
For resetting your password please follow this <?= Html::a(Html::encode($confirmLink), $confirmLink) ?> <br>
Best regards.