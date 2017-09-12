<?php
/**
 * Created by mirjalol.
 * Date: 9/9/2017
 * Time: 10:24 AM
 */

namespace murodov20\redactor\controllers;


use murodov20\redactor\actions\UploadAction;
use murodov20\redactor\components\ManageController;
use murodov20\redactor\entities\MPhoto;
use murodov20\redactor\PhotoManagerModule;
use murodov20\redactor\widgets\PhotoInputWidget;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Class ImageController
 * @package murodov20\redactor\controllers
 */
class ImageController extends ManageController
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        /** @var PhotoManagerModule $module */
        $module = $this->module;
        return [
            'ajax-upload' => [
                'class' => UploadAction::className(),
                'uploadPath' => $module->uploadPath,
                'uploadUrl' => $module->uploadUrl,
                'unique' => $module->saveUnique,
                'validatorOptions' => $module->validatorOptions,
                'photoClass' => $module->photoClass,
                'filenameAttribute' => $module->filenameAttribute,
                'thumbFolder' => $module->thumbFolder,
                'thumbWidth' => $module->thumbWidth,
                'thumbHeight' => $module->thumbHeight
            ]
        ];
    }

    /**
     * Action for getting images with pagination by ajax
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionGet()
    {
        if (\Yii::$app->request->isAjax) {
            /** @var PhotoManagerModule $module */
            $module = $this->module;
            if ($module->photoClass !== null and is_callable([$module->photoClass, 'find'])) {
                /** @var ActiveQuery $query */
                $query = call_user_func([$module->photoClass, 'find']);
                $dataProvider = new ActiveDataProvider([
                    'query' => $query
                ]);
                $attribute = $module->filenameAttribute;
                $dataProvider->pagination->pageSize = 10;
                return $this->renderAjax('get', ['module' => $module, 'dataProvider' => $dataProvider, 'attribute' => $attribute]);
            } else {
                //TODO Add this feature to next release
                $files = FileHelper::findFiles(Yii::getAlias($module->uploadPath), [
                    'only' => ['*.png', '*.jpeg', '*.jpg', '*.bmp', '*.gif']
                ]);
                $dataProvider = new ArrayDataProvider(['allModels' => $files]);
                return $this->renderAjax('getfiles', [
                    'dataProvider' => $dataProvider
                ]);
            }
        } else {
            throw new BadRequestHttpException('Only ajax allowed');
        }
    }

    /**
     * @return array Returns image's src and thumbnail src by its id
     * @throws BadRequestHttpException
     * @throws InvalidConfigException
     */
    public function actionSrcById()
    {
        /** @var MPhoto $photo */
        /** @var ActiveQuery $query */
        /** @var PhotoManagerModule $module */

        if (\Yii::$app->request->isAjax && ($id = \Yii::$app->request->get('id')) !== null) {

            $module = $this->module;
            if ($module->photoClass !== null and is_callable([$module->photoClass, 'find'])) {
                $query = call_user_func([$module->photoClass, 'find']);
                $query->where(['id' => $id]);
                $photo = $query->one();
                \Yii::$app->response->format = Response::FORMAT_JSON;
                return ['src' => $this->to($module->uploadUrl, $photo->filename), 'thumbSrc' => $this->to($module->uploadUrl . '/thumbs', $photo->filename)];
            } else {
                throw new InvalidConfigException('Invalid configuration of Module');
            }
        } else {
            throw new BadRequestHttpException('Only ajax allowed');
        }
    }

    /**
     * Adds Photowidget to multiple widget
     * @throws BadRequestHttpException
     */
    public function actionAddToWidget()
    {
        /** @var MPhoto $photo */
        /** @var ActiveQuery $query */
        /** @var PhotoManagerModule $module */
        if (\Yii::$app->request->isAjax) {

            $attrs = $this->checkAttributes();
            if (!$attrs) {
                throw new InvalidParamException('Not enough parameters');
            } else {
                echo Html::beginTag('div', ['class' => $attrs['itemClass'] . ' image-item']);
                $id = $attrs['id'] . '-' . $attrs['l'];
                $name = $attrs['nm'] . '[' . $attrs['l'] . ']';
                echo PhotoInputWidget::widget([
                    'inputId' => $id,
                    'name' => $name,
                    'value' => '',
                    'imgWidth' => $attrs['w'],
                    'imgHeight' => $attrs['h'],
                    'imageLoadUrl' => $attrs['imgLoadUrl'],
                    'srcByIdUrl' => $attrs['srcByIdUrl'],
                    'urlManager' => \Yii::$app->urlManager,
                    'thumbOnly' => $attrs['thumbOnly'] === '1',
                    'noImageSrc' => $attrs['noImageSrc'],
                    'multi' => true
                ]);

                echo Html::endTag('div');
            }
        } else {
            throw new BadRequestHttpException('Only ajax allowed');
        }
    }

    /**
     * Checking post parameters for add-to-widget action
     * @return array|bool
     */
    private function checkAttributes()
    {
        $attrs = [
            'l',
            'nm',
            'id',
            'w',
            'h',
            'itemClass',
            'imgLoadUrl',
            'srcByIdUrl',
            'thumbOnly',
            'noImageSrc'
        ];
        $all = [];
        foreach ($attrs as $value) {
            if (!\Yii::$app->request->post($value))
                return false;
            $all[$value] = \Yii::$app->request->post($value);
        }
        return $all;
    }
}
