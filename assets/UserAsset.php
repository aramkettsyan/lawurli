<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class UserAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'include/jquery-ui/jquery-ui-1.9.2.custom.css',
        'https://fontastic.s3.amazonaws.com/uQrZqTnHFXueUkKYTVpcnZ/icons.css',
        'https://fonts.googleapis.com/css?family=Roboto:300,400,500',
        'include/animated-headline/animated-headline.css',
        'include/magnific-popup/magnific-popup.css',
        'include/custom-scrollbar/jquery.mCustomScrollbar.css',
        'include/flexslider/flexslider.css',
        'css/fileuploader.css',
        'css/main.css'
    ];
    public $jsOptions = array(
        'position' => \yii\web\View::POS_HEAD
    );
    public $js = [
        'js/fileuploader.js',
        'js/jquery.validate.js',
        'include/webfontloader/webfontloader.js',
        'include/jquery/jquery-2.1.4.js',
        'include/jquery-ui/jquery-ui-1.9.2.custom.js',
        'include/animated-headline/animated-headline.js',
        'include/magnific-popup/jquery.magnific-popup.js',
        'include/custom-scrollbar/jquery.mCustomScrollbar.js',
        'include/flexslider/jquery.flexslider-min.js',
        "js/jquery.tablesorter.js",
        'include/main.js',
        'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places'
    ];
    public $depends = [
        'yii\web\YiiAsset'
    ];

}
