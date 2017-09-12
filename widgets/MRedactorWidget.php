<?php
/**
 * Created by mirjalol.
 * Date: 9/8/2017
 * Time: 1:08 PM
 */

namespace murodov20\redactor\widgets;

use murodov20\redactor\web\MRedactorAsset;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;

/**
 * Class MRedactorWidget Widget for Imperavi Redactor with image manager functionality via database
 * Thanks to @vova07 for [vova07/yii2-imperavi-widget](https://github.com/vova07/yii2-imperavi-widget)
 * This widget extends image manager of redactor
 * To use this widget, you need to setup PhotoManager module
 * You can use Imperavi Redactor's this version for commercial projects If you are using Yii
 * Because Yii community bought OEM license for it
 * But you can't update redactor for commercial projects with this license
 * @package murodov20\redactor
 */
class MRedactorWidget extends Widget
{
    public $model;
    public $attribute;
    public $name;
    public $value;
    public $selector;
    public $htmlOptions = [];
    public $settings = [];
    public $defaultSettings = [];
    public $plugins = [];
    private $_renderTextarea = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->name === null && !$this->hasModel() && $this->selector === null) {
            throw new InvalidConfigException("Either 'name', or 'model' and 'attribute' properties must be specified.");
        }
        if (!isset($this->htmlOptions['id'])) {
            $this->htmlOptions['id'] = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->getId();
        }
        if (!empty($this->defaultSettings)) {
            $this->settings = ArrayHelper::merge($this->defaultSettings, $this->settings);
        }
        if (isset($this->settings['plugins']) && !is_array($this->settings['plugins']) || !is_array($this->plugins)) {
            throw new InvalidConfigException('The "plugins" property must be an array.');
        }
        if (!isset($this->settings['lang']) && Yii::$app->language !== 'en-US') {
            $this->settings['lang'] = substr(Yii::$app->language, 0, 2);
        }
        if ($this->selector === null) {
            $this->selector = '#' . $this->htmlOptions['id'];
        } else {
            $this->_renderTextarea = false;
        }

        // @codeCoverageIgnoreStart
        $request = Yii::$app->getRequest();

        if ($request->enableCsrfValidation) {
            $this->settings['uploadImageFields'][$request->csrfParam] = $request->getCsrfToken();
            $this->settings['uploadFileFields'][$request->csrfParam] = $request->getCsrfToken();
        }
        // @codeCoverageIgnoreEnd

        \Yii::$app->i18n->translations['redactor'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en',
            'basePath' => __DIR__ . '/../messages'
        ];
        parent::init();
    }


    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->register();

        if ($this->_renderTextarea === true) {
            if ($this->hasModel()) {
                return Html::activeTextarea($this->model, $this->attribute, $this->htmlOptions);
            } else {
                return Html::textarea($this->name, $this->value, $this->htmlOptions);
            }
        }
        return parent::run();
    }

    /**
     * Register all widget logic.
     */
    protected function register()
    {
        $this->registerDefaultCallbacks();
        $this->registerClientScripts();
    }

    /**
     * Register widget asset.
     */
    protected function registerClientScripts()
    {
        $view = $this->getView();
        $selector = Json::encode($this->selector);
        $asset = Yii::$container->get(MRedactorAsset::className());
        $asset = $asset::register($view);

        if (isset($this->settings['lang'])) {
            $asset->language = $this->settings['lang'];
        }
        if (isset($this->settings['plugins'])) {
            $asset->plugins = $this->settings['plugins'];
        }
        if (!empty($this->plugins)) {
            /** @var \yii\web\AssetBundle $bundle Asset bundle */
            foreach ($this->plugins as $plugin => $bundle) {
                $this->settings['plugins'][] = $plugin;
                $bundle::register($view);
            }
        }

        $settings = !empty($this->settings) ? Json::encode($this->settings) : '';

        $view->registerJs("jQuery($selector).redactor($settings);", $view::POS_READY, $this->htmlOptions['id']);
    }

    /**
     * Register default callbacks.
     */
    protected function registerDefaultCallbacks()
    {
        if (isset($this->settings['imageUpload']) && !isset($this->settings['imageUploadErrorCallback'])) {
            $this->settings['imageUploadErrorCallback'] = new JsExpression('function (response) { alert(response.error); }');
        }
        if (isset($this->settings['fileUpload']) && !isset($this->settings['fileUploadErrorCallback'])) {
            $this->settings['fileUploadErrorCallback'] = new JsExpression('function (response) { alert(response.error); }');
        }
    }

    /**
     * @return boolean whether this widget is associated with a data model.
     */
    protected function hasModel()
    {
        return $this->model instanceof Model && $this->attribute !== null;
    }

}
