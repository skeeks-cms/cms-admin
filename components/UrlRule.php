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
 * Class Storage
 * @package skeeks\cms\components
 */
class UrlRule
    extends \skeeks\cms\backend\UrlRule
    //extends \yii\web\UrlRule
{
    /**
     * TODO:: is deprecated;
     */
    const ADMIN_PARAM_NAME  = "namespace";
    const ADMIN_PARAM_VALUE = "admin";

    /**
     * @var string
     */
    public $adminPrefix = '~sx';

    public function init()
    {
        $this->urlPrefix    = $this->adminPrefix;

        if (!$this->routePrefix)
        {
            $this->routePrefix = 'admin';
        }

        parent::init();
    }


    /**
     * @param \yii\web\UrlManager $manager
     * @param \yii\web\Request $request
     * @return array|bool
     */
    public function parseRequest($manager, $request)
    {
        if ($result = parent::parseRequest($manager, $request))
        {
            if (!\Yii::$app->admin->checkAccess())
            {
                return false;
            }
        }

        return $result;
    }
}
