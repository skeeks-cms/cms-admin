<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 22.05.2016
 */
namespace skeeks\cms\modules\admin\widgets\filters;

use skeeks\cms\modules\admin\models\CmsAdminFilter;
use yii\helpers\ArrayHelper;

/**
 * Class EditFilterForm
 * @package skeeks\cms\modules\admin\widgets\filters
 */
class EditFilterForm extends CmsAdminFilter
{
    public $is_public;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['is_public'], 'integer'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'is_public' => \Yii::t('skeeks/admin', 'Is public'),
        ]);
    }
}