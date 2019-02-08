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
use skeeks\cms\helpers\StringHelper;
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
    public $filterApplyedParametrName = 'sx-applyed';


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

        if (!$this->indexUrl && \Yii::$app->controller && isset(\Yii::$app->controller->url))
        {
            $this->indexUrl = \Yii::$app->controller->url;
        }

        if (!$this->indexUrl)
        {
            $this->indexUrl = \Yii::$app->request->url;
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
                //['cms_user_id' => ''],
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
                } else
                {
                    \Yii::$app->response->redirect($this->indexUrl);
                    \Yii::$app->end();
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
        $query = [];
        $url = $this->indexUrl;

        if ($pos = strpos($url, "?"))
        {
            $url = StringHelper::substr($url, 0, $pos);
            $stringQuery = StringHelper::substr($this->indexUrl, $pos + 1, StringHelper::strlen($this->indexUrl));
            parse_str($stringQuery, $query);
        }

        if ($filter->values)
        {
            $query = (array) $filter->values;
            //$query = ArrayHelper::merge((array) $query, (array) $filter->values);
        }

        $query[$this->filterParametrName] = $filter->id;


        return $url . "?" . http_build_query($query);
    }

    public function run()
    {

        $result = $this->render('_header');

        $closeUrl = $this->indexUrl;

        $result .= Html::hiddenInput($this->filterParametrName, $this->filter->id);


        //$result .= parent::run();

        if (!empty($this->_fields)) {
            throw new InvalidCallException('Each beginField() should have a matching endField() call.');
        }

        $content = ob_get_clean();
        $result .= Html::beginForm($this->action, $this->method, $this->options);
        $result .= $content;

        if ($this->enableClientScript) {
            $this->registerClientScript();
        }
        $result .= $this->render('_footer-group');

        $result .= Html::endForm();



        $result .= <<<HTML

                </div>
            </div>
        </div>
    </div>
</div>
HTML;
;

        $result .=  $this->render('_edit-filter-form');

        AdminFiltersFormAsset::register($this->view);

        $jsOptions = Json::encode([
            'id'                                => $this->id,
            'createModalId'                     => $this->getCreateModalId(),
            'backendSaveVisibles'               => Url::to(['/admin/admin-filter/save-visibles', 'pk' => $this->filter->id]),
            'backendSaveValues'                 => Url::to(['/admin/admin-filter/save-values', 'pk' => $this->filter->id]),
            'backendDelete'                     => Url::to(['/admin/admin-filter/delete', 'pk' => $this->filter->id]),
            'visibles'                          => $this->filter->visibles,
            'indexUrl'                          => $this->indexUrl,
            'showAllTitle'                      => \Yii::t('skeeks/admin', 'Show all conditions'),
            'hideAllTitle'                      => \Yii::t('skeeks/admin', 'Hide all conditions'),
        ]);

        $this->view->registerJs(<<<JS
        new sx.classes.filters.Form({$jsOptions});
JS
);

        return $result;
    }


    public function getEditFilterFormModalId()
    {
        return $this->id . '-modal-update-filter';
    }

    public function getCreateModalId()
    {
        return $this->id . '-modal-create-filter';
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


    /**
     * @param $model
     */
    public function relatedFields($searchRelatedPropertiesModel)
    {
        echo $this->render('_related-fields', [
            'searchRelatedPropertiesModel'     => $searchRelatedPropertiesModel,
        ]);
    }
}