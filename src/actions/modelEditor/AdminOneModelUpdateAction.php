<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */
namespace skeeks\cms\modules\admin\actions\modelEditor;
use skeeks\admin\components\AccessControl;
use skeeks\cms\backend\actions\BackendModelUpdateAction;
use skeeks\cms\helpers\RequestResponse;
use skeeks\cms\rbac\CmsManager;
use yii\base\InvalidParamException;
use yii\behaviors\BlameableBehavior;
use yii\web\Response;

/**
 * Class AdminOneModelUpdateAction
 * @package skeeks\cms\modules\admin\actions\modelEditor
 */
class AdminOneModelUpdateAction extends BackendModelUpdateAction
{
    //TODO: is deprecated;
    //TODO: not used visible;
    public $visible = true;
}