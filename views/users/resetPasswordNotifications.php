
<div class="container">
<?php 
    echo \Yii::$app->getSession()->getFlash('success');
    echo \Yii::$app->getSession()->getFlash('warning');
?>
</div>