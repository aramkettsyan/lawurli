<?php
use yii\helpers\Html;
 
/* @var $this yii\web\View */
/* @var $user app\modules\user\models\User */
 
$confirmLink = Yii::$app->urlManager->createAbsoluteUrl([$action,'action'=>'reset', 'id' => $user->id, 'key' => $user->password_reset_token]).'#forgpass-popup-2';
?>
<img src="<?= \Yii::getAlias('@webroot') ?>/images/user-1.png">

Dear <?= Html::encode($user->first_name.' '.$user->last_name) ?>,<br>
For resetting your password please follow this <?= Html::a(Html::encode($confirmLink), $confirmLink) ?> <br>
Best regards.