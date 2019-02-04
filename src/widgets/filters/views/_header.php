
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

$adminFilter = new \skeeks\cms\modules\admin\models\CmsAdminFilter();
$adminFilter->loadDefaultValues();
$adminFilter->namespace = $widget->namespace;
$createFormId = $widget->id . '-create-filter';
?>




<? $createModal = \yii\bootstrap\Modal::begin([
    'id'        => $widget->getCreateModalId(),
    'header'    => '<b>' . \Yii::t('skeeks/admin', 'Save filter') . '</b>',
    'footer'    => '
        <button class="btn btn-primary" onclick="$(\'#' . $createFormId . '\').submit(); return false;">' . \Yii::t('skeeks/cms', 'Create') . '</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">' . \Yii::t('skeeks/admin', 'Close') . '</button>
    ',
]);?>

    <? $form = \skeeks\cms\base\widgets\ActiveFormAjaxSubmit::begin([
            'id'                => $createFormId,
            'action'            => \yii\helpers\Url::to(['/admin/admin-filter/create']),
            'validationUrl'     => \yii\helpers\Url::to(['/admin/admin-filter/validate']),
            'afterValidateCallback'     => new \yii\web\JsExpression(<<<JS
        function(jForm, AjaxQuery)
        {
            var Handler = new sx.classes.AjaxHandlerStandartRespose(AjaxQuery);
            var Blocker = new sx.classes.AjaxHandlerBlocker(AjaxQuery, {
                'wrapper' : jForm.closest('.modal-content')
            });

            Handler.bind('success', function()
            {
                _.delay(function()
                {
                    window.location.reload();
                }, 1000);
            });
        }
JS
            )
        ]); ?>
        <?= $form->field($adminFilter, 'name'); ?>
        <?= $form->field($adminFilter, 'isPublic')->checkbox(\Yii::$app->formatter->booleanFormat); ?>
        <?= $form->field($adminFilter, 'namespace')->hiddenInput()->label(false); ?>

        <?= \yii\bootstrap\Html::hiddenInput('visibles'); ?>
        <?= \yii\bootstrap\Html::hiddenInput('values'); ?>
        <button style="display: none;"></button>
    <? \skeeks\cms\base\widgets\ActiveFormAjaxSubmit::end(); ?>

<? $createModal::end();?>




<div id="<?= $widget->id; ?>-wrapper" class="sx-admin-filters-form-wrapper">
    <div class="row">
        <div class="col-lg-9 col-md-12">
            <ul class="nav nav-tabs">
                <? foreach($widget->savedFilters as $filter) : ?>
                    <li class="sx-tab <?= $widget->filter->id == $filter->id ? "active" : "" ?>">
                        <a href="<?= $widget->getFilterUrl($filter); ?>"><?= $filter->displayName; ?></a>
                    </li>
                <? endforeach; ?>

                <li>
                    <a href="#<?= $createModal->id; ?>" class="sx-btn-filter-create">
                        <i class="fa fa-plus"></i>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="<?= $widget->id; ?>-default" class="tab-panel active">
