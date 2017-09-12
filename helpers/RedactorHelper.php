<?php
/**
 * Created by mirjalol.
 * Date: 9/9/2017
 * Time: 10:00 AM
 */

namespace murodov20\redactor\helpers;


use yii\web\UrlManager;

/**
 * Class RedactorHelper Helper class for PhotoManager module
 * @package murodov20\redactor\helpers
 */
class RedactorHelper
{
    /**
     * Creates url for image
     * @param $urlManager UrlManager
     * @param $url string
     * @param $filename string
     * @param $isAbsolute boolean
     * @return string
     */
    public static function to($urlManager, $url, $filename, $isAbsolute)
    {
        return $isAbsolute ? $urlManager->createAbsoluteUrl($url . '/' . $filename) : $urlManager->createUrl($url . '/' . $filename);
    }
}
