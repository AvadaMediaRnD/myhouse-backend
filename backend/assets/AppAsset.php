<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

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
        'bower_components/bootstrap/dist/css/bootstrap.min.css',
        'bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',
        'bower_components/font-awesome/css/font-awesome.min.css',
        'bower_components/Ionicons/css/ionicons.min.css',
        'bower_components/jvectormap/jquery-jvectormap.css',
        'dist/css/AdminLTE.min.css',
        'plugins/iCheck/flat/blue.css',
        'plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css',
        'dist/css/skins/_all-skins.min.css',
        'dist/css/style.css',
        'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic',
        'custom.css',
    ];
    public $js = [
//        'bower_components/jquery/dist/jquery.min.js',
        'bower_components/bootstrap/dist/js/bootstrap.min.js',
        'bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
        'bower_components/fastclick/lib/fastclick.js',
        'dist/js/adminlte.min.js',
        'plugins/iCheck/icheck.min.js',
        'plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.js',
        'plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.ru-RU.js',
        'bower_components/jquery-sparkline/dist/jquery.sparkline.min.js',
        'plugins/jvectormap/jquery-jvectormap-1.2.2.min.js',
        'plugins/jvectormap/jquery-jvectormap-world-mill-en.js',
        'bower_components/jquery-slimscroll/jquery.slimscroll.min.js',
        'bower_components/chart.js/Chart.js',
        'dist/js/main.js',
        'custom.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        // 'yii\bootstrap\BootstrapAsset',
    ];
}
