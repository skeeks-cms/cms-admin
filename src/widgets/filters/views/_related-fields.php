
<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 22.05.2016
 */
/* @var $this yii\web\View */
/* @var $widget \skeeks\cms\modules\admin\widgets\filters\AdminFiltersForm */
/* @var $model \skeeks\cms\models\CmsContentElement */
/* @var $searchRelpatedPropertiesModel \skeeks\cms\models\searchs\SearchRelatedPropertiesModel */
$widget = $this->context;
$form = $widget;

?>

<? foreach ($searchRelatedPropertiesModel->properties as $property) : ?>
<?/* foreach ($model->relatedPropertiesModel->toArray($model->relatedPropertiesModel->attributes()) as $name => $value) : */?>

    <?
        //$property = $model->relatedPropertiesModel->getRelatedProperty($name);
        $name = $property->code;
        $filter = '';
    ?>

    <?/* if ($property->filtrable == 'Y') : */?>

        <? if ($property->property_type == \skeeks\cms\relatedProperties\PropertyType::CODE_STRING) :?>
            <?= $form->field($searchRelatedPropertiesModel, $name); ?>
        <? endif; ?>

        <? if ($property->property_type == \skeeks\cms\relatedProperties\PropertyType::CODE_BOOL) :?>
            <?= $form->field($searchRelatedPropertiesModel, $name)->checkbox(); ?>
        <? endif; ?>

        <? if ($property->property_type == \skeeks\cms\relatedProperties\PropertyType::CODE_NUMBER) :?>
            <?= $form->field($searchRelatedPropertiesModel, $searchRelatedPropertiesModel->getAttributeNameRangeFrom($name))->label($searchRelatedPropertiesModel->getAttributeLabel($name) . ' (от)'); ?>
            <?= $form->field($searchRelatedPropertiesModel, $searchRelatedPropertiesModel->getAttributeNameRangeTo($name))->label($searchRelatedPropertiesModel->getAttributeLabel($name) . ' (до)'); ?>
        <? endif; ?>
        <? if ($property->property_type == \skeeks\cms\relatedProperties\PropertyType::CODE_LIST) :?>
            <?

            /*$items = \yii\helpers\ArrayHelper::merge(['' => ''], \yii\helpers\ArrayHelper::map(
                $property->enums, 'id', 'value'
            ));

            echo $form->field($searchRelatedPropertiesModel, $name)->dropDownList($items);*/
            echo $form->field($searchRelatedPropertiesModel, $name)->widget(
                \skeeks\cms\backend\widgets\SelectModelDialogWidget::class,
                [
                    'modelClassName' => \skeeks\cms\models\CmsContentPropertyEnum::class,
                    'dialogRoute' => ['/cms/admin-cms-content-property-enum', 'CmsContentPropertyEnum' => [
                        'property_id' => $property->id
                    ]],
                ]
            );

            ?>
        <? endif; ?>

        <? if ($property->property_type == \skeeks\cms\relatedProperties\PropertyType::CODE_ELEMENT) :?>
           <?
                $propertyType = $property->handler;
                /*$options = \skeeks\cms\models\CmsContentElement::find()->active()->andWhere([
                    'content_id' => $propertyType->content_id
                ])->all();

                $items = \yii\helpers\ArrayHelper::merge(['' => ''], \yii\helpers\ArrayHelper::map(
                    $options, 'id', 'name'
                ));*/

                echo $form->field($searchRelatedPropertiesModel, $name)->widget(
                    \skeeks\cms\backend\widgets\SelectModelDialogContentElementWidget::class,
                    [
                        'content_id' => $propertyType->content_id
                    ]
                );
                //echo $form->field($searchRelatedPropertiesModel, $name)->dropDownList($items);

            ?>

        <? endif; ?>

    <?/* endif; */?>

<? endforeach; ?>
