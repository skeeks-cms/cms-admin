<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 18.03.2017
 */

namespace skeeks\cms\modules\admin\controllers;
use skeeks\admin\components\AccessControl;
use skeeks\cms\backend\actions\IBackendModelAction;
use skeeks\cms\backend\actions\IBackendModelMultiAction;
use skeeks\cms\backend\BackendInfoInterface;
use skeeks\cms\backend\controllers\IBackendModelController;
use skeeks\cms\backend\controllers\TBackendModelController;
use skeeks\cms\base\widgets\ActiveForm;
use skeeks\cms\components\Cms;
use skeeks\cms\Exception;
use skeeks\cms\helpers\ComponentHelper;
use skeeks\cms\helpers\RequestResponse;
use skeeks\cms\helpers\UrlHelper;
use skeeks\cms\IHasModel;
use skeeks\cms\models\Search;
use skeeks\cms\modules\admin\actions\AdminAction;
use skeeks\cms\modules\admin\actions\AdminModelAction;
use skeeks\cms\modules\admin\actions\modelEditor\AdminModelEditorCreateAction;
use skeeks\cms\modules\admin\actions\modelEditor\AdminModelEditorUpdateAction;
use skeeks\cms\modules\admin\actions\modelEditor\AdminMultiModelEditAction;
use skeeks\cms\modules\admin\actions\modelEditor\AdminOneModelEditAction;
use skeeks\cms\modules\admin\actions\modelEditor\AdminOneModelUpdateAction;
use skeeks\cms\modules\admin\actions\modelEditor\ModelEditorGridAction;
use skeeks\cms\modules\admin\controllers\helpers\Action;
use skeeks\cms\modules\admin\controllers\helpers\ActionModel;
use skeeks\cms\modules\admin\controllers\helpers\rules\HasModel;
use skeeks\cms\modules\admin\controllers\helpers\rules\NoModel;
use skeeks\cms\admin\AdminAccessControl;
use skeeks\cms\modules\admin\widgets\ControllerActions;
use skeeks\cms\modules\admin\widgets\ControllerModelActions;
use skeeks\cms\rbac\CmsManager;
use yii\base\ActionEvent;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\behaviors\BlameableBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\web\Application;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class AdminModelEditorController
 * @package skeeks\cms\modules\admin\controllers
 */
abstract class AdminModelEditorController extends AdminController
    implements IHasModel, IBackendModelController
{
    use TBackendModelController;

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(parent::behaviors(), [

            'verbs' =>
            [
                'class' => VerbFilter::className(),
                'actions' =>
                [
                    'delete'        => ['post'],
                    'delete-multi'  => ['post'],
                ],
            ],

            'accessDelete' =>
            [
                'class'         => \skeeks\cms\admin\AdminAccessControl::className(),
                'only'          => ['delete'],
                'rules'         =>
                [
                    [
                        'allow'         => true,
                        'matchCallback' => function($rule, $action)
                        {
                            if (ComponentHelper::hasBehavior($this->model, BlameableBehavior::className()))
                            {
                                //Если такая привилегия заведена, нужно ее проверять.
                                if ($permission = \Yii::$app->authManager->getPermission(CmsManager::PERMISSION_ALLOW_MODEL_DELETE))
                                {
                                    if (!\Yii::$app->user->can($permission->name, [
                                        'model' => $this->model
                                    ]))
                                    {
                                        return false;
                                    }
                                }
                            }

                            return true;
                        }
                    ],
                ],
            ]
        ]);

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(),
            [
                'index' =>
                [
                    'class'         => ModelEditorGridAction::className(),
                    'name'          => \Yii::t('skeeks/cms','List'),
                    "icon"          => "glyphicon glyphicon-th-list",
                    "priority"      => 10,
                ],

                'create' =>
                [
                    'class'         => AdminModelEditorCreateAction::className(),
                    'name'          => \Yii::t('skeeks/cms','Add'),
                    "icon"          => "glyphicon glyphicon-plus",
                ],


                "update" =>
                [
                    'class'         => AdminOneModelUpdateAction::className(),
                    "name"         => \Yii::t('skeeks/cms',"Edit"),
                    "icon"          => "glyphicon glyphicon-pencil",
                    "priority"      => 10,
                ],

                "delete" =>
                [
                    'class'         => AdminOneModelEditAction::className(),
                    "name"          => \Yii::t('skeeks/cms',"Delete"),
                    "icon"          => "glyphicon glyphicon-trash",
                    "confirm"       => \Yii::t('skeeks/cms', 'Are you sure you want to delete this item?'),
                    "method"        => "post",
                    "request"       => "ajax",
                    "callback"      => [$this, 'actionDelete'],
                    "priority"      => 99999,
                ],

                "delete-multi" =>
                [
                    'class'             => AdminMultiModelEditAction::className(),
                    "name"              => \Yii::t('skeeks/cms',"Delete"),
                    "icon"              => "glyphicon glyphicon-trash",
                    "confirm"           => \Yii::t('skeeks/cms', 'Are you sure you want to permanently delete the selected items?'),
                    "eachCallback"      => [$this, 'eachMultiDelete'],
                    "priority"          => 99999,
                ],

            ]
        );
    }

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        $this->_ensureBackendModelController();
    }




    /**
     * TODO: is deprecated!
     * @return array|null|\skeeks\cms\modules\admin\actions\modelEditor\AdminMultiModelEditAction[]
     */
    public function getMultiActions()
    {
        return $this->modelMultiActions;
    }



    /**
     * @return $this
     */
    protected function _initMetaData()
    {
        $data = [];
        $data[] = \Yii::$app->name;
        $data[] = $this->name;

        if ($this->model)
        {
            if ($this->action instanceof IBackendModelAction)
            {
                $data[] = $this->model->{$this->modelShowAttribute};
            }
        }

        if ($this->action && property_exists($this->action, 'name'))
        {
            $data[] = $this->action->name;
        }

        $this->view->title = implode(" / ", $data);
        return $this;
    }





    /**
     * Deletes an existing Game model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionDelete()
    {
        $rr = new RequestResponse();

        if ($rr->isRequestAjaxPost())
        {
            try
            {
                if ($this->model->delete())
                {
                    $rr->message = \Yii::t('skeeks/cms','Record deleted successfully');
                    $rr->success = true;
                } else
                {
                    $rr->message = \Yii::t('skeeks/cms','Record deleted unsuccessfully');
                    $rr->success = false;
                }
            } catch (\Exception $e)
            {
                $rr->message = $e->getMessage();
                $rr->success = false;
            }

            return (array) $rr;
        }
    }

    /**
     * @param $model
     * @param $action
     * @return bool
     */
    public function eachMultiDelete($model, $action)
    {
        try
        {
            return $model->delete();
        } catch (\Exception $e)
        {
            return false;
        }
    }






    /**
     * @return array
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionSortablePriority()
    {
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost)
        {
            \Yii::$app->response->format = Response::FORMAT_JSON;

            if ($keys = \Yii::$app->request->post('keys'))
            {
                //$counter = count($keys);

                foreach ($keys as $counter => $key)
                {
                    $priority = ($counter + 1) * 1000;

                    $modelClassName = $this->modelClassName;
                    $model = $modelClassName::findOne($key);
                    if ($model)
                    {
                        $model->priority = $priority;
                        $model->save(false);
                    }

                    //$counter = $counter - 1;
                }
            }

            return [
                'success' => true,
                'message' => \Yii::t('skeeks/cms','Changes saved'),
            ];
        }
    }

}