<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */
namespace skeeks\cms\modules\admin\actions\modelEditor;

use skeeks\cms\models\Search;
use skeeks\cms\modules\admin\actions\AdminAction;
use skeeks\cms\modules\admin\controllers\AdminModelEditorController;
use skeeks\cms\modules\admin\widgets\ControllerActions;
use yii\authclient\AuthAction;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\web\Application;
use yii\web\ViewAction;
use \skeeks\cms\modules\admin\controllers\AdminController;

/**
 *
 * Class AdminModelsGridAction
 * @package skeeks\cms\modules\admin\actions
 */
class ModelEditorGridAction extends AdminAction
{
    /**
     * @var string
     */
    public $modelSearchClassName = '';

    /**
     * @var array
     */
    public $columns = [];

    /**
     * @var array
     */
    public $gridConfig = [];

    /**
     * @var callable
     */
    public $dataProviderCallback    = null;

    public $filter          = null;

    public $viewParams = [];

    /**
     * @return string
     */
    public function run()
    {
        $modelSeacrhClass = $this->modelSearchClassName;

        if (!$modelSeacrhClass)
        {
            $search         = new Search($this->controller->modelClassName);
            $dataProvider   = $search->search(\Yii::$app->request->queryParams);
            $searchModel    = $search->loadedModel;
        } else
        {
            $searchModel    = new $modelSeacrhClass();
            $dataProvider   = $searchModel->search(\Yii::$app->request->queryParams);
        }

        //Дополнительная обработка Дата провайдера
        if ($this->dataProviderCallback && is_callable($this->dataProviderCallback))
        {
            $dataProviderCallback = $this->dataProviderCallback;
            $dataProviderCallback($dataProvider);
        }

        //Дополнительная обработка Дата провайдера
        if ($this->filter && is_callable($this->filter))
        {
            $filter = $this->filter;
            $filter($dataProvider, \Yii::$app->request->queryParams);
        }


        $gridConfig =
        [
            'dataProvider'      => $dataProvider,
            'filterModel'       => $searchModel,
            'adminController'   => $this->controller,
            'columns'           => $this->columns,
        ];

        $gridConfig = ArrayHelper::merge($gridConfig, $this->gridConfig);

        $this->viewParams =
        [
            'searchModel'   => $searchModel,
            'dataProvider'  => $dataProvider,
            'controller'    => $this->controller,
            'columns'       => $this->columns,

            'gridConfig' => $gridConfig
        ];

        return parent::run();
    }


    /**
     * Renders a view
     *
     * @param string $viewName view name
     * @return string result of the rendering
     */
    protected function render($viewName, $params = [])
    {
        $result = '';

        try
        {
            //Если шаблона нет в стандартном пути, или в нем ошибки берем базовый
            return $this->controller->render($viewName, $this->viewParams);
        } catch (InvalidParamException $e)
        {
            if (!file_exists(\Yii::$app->view->viewFile))
            {
                \Yii::warning($e->getMessage(), 'template-render');
                $result = $this->controller->render("@skeeks/cms/modules/admin/views/base-actions/grid-standart", (array) $this->viewParams);
            }
        }

        return $result;
    }
}