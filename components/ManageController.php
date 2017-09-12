<?php
/**
 * Created by mirjalol.
 * Date: 9/9/2017
 * Time: 10:17 AM
 */

namespace murodov20\redactor\components;


use murodov20\redactor\helpers\RedactorHelper;
use murodov20\redactor\PhotoManagerModule;
use yii\web\Controller;

/**
 * Class ManageController
 * @package murodov20\redactor\components
 */
class ManageController extends Controller
{

    /**
     * This function will be used for creating url for image load
     * @link murodov20\redactor\helpers\RedactorHelper::to()
     * @param $url string
     * @param $filename string
     * @return string
     */
    public function to($url, $filename)
    {
        /** @var PhotoManagerModule $module */
        $module = $this->module;
        return RedactorHelper::to($module->urlManager, $url, $filename, $module->isAbsolute);
    }

}
