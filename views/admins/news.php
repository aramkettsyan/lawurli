<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = 'News';
?>

<div class="container mainContainer">

    <div style="float: left">
        <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
        <div class="formRow">
            <?= $form->field($model, 'resource_url')->textInput(['class' => 'formControl']) ?>
        </div>
        <span style="color:green"><?= \Yii::$app->session->getFlash('newsSuccess') ?></span>
        <div class="submitSect">
            <button type="submit" class="btn defBtn"><i class="icon-paper-plane"></i>Save</button>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <table border="1" style="display: inline-block;position: absolute;right: 50px">
        <?php if (!empty($newsList)) { ?>
            <tr>
                <th>Url</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>

            <?php foreach ($newsList as $news_url) { ?>
                <tr>
                    <td><?= $news_url->resource_url ?></td>
                    <td><a href="/admins/news/?edit=true&id=<?= $news_url->resource_id ?>" >Edit</a></td>
                    <td><a href="/admins/news/?delete=true&id=<?= $news_url->resource_id ?>" >Delete</a></td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>There are no sites</tr>
        <?php } ?>

    </table>


</div>