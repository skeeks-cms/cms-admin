<?php
/* @var $this yii\web\View */
/* @var $dashboard skeeks\cms\models\CmsDashboard */
$model = new \skeeks\cms\models\CmsDashboard();
$modelWidget = new \skeeks\cms\models\CmsDashboardWidget();

$modelWidget->cms_dashboard_id = $dashboard->id;
$model->columns = 1;

?>
<div class="row sx-main-head sx-bg-glass sx-bg-glass-hover">
    <div class="col-md-6 pull-left">
        <a href="#sx-dashboard-create" class="btn btn-default btn-primary sx-fancybox"><i class="glyphicon glyphicon-plus"></i> <?= \Yii::t('app', 'Add dashboard'); ?></a>
    </div>
    <div class="col-md-6">
        <div class="pull-right">
            <a href="#sx-dashboard-widget-create" class="btn btn-default btn-primary sx-fancybox"><i class="icon-calculator"></i> <?= \Yii::t('app', 'Add widget'); ?></a>
            <a href="#sx-dashboard-edit" class="btn btn-default btn-primary sx-fancybox"><i class="glyphicon glyphicon-cog"></i>  <?= \Yii::t('app', 'Settings'); ?></a>
            <a href="#" onclick="sx.DashboardsControll.remove(); return false;" class="btn btn-default btn-danger"><i class="glyphicon glyphicon-remove"></i> <?= \Yii::t('app', 'Delete'); ?></a>
        </div>
    </div>
</div>

<div style="display: none;">
    <div id="sx-dashboard-edit" style="min-width: 500px; max-width: 800px;">
        <? $form = \skeeks\cms\modules\admin\widgets\ActiveForm::begin([
            'usePjax'           => false,
            'useAjaxSubmit'     => true,
            'validationUrl'     => \skeeks\cms\helpers\UrlHelper::construct(['admin/index/dashboard-validate', 'pk' => $dashboard->id])->enableAdmin()->toString(),
            'action'            => \skeeks\cms\helpers\UrlHelper::construct(['admin/index/dashboard-save', 'pk' => $dashboard->id])->enableAdmin()->toString(),

            'afterValidateCallback'                     => new \yii\web\JsExpression(<<<JS
                function(jForm, ajaxQuery){
                    new sx.classes.DashboardsControllCallback(jForm, ajaxQuery);
                };
JS
    ),

        ])?>
            <?= $form->field($dashboard, 'name'); ?>
            <?= $form->field($dashboard, 'columns'); ?>
            <?= $form->field($dashboard, 'priority'); ?>
            <?= $form->buttonsStandart($dashboard, ['save']); ?>
        <? \skeeks\cms\modules\admin\widgets\ActiveForm::end()?>
    </div>

    <div id="sx-dashboard-create" style="min-width: 500px; max-width: 800px;">
        <? $form = \skeeks\cms\modules\admin\widgets\ActiveForm::begin([
            'usePjax'           => false,
            'useAjaxSubmit'     => true,
            'validationUrl'     => \skeeks\cms\helpers\UrlHelper::construct(['admin/index/dashboard-create-validate', 'pk' => $model->id])->enableAdmin()->toString(),
            'action'            => \skeeks\cms\helpers\UrlHelper::construct(['admin/index/dashboard-create-save', 'pk' => $model->id])->enableAdmin()->toString(),

            'afterValidateCallback'                     => new \yii\web\JsExpression(<<<JS
                function(jForm, ajaxQuery){
                    new sx.classes.DashboardsControllCallback(jForm, ajaxQuery);
                };
JS
    ),

        ])?>
            <?= $form->field($model, 'name'); ?>
            <?= $form->field($model, 'columns'); ?>
            <?= $form->field($model, 'priority'); ?>
            <?= $form->buttonsStandart($model, ['save']); ?>
        <? \skeeks\cms\modules\admin\widgets\ActiveForm::end()?>
    </div>

    <div id="sx-dashboard-widget-create" style="min-width: 500px; min-height: 500px; max-width: 800px;">
        <? $form = \skeeks\cms\modules\admin\widgets\ActiveForm::begin([
            'usePjax'           => false,
            'useAjaxSubmit'     => true,
            'validationUrl'     => \skeeks\cms\helpers\UrlHelper::construct(['admin/index/dashboard-widget-create-validate', 'pk' => $model->id])->enableAdmin()->toString(),
            'action'            => \skeeks\cms\helpers\UrlHelper::construct(['admin/index/dashboard-widget-create-save', 'pk' => $model->id])->enableAdmin()->toString(),

            'afterValidateCallback'                     => new \yii\web\JsExpression(<<<JS
                function(jForm, ajaxQuery){
                    new sx.classes.DashboardsControllCallback(jForm, ajaxQuery);
                };
JS
    ),

        ])?>
            <?= $form->field($modelWidget, 'cms_dashboard_id')->hiddenInput()->label(false); ?>

            <?= $form->fieldSelect($modelWidget, 'component', \Yii::$app->admin->dasboardWidgetsLabels); ?>

            <!--<div class="row">
                <div class="col-md-6">
                    --><?/*= $form->fieldSelect($modelWidget, 'component', \Yii::$app->admin->dasboardWidgetsLabels); */?>
                    <?/*= $form->field($modelWidget, 'component')->listBox(\Yii::$app->admin->dasboardWidgetsLabels, ['size' => 1]); */?>
                <!--</div>-->
                <!--<div class="col-md-6">
                    <label></label>
                    <?/*= $form->field($modelWidget, 'componentSettingsString')->label(false)->widget(
                        \skeeks\cms\widgets\formInputs\componentSettings\ComponentSettingsWidget::className(),
                        [
                            'componentSelectId' => \yii\helpers\Html::getInputId($modelWidget, "component"),
                            'buttonText'        => \skeeks\cms\shop\Module::t('app', 'Settings handler'),
                            'buttonClasses'     => "sx-btn-edit btn btn-default"
                        ]
                    ); */?>
                </div>-->
            <!--</div>-->

            <?= $form->buttonsStandart($modelWidget, ['save']); ?>
        <? \skeeks\cms\modules\admin\widgets\ActiveForm::end()?>
    </div>
