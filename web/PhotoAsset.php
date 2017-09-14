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
    public $sourcePath = __DIR__ . '/../assets/photo';

    public $plugins = [];

    public $css = [
        'photo.css'
    ];
    public $js = [
        'photo.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'murodov20\redactor\web\VenoBoxAsset',
        'murodov20\redactor\web\BaseAsset',
    ];

}
