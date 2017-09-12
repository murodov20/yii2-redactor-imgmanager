<?php
/**
 * Created by mirjalol.
 * Date: 9/9/2017
 * Time: 3:17 PM
 */

/**
 * @var $attribute string
 * @var $controller \murodov20\redactor\controllers\ImageController
 * @var $module \murodov20\redactor\PhotoManagerModule
 * @var $url string
 * @var $thumbUrl string
 * @var $listViewId string
 * @var $data \murodov20\redactor\entities\MPhoto
 */

?>
<div class="col-md-4 col-sm-6 no-padding">
    <div class="thumbnail text-center no-radius no-padding m-10">
        <a class="ph-m-fancy-for-img venobox-pm" data-gall="<?= $listViewId ?>"
           data-caption="<?= $attribute ?>"
           href="<?= $url ?>" data-pjax="false"
           style="position: relative;">
            <img class="ph-m-thumb-field" src="<?= $thumbUrl ?>" alt="<?= $attribute ?>"
                 style="width: <?= $module->thumbWidth ?>px; height: <?= $module->thumbHeight ?>;">
            <div class="ph-m-rollover ">
                <i class="glyphicon glyphicon-zoom-in" style="font-size: 4em"></i>
            </div>
        </a>
        <div class="caption" style="padding: 1px;">
            <div class="thumb-title" title="<?= $attribute ?>"><?= \yii\helpers\Html::encode($attribute) ?></div>
            <button class="btn btn-primary btn-flat btn-block img-select-and-add" type="button"
                    data-thumb="<?= $thumbUrl ?>"
                    data-src="<?= $url ?>"
                    data-alt="<?= $data->alt ?>"
                    data-id="<?= $data->id ?>">
                Вставить
            </button>
        </div>
    </div>
</div>
