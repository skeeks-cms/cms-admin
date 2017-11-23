<?php
/**
 * TODO: is deprecated!
 */


namespace skeeks\cms\modules\admin\components;

/**
 * Class Storage
 * @package skeeks\cms\components
 */
class UrlRule
    extends \yii\web\UrlRule
{
    const ADMIN_PARAM_NAME = "namespace";
    const ADMIN_PARAM_VALUE = "admin";
    /**
     * @var string
     */
    public $adminPrefix = '~sx';

    /**
     * Deleted not use it!
     */

}
