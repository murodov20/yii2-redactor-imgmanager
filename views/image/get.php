<?php
/**
 * Created by mirjalol.
 * Date: 9/9/2017
 * Time: 1:46 PM
 */
use murodov20\redactor\web\VenoBoxAsset;

/**
 * @var $this \yii\web\View
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $attribute string
 * @var $module \murodov20\redactor\PhotoManagerModule
 */
/** @var \murodov20\redactor\controllers\ImageController $controller */
$controller = $this->context;
VenoBoxAsset::register($this);
?>
<?php
$pjaxContainer = 'pjax-pm-img-get-' . rand(1, 100000) . '-' . rand(1, 100000);
\yii\widgets\Pjax::begin([
    'enablePushState' => false,
    'enableReplaceState' => false,
    'id' => $pjaxContainer
]) ?>
<?php
$listRandom = rand(1, 100000) . '-' . rand(1, 100000);
$listViewId = 'photo_manager-image-get-' . $listRandom;
$galleryRelRandom = $listRandom;
?>

<?= \yii\widgets\ListView::widget([
    'dataProvider' => $dataProvider,
    'id' => $listViewId,
    'summary' => false,
    'layout' => '<div style="max-height: 250px; overflow-y: scroll;">{items}</div><hr style="margin-bottom: 0;"><div class="text-right">{pager}</div>',
    'itemView' => function ($data) use ($attribute, $controller, $module, $listViewId) {
        $url = $controller->to($module->uploadUrl, $data->$attribute);
        if ($module->thumbFolder !== null)
            $thumbUrl = $controller->to($module->uploadUrl . '/' . $module->thumbFolder, $data->$attribute);
        else $thumbUrl = $url;
        /** @var \yii\web\View $this */
        return $this->render('_item', [
            'attribute' => $data->$attribute,
            'module' => $module,
            'url' => $url,
            'thumbUrl' => $thumbUrl,
            'listViewId' => $listViewId,
            'data' => $data
        ]);
    }
]) ?>


<?php \yii\widgets\Pjax::end() ?>

