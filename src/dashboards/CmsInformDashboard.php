<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 25.05.2015
 */

namespace skeeks\cms\modules\admin\dashboards;

use skeeks\cms\modules\admin\base\AdminDashboardWidget;
use skeeks\cms\modules\admin\base\AdminDashboardWidgetRenderable;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/**
 * Class BaseDashboardWidget
 * @package skeeks\cms\modules\admin\dashboards\base
 */
class CmsInformDashboard extends AdminDashboardWidget
{
    static public function descriptorConfig()
    {
        return array_merge(parent::descriptorConfig(), [
            'name' => \Yii::t('skeeks/cms', 'Short info')
        ]);
    }

    public $viewFile = 'cms-inform';
    public $name;

    public function init()
    {
        parent::init();

        if (!$this->name) {
            $this->name = \Yii::t('skeeks/cms', 'Short info');
        }
    }

    /**
     * @return array
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['name'], 'string'],
        ]);
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'name' => \Yii::t('skeeks/cms', 'Name'),
        ]);
    }

    /**
     * @param ActiveForm $form
     */
    public function renderConfigForm(ActiveForm $form = null)
    {
        echo $form->field($this, 'name');
    }
}