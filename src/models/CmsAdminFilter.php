<?php

namespace skeeks\cms\modules\admin\models;

use skeeks\cms\models\behaviors\Implode;
use skeeks\cms\models\behaviors\Serialize;
use skeeks\cms\models\CmsUser;
use Yii;

/**
 * This is the model class for table "cms_admin_filter".
 *
 * @property integer $id
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $cms_user_id
 * @property integer $is_default
 * @property string $name
 * @property string $namespace
 * @property string $values
 * @property string $visibles
 *
 * @property integer $isPublic
 *
 * @property CmsUser $cmsUser
 * @property CmsUser $createdBy
 * @property CmsUser $updatedBy
 *
 * @property string $displayName
 */
class CmsAdminFilter extends \skeeks\cms\models\Core
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cms_admin_filter}}';
    }


    /**
     * @return array
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [

            Serialize::className() =>
                [
                    'class' => Serialize::className(),
                    'fields' => ['values']
                ],

            Implode::className() =>
                [
                    'class' => Implode::className(),
                    'fields' => ['visibles']
                ],

        ]);
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['created_by', 'updated_by', 'created_at', 'updated_at', 'cms_user_id', 'is_default'], 'integer'],
            [['namespace'], 'required'],
            [['values', 'visibles'], 'safe'],
            [['name'], 'string', 'max' => 64],
            [['namespace'], 'string', 'max' => 255],
            [
                ['cms_user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => CmsUser::className(),
                'targetAttribute' => ['cms_user_id' => 'id']
            ],
            [
                ['created_by'],
                'exist',
                'skipOnError' => true,
                'targetClass' => CmsUser::className(),
                'targetAttribute' => ['created_by' => 'id']
            ],
            [
                ['updated_by'],
                'exist',
                'skipOnError' => true,
                'targetClass' => CmsUser::className(),
                'targetAttribute' => ['updated_by' => 'id']
            ],

            [['cms_user_id'], 'default', 'value' => null],
            [['name'], 'default', 'value' => null],
            [['is_default'], 'default', 'value' => null],

            [['isPublic'], 'safe'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'id' => Yii::t('skeeks/admin', 'ID'),
            'created_by' => Yii::t('skeeks/admin', 'Created By'),
            'updated_by' => Yii::t('skeeks/admin', 'Updated By'),
            'created_at' => Yii::t('skeeks/admin', 'Created At'),
            'updated_at' => Yii::t('skeeks/admin', 'Updated At'),
            'cms_user_id' => Yii::t('skeeks/admin', 'Cms User ID'),
            'name' => Yii::t('skeeks/admin', 'Name'),
            'namespace' => Yii::t('skeeks/admin', 'Namespace'),
            'values' => Yii::t('skeeks/admin', 'Values filters'),
            'visibles' => Yii::t('skeeks/admin', 'Visible fields'),
            'is_default' => Yii::t('skeeks/admin', 'Is Default'),
            'isPublic' => \Yii::t('skeeks/admin', 'Available for all'),
        ]);
    }


    protected $_isPublic = null;

    public function getIsPublic()
    {
        return (int)($this->cms_user_id ? 0 : 1);
    }

    public function setIsPublic($value)
    {
        if ($value) {
            $this->cms_user_id = null;
        } else {
            $this->cms_user_id = \Yii::$app->user->id;
        }
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        if (!$this->name) {
            return \Yii::t('skeeks/admin', 'Filter');
        }

        return $this->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCmsUser()
    {
        return $this->hasOne(CmsUser::className(), ['id' => 'cms_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(CmsUser::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(CmsUser::className(), ['id' => 'updated_by']);
    }
}