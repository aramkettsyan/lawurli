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
                    <th style="width: 20%;" id="i1">Organization <i></i></th>
                    <th style="width: 16%;" id="i2"># of units <i></i></th>
                    <th style="width: 17%;" id="i3">Date <i></i></th>
                    <th style="width: 19%;" id="i4">Legal Ethics <i></i></th>
                    <th style="width: 16%;" class="noSort">Certificate <i></i></th>
                    <th style="width: 12%;" class="noSort">Actions <i></i></th>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="6" class="tableWrapTd">
                    <div class="custom-scroll" style="height: 170px;">
                        <table id="sec_table">
                            <thead style="display: none;">
                            <tr>
                                <th style="width: 20%;"></th>
                                <th style="width: 16%;"></th>
                                <th style="width: 17%;"></th>
                                <th style="width: 19%;"></th>
                                <th style="width: 16%;" class="noSort"></th>
                                <th style="width: 12%;" class="noSort"></th>
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
                    </div>
                </td>
            </tr>



            </tbody>
        </table>

    <?php } else { ?>
        <span>No information</span>
    <?php } ?>
</div>

<script>
    $(document).ready(function () {

//        $("#cles_table").tablesorter({
//            sortList: [[2, 1]],
//            headers: {
//                // disable sorting of the first & second column - before we would have to had made two entries
//                // note that "first-name" is a class on the span INSIDE the first column th cell
//                4: {
//                    // disable it by setting the property sorter to false
//                    sorter: false
//                },
//                5: {
//                    // disable it by setting the property sorter to false
//                    sorter: false
//                }
//            }
//        });
        
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

        /////////////////////
        /// mCustomScroll ///
        /////////////////////
        $(".custom-scroll").mCustomScrollbar();

        ////////////////////
        /// table sorter ///
        ////////////////////
        $("#sec_table").tablesorter();
        var i1 = 0;
        $("#i1").click(function () {
            $('#cles_table').find('th').attr('class','');
            if(i1===0){
                i1=1;
                $(this).removeClass('headerSortDown');
                $(this).addClass('headerSortUp');
            }else if(i1===1){
                i1=0;
                $(this).removeClass('headerSortUp');
                $(this).addClass('headerSortDown');
            }
            var sorting = [[0, i1]];
            console.log($(this))
            $("#sec_table").trigger("sorton", [sorting]);
            return false;
        });
        var i2 = 0;
        $("#i2").click(function () {
            $('#cles_table').find('th').attr('class','');
            if(i2===0){
                i2=1;
                $(this).removeClass('headerSortDown');
                $(this).addClass('headerSortUp');
            }else if(i2===1){
                i2=0;
                $(this).removeClass('headerSortUp');
                $(this).addClass('headerSortDown');
            }
            var sorting = [[1, i2]];
            $("#sec_table").trigger("sorton", [sorting]);
            return false;
        });
        var i3 = 0;
        $("#i3").click(function () {
            $('#cles_table').find('th').attr('class','');
            if(i3===0){
                i3=1;
                $(this).removeClass('headerSortDown');
                $(this).addClass('headerSortUp');
            }else if(i3===1){
                i3=0;
                $(this).removeClass('headerSortUp');
                $(this).addClass('headerSortDown');
            }
            var sorting = [[2, i3]];
            $("#sec_table").trigger("sorton", [sorting]);
            return false;
        });
        var i4 = 0;
        $("#i4").click(function () {
            $('#cles_table').find('th').attr('class','');
            if(i4===0){
                i4=1;
                $(this).removeClass('headerSortDown');
                $(this).addClass('headerSortUp');
            }else if(i4===1){
                i4=0;
                $(this).removeClass('headerSortUp');
                $(this).addClass('headerSortDown');
            }
            var sorting = [[3, i4]];
            $("#sec_table").trigger("sorton", [sorting]);
            return false;
        });

    })

</script>