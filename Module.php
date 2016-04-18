<?php
/**
 * Module
 *
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010-2014 SkeekS (Sx)
 * @date 28.10.2014
 * @since 1.0.0
 */
namespace skeeks\cms\modules\admin;

use skeeks\cms\base\Module as CmsModule;
use skeeks\cms\helpers\UrlHelper;
use skeeks\cms\models\Site;
use skeeks\cms\modules\admin\assets\AdminAsset;
use skeeks\cms\modules\admin\components\UrlRule;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\Object;
use yii\web\Application;

/**
 * Class Module
 * @package skeeks\modules\cms\user
 */
class Module extends CmsModule
{
    //Скрывать кнопки действи сущьности
    const SYSTEM_QUERY_NO_ACTIONS_MODEL = 'no-actions';
    const SYSTEM_QUERY_EMPTY_LAYOUT     = 'sx-empty-layout';

    public $controllerNamespace = 'skeeks\cms\modules\admin\controllers';

    public $noImage = '';

    public function init()
    {
        parent::init();

        \Yii::setAlias('@admin/views', $this->viewPath);

        if (\Yii::$app->admin->requestIsAdmin)
        {
            if (!$this->noImage)
            {
                $this->noImage = AdminAsset::getAssetUrl("images/no-photo.gif");
            }
        }
    }

    /**
     * @param array $data
     * @return string
     */
    public function createUrl(array $data)
    {
        $data[UrlRule::ADMIN_PARAM_NAME] = UrlRule::ADMIN_PARAM_VALUE;
        return \Yii::$app->urlManager->createUrl($data);
    }
}