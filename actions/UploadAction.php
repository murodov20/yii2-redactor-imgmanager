<?php
/**
 * Created by mirjalol.
 * Date: 9/9/2017
 * Time: 10:48 AM
 */

namespace murodov20\redactor\actions;


use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use murodov20\redactor\components\ManageController;
use Yii;
use yii\base\Action;
use yii\base\DynamicModel;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * Class UploadAction Action for Upload photo
 * @package murodov20\redactor\actions
 */
class UploadAction extends Action
{

    /**
     * @var string Where to upload
     */
    public $uploadPath;

    /**
     * @var string Url for upload folder
     */
    public $uploadUrl;

    /**
     * @var string Upload parameter, Don't change if you haven't any target
     */
    public $uploadParam = 'file';

    /**
     * @var bool whether to create unique name for file
     */
    public $unique = true;

    /**
     * @var array Validator options
     */
    public $validatorOptions = [];

    /**
     * @var string Model class
     * uses for saving to database
     * save() method will be call
     * assign null if you don't want save to database
     */
    public $photoClass = 'murodov20\redactor\entities\MPhoto';

    /**
     * @var string attribute for filename when saving to database
     * Uses when $photoClass not null
     */
    public $filenameAttribute = 'filename';

    /**
     * @var string|null Folder for saving thumbnail images located in $uploadPath
     * Thumbnail will be generated via GD or Imagick plugin when
     * original image size will be larger than thumbnail size that will be specify
     * via $thumbWidth and $thumbHeight attributes
     * defaults to 'thumbs'
     * If set to null, thumbnails will not be generated
     */
    public $thumbFolder = 'thumbs';

    /**
     * @var int Image thumbnail width
     */
    public $thumbWidth = 120;

    /**
     * @var int Image thumbnail height
     */
    public $thumbHeight = 120;

    /**
     * @var null|string Full path for thumb
     */
    private $_thumbPath = null;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if ($this->uploadUrl === null) {
            throw new InvalidConfigException('The "url" attribute must be set.');
        } else {
            $this->uploadUrl = rtrim($this->uploadUrl, '/') . '/';
        }
        if ($this->uploadPath === null) {
            throw new InvalidConfigException('The "uploadPath" attribute must be set.');
        } else {
            $this->uploadPath = rtrim(Yii::getAlias($this->uploadPath), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

            if (!FileHelper::createDirectory($this->uploadPath)) {
                throw new InvalidCallException("Directory specified in 'uploadPath' attribute doesn't exist or cannot be created.");
            }
            if ($this->thumbFolder !== null) {
                $this->_thumbPath = rtrim($this->uploadPath . $this->thumbFolder, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
                if (!FileHelper::createDirectory($this->_thumbPath))
                    return;
            }

        }
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function run()
    {
        if (Yii::$app->request->isPost) {
            $file = UploadedFile::getInstanceByName($this->uploadParam);
            $model = new DynamicModel(compact('file'));
            $model->addRule('file', 'image', $this->validatorOptions)->validate();
            if ($model->hasErrors()) {
                $result = [
                    'error' => $model->getFirstError('file')
                ];
            } else {
                if ($this->unique === true && $model->file->extension) {
                    $model->file->name = uniqid() . '.' . $model->file->extension;
                }
                if ($model->file->saveAs($this->uploadPath . $model->file->name)) {

                    if ($this->photoClass !== null) {
                        //Saving photo to database
                        $photo = Yii::createObject([
                            'class' => $this->photoClass
                        ]);
                        $photo->setAttribute($this->filenameAttribute, $model->file->name);
                        $photo->save();
                    }

                    if ($this->thumbFolder !== null) {
                        Image::getImagine()
                            ->open($this->uploadPath . $model->file->name)
                            ->resize(new Box($this->thumbWidth, $this->thumbHeight), ImageInterface::FILTER_LANCZOS)
                            ->save($this->_thumbPath . $model->file->name);

                    }

                    /** @var ManageController $controller */
                    $controller = $this->controller;
                    if (method_exists($controller, 'to'))
                        $result = ['filelink' => $controller->to($this->uploadUrl, $model->file->name)];
                    else
                        $result = ['filelink' => $this->uploadUrl . $model->file->name];
                } else {
                    $result = [
                        'error' => Yii::t('redactor', 'We could not upload file')
                    ];
                }
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        } else {
            throw new BadRequestHttpException('Only POST is allowed');
        }
    }
}
