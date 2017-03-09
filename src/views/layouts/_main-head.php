<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 12.04.2016
 */
?>

<div class="col-md-12 sx-empty-hide">

    <div class="row sx-main-head sx-bg-glass sx-bg-glass-hover">
        <div class="col-md-11 pull-left">
            <? $controller = \Yii::$app->controller; ?>
            <? if ($controller && $controller instanceof \skeeks\cms\backend\IHasBreadcrumbs) : ?>
                <?= \yii\widgets\Breadcrumbs::widget([
                    'homeLink' => ['label' => \Yii::t("yii", "Home"), 'url' =>
                        \yii\helpers\Url::to(['/admin/index'])
                    ],
                    'links' => $controller->breadcrumbsData,
                ]) ?>
            <? endif; ?>
        </div>
        <div class="col-md-1">
            <div class="pull-right">

                <? if (\Yii::$app->user->can('admin/admin-role') && \Yii::$app->controller instanceof \skeeks\cms\modules\admin\controllers\AdminController) : ?>

                    <a href="#sx-permissions-for-controller" class="btn btn-default btn-primary sx-fancybox">
                        <i class="glyphicon glyphicon-exclamation-sign" data-sx-widget="tooltip-b" data-original-title="<?=\Yii::t('skeeks/cms','Setting up access to this section')?>" style="color: white;"></i>

                    </a>

                    <div style="display: none;">
                        <div id="sx-permissions-for-controller" style="min-height: 300px;">

                            <?
                            $adminPermission = \Yii::$app->authManager->getPermission(\skeeks\cms\rbac\CmsManager::PERMISSION_ADMIN_ACCESS);
                            $items = [];
                            foreach (\Yii::$app->authManager->getRoles() as $role)
                            {
                                if (\Yii::$app->authManager->hasChild($role, $adminPermission))
                                {
                                    $items[] = $role;
                                }
                            }
                            ?>
                            <?= \skeeks\cms\rbac\widgets\adminPermissionForRoles\AdminPermissionForRolesWidget::widget([
                                'permissionName'        => \Yii::$app->controller->permissionName,
                                'permissionDescription' => \Yii::t('skeeks/cms','Administration')." | " . \Yii::$app->controller->name,
                                'label'                 => \Yii::t('skeeks/cms','Setting up access to the section').": " . \Yii::$app->controller->name,
                                'items'                 => \yii\helpers\ArrayHelper::map($items, 'name', 'description'),
                            ]); ?>
                            <?=\Yii::t('skeeks/cms','Specify which groups of users will have access.')?>
                            <hr />
                            <? \yii\bootstrap\Alert::begin([
                                'options' => [
                                  'class' => 'alert-info',
                                ],
                            ])?>
                                <p><?=\Yii::t('skeeks/cms','Code privileges')?>: <b><?= \Yii::$app->controller->permissionName; ?></b></p>
                                <p><?=\Yii::t('skeeks/cms','The list displays only those groups that have access to the system administration.')?></p>
                            <? \yii\bootstrap\Alert::end()?>
                        </div>
                    </div>

                <? endif; ?>

            </div>
        </div>
    </div>
</div>
