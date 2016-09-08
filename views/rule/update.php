<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var skeeks\cms\models\AuthItem $model
 */
$this->title = \Yii::t('skeeks/cms', 'Update Rule') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('skeeks/cms', 'Rules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->name]];
$this->params['breadcrumbs'][] = \Yii::t('skeeks/cms', 'Update');
?>
<div class="auth-item-update">

	<h1><?= Html::encode($this->title) ?></h1>
	<?php
    echo $this->render('_form', [
        'model' => $model,
    ]);
    ?>
</div>