</div>

<?
$jsonData = \yii\helpers\Json::encode([
    'model' => $dashboard,
    'confirmMsg' => \Yii::t('app', 'Are you sure you want to delete this desktop?'),
    'removeBackend' => \skeeks\cms\helpers\UrlHelper::construct(['/admin/index/dashboard-remove', 'pk' => $dashboard->id])->enableAdmin()->toString(),
]);

$this->registerJs(<<<JS
(function(sx, $, _)
{
    sx.classes.DashboardsControll = sx.classes.Component.extend({

        _onDomReady: function()
        {},

        remove: function()
        {
            var self = this;

            sx.confirm(this.get('confirmMsg'), {
                'yes': function()
                {
                    var AjaxQuery = sx.ajax.preparePostQuery(self.get('removeBackend'));
                    var handler = new sx.classes.AjaxHandlerStandartRespose(AjaxQuery);
                    AjaxQuery.execute();
                }
            });
        },
    });

    sx.DashboardsControll = new sx.classes.DashboardsControll({$jsonData});

    sx.classes.DashboardsControllCallback = sx.classes.Component.extend({

        construct: function (jForm, ajaxQuery, opts)
        {
            var self = this;
            opts = opts || {};

            this._jForm     = jForm;
            this._ajaxQuery = ajaxQuery;

            this.applyParentMethod(sx.classes.Component, 'construct', [opts]); // TODO: make a workaround for magic parent calling
        },

        _init: function()
        {
            var jForm   = this._jForm;
            var ajax    = this._ajaxQuery;

            var handler = new sx.classes.AjaxHandlerStandartRespose(ajax, {
                'blockerSelector' : '#' + jForm.attr('id'),
                'enableBlocker' : true,
            });

            handler.bind('success', function(response)
            {
                $.fancybox.close();

                _.delay(function()
                {
                    window.location.reload();
                }, 1000);
            });
        }
    });


})(sx, sx.$, sx._);
JS
)
?>