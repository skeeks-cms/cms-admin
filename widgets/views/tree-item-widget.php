<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 18.12.2016
 */
/* @var $this yii\web\View */
/* @var $widget \skeeks\cms\modules\admin\widgets\Tree */
/* @var $model \skeeks\cms\models\CmsTree */
$widget = $this->context;
?>
<?= \yii\helpers\Html::beginTag('li', [
    "class" => "sx-tree-node " . ($widget->isOpenNode($model) ? " open" : ""),
    "data-id" => $model->id,
    "title" => ""
]); ?>

    <div class="row">
        <? if ($model->children) : ?>
            <div class="sx-node-open-close">
                <? if ($widget->isOpenNode($model)) : ?>
                    <a href="<?= $widget->getLink($model); ?>" class="btn btn-sm btn-default">
                        <span class="glyphicon glyphicon-minus" title="<?= \Yii::t('skeeks/cms',"Minimize"); ?>"></span>
                    </a>
                <? else : ?>
                    <a href="<?= $widget->getLink($model); ?>" class="btn btn-sm btn-default">
                        <span class="glyphicon glyphicon-plus" title="<?= \Yii::t('skeeks/cms',"Restore"); ?>"></span>
                    </a>
                <? endif; ?>
            </div>
        <? endif; ?>
        <?= $controllElement; ?>
        <div class="sx-label-node level-<?= $model->level; ?> status-<?= $model->active; ?>">
            <a href="<?= $widget->getLink($model); ?>">
                <?= $widget->getNodeName($model); ?>
            </a>
        </div>
        <!-- Possible actions -->
        <div class="sx-controll-node row">
            <?
                $controller = \Yii::$app->cms->moduleCms->createControllerByID("admin-tree");
                $controller->setModel($model);
            ?>
            <?= \skeeks\cms\modules\admin\widgets\DropdownControllerActions::widget([
                "controller"            => $controller,
                "renderFirstAction"     => true,
                "containerClass"        => "dropdown pull-left",
                'clientOptions'         =>
                [
                    'pjax-id' => 'sx-pjax-tree'
                ]
            ]); ?>
            <div class="pull-left sx-controll-act">
                <a href="#" class="btn-tree-node-controll btn btn-default btn-sm add-tree-child" title="<?= \Yii::t('skeeks/cms','Create subsection'); ?>" data-id="<?= $model->id; ?>"><span class="glyphicon glyphicon-plus"></span></a>
            </div>
            <div class="pull-left sx-controll-act">
                <a href="<?= $model->absoluteUrl; ?>" target="_blank" class="btn-tree-node-controll btn btn-default btn-sm show-at-site" title="<?= \Yii::t('skeeks/cms',"Show at site"); ?>">
                    <span class="glyphicon glyphicon-eye-open"></span>
                </a>
            </div>
            <? if ($model->level > 0) : ?>
                <div class="pull-left sx-controll-act">
                    <a href="#" class="btn-tree-node-controll btn btn-default btn-sm sx-tree-move" title="<?= \Yii::t('skeeks/cms',"Change sorting"); ?>">
                        <span class="glyphicon glyphicon-move"></span>
                    </a>
                </div>
            <? endif; ?>
        </div>

        <? if ($model->treeType) : ?>
            <div class="pull-right sx-tree-type">
                <?= $model->treeType->name; ?>
            </div>
        <? endif; ?>
    </div>

    <!-- Construction of child elements -->
    <? if ($widget->isOpenNode($model) && $model->children) : ?>
        <?= $widget->renderNodes($model->children); ?>
    <? endif;  ?>

<?= \yii\helpers\Html::endTag('li'); ?>

