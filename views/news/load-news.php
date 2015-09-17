<?php if(!empty($resources)){ ?>    
<div style="position: relative">   
        <ul id="newsContent" class="news_cont">
            <?php foreach ($resources as $xmlObject) { ?>
                <li>
                    <p style="font-weight:bold"><a href="<?php echo $xmlObject['link']; ?>" target="_blank"><?php echo $xmlObject['title']; ?></a></p>
                    <time><?php echo $xmlObject['pubDate']; ?></time>
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
<?php }else{ ?>
    <div>
        There are no news...
    </div>
<?php } ?>