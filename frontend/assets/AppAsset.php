<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/fontawesome-all.min.css',
        'css/bootstrap.min.css',
        // 'css/lightgallery.css',
        'css/slick.css',
        'css/style.css',
        
        'js/simplelightbox/simplelightbox.min.css',
    ];
    public $js = [
        'js/bootstrap.min.js',
        // 'js/lightgallery-all.min.js',
        'js/slick.min.js',
        'js/main.js',
        
        'js/simplelightbox/simple-lightbox.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        // 'yii\bootstrap\BootstrapAsset',
    ];
}
