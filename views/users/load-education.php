<?php

use yii\widgets\LinkPager;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */
?>

<div class="clePage">
    <div class="cleCounts clearAfter">
        <div>
            <h4><?= $sum_of_units ?></h4>
            <p>Total CLE units</p>
        </div>
        <div>
            <h4><?= $sum_of_ethics ?></h4>
            <p>Total Legal Ethics CLE Units</p>
        </div>
    </div>
    <div class="mt40 alignCenter">
        <a href="#add-cle" class="btn defBtn popupBtn add_cle" >Add CLE's</a>
    </div>
    <?php if (!empty($cles)) { ?>
        <table class="tableStyle mt40" id="cles_table">
            <thead>
                <tr>
                    <th style="width: 20%;">Organization <i></i></th>
                    <th style="width: 16%;"># of units <i></i></th>
                    <th style="width: 17%;">Date <i></i></th>
                    <th style="width: 19%;">Legal Ethics <i></i></th>
                    <th style="width: 16%;" class="noSort">Certificate <i></i></th>
                    <th style="width: 12%;" class="noSort">Actions <i></i></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cles as $cle) { ?>

                    <tr>
                        <td><?= $cle['organization'] ?></td>
                        <td><?= $cle['number_of_units'] ?></td>
                        <?php
                        $date = date('F d,Y', strtotime($cle['date']));
                        $cle['date'] = $date;
                        ?>
                        <td><?= $cle['date'] ?></td>
                        <td><?= $cle['ethics'] ?></td>
                        <td class="alignCenter">
                            <a href="<?= \Yii::getAlias('@web') . '/images/users_uploads/' . $cle['certificate'] ?>" download class="certifBtn tableIcon"><i class="icon-certificate-file"></i></a>
                        </td>
                        <td>
                            <a href="<?= \yii\helpers\Url::to(['users/profile?educationTab=open&cleid=' . $cle['id']]) ?>" class="tableIcon tbEditBtn"><i class="icon-page-edit"></i></a>
                            <a href="<?= \yii\helpers\Url::to(['users/delete-education?cleid=' . $cle['id']]) ?>" class="tableIcon tbDelBtn"><i class="icon-cross-mark"></i></a>
                        </td>
                    </tr>

                <?php } ?>

            </tbody>
        </table>

    <?php } else { ?>
        <span>No information</span>
    <?php } ?>
</div>

<script>
    $(document).ready(function () {

        $("#cles_table").tablesorter({
            sortList: [[2, 1]],
            headers: {
                // disable sorting of the first & second column - before we would have to had made two entries
                // note that "first-name" is a class on the span INSIDE the first column th cell
                4: {
                    // disable it by setting the property sorter to false
                    sorter: false
                },
                5: {
                    // disable it by setting the property sorter to false
                    sorter: false
                }
            }
        });
        
        $('.noSort i').remove();

        $('.add_cle').on('click', function () {
            $('#add-cle input').val('');
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
        var showAddEducation = <?php echo Yii::$app->getSession()->readSession('addEducation') ? 'true' : 'false' ?>;
<?php Yii::$app->getSession()->destroySession('addEducation'); ?>
        if (showAddEducation) {
            $.magnificPopup.open({
                items: {src: '#add-cle'}, type: 'inline'
            }, 0);
        }

    })

</script>