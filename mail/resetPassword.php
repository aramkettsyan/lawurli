<?php
use yii\helpers\Html;
 
/* @var $this yii\web\View */
/* @var $user app\modules\user\models\User */
 
$confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['users/index','action'=>'reset', 'id' => $user->id, 'key' => $user->password_reset_token]).'#forgpass-popup-2';
?>

Dear <?= Html::encode($user->first_name.' '.$user->last_name) ?>,<br>
For resetting your password please follow this <?= Html::a(Html::encode($confirmLink), $confirmLink) ?> <br>
Best regards.