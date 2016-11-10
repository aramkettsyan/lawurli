<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = 'Users';
?>

<div class="container mainContainer">

    <table border="1" style="margin-top:50px">
        <?php if (!empty($usersList)) { ?>
            <tr>
                <th>ID</th>
                <th>First name</th>
                <th>Last name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Location</th>
                <th>Latitude and longitude</th>
                <th>Image</th>
                <th>Created</th>
                <th>Modified</th>
            </tr>

            <?php foreach ($usersList as $user) { ?>
                <tr>
                    <td style="padding: 0 5px"><?= $user->id ?></td>
                    <td><?= htmlspecialchars($user->first_name) ?></td>
                    <td><?= htmlspecialchars($user->last_name) ?></td>
                    <td><?= htmlspecialchars($user->email) ?></td>
                    <td><?= htmlspecialchars($user->phone) ?></td>
                    <td><?= htmlspecialchars($user->location) ?></td>
                    <td><?= htmlspecialchars($user->latlng) ?></td>
                    <?php if ($user->image) { ?>
                        <?php if(is_file('/images/users_images/'.$user->image)){ ?>
                            <td><img style="width: 220px;height: 147px;" src="/images/users_images/<?= $user->image ?>"></td>
                        <?php }else{ ?>
                            <td><img style="width: 220px;height: 147px;" src="/images/users_images/default.png"></td>
                        <?php } ?>
                    <?php } else { ?>
                        <td>No image</td>
                    <?php } ?>
                    <td><?= htmlspecialchars($user->created) ?></td>
                    <td><?= htmlspecialchars($user->modified) ?></td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>There are no users</tr>
        <?php } ?>

    </table>
    <?php
    echo LinkPager::widget([
        'pagination' => $pages,
    ]);
    ?>


</div>