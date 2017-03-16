<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */
namespace skeeks\cms\modules\admin\actions\modelEditor;
use skeeks\admin\components\AccessControl;
use skeeks\cms\helpers\RequestResponse;
use skeeks\cms\helpers\UrlHelper;
use skeeks\cms\modules\admin\actions\AdminAction;
use skeeks\cms\modules\admin\filters\AdminAccessControl;
use skeeks\cms\rbac\CmsManager;
use yii\base\InvalidParamException;
use yii\behaviors\BlameableBehavior;
use yii\web\Response;

/**
 * Class AdminOneModelUpdateAction
 * @package skeeks\cms\modules\admin\actions\modelEditor
 */
class AdminOneModelUpdateAction extends AdminOneModelEditAction
{
    /**
     * @var bool
     */
    public $modelValidate = true;

    /**
     * @var string
     */
    public $modelScenario = "";

    /**
     * @var string
     */
    public $defaultView = "_form";

    public function run()
    {
        if ($this->callback)
        {
            return call_user_func($this->callback, $this);
        }

        $model          = $this->controller->model;

        $scenarios = [];
        if (method_exists($model, 'scenarios'))
        {
            $scenarios      = $model->scenarios();
        }

        if ($scenarios && $this->modelScenario)
        {
            if (isset($scenarios[$this->modelScenario]))
            {
                $model->scenario = $this->modelScenario;
            }
        }

        $rr = new RequestResponse();

        if (\Yii::$app->request->isAjax && !\Yii::$app->request->isPjax)
        {
            return $rr->ajaxValidateForm($model);
        }

        if ($rr->isRequestPjaxPost())
        {
            if ($model->load(\Yii::$app->request->post()) && $model->save($this->modelValidate))
            {
                \Yii::$app->getSession()->setFlash('success', \Yii::t('skeeks/cms','Saved'));

                if (\Yii::$app->request->post('submit-btn') == 'apply')
                {

                } else
                {
                    return $this->controller->redirect(
                        $this->controller->indexUrl
                    );
                }

                $model->refresh();

            } else
            {
                $errors = [];

                if ($model->getErrors())
                {
                    foreach ($model->getErrors() as $error)
                    {
                        $errors[] = implode(', ', $error);
                    }
                }

                \Yii::$app->getSession()->setFlash('error', \Yii::t('skeeks/cms','Could not save') . $errors);
            }
        }


        return parent::run();
    }

    /**
     * Renders a view
     *
     * @param string $viewName view name
     * @return string result of the rendering
     */
    protected function render($viewName)
    {
        return $this->controller->render($viewName, ['model' => $this->controller->model]);
    }
}