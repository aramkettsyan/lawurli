<?php
/* @var $this yii\web\View */
?>
<div class="container">
<h1>users/index</h1>

<?php 
    echo \Yii::$app->getSession()->getFlash('success');
?>

<?php 
    echo \Yii::$app->getSession()->getFlash('warning');
?>
</div>