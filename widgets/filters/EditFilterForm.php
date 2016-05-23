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

    public function init()
    {
        parent::init();

        $this->on(static::EVENT_BEFORE_INSERT, [$this, '_brefreSavePublic']);
        $this->on(static::EVENT_BEFORE_UPDATE, [$this, '_brefreSavePublic']);
    }

    public function _brefreSavePublic($e)
    {
        if ($this->is_public == 1)
        {
            $this->cms_user_id = null;
        } else
        {
            $this->cms_user_id = \Yii::$app->user->id;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['is_public'], 'integer'],
            [['name'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'is_public' => \Yii::t('skeeks/admin', 'Available for all'),
        ]);
    }
}