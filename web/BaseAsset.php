<?php

/**
* Created by mirjalol
*/

namespace murodov20\redactor\web;

use yii\web\AssetBundle;

class BaseAsset extends AssetBundle {

	public $sourcePath = __DIR__ . '/../assets/base';


	public $css = [
        'style.css'
    ];

    public $js = [
        'script.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'murodov20\redactor\web\VenoBoxAsset',
    ];
}
