<?php
/**
 * ActiveForm
 *
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010-2014 SkeekS (Sx)
 * @date 11.11.2014
 * @since 1.0.0
 */
namespace skeeks\cms\modules\admin\widgets\filters;
use skeeks\cms\base\widgets\ActiveFormAjaxSubmit;
use skeeks\cms\helpers\UrlHelper;
use skeeks\cms\modules\admin\assets\AdminFormAsset;
use skeeks\cms\modules\admin\controllers\AdminModelEditorController;
use skeeks\cms\modules\admin\models\CmsAdminFilter;
use skeeks\cms\modules\admin\traits\ActiveFormTrait;
use skeeks\cms\modules\admin\traits\AdminActiveFormTrait;
use skeeks\cms\traits\ActiveFormAjaxSubmitTrait;
use skeeks\widget\chosen\Chosen;
use yii\base\Exception;
use yii\base\Model;
use yii\bootstrap\Modal;
use yii\bootstrap\Tabs;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use skeeks\cms\modules\admin\widgets\Pjax;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\jui\Dialog;

/**
 * @property CmsAdminFilter[] $savedFilters
 * @property CmsAdminFilter $filter
 *
 * Class ActiveForm
 * @package skeeks\cms\modules\admin\widgets
 */
class AdminFiltersForm extends \skeeks\cms\base\widgets\ActiveForm
{
    public $fieldClass = 'skeeks\cms\modules\admin\widgets\filters\FilterActiveField';

    public $namespace;

    public $method = 'get';

    public $enableClientValidation = false;

    public $options = [
        'data-pjax' => true
    ];

    public $indexUrl = null;

    public $filterParametrName = 'sx-filter';



    /**
     * Initializes the widget.
     * This renders the form open tag.
     */
    public function init()
    {
        if (!$this->namespace)
        {
            $this->namespace = \Yii::$app->controller->uniqueId;
        }

        if (!$this->indexUrl)
        {
            $this->indexUrl = \Yii::$app->controller->indexUrl;
        }

        $this->filter;

        if ($classes = ArrayHelper::getValue($this->options, 'class'))
        {
            $this->options = ArrayHelper::merge($this->options, [
                'class' => $classes . 'sx-admin-filters-form form-horizontal'
            ]);
        } else
        {
            $this->options = ArrayHelper::merge($this->options, [
                'class' => 'sx-admin-filters-form form-horizontal'
            ]);
        }

        echo $this->render('_header');

        parent::init();

    }

    /**
     * @return CmsAdminFilter[]
     */
    public function getSavedFilters()
    {
        $query = CmsAdminFilter::find()
            ->where(['namespace' => $this->namespace])
            ->andWhere([
                'or',
                ['cms_user_id' => null],
                ['cms_user_id' => \Yii::$app->user->id]
            ])
            ->orderBy(['is_default' => SORT_DESC])
        ;

        return $query->all();
    }


    /**
     * @var CmsAdminFilter
     */
    protected $_filter = null;

    public function getFilter()
    {
        if ($this->_filter === null || !$this->_filter instanceof CmsAdminFilter)
        {
            //Find in get params
            if ($activeFilterId = (int) \Yii::$app->request->get($this->filterParametrName))
            {
                if ($filter = CmsAdminFilter::findOne($activeFilterId))
                {
                    $this->_filter = $filter;
                    return $this->_filter;
                }
            }

            //Defauilt filter
            $filter = CmsAdminFilter::find()
                ->where(['namespace' => $this->namespace])
                ->andWhere(['cms_user_id' => \Yii::$app->user->id])
                ->andWhere(['is_default' => 1])
                ->one()
            ;

            if (!$filter)
            {
                $filter = new CmsAdminFilter([
                    'namespace' => $this->namespace,
                    'cms_user_id' => \Yii::$app->user->id,
                    'is_default' => 1
                ]);
                $filter->loadDefaultValues();

                if ($filter->save())
                {

                } else
                {
                    throw new Exception('Filter not saved');
                }
            }

            $this->_filter = $filter;
        }

        return $this->_filter;
    }

    /**
     * @param CmsAdminFilter $filter
     * @return string
     */
    public function getFilterUrl(CmsAdminFilter $filter)
    {
        $query[$this->filterParametrName] = $filter->id;
        return $this->indexUrl . "?" . http_build_query($query);
    }

    public function run()
    {

        $closeUrl = $this->indexUrl;

        echo Html::tag('div', Html::hiddenInput('filterId', $this->filter->id), ['style' => 'display: none;']);

        echo <<<HTML


                <div class="form-group form-group-footer">
                    <div class="col-sm-12">
                        <hr style="margin-top: 5px; margin-bottom: 15px;"/>

                        <button type="submit" class="btn btn-default pull-left">
                            <i class="glyphicon glyphicon-search"></i> Найти
                        </button>

                        <a href="{$closeUrl}" class="btn btn-default pull-left" style="margin-left: 10px;">
                            <i class="glyphicon glyphicon-remove"></i> Отмена
                        </a>



                        <div class="dropdown pull-right sx-btn-trigger-fields" style="margin-left: 10px;">
                            <a class="btn btn-default btn-sm dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="glyphicon glyphicon-plus"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    Фильтров нет
                                </li>
                           </ul>
                        </div>

                        <a class="btn btn-default btn-sm pull-right" href="#">
                            <i class="glyphicon glyphicon-cog"></i>
                        </a>

                    </div>
                </div>
HTML;
        parent::run();

        echo <<<HTML

                </div>
            </div>
        </div>
    </div>
</div>
HTML;
;

        AdminFiltersFormAsset::register($this->view);

        $jsOptions = Json::encode([
            'id'                                => $this->id,
            'backendSaveVisibles'               => Url::to(['/admin/admin-filter/save-visibles', 'pk' => $this->filter->id]),
            'visibles'                          => $this->filter->visibles,
        ]);

        $this->view->registerJs(<<<JS
        new sx.classes.filters.Form({$jsOptions});
JS
);
    }


    public function fieldSet($name, $options = [])
    {
        return <<<HTML
        <div class="sx-form-fieldset">
            <h3 class="sx-form-fieldset-title">{$name}</h3>
            <div class="sx-form-fieldset-content">
HTML;

    }

    public function fieldSetEnd()
    {
        return <<<HTML
            </div>
        </div>
HTML;

    }


    public $fields = [];

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return FilterActiveField
     */
    public function field($model, $attribute, $options = [])
    {
        $field = parent::field($model, $attribute, $options);

        if ($model && $attribute) {
            $this->fields[Html::getInputId($model, $attribute)] = Html::getInputId($model, $attribute);
        }
        return $field;
    }

}