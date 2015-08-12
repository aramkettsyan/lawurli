
<div class="container">
<?php 
    echo \Yii::$app->getSession()->getFlash('success');
    echo \Yii::$app->getSession()->getFlash('warning');
?>

<?php echo $email?'<p>Your email is: '.$email.'</p>':'' ?>
</div>

