<?php if (!empty($resources)) { ?>    
    <div style="position: relative">   
        <ul id="newsContent" class="news_cont">
            <li>

                <div class="clearAfter">
                    <div class="newsImg">
                        <img src="/images/news.jpg" alt="">
                    </div>
                    <div class="newsCont">
                        <span><span class="newsSource">News Source</span> shared:</span>
                        <h6>Lorem ipsum dolor.</h6>
                        <p>Delectus labore natus quia quisquam voluptatem. Eos eum, fuga illo maxime necessitatibus quaerat totam veniam!</p>
                        <time>October 14, 2015 3:34 AM</time>
                    </div>
                </div>
            </li>
            <?php foreach ($resources as $xmlObject) { ?>
                <li>
                    <p style="font-weight:bold"><a href="<?php echo $xmlObject['link']; ?>" target="_blank"><?php echo $xmlObject['title']; ?></a></p>
                    <?php if (!empty($xmlObject['pubDate'])) { ?>
                        <time><?php echo $xmlObject['pubDate']; ?></time>
                    <?php } ?>
                    <?php // $ex = explode('/', $xmlObject['site_url']) ?>
                    <?php // $formatted_url = $ex[2] ?>
                    <?php // if (substr($formatted_url, 0, 4) === 'www.') { ?>
                        <?php // $formatted_url = substr($formatted_url, 4, strlen($formatted_url)); ?>
                    <?php // } ?>
                    <?php // $formatted_url = 'www.' . $formatted_url; ?>
                        <?php $formatted_url = $xmlObject['site_title'] ?>
                    <time><a target="_blank" href="<?php echo $xmlObject['site_url']; ?>"><?php echo $formatted_url; ?></a></time>
                </li>
            <?php } ?>
        </ul>
        <div class="pagin">
            <ul class="pagination">
                <?php echo $current_page != 0 ? '<li id="previous_page" class="prev"><a style="cursor:pointer">«</a></li>' : '<li class="prev disabled"><span>«</span></li>' ?>
                <?php $count = 0; ?>
                <?php $checkPos = 5 - ($pagesCount - $current_page) ?>
                <?php for ($i = 0; $i < $pagesCount; $i++) { ?>
                    <?php if ($current_page == $i) { ?>
                        <?php $count++; ?>
                        <li class="pagination_item active" id="<?= $i ?>"><a><?= $i + 1 ?></a></li>&nbsp;
                    <?php } else if ($i < $current_page && abs($i - $current_page) < 6 || ($checkPos > 0 && abs($i - $current_page) < (6 + $checkPos))) { ?>
                        <?php if ($checkPos > 0) { ?>
                            <?php $checkPos--; ?>
                        <?php } ?>
                        <?php $count++; ?>
                        <li class="pagination_item" style="cursor: pointer" id="<?= $i ?>"><a><?= $i + 1 ?></a></li>&nbsp;
                    <?php } ?>
                    <?php if ($i > $current_page && $count < 10) { ?>
                        <?php $count++; ?>
                        <li class="pagination_item" style="cursor: pointer" id="<?= $i ?>"><a><?= $i + 1 ?></a></li>&nbsp;
                            <?php } ?>
                        <?php } ?>
                        <?php echo ($current_page != ($pagesCount - 1)) ? '<li id="next_page" class="next"><a style="cursor:pointer">»</a></li>' : '<li class="next disabled"><span>»</span></li>' ?>
            </ul>
        </div>
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
<?php } else { ?>
    <div>
        There are no news...
    </div>
<?php } ?>