
<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 22.05.2016
 */
/* @var $this yii\web\View */
/* @var $widget \skeeks\cms\modules\admin\widgets\filters\AdminFiltersForm */
/* @var $filter \skeeks\cms\modules\admin\models\CmsAdminFilter */
$widget = $this->context;

$adminFilter = new \skeeks\cms\modules\admin\widgets\filters\EditFilterForm();
$adminFilter->loadDefaultValues();
$adminFilter->namespace = $widget->namespace;

/*$adminFilter->name = 'test';
$adminFilter->namespace = $widget->namespace;

$adminFilter->save();*/

?>
<? $createModal = \yii\bootstrap\Modal::begin([
    'header'    => '<b>Сохранить фильтр</b>',
    'footer'    => '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>',
]);?>

    <? $form = \skeeks\cms\base\widgets\ActiveFormAjaxSubmit::begin([
            'action'            => \yii\helpers\Url::to(['/admin/admin-filter/create']),
            'validationUrl'     => \yii\helpers\Url::to(['/admin/admin-filter/validate']),
            'afterValidateCallback'     => new \yii\web\JsExpression(<<<JS
        function(jForm, AjaxQuery)
        {
            var Handler = new sx.classes.AjaxHandlerStandartRespose(AjaxQuery);
            Handler.bind('success', function()
            {
                _.delay(function()
                {
                    window.location.reload();
                }, 500);
            });
        }
JS
            )
        ]); ?>
        <?= $form->field($adminFilter, 'name'); ?>
        <?= $form->field($adminFilter, 'is_public')->checkbox(\Yii::$app->formatter->booleanFormat); ?>
        <?= $form->field($adminFilter, 'namespace')->hiddenInput()->label(false); ?>
        <button class="btn btn-primary"><?= \Yii::t('app', 'Create'); ?></button>
    <? \skeeks\cms\base\widgets\ActiveFormAjaxSubmit::end(); ?>

<? \yii\bootstrap\Modal::end();?>

<div id="<?= $widget->id; ?>-wrapper" class="sx-admin-filters-form-wrapper">
    <div class="row">
        <div class="col-md-8">
            <ul class="nav nav-tabs">
                <li class="<?= !$widget->filter_id ? "active" : "" ?>">
                    <a href="#<?= $widget->id; ?>-default" data-toggle="tab">Фильтр</a>
                </li>

                <? foreach($widget->savedFilters as $filter) : ?>
                    <li class="<?= $widget->filter_id == $filter->id ? "active" : "" ?>">
                        <a href="<?= $widget->getFilterUrl($filter); ?>"><?= $filter->name; ?></a>
                    </li>
                <? endforeach; ?>

                <li>
                    <a href="#<?= $createModal->id; ?>" data-toggle="modal" data-target="#<?= $createModal->id; ?>">
                        <i class="glyphicon glyphicon-plus"></i>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="<?= $widget->id; ?>-default" class="tab-panel active">
