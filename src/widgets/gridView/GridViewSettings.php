<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 02.06.2015
 */
namespace skeeks\cms\modules\admin\widgets\gridView;
use skeeks\cms\base\Component;
use skeeks\cms\components\Cms;
use skeeks\cms\modules\admin\widgets\GridViewHasSettings;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/**
 * Class GridViewSettings
 * @package skeeks\cms\modules\admin\widgets\gridView
 */
class GridViewSettings extends Component
{
    static public function descriptorConfig()
    {
        return array_merge(parent::descriptorConfig(), [
            'name' => \Yii::t('skeeks/admin','Table settings')
        ]);
    }

    public $enabledPjaxPagination;
    /**
     * @var int
     */
    public $pageSize;

    public $pageSizeLimitMin;
    public $pageSizeLimitMax;

    /**
     * @var string
     */
    public $pageParamName;


    /**
     * @var array
     */
    public $visibleColumns = [];


    /**
     * @var GridViewHasSettings
     */
    public $grid;

    //Сортировка
    public $orderBy                     = "id";
    public $order                       = SORT_DESC;

    public function init()
    {
        if (!$this->pageSize)
        {
            $this->pageSize = \Yii::$app->admin->pageSize;
        }
        if (!$this->pageSizeLimitMin)
        {
            $this->pageSizeLimitMin = \Yii::$app->admin->pageSizeLimitMin;
        }
        if (!$this->pageSizeLimitMax)
        {
            $this->pageSizeLimitMax = \Yii::$app->admin->pageSizeLimitMax;
        }
        if (!$this->pageParamName)
        {
            $this->pageParamName = \Yii::$app->admin->pageParamName;
        }
        if (!$this->enabledPjaxPagination)
        {
            $this->enabledPjaxPagination = \Yii::$app->admin->enabledPjaxPagination;
        }

        parent::init();
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),
        [
            'enabledPjaxPagination'     => \Yii::t('skeeks/cms','Inclusion {ajax} navigation',['ajax' => 'ajax']),
            'pageParamName'             => \Yii::t('skeeks/cms','Parameter name pages, pagination'),
            'pageSize'                  => \Yii::t('skeeks/cms','Number of records on one page'),
            'pageSizeLimitMin'          => \Yii::t('skeeks/cms','The maximum number of records per page'),
            'pageSizeLimitMax'          => \Yii::t('skeeks/cms','The minimum number of records per page'),

            'orderBy'                   => \Yii::t('skeeks/cms','Sort by what parameter'),
            'order'                     => \Yii::t('skeeks/admin','sorting direction'),

            'visibleColumns'            => \Yii::t('skeeks/admin','Display column'),
        ]);
    }

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(),
        [
            [['enabledPjaxPagination'], 'string'],
            [['pageParamName'], 'string'],
            [['pageSize'], 'integer'],
            [['pageSizeLimitMin'], 'integer'],
            [['pageSizeLimitMax'], 'integer'],
            [['orderBy'], 'string'],
            [['order'], 'integer'],
            [['visibleColumns'], 'safe'],
        ]);
    }

    public function renderConfigForm(ActiveForm $form)
    {
        echo \Yii::$app->view->renderFile(__DIR__ . '/_form.php', [
            'form'  => $form,
            'model' => $this
        ], $this);
    }

    /**
     * @return array
     */
    public function getCallableData()
    {
        $result = parent::getCallableData();

        if ($this->grid)
        {
            $columnsData = $this->grid->getColumnsKeyLabels();

            $result['columns']          = $columnsData;
            $result['selectedColumns']  = array_keys($this->grid->columns);
        }

        return $result;
    }
}