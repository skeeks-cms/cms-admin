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
        <a href="#" data-toggle="modal" data-target="#sx-dashboard-create" class="btn btn-default btn-primary"><i class="glyphicon glyphicon-plus"></i> <?= \Yii::t('skeeks/cms', 'Add dashboard'); ?></a>
    </div>
    <div class="col-md-6">
        <div class="pull-right">
            <a href="#" data-toggle="modal" data-target="#sx-dashboard-widget-create" class="btn btn-default btn-primary"><i class="icon-calculator"></i> <?= \Yii::t('skeeks/cms', 'Add widget'); ?></a>
            <a href="#" data-toggle="modal" data-target="#sx-dashboard-edit" class="btn btn-default btn-primary"><i class="glyphicon glyphicon-cog"></i>  <?= \Yii::t('skeeks/cms', 'Settings'); ?></a>
            <a href="#" onclick="sx.DashboardsControll.remove(); return false;" class="btn btn-default btn-danger"><i class="glyphicon glyphicon-remove"></i> <?= \Yii::t('skeeks/cms', 'Delete'); ?></a>
        </div>
    </div>
</div>

<? $createModal = \yii\bootstrap\Modal::begin([
    'id'        => 'sx-dashboard-widget-create',
    'header'    => '<b>' . \Yii::t('skeeks/admin', 'Add widget') . '</b>',
    'footer'    => '
        <button class="btn btn-primary" onclick="$(\'#sx-create-widget-form\').submit(); return false;">' . \Yii::t('skeeks/admin', 'Add') . '</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">' . \Yii::t('skeeks/admin', 'Close') . '</button>
    ',
]); ?>

    <? $form = \skeeks\cms\modules\admin\widgets\ActiveForm::begin([
        'id'                => 'sx-create-widget-form',
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
        <div style="display: none;">
            <?= $form->field($modelWidget, 'cms_dashboard_id')->hiddenInput()->label(false); ?>
        </div>
        <?= $form->fieldSelect($modelWidget, 'component', \Yii::$app->admin->dasboardWidgetsLabels)->label(\Yii::t('skeeks/admin', 'Widget')); ?>

        <div style="display: none;">
            <?= $form->buttonsStandart($model, ['save']); ?>
        </div>
    <? \skeeks\cms\modules\admin\widgets\ActiveForm::end()?>
<? \yii\bootstrap\Modal::end();?>


<? $createModal = \yii\bootstrap\Modal::begin([
    'id'        => 'sx-dashboard-create',
    'header'    => '<b>' . \Yii::t('skeeks/admin', 'Add desktop') . '</b>',
    'footer'    => '
        <button class="btn btn-primary" onclick="$(\'#sx-dashboard-create-form\').submit(); return false;">' . \Yii::t('skeeks/admin', 'Add') . '</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">' . \Yii::t('skeeks/admin', 'Close') . '</button>
    ',
]); ?>

    <? $form = \skeeks\cms\modules\admin\widgets\ActiveForm::begin([
            'id'                => 'sx-dashboard-create-form',
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
            <div style="display: none;">
                <?= $form->buttonsStandart($model, ['save']); ?>
            </div>
        <? \skeeks\cms\modules\admin\widgets\ActiveForm::end()?>

<? \yii\bootstrap\Modal::end();?>


<? $createModal = \yii\bootstrap\Modal::begin([
    'id'        => 'sx-dashboard-edit',
    'header'    => '<b>' . \Yii::t('skeeks/admin', 'Desktop customization') . '</b>',
    'footer'    => '
        <button class="btn btn-primary" onclick="$(\'#sx-dashboard-edit-form\').submit(); return false;">' . \Yii::t('skeeks/admin', 'Save') . '</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">' . \Yii::t('skeeks/admin', 'Close') . '</button>
    ',
]); ?>

    <? $form = \skeeks\cms\modules\admin\widgets\ActiveForm::begin([
            'id'                => 'sx-dashboard-edit-form',
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
            <div style="display: none;">
                <?= $form->buttonsStandart($model, ['save']); ?>
            </div>
        <? \skeeks\cms\modules\admin\widgets\ActiveForm::end()?>

<? \yii\bootstrap\Modal::end();?>

<?
$jsonData = \yii\helpers\Json::encode([
    'model' => $dashboard,
    'confirmMsg' => \Yii::t('skeeks/cms', 'Are you sure you want to delete this desktop?'),
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
                $('div').modal('hide');

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