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
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
    public $css = [
        'css/site.css',
        'css/loader.css',
        'css/order.css'
    ];
    public $cssOptions = [
        'type' => 'text/css',
    ];
    public $js = [
        'js/site.js',
//        'https://api-maps.yandex.ru/2.1/?apikey=16eacdd2-acfd-4122-b0c7-639d10363985&lang=ru_RU'
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\YiiAsset',
        'mazurva\web\DaDataAsset',
    ];
}
