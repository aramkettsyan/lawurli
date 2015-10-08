<?php

use yii\widgets\LinkPager;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */
?>

<div class="clePage">
    <div class="cleCounts clearAfter">
        <div>
            <h4>47</h4>
            <p>Total CLE units</p>
        </div>
        <div>
            <h4>12</h4>
            <p>Total Ethics CLE units</p>
        </div>
    </div>
    <div class="mt40 alignCenter">
        <a href="#add-cle" class="btn defBtn popupBtn">Add CLE's</a>
    </div>
    <table class="tableStyle mt40">
        <thead>
            <tr>
                <th style="width: 25%;">Organization</th>
                <th style="width: 16%;"># of units</th>
                <th style="width: 20%;">Date</th>
                <th style="width: 12%;">Ethics</th>
                <th style="width: 15%;">Certificate</th>
                <th style="width: 12%;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Blah organization</td>
                <td>47</td>
                <td>22 June 2014</td>
                <td>Yes</td>
                <td class="alignCenter">
                    <a href="#" class="certifBtn tableIcon"><i class="icon-certificate-file"></i></a>
                </td>
                <td>
                    <a href="#" class="tableIcon tbEditBtn"><i class="icon-page-edit"></i></a>
                    <a href="#" class="tableIcon tbDelBtn"><i class="icon-cross-mark"></i></a>
                </td>
            </tr>
            <tr>
                <td>Blah organization</td>
                <td>47</td>
                <td>22 June 2014</td>
                <td>Yes</td>
                <td class="alignCenter">
                    <a href="#" class="certifBtn tableIcon"><i class="icon-certificate-file"></i></a>
                </td>
                <td>
                    <a href="#" class="tableIcon tbEditBtn"><i class="icon-page-edit"></i></a>
                    <a href="#" class="tableIcon tbDelBtn"><i class="icon-cross-mark"></i></a>
                </td>
            </tr>
            <tr>
                <td>Blah organization</td>
                <td>47</td>
                <td>22 June 2014</td>
                <td>Yes</td>
                <td class="alignCenter">
                    <a href="#" class="certifBtn tableIcon"><i class="icon-certificate-file"></i></a>
                </td>
                <td>
                    <a href="#" class="tableIcon tbEditBtn"><i class="icon-page-edit"></i></a>
                    <a href="#" class="tableIcon tbDelBtn"><i class="icon-cross-mark"></i></a>
                </td>
            </tr>
        </tbody>
    </table>
</div>


<div id="add-cle" class="popupWrap mfp-hide">
    <div class="popupTitle">
        <h5>Add</h5>
        <button class="mfp-close"></button>
    </div>
    <div class="popupCont srchPopupCont">
        <div class="formRow">
            <label>Organization name</label>
            <input type="text" class="formControl"/>
        </div>
        <div class="formRow">
            <label># of Units</label>
            <input type="text" class="formControl"/>
        </div>
        <div class="formRow">
            <label>Date</label>
            <input type="text" class="formControl datepicker">
        </div>
        <div class="formRow">
            <label>Ethics (Y/N)</label>
            <select class="formControl">
                <option>Yes</option>
                <option>No</option>
            </select>
        </div>
        <div class="formRow">
            <label>Upload certificate</label>
            <input type="file" class="formControl"/>
        </div>
        <div class="submitSect">
            <input class="btn defBtn" type="submit" value="Save">
        </div>
    </div>
</div>


<script>
    $(function() {
        $( ".datepicker" ).datepicker();
    });
    //////////////////////
    /// magnific popup ///
    //////////////////////
    $('.popupBtn').magnificPopup({
        type: 'inline',
        preloader: false,
        focus: '#name',
        mainClass: 'popupAnim',
        removalDelay: 300
    });
</script>