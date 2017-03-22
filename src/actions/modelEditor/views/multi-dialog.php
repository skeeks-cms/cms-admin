<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 31.10.2016
 */
/* @var $this yii\web\View */
/* @var $context \skeeks\cms\modules\admin\actions\modelEditor\AdminMultiDialogModelEditAction */
$context = $this->context;
?>
<? \yii\bootstrap\Modal::begin([
    'header' => '<b>' . $context->name . '</b>',
    'size' => \yii\bootstrap\Modal::SIZE_LARGE,
    'id' => $dialogId,
]); ?>
    <?= $content; ?>
<? \yii\bootstrap\Modal::end(); ?>
