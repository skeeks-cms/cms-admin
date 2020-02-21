<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 19.05.2015
 */

namespace skeeks\cms\modules\admin\traits;

use skeeks\widget\chosen\Chosen;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveField;

/**
 * @deprecated
 * 
 * Class AdminActiveFormTrait
 * @package skeeks\cms\modules\admin\traits
 */
trait AdminActiveFormTrait
{

    /**
     *
     * Стилизованный селект админки
     *
     * @param $model
     * @param $attribute
     * @param $items
     * @param array $config
     * @param array $fieldOptions
     * @return ActiveField
     */
    public function fieldSelect($model, $attribute, $items, $config = [], $fieldOptions = [])
    {
        $config = ArrayHelper::merge(
            ['allowDeselect' => false],
            $config,
            [
                'items' => $items,
            ]
        );

        foreach ($config as $key => $value) {
            if (property_exists(Chosen::className(), $key) === false) {
                unset($config[$key]);
            }
        }

        return $this->field($model, $attribute, $fieldOptions)->widget(
            Chosen::className(),
            $config
        );
    }
}