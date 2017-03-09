<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 05.03.2017
 */
namespace skeeks\cms\modules\admin;

use skeeks\cms\helpers\UrlHelper;
use skeeks\cms\models\Site;
use skeeks\cms\modules\admin\assets\AdminAsset;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\Object;
use yii\web\Application;

/**
 * Class Module
 * @package skeeks\modules\cms\user
 */
class Module extends \yii\base\Module
{
    //Скрывать кнопки действи сущьности
    const SYSTEM_QUERY_NO_ACTIONS_MODEL = 'no-actions';
    const SYSTEM_QUERY_EMPTY_LAYOUT     = 'sx-empty-layout';

    public $controllerNamespace = 'skeeks\cms\modules\admin\controllers';
}
