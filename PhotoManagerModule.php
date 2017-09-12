<?php
/**
 * Created by mirjalol.
 * Date: 9/8/2017
 * Time: 5:07 PM
 */

namespace murodov20\redactor;


use Yii;
use yii\base\Module;
use yii\helpers\FileHelper;
use yii\web\UrlManager;

/**
 * Class PhotoManagerModule Use this module for listing images
 * @package murodov20\redactor
 */
class PhotoManagerModule extends Module
{

    /**
     * Module version
     */
    const VERSION = '0.0.1';

    /**
     * @var string Upload path for images
     * defaults to '@frontend/web/files/uploads'
     * You can change this in module options
     *       [
     *      ...
     *           'uploadPath' => '@webroot/web/files/uploads'
     *      ...
     *      ]
     */
    public $uploadPath = '@frontend/web/files/uploads';

    /**
     * @var string Upload url of path of image for displaying image
     * defaults to '/files/uploads'
     * Url will be generate with $urlManager parameter and $isAbsolute parameter
     */
    public $uploadUrl = '/files/uploads';

    /**
     * @var null|UrlManager|string Urlmanager that will be used in url generation of displaying image
     * defaults to null and will be set \Yii::$app->urlManager
     * when $isAbsolute will be true generates absolute url
     */
    public $urlManager = null;

    /**
     * @var bool whether create absolute url
     * defaults to false
     */
    public $isAbsolute = false;

    /**
     * @var bool whether create new unique name for image
     * defaults to true
     */
    public $saveUnique = true;


    /**
     * @var array Validator options for image upload
     * defaults to [] and will be set ['maxWidth' => 1400, 'maxHeight' => 1000]
     */
    public $validatorOptions = [];

    /**
     * @var string Model class
     * uses in upload action for saving to database
     * save() method will be call
     * assign null if you don't want save to database (TODO this feature will be add to next releases)
     */
    public $photoClass = 'murodov20\redactor\entities\MPhoto';

    /**
     * @var string attribute for filename when saving to database
     * Uses when $photoClass not null
     * defaults to 'filename'
     */
    public $filenameAttribute = 'filename';

    /**
     * @var string|null Folder for saving thumbnail images located in $uploadPath
     * Thumbnail will be generated via Imagick extension when
     * original image size will be larger than thumbnail size that will be specify
     * via $thumbWidth and $thumbHeight attributes
     * defaults to 'thumbs'
     * If set to null, thumbnails will not be generated
     */
    public $thumbFolder = 'thumbs';

    /**
     * @var int Image thumbnail width
     */
    public $thumbWidth = 160;

    /**
     * @var int Image thumbnail height
     */
    public $thumbHeight = 160;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (is_string($this->urlManager) && ($manager = \Yii::$app->get($this->urlManager)) !== null && $manager instanceof UrlManager) {
            $this->urlManager = $manager;
        } elseif ($this->urlManager === null && !($this->urlManager instanceof UrlManager)) {
            $this->urlManager = Yii::$app->urlManager;
        }
        if (empty($this->validatorOptions))
            $this->validatorOptions = ['maxWidth' => 1400, 'maxHeight' => 1000];
        if (!FileHelper::createDirectory(Yii::getAlias($this->uploadPath)))
            return;
        if (($this->thumbFolder !== null) && !FileHelper::createDirectory(Yii::getAlias($this->uploadPath . DIRECTORY_SEPARATOR . $this->thumbFolder)))
            return;
        \Yii::$app->i18n->translations['redactor'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en',
            'basePath' => __DIR__ . '/messages'
        ];
    }

}
