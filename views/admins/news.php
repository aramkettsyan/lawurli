<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = 'News';
?>

<div class="container mainContainer">

    <div>
        <?php
        $form = ActiveForm::begin(['id' => 'contact-form',
                    'options' => ['enctype' => 'multipart/form-data']]);
        ?>
        <div class="formRow">
            <?= $form->field($model, 'resource_url')->textInput(['class' => 'formControl']) ?>
            <?= $form->field($model, 'resource_image')->fileInput(['class' => 'formControl']) ?>
        </div>
        <span style="color:green"><?= \Yii::$app->session->getFlash('newsSuccess') ?></span>
        <div class="submitSect">
            <button type="submit" class="btn defBtn"><i class="icon-paper-plane"></i>Save</button>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <table border="1" style="margin-top:50px">
        <?php if (!empty($newsList)) { ?>
            <tr>
                <th>ID</th>
                <th>Url</th>
                <th>Image</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>

            <?php foreach ($newsList as $news_url) { ?>
                <tr>
                    <td style="padding: 0 5px"><?= $news_url->resource_id ?></td>
                    <td><?= $news_url->resource_url ?></td>
                    <?php if ($news_url->resource_image) { ?>
                        <td><img style="width: 220px;height: 147px;" src="/images/news/<?= $news_url->resource_image ?>"></td>
                        <?php } else { ?>
                        <td>No image</td>
                        <?php } ?>
                    <td><a href="/admins/news?edit=true&id=<?= $news_url->resource_id ?>" >Edit</a></td>
                    <td><a href="/admins/news?delete=true&id=<?= $news_url->resource_id ?>" >Delete</a></td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>There are no sites</tr>
        <?php } ?>

    </table>


</div>