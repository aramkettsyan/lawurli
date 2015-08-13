<?php
    use yii\helpers\Html;
?>

<div class="container">
    <img src="/images/users_images/<?= $model->image  ?>"  style="width:150px;height:180px">
    <p><?= $model->first_name ?></p>
    <p><?= $model->last_name ?></p>
</div>