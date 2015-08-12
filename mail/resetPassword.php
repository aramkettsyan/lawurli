<?php
use yii\helpers\Html;
 
/* @var $this yii\web\View */
/* @var $user app\modules\user\models\User */
 
$confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['users/reset', 'id' => $user->id, 'key' => $user->password_reset_token]);
?>

Dear <?= Html::encode($user->username) ?>,<br>
For resetting your password please follow this <?= Html::a(Html::encode($confirmLink), $confirmLink) ?> <br>
Best regards.