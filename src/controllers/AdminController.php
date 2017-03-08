<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 29.05.2015
 */
namespace skeeks\cms\modules\admin\controllers;

use skeeks\cms\backend\controllers\BackendController;
use skeeks\cms\modules\admin\filters\AdminAccessControl;
use skeeks\cms\modules\admin\filters\AdminLastActivityAccessControl;
use skeeks\cms\rbac\CmsManager;
use yii\helpers\ArrayHelper;

/**
 * @property array             $permissionNames
 * @property string             $permissionName
 *
 * Class AdminController
 * @package skeeks\cms\modules\admin\controllers
 */
abstract class AdminController extends BackendController
{
    /**
     * @return array
     */
    public function getPermissionNames()
    {
        return [
            CmsManager::PERMISSION_ADMIN_ACCESS,
            $this->permissionName
        ];
    }

    /**
     * Проверка доступа к админке
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),
        [
            //Проверка основной привелигии доступа к админ панели
            'access' =>
            [
                'class'         => AdminAccessControl::className(),
            ],

            //Обновление активности пользователя взаимдействие с админкой
            'adminLastActivityAccess' =>
            [
                'class'         => AdminLastActivityAccessControl::className(),
                'rules' =>
                [
                    [
                        'allow'         => true,
                        'matchCallback' => function($rule, $action)
                        {
                            if (\Yii::$app->user->identity->lastAdminActivityAgo > \Yii::$app->admin->blockedTime)
                            {
                                return false;
                            }

                            if (\Yii::$app->user->identity)
                            {
                                \Yii::$app->user->identity->updateLastAdminActivity();
                            }

                            return true;
                        }
                    ]
                ],
            ],
        ]);
    }


    public function init()
    {
        \Yii::$app->admin;
        parent::init();
    }

    /**
     * TODO::Is deprecated
     *
     * The name of the privilege of access to this controller
     * @return string
     */
    public function getPermissionName()
    {
        return $this->getUniqueId();
    }

}