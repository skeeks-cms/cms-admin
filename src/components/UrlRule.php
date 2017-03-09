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
    extends \skeeks\cms\backend\BackendUrlRule
{
    /**
     * @var string
     */
    public $urlPrefix   = '~sx';

    /**
     * @var string
     */
    public $controllerPrefix = 'admin';

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
