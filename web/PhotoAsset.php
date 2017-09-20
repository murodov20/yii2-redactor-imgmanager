<?php
/**
 * Created by mirjalol.
 * Date: 9/11/2017
 * Time: 10:59 PM
 */

namespace murodov20\redactor\web;


use yii\web\AssetBundle;

class PhotoAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../assets';

    public $plugins = [];

    public $css = [
        'photo/photo.css',
        'base/style.css'
    ];
    public $js = [
        'photo/photo.js',
        'base/script.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'murodov20\redactor\web\VenoBoxAsset'
    ];

}
