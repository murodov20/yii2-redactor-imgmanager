<?php
/**
 * Created by mirjalol.
 * Date: 9/10/2017
 * Time: 3:09 PM
 */

namespace murodov20\redactor\widgets;


use murodov20\redactor\web\PhotoAsset;
use murodov20\redactor\web\VenoBoxAsset;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\web\UrlManager;
use yii\widgets\InputWidget;

/**
 * Class PhotoInputWidget Use this widget when you want to add image to model, for example page's or new's logo
 * This widget uses PhotoManager module
 * @package murodov20\redactor
 */
class PhotoInputWidget extends InputWidget
{

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
     */
    public $srcByIdUrl = '/photo-manager/image/src-by-id';

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
     * @var bool Indicator for multi widget. When MultiplePhoto widget will be use this attribute
     * Otherwise don't use this parameter
     */
    public $multi = false;

    /**
     * @var string Html id for image input
     * Only in multiple mode will be use
     * Otherwise don't use this parameter
     */
    public $inputId;

    /**
     * @var string Input value for image hidden input
     * Only in multiple mode will be use
     * Otherwise don't use this parameter
     */
    public $value = '';

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        if ($this->multi) {
            if ($this->inputId === null || $this->name === null) {
                throw new InvalidConfigException('If you use multiple input, fill "inputId" and "name" fields');
            }
        }
        if (!$this->urlManager instanceof UrlManager) {
            if (is_string($this->urlManager) && ($manager = \Yii::$app->get($this->urlManager)) !== null && $manager instanceof UrlManager) {
                $this->urlManager = $manager;
            } else {
                throw new InvalidConfigException('"urlManager" parameter must be string or instance of yii\\web\\UrlManager');
            }
        }
        $this->imageLoadUrl = $this->urlManager->createUrl($this->imageLoadUrl);
        $this->srcByIdUrl = $this->urlManager->createUrl($this->srcByIdUrl);
        VenoBoxAsset::register($this->view);
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
        $this->renderPhotoInput();
    }

    /**
     * Renders the photo input
     */
    protected function renderPhotoInput()
    {
        $id = $this->multi ? $this->inputId : Html::getInputId($this->model, $this->attribute);
        echo Html::beginTag('div', ['class' => 'thumbnail', 'id' => $id . 'Widget']);
        echo Html::beginTag('a', ['href' => $this->noImageSrc, 'id' => $id . 'Cover', 'class' => 'has-plus venobox-piw', 'style' => ['position' => 'relative']]);
        echo Html::img($this->noImageSrc, ['id' => $id . 'Image', 'class' => 'placed-img', 'style' => ['width' => $this->imgWidth . 'px', 'height' => $this->imgHeight . 'px']]);
        echo Html::beginTag('div', ['class' => 'rollover']);
        echo '<i class="glyphicon glyphicon-zoom-in" style="font-size: 4em"></i>';
        echo Html::endTag('div');
        echo Html::endTag('a');
        echo Html::button(Yii::t('redactor', 'Select'), ['type' => 'button', 'class' => 'btn btn-primary btn-flat btn-block', 'data-toggle' => 'modal', 'data-target' => '#' . $id . 'Modal']);
        $removerStyle = ['display' => 'none'];
        if ($this->multi) {
            $removerStyle = ['visibility' => 'visible'];
        }
        echo Html::button('<i class="glyphicon glyphicon-remove" ></i> ' . Yii::t('redactor', 'Remove image'), ['data-id' => $id, 'id' => $id . 'Remove', 'type' => 'button', 'class' => 'btn btn-danger btn-flat btn-block image-remover ' . ($this->multi ? 'multi-remover' : ''), 'style' => $removerStyle, 'no-image' => $this->noImageSrc]);
        if ($this->multi) {
            echo Html::hiddenInput($this->name, $this->value, [
                'id' => $id,
                'class' => 'hidden-widget-input',
                'srcByIdUrl' => $this->srcByIdUrl,
                'hasThumb' => $this->thumbOnly ? '1' : '0',
                'noImageSrc' => $this->noImageSrc,
                'multi' => $this->multi ? '1' : '0'
            ]);
        } else {
            echo Html::activeHiddenInput($this->model, $this->attribute, [
                'class' => 'hidden-widget-input',
                'srcByIdUrl' => $this->srcByIdUrl,
                'hasThumb' => $this->thumbOnly ? '1' : '0',
                'noImageSrc' => $this->noImageSrc,
                'multi' => $this->multi ? '1' : '0'
            ]);
        }
        echo Html::endTag('div');
        $this->renderModal($id);
    }


    /**
     * Renders modal for photo widget
     * @param $id string
     */
    protected function renderModal($id)
    {
        $modalId = $id . 'Modal';
        echo Html::beginTag('div', [
            'class' => 'modal fade image-selector',
            'id' => $modalId,
            'tabindex' => '-1',
            'role' => 'dialog',
            'aria-labelledby' => $modalId,
            'select-for' => $id,
            'loadUrl' => $this->imageLoadUrl
        ]);
        echo Html::beginTag('div', [
            'class' => 'modal-dialog',
            'role' => 'document',
        ]);
        echo Html::beginTag('div', [
            'class' => 'modal-content',
        ]);
        echo Html::beginTag('div', [
            'class' => 'modal-header',
        ]);
        echo Html::beginTag('button', ['type' => 'button', 'class' => 'close', 'data-dismiss' => 'modal', 'aria-label' => 'Close']);
        echo '<span aria-hidden="true">&times;</span>';
        echo Html::endTag('button');
        echo Html::beginTag('h3', ['class' => 'modalTitle', 'id' => $modalId . 'Label']);
        echo Yii::t('redactor', 'Select image');
        echo Html::endTag('h3');
        echo Html::endTag('div');
        echo Html::beginTag('div', [
            'class' => 'modal-body',
        ]);
        echo Html::endTag('div');
        echo Html::endTag('div');
        echo Html::endTag('div');
        echo Html::endTag('div');
    }

}
