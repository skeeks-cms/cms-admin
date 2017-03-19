<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */
namespace skeeks\cms\modules\admin\actions\modelEditor;

use skeeks\cms\backend\actions\IBackendModelAction;
use skeeks\cms\helpers\ComponentHelper;
use skeeks\cms\helpers\UrlHelper;
use skeeks\cms\models\Search;
use skeeks\cms\modules\admin\actions\AdminAction;
use skeeks\cms\modules\admin\controllers\AdminModelEditorController;
use skeeks\cms\admin\AdminAccessControl;
use skeeks\cms\modules\admin\widgets\ControllerActions;
use skeeks\cms\rbac\CmsManager;
use yii\authclient\AuthAction;
use yii\behaviors\BlameableBehavior;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\Application;
use yii\web\ViewAction;
use \skeeks\cms\modules\admin\controllers\AdminController;

/**
 * @property AdminModelEditorController    $controller
 *
 * Class AdminModelsGridAction
 * @package skeeks\cms\modules\admin\actions
 */
class AdminOneModelEditAction extends AdminAction
    implements IBackendModelAction
{

    public function init()
    {
        parent::init();

        //Для работы с любой моделью нужно как минимум иметь привилегию CmsManager::PERMISSION_ALLOW_MODEL_UPDATE
        /*$this->controller->attachBehavior('accessUpdate',
        [
            'class'         => \skeeks\cms\admin\AdminAccessControl::className(),
            'only'          => [$this->id],
            'rules'         =>
            [
                [
                    'allow'         => true,
                    'matchCallback' => [$this, 'checkUpdateAccess']
                ],
            ],
        ]);*/
    }


    protected function _initUrl()
    {
        if (!$this->_url)
        {
            if (!$this->controller->model)
            {
                return $this;
            }

            if ($this->controller->module instanceof Application)
            {
                $this->_url = [
                    '/' . $this->controller->id . '/' . $this->id,
                    $this->controller->requestPkParamName => $this->controller->modelPkValue
                ];
            } else
            {
                $this->_url = [
                    '/' . $this->controller->module->id . '/' . $this->controller->id . '/' . $this->id,
                    $this->controller->requestPkParamName => $this->controller->modelPkValue
                ];
            }
        }

        return $this;
    }


    protected function beforeRun()
    {
        if (parent::beforeRun())
        {
            if (!$this->controller->model)
            {
                $this->controller->redirect($this->controller->url);
                return false;
            }

            return true;
        }
    }

    public function run()
    {
        if ($this->callback)
        {
            return call_user_func($this->callback, $this);
        }

        if (!$this->controller->model)
        {
            return $this->controller->redirect($this->controller->url);
        }

        return parent::run();
    }

    /**
     * @return bool
     */
    public function getIsVisible()
    {
        if (!parent::getIsVisible())
        {
            return false;
        }

        return $this->checkUpdateAccess();
    }

    public function checkUpdateAccess()
    {
        $model = $this->controller->model;
        if (ComponentHelper::hasBehavior($model, BlameableBehavior::className()))
        {
            //Если такая привилегия заведена, нужно ее проверять.
            if ($permission = \Yii::$app->authManager->getPermission(CmsManager::PERMISSION_ALLOW_MODEL_UPDATE))
            {
                if (!\Yii::$app->user->can($permission->name, [
                    'model' => $this->controller->model
                ]))
                {
                    return false;
                }
            }
        } else
        {
            //Если такая привилегия заведена, нужно ее проверять.
            if ($permission = \Yii::$app->authManager->getPermission(CmsManager::PERMISSION_ALLOW_MODEL_UPDATE))
            {
                if (!\Yii::$app->user->can($permission->name))
                {
                    return false;
                }
            }
        }

        return true;
    }




    /**
     * @return bool
     */
    /*public function getIsAllow()
    {
        if ($this->permissionNames)
        {
            foreach ($this->permissionNames as $permissionName => $permissionLabel)
            {
                //Привилегия доступу к админке
                if (!$permission = \Yii::$app->authManager->getPermission($permissionName))
                {
                    $permission = \Yii::$app->authManager->createPermission($permissionName);
                    $permission->description = $permissionLabel;
                    \Yii::$app->authManager->add($permission);
                }

                if ($roleRoot = \Yii::$app->authManager->getRole(CmsManager::ROLE_ROOT))
                {
                    if (!\Yii::$app->authManager->hasChild($roleRoot, $permission))
                    {
                        \Yii::$app->authManager->addChild($roleRoot, $permission);
                    }
                }

                if (!\Yii::$app->user->can($permissionName))
                {
                    return false;
                }
            }

            return true;
        }

        return false;
    }*/
}