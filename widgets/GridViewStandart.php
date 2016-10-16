<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 24.07.2015
 */
namespace skeeks\cms\modules\admin\widgets;
use skeeks\cms\modules\admin\actions\modelEditor\AdminMultiModelEditAction;
use skeeks\cms\modules\admin\controllers\AdminModelEditorController;
use skeeks\cms\modules\admin\grid\CheckboxColumn;
use skeeks\cms\modules\admin\widgets\gridViewStandart\GridViewStandartAsset;
use skeeks\cms\relatedProperties\models\RelatedPropertiesModel;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;

/**
 * @property string $gridJsObject
 *
 * Class GridViewStandart
 * @package skeeks\cms\modules\admin\widgets
 */
class GridViewStandart extends GridViewHasSettings
{
    /**
     * @var AdminModelEditorController
     */
    public $adminController = null;
    public $isOpenNewWindow = false;
    public $enabledCheckbox = true;

    public function init()
    {
        $defaultColumns = [];

        if ($this->enabledCheckbox)
        {
            $defaultColumns[] = ['class' => 'skeeks\cms\modules\admin\grid\CheckboxColumn'];
        }

        if ($this->adminController)
        {
            $defaultColumns[] = [
                'class'                 => \skeeks\cms\modules\admin\grid\ActionColumn::className(),
                'controller'            => $this->adminController,
                'isOpenNewWindow'       => $this->isOpenNewWindow
            ];
        }

        $defaultColumns[] = [
            'class' => 'yii\grid\SerialColumn',
            'visible' => false
        ];

        $this->columns = ArrayHelper::merge($defaultColumns, $this->columns);

        parent::init();
    }

    /**
     * @return string
     */
    public function getGridJsObject()
    {
        return "sx.Grid" . $this->id;
    }
    /**
     * @return string
     */
    public function renderAfterTable()
    {
        $id = $this->id;

        GridViewStandartAsset::register($this->view);

        $checkbox = Html::checkbox('sx-select-full-all', false, [
            'class' => 'sx-select-full-all'
        ]);

        $multiActions = [];
        if ($this->adminController)
        {
            $multiActions = $this->adminController->getMultiActions();
        }

        if (!$multiActions)
        {
            return parent::renderAfterTable();
        }


        $options = [
            'id'                    => $this->id,
            'enabledPjax'           => $this->enabledPjax,
            'pjaxId'                => $this->pjax->id,
            'requestPkParamName'    => $this->adminController->requestPkParamName
        ];
        $optionsString = Json::encode($options);

        $gridJsObject = $this->getGridJsObject();

        $this->view->registerJs(<<<JS
        {$gridJsObject} = new sx.classes.grid.Standart($optionsString);
JS
);

        $buttons = "";

        $additional = [];
        foreach ($multiActions as $action)
        {
            $additional[]               = $action->registerForGrid($this);

            $buttons .= <<<HTML
            <button class="btn btn-default btn-sm sx-grid-multi-btn" data-id="{$action->id}">
                <i class="{$action->icon}"></i> {$action->name}
            </button>
HTML;
        }

        $additional = implode("", $additional);
        $this->afterTableLeft = <<<HTML
    {$checkbox} для всех
    <span class="sx-grid-multi-controlls">
        {$buttons}
    </span>
    <span style="display: none;">{$additional}</span>
HTML;




        $this->view->registerCss(<<<CSS
    .sx-grid-multi-controlls
    {
        margin-left: 20px;
    }
CSS
);

        return parent::renderAfterTable();


    }

    /**
     * @param RelatedPropertiesModel|null $relatedPropertiesModel
     * @return array
     */
    static public function getColumnsByRelatedPropertiesModel(RelatedPropertiesModel $relatedPropertiesModel = null, $searchModel)
    {
        $autoColumns = [];
        $searchRelatedPropertiesModel = $searchModel;
        /**
         * @var $model \skeeks\cms\models\CmsContentElement
         */
        if ($relatedPropertiesModel)
        {
            foreach ($relatedPropertiesModel->toArray($relatedPropertiesModel->attributes()) as $name => $value) {


                $property = $relatedPropertiesModel->getRelatedProperty($name);
                $filter = '';

                if ($property->property_type == \skeeks\cms\relatedProperties\PropertyType::CODE_ELEMENT)
                {
                    $propertyType = $property->handler;
                        $options = \skeeks\cms\models\CmsContentElement::find()->active()->andWhere([
                            'content_id' => $propertyType->content_id
                        ])->all();

                        $items = \yii\helpers\ArrayHelper::merge(['' => ''], \yii\helpers\ArrayHelper::map(
                            $options, 'id', 'name'
                        ));

                    $filter = \yii\helpers\Html::activeDropDownList($searchRelatedPropertiesModel, $name, $items, ['class' => 'form-control']);

                } else if ($property->property_type == \skeeks\cms\relatedProperties\PropertyType::CODE_LIST)
                {
                    $items = \yii\helpers\ArrayHelper::merge(['' => ''], \yii\helpers\ArrayHelper::map(
                        $property->enums, 'id', 'value'
                    ));

                    $filter = \yii\helpers\Html::activeDropDownList($searchRelatedPropertiesModel, $name, $items, ['class' => 'form-control']);

                } else if ($property->property_type == \skeeks\cms\relatedProperties\PropertyType::CODE_STRING)
                {
                    $filter = \yii\helpers\Html::activeTextInput($searchRelatedPropertiesModel, $name, [
                        'class' => 'form-control'
                    ]);
                }
                else if ($property->property_type == \skeeks\cms\relatedProperties\PropertyType::CODE_NUMBER)
                {
                    $filter = "<div class='row'><div class='col-md-6'>" . \yii\helpers\Html::activeTextInput($searchRelatedPropertiesModel, $searchRelatedPropertiesModel->getAttributeNameRangeFrom($name), [
                                    'class' => 'form-control',
                                    'placeholder' => 'от'
                                ]) . "</div><div class='col-md-6'>" .
                                    \yii\helpers\Html::activeTextInput($searchRelatedPropertiesModel, $searchRelatedPropertiesModel->getAttributeNameRangeTo($name), [
                                    'class' => 'form-control',
                                    'placeholder' => 'до'
                                ]) . "</div></div>"
                            ;
                }

                $autoColumns[] = [
                    'attribute' => $name,
                    'label' => \yii\helpers\ArrayHelper::getValue($relatedPropertiesModel->attributeLabels(), $name),
                    'visible' => false,
                    'format' => 'raw',
                    'filter' => $filter,
                    'class' => \yii\grid\DataColumn::className(),
                    'value' => function($model, $key, $index) use ($name)
                    {
                        /**
                         * @var $model \skeeks\cms\models\CmsContentElement
                         */
                        $value = $model->relatedPropertiesModel->getSmartAttribute($name);
                        if (is_array($value))
                        {
                            return implode(",", $value);
                        } else
                        {
                            return $value;
                        }
                    },
                ];
            }
        }

        return $autoColumns;
    }

}