<div style="position: relative">   
    <ul id="newsContent">
        <?php foreach ($resources as $xmlObject) { ?>
            <li style="border-bottom:1px solid rgb(100,100,100);margin-top: 10px">
                <p style="font-weight:bold"><a href="<?php echo $xmlObject['link']; ?>" target="_blank"><?php echo $xmlObject['title']; ?></a></p>
                <small><?php echo $xmlObject['pubDate']; ?></small>
            </li>
        <?php } ?>
    </ul>
</div>   

<div>
    <?php echo $current_page != 0 ? '<span id="previous_page"><</span>' : '' ?>
    <?php for ($i = 0; $i < $pagesCount; $i++) { ?>
        <?php if ($current_page == $i) { ?>
            <span class="pagination_item" style="color:red" id="<?= $i ?>"><?= $i + 1 ?></span>&nbsp;
        <?php } else if (abs($i - $current_page) < 5) { ?>
            <span class="pagination_item" style="cursor: pointer" id="<?= $i ?>"><?= $i + 1 ?></span>&nbsp;
        <?php } ?>
    <?php } ?>
    <?php echo ($current_page != ($pagesCount - 1)) ? '<span id="next_page">></span>' : '' ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.pagination_item').on('click', function () {

            var id = $(this).attr('id');
            $("#tabContent").load("/news/load-news?page=" + id, function () {

            });
        });
        $('#previous_page').on('click', function () {

            var id = <?= $current_page - 1 ?>;
            $("#tabContent").load("/news/load-news?page=" + id, function () {

            });
        });
        $('#next_page').on('click', function () {

            var id = <?= $current_page + 1 ?>;
            $("#tabContent").load("/news/load-news?page=" + id, function () {

            });
        });
    });
</script>