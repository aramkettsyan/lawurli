

<?php Yii::$app->view->params['user'] = $user; ?>
<div class="container mainContainer">
    <div class="profileL">
        <div class="userImage">
            <?php $filename = \Yii::getAlias('@webroot') . '/images/users_images/' . $user->image; ?>
            <?php if (is_file($filename)) { ?>
                <img src="/images/user-1.png">
                <span class="profileImage" style="background-image: url('<?php echo \Yii::getAlias('@web') . '/images/users_images/' . $user->image; ?>')"></span>
            </div>

        <!--<img src="<?php echo \Yii::getAlias('@web') . '/images/users_images/' . $user->image; ?>"  alt="User image" >-->
        <?php } ?>
        <p style="color:green;display: none" id="imageUploadSuccess">Image uploaded successfully!</p>
        <p style="color:red;display: none" id="imageUploadError"></p>

        <div class="userDetails">
            <h3 class="userName"><?= $user->first_name ?> <?= $user->last_name ?></h3>
            <!--            <div class="proffInfo">
                            <span class="userProff">Bandit</span> 
                        </div>-->
            <ul class="listWithIcons">
                <li>
                    <i class="icon-location"></i>
                    <p>Clay County, Missouri, US</p>
                </li>
                <li>
                    <i class="icon-smart-phone-2"></i>
                    <p>+421 756 32 12</p>
                </li>
                <li>
                    <i class="icon-letter-mail-1"></i>
                    <p><?= $user->email ?></p>
                </li>
            </ul>
        </div>
    </div>
    <div class="profileR">
        <div class="profileTabs">
            <ul class="clearAfter">
                <li class="active"><a href="my-profile.html"><i class="icon-card-user-2"></i>My Profile</a></li>
                <li><a href="my-colleagues.html"><i class="icon-contacts"></i>Colleagues</a></li>
                <li><a href="my-notifications.html"><i class="icon-bell-two"></i>Notifications</a></li>
            </ul>
        </div>
        <div class="tabsContent">
            <div class="cvTimeline">
                <h4>Education</h4>
                <div class="cvSub">
                    <div class="cvSubLabel">
                        <h5>University</h5>
                    </div>
                    <div class="cvSubCont">
                        <ul>
                            <li>
                                <label class="cvSingleLabel">University Name</label>
                                <p class="cvSingleDet">Blah blah University</p>
                                <div class="labelValue">
                                    <label>Some Label</label>
                                    <span>Blah blha value</span>
                                </div>
                                <div class="labelValue">
                                    <label>Another label</label>
                                    <span>Blah blha value</span>
                                </div>
                            </li>
                            <li>
                                <p class="cvSingleTitle">At eos, delectus cum deleniti non.</p>
                                <p class="cvSingleDet">Asperiores deleniti quidem quo nobis quae quos alias amet labore nostrum minus consectetur corrupti esse nihil, vel odio ducimus temporibus quaerat qui!</p>
                            </li>
                            <li>
                                <p class="cvSingleTitle">Quis molestias reprehenderit repellat placeat.</p>
                                <p class="cvSingleDet">Adipisci tenetur, voluptate architecto omnis obcaecati earum cum laborum dolores vero ipsam tempore officiis voluptatum delectus inventore, corporis repellat quo pariatur accusantium?</p>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="cvSub">
                    <div class="cvSubLabel">
                        <h5>School</h5>
                    </div>
                    <div class="cvSubCont">
                        <ul>
                            <li>
                                <p class="cvSingleTitle">Lorem ipsum dolor sit amet</p>
                                <p class="cvSingleDet">Fugiat molestias odit sapiente ipsam inventore maiores voluptates sed doloribus beatae explicabo, nisi minima quae iusto, facere velit sunt commodi.</p>
                                <div class="labelValue">
                                    <label>Some Label</label>
                                    <span>Blah blha value</span>
                                </div>
                                <div class="labelValue">
                                    <label>Another label</label>
                                    <span>Blah blha value</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="cvTimeline">
                <h4>military service</h4>
                <div class="cvSub">
                    <div class="cvSubCont">
                        <ul>
                            <li>
                                <p class="cvSingleTitle">Lorem ipsum dolor sit amet</p>
                                <p class="cvSingleDet">Fugiat molestias odit sapiente ipsam inventore maiores voluptates sed doloribus beatae explicabo, nisi minima quae iusto, facere velit sunt commodi.</p>
                            </li>
                            <li>
                                <p class="cvSingleTitle">At eos, delectus cum deleniti non.</p>
                                <p class="cvSingleDet">Asperiores deleniti quidem quo nobis quae quos alias amet labore nostrum minus consectetur corrupti esse nihil, vel odio ducimus temporibus quaerat qui!</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>