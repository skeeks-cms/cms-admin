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
namespace skeeks\cms\modules\admin\widgets;
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

        $this->view->registerCss(<<<CSS
.panel-body .sx-admin-filters-form-wrapper .tab-content
{
    box-shadow: inset 0 1px 0 #fff/*, inset 0 0 2px -1px rgba(232,19,64, 1)*/;
    background: #EFF3F4;
    border-radius: 0px 4px 4px 4px;
    border-bottom: 1px solid #adbdca;
    border-right: 1px solid #adbdca;
    border-top: 1px solid #adbdca;
    border-left: 1px solid #adbdca;
    padding: 5px;
    border-color: #d6dfe1 #ced7d8 #969d9e #bec7c8;
    margin-bottom: 10px;
}

.sx-admin-filters-form
{
    padding-top: 10px;
}
.sx-admin-filters-form .form-group:hover
{
    background: rgba(93, 195, 231, 0.15);
}
.sx-admin-filters-form-wrapper
{
    margin-bottom: 10px;
}
.sx-admin-filters-form-wrapper .form-horizontal .form-group
{
    margin-bottom: 0px;
    padding-bottom: 5px;
    padding-top: 5px;
}
.sx-admin-filters-form-wrapper .nav-tabs
{
    border: 0px;
}

.sx-admin-filters-form-wrapper .nav-tabs>li.active>a
{
    background-color: #EFF3F4;
    border-right: 1px solid #adbdca;
    border-left: 1px solid #adbdca;
    border-top: 1px solid #adbdca;
    font-weight: bold;
}
.sx-admin-filters-form-wrapper .nav-tabs>li>a
{
    border-right: 1px solid silver;
    border-left: 1px solid silver;
    border-top: 1px solid silver;
}
.sx-admin-filters-form-wrapper .nav-tabs>li
{
    margin-bottom: -1px;
    margin-right: 3px;
}
CSS
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


    public function field($model, $attribute, $options = [])
    {
        if (!isset($options['template']))
        {
            $options['template'] = "<div class='col-sm-2'>{label}</div><div class='col-sm-9'>{input}{hint}\n{error}</div><div class='col-sm-1 pull-right'>
            <a class=\"btn btn-default btn-sm pull-right\" href=\"#\">
                <i class=\"glyphicon glyphicon-minus\"></i>
            </a>
</div>";
        }

        return parent::field($model, $attribute, $options);
    }

}