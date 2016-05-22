
<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 22.05.2016
 */
/* @var $this yii\web\View */
/* @var $widget \skeeks\cms\modules\admin\widgets\filters\AdminFiltersForm */
$widget = $this->context;

$adminFilter = new \skeeks\cms\modules\admin\models\CmsAdminFilter();
?>
<? $createModal = \yii\bootstrap\Modal::begin([
    'header'    => '<b>Сохранить фильтр</b>',
    'footer'    => '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>',
]);?>

    <? $form = \skeeks\cms\base\widgets\ActiveFormAjaxSubmit::begin([
            'action'            => \yii\helpers\Url::to(['/admin/admin-']),
            'validationUrl'     => \yii\helpers\Url::to(['/admin/validate']),
            'afterValidateCallback'     => new \yii\web\JsExpression(<<<JS
        function(jForm, AjaxQuery)
        {
        var Handler = new sx.classes.AjaxHandlerStandartRespose(AjaxQuery);
        Handler.bind('success', function()
        {
        $('#{$modal->id}').modal('hide');
        _.delay(function()
        {
        window.location.reload();
        }, 500);
        });
        }
JS
            )
        ]); ?>
        <?= $form->field(\Yii::$app->user->identity, 'name'); ?>
        <button class="btn btn-primary"><?= \Yii::t('app', 'Create'); ?></button>
    <? \skeeks\cms\base\widgets\ActiveFormAjaxSubmit::end(); ?>

<? \yii\bootstrap\Modal::end();?>

<div id="<?= $widget->id; ?>-wrapper" class="sx-admin-filters-form-wrapper">
    <div class="row">
        <div class="col-md-8">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#<?= $widget->id; ?>-tab0" data-toggle="tab">Фильтр</a>
                </li>

                <li>
                    <a href="#<?= $createModal->id; ?>" data-toggle="modal" data-target="#<?= $createModal->id; ?>">
                        <i class="glyphicon glyphicon-plus"></i>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="<?= $widget->id; ?>-tab0" class="tab-panel active">
