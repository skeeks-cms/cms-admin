<?php
/**
 * Storage
 *
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010-2014 SkeekS (Sx)
 * @date 17.10.2014
 * @since 1.0.0
 */
namespace skeeks\cms\modules\admin\components;
use \yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * TODO: is deprecated!
 *
 * Class Storage
 * @package skeeks\cms\components
 */
class UrlRule
    extends \yii\web\UrlRule
{
    const ADMIN_PARAM_NAME  = "namespace";
    const ADMIN_PARAM_VALUE = "admin";
    /**
     * @var string
     */
    public $adminPrefix = '~sx';

    /**
     * Deleted not use it!
     */

}
