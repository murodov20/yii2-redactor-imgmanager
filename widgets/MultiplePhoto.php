<?php
/**
 * Created by mirjalol.
 * Date: 9/11/2017
 * Time: 4:40 PM
 */

namespace murodov20\redactor\widgets;


use murodov20\redactor\web\PhotoAsset;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\web\UrlManager;
use yii\widgets\InputWidget;

/**
 * Class MultiplePhoto Use this widget when you want add multiple images to one model
 * This widget uses MultiplePhoto widget and PhotoManager module
 * @package murodov20\redactor
 */
class MultiplePhoto extends InputWidget
{
    /**
     * @var string Item css class
     */
    public $itemCssClass = 'col-md-4';

    /**
     * @var int
     */
    public $imgWidth = 150;

    /**
     * @var int
     */
    public $imgHeight = 150;

    /**
     * @var string|array Image manager load url
     */
    public $imageLoadUrl = '/photo-manager/image/get';

    /**
     * @var string Url for load one image src by image id
     *
     */
    public $srcByIdUrl = '/photo-manager/image/src-by-id';

    /**
     * @var string Url for load one image src by image id
     *
     */
    public $generateUrl = '/photo-manager/image/add-to-widget';

    /**
     * @var string|UrlManager UrlManager component that will build $imageLoadURl
     * defaults to urlManager
     */
    public $urlManager = 'urlManager';

    /**
     * @var bool Whether display only thumb image on exemplar image
     * defaults to false
     */
    public $thumbOnly = true;

    /**
     * @var string Source image for 'no image' label
     * for example: '@web/uploads/no-image.png' or '/uploads/no-image.png'
     * if parameter will be null, Extension's no-image fill will be load
     */
    public $noImageSrc = null;


    /**
     * @var array
     */
    private $_values = [];

    /**
     * @var int
     */
    private $_last = 1;

    /**
     * @var integer will be generated with model and attribute fields
     */
    private $_id;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->model === null or $this->attribute === null)
            throw new InvalidConfigException('"model" and "attribute" parameters must be set');
        if (!$this->urlManager instanceof UrlManager) {
            if (is_string($this->urlManager) && ($manager = \Yii::$app->get($this->urlManager)) !== null && $manager instanceof UrlManager) {
                $this->urlManager = $manager;
            } else {
                throw new InvalidConfigException('"urlManager" parameter must be string or instance of yii\\web\\UrlManager');
            }
        }
        $this->generateUrl = $this->urlManager->createUrl($this->generateUrl);
        $this->_id = Html::getInputId($this->model, $this->attribute);
        $attr = $this->attribute;
        if (is_array($this->model->$attr) && !empty($this->model->$attr)) {
            foreach ($this->model->$attr as $item) {
                $this->_values[] = $item;
                $this->_last++;
            }
        }
        PhotoAsset::register($this->view);

        if ($this->noImageSrc === null) {
            $this->noImageSrc = \Yii::$app->assetManager->getPublishedUrl('@vendor/murodov20/redactor/assets') . '/photo/no-image.png';
        }
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
        parent::run();
        $this->renderWidget();
    }

    /**
     * Renders the Multiple photo widget
     */
    protected function renderWidget()
    {
        echo Html::beginTag('div', [
            'class' => 'multiple-photo',
            'id' => $this->_id
        ]);
        $this->renderPlus();
        echo Html::beginTag('div', [
            'class' => 'multiple-photo-items',
        ]);
        $this->renderItems();
        echo Html::endTag('div');
        echo Html::endTag('div');
    }

    /**
     * Renders the plus button
     */
    protected function renderPlus()
    {
        $sampleId = Html::getInputId($this->model, $this->attribute);
        $sampleName = Html::getInputName($this->model, $this->attribute);
        echo Html::beginTag('div', [
            'class' => 'col-xs-12 plus-item'
        ]);
        echo Html::beginTag('div', [
            'class' => 'plus-abs',
            'data-toggle' => 'tooltip',
            'title' => Yii::t('redactor', 'Add image'),
            'last' => $this->_last,
            'generateUrl' => $this->generateUrl,
            'sampleName' => $sampleName,
            'sampleId' => $sampleId,
            'imgWidth' => $this->imgWidth,
            'imgHeight' => $this->imgHeight,
            'itemCssClass' => $this->itemCssClass,
            'imageLoadUrl' => $this->imageLoadUrl,
            'srcByIdUrl' => $this->srcByIdUrl,
            'thumbOnly' => $this->thumbOnly ? '1' : '0',
            'noImageSrc' => $this->noImageSrc
        ]);
        echo '<i class="glyphicon glyphicon-plus"></i>';
        echo Html::endTag('div');
        echo Html::endTag('div');
    }

    /**
     * Renders the items - PhotoInputWidgets
     */
    protected function renderItems()
    {
        $i = 1;
        foreach ($this->_values as $value) {
            echo Html::beginTag('div', ['class' => $this->itemCssClass . ' image-item']);
            $id = Html::getInputId($this->model, $this->attribute . '[' . $i . ']');
            $name = Html::getInputName($this->model, $this->attribute . '[' . $i . ']');
            echo PhotoInputWidget::widget([
                'inputId' => $id,
                'name' => $name,
                'value' => $value,
                'imgWidth' => $this->imgWidth,
                'imgHeight' => $this->imgHeight,
                'imageLoadUrl' => $this->imageLoadUrl,
                'srcByIdUrl' => $this->srcByIdUrl,
                'urlManager' => $this->urlManager,
                'thumbOnly' => $this->thumbOnly,
                'noImageSrc' => $this->noImageSrc,
                'multi' => true
            ]);
            echo Html::endTag('div');
            $i++;
        }
    }

}
