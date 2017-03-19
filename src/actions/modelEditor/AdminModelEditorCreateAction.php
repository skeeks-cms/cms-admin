<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */
namespace skeeks\cms\modules\admin\actions\modelEditor;
use skeeks\cms\helpers\RequestResponse;
use skeeks\cms\helpers\UrlHelper;
use skeeks\cms\admin\AdminAccessControl;
use skeeks\cms\modules\admin\actions\AdminAction;
use skeeks\cms\rbac\CmsManager;
use yii\base\InvalidParamException;
use yii\behaviors\BlameableBehavior;
use yii\web\Response;

/**
 * Class AdminModelsGridAction
 * @package skeeks\cms\modules\admin\actions\modelEditor
 */
class AdminModelEditorCreateAction extends AdminAction
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


        $modelClassName = $this->controller->modelClassName;
        $model          = new $modelClassName();
        $scenarios      = $model->scenarios();

        if ($scenarios && $this->modelScenario)
        {
            if (isset($scenarios[$this->modelScenario]))
            {
                $model->scenario = $this->modelScenario;
            }
        }

        $model->loadDefaultValues();

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
                    return $this->controller->redirect(
                        UrlHelper::constructCurrent()->setCurrentRef()->enableAdmin()->setRoute($this->controller->modelDefaultAction)->normalizeCurrentRoute()
                            ->addData([$this->controller->requestPkParamName => $model->{$this->controller->modelPkAttribute}])
                            ->toString()
                    );
                } else
                {
                    return $this->controller->redirect(
                        $this->controller->indexUrl
                    );
                }

            } else
            {
                \Yii::$app->getSession()->setFlash('error', \Yii::t('skeeks/cms','Could not save'));
            }
        }

        $this->controller->model = $model;

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