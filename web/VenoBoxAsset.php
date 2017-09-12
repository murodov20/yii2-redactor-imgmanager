<?php
/**
 * Created by mirjalol.
 * Date: 9/11/2017
 * Time: 2:52 PM
 */

namespace murodov20\redactor\web;


use yii\web\AssetBundle;

class VenoBoxAsset extends AssetBundle
{

    public $sourcePath = '@bower/venobox/venobox';

    public $css = [
        'venobox.css'
    ];

    public $js = [
        'venobox.min.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
