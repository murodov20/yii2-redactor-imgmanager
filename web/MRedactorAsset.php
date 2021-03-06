<?php
/**
 * Created by mirjalol.
 * Date: 9/8/2017
 * Time: 1:20 PM
 */

namespace murodov20\redactor\web;


use yii\web\AssetBundle;

class MRedactorAsset extends AssetBundle
{

    public $sourcePath = __DIR__ . '/../assets';

    /**
     * @var string language for Redactor widget
     */
    public $language = 'ru';

    /**
     * @var array plugins for Redactor widget
     */
    public $plugins = [];

    public $css = [
        'redactor/redactor.css',
        'base/style.css'
    ];

    public $js = [
        'redactor/redactor.min.js',
        'base/script.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'murodov20\redactor\web\VenoBoxAsset'
    ];

    /**
     * Register asset bundle language files and plugins.
     * @param \yii\web\View $view
     */
    public function registerAssetFiles($view)
    {
        if ($this->language !== null) {
            $this->js[] = 'redactor/lang/' . $this->language . '.js';
        }
        if (!empty($this->plugins)) {
            foreach ($this->plugins as $plugin) {
                if ($plugin === 'clips') {
                    $this->css[] = 'redactor/plugins/' . $plugin . '/' . $plugin . '.css';
                }
                $this->js[] = 'redactor/plugins/' . $plugin . '/' . $plugin . '.js';
            }
        }
        parent::registerAssetFiles($view);
    }

}
