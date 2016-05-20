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
use skeeks\cms\helpers\UrlHelper;
use skeeks\cms\modules\admin\assets\AdminFormAsset;
use skeeks\cms\modules\admin\controllers\AdminModelEditorController;
use skeeks\cms\modules\admin\traits\ActiveFormTrait;
use skeeks\cms\modules\admin\traits\AdminActiveFormTrait;
use skeeks\cms\traits\ActiveFormAjaxSubmitTrait;
use skeeks\widget\chosen\Chosen;
use yii\base\Model;
use yii\bootstrap\Tabs;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use skeeks\cms\modules\admin\widgets\Pjax;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * Class ActiveForm
 * @package skeeks\cms\modules\admin\widgets
 */
class AdminFiltersForm extends \skeeks\cms\base\widgets\ActiveForm
{
    use AdminActiveFormTrait;

    public $fieldClass = 'skeeks\cms\modules\admin\widgets\filters\FilterActiveField';

    public $method = 'get';
    public $usePjax = false;
    public $enableAjaxValidation = false;
    public $enableClientValidation = false;
    public $options = [
        'data-pjax' => true
    ];

    public $useAjaxSubmit = false;
    public $afterValidateCallback = "";


    /**
     * Initializes the widget.
     * This renders the form open tag.
     */
    public function init()
    {
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



        echo <<<HTML
        <div id="{$this->id}" class="sx-admin-filters-form-wrapper">
        <div class="row">
        <div class="col-md-8">
        <div data-pjax-container="" data-pjax-push-state data-pjax-timeout="1000">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#w1-tab0" data-toggle="tab">Фильтры</a>
                </li>
                <li>
                    <a href="#w1-tab1" data-toggle="tab1">Новые товары</a>
                </li>
                <li>
                    <a href="#w1-tab1" data-toggle="tab1"> <i class="glyphicon glyphicon-plus"></i> </a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="w1-tab0" class="tab-pane active">

HTML;

        parent::init();
    }

    public function run()
    {
        echo <<<HTML


                <div class="form-group">
                    <div class="col-sm-12">
                        <hr style="margin-top: 5px; margin-bottom: 15px;"/>
                        <button type="submit" class="btn btn-default pull-left">
                            <i class="glyphicon glyphicon-search"></i> Найти
                        </button>


                        <a class="btn btn-default btn-sm pull-right" href="#" style="margin-left: 10px;">
                            <i class="glyphicon glyphicon-plus"></i>
                        </a>

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
</div>
HTML;
;

        AdminFiltersFormAsset::register($this->view);

        $jsOptions = Json::encode([
            'id' => $this->id
        ]);

        $this->view->registerJs(<<<JS
        new sx.classes.FiltersForm({$jsOptions})
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


    /**
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return FilterActiveField
     */
    public function field($model, $attribute, $options = [])
    {
        $field = parent::field($model, $attribute, $options);

        return $field;
    }

}