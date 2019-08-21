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
 * Class AdminActiveFormTrait
 * @package skeeks\cms\modules\admin\traits
 */
trait AdminActiveFormTrait
{

    /**
     * @param Model $model
     * @return string
     */
    public function buttonsStandart(Model $model, $buttons = ['apply', 'save', 'close'])
    {
        $baseData = [];
        $baseData['indexUrl'] = ((\Yii::$app->controller && isset(\Yii::$app->controller->url)) ? \Yii::$app->controller->url : "");
        if (\Yii::$app->request->referrer) {
            $baseData['indexUrl'] = \Yii::$app->request->referrer;
        }
        $baseData['input-id'] = $this->id . '-submit-btn';

        $baseDataJson = Json::encode($baseData);

        $submit = "";
        if (in_array("save", $buttons)) {
            $submit .= Html::submitButton("<i class=\"fa fa-check\"></i> " . \Yii::t('skeeks/cms', 'Сохранить и закрыть'),  [
                    'title' => 'Результат будет сохранен и окно редактирования будет закрыто',
                    'class' => 'btn btn-success',
                    'onclick' => "return sx.CmsActiveFormButtons.go('save');",
                ]);
        }

        if (in_array("apply", $buttons)) {
            $submit .= ' ' . Html::submitButton("<i class=\"glyphicon glyphicon-ok\"></i> " . \Yii::t('skeeks/cms',
                        'Apply'), [
                    'title' => 'Результат будет сохранен и вы сможете дальше редактировать данные.',
                    'class' => 'btn btn-primary',
                    'onclick' => "return sx.CmsActiveFormButtons.go('apply');",
                ]);
        }

        if (in_array("close", $buttons)) {
            $submit .= ' ' . Html::submitButton("<i class=\"fa fa-times\"></i> " . \Yii::t('skeeks/cms',
                        'Cancel'), [
                    'class' => 'btn btn-danger pull-right',
                    'onclick' => "return sx.CmsActiveFormButtons.go('close');",
                ]);
        }

        $submit .= Html::hiddenInput("submit-btn", 'apply', [
            'id' => $baseData['input-id']
        ]);

        \Yii::$app->view->registerJs(<<<JS
    (function(sx, $, _)
    {
        sx.classes.CmsActiveFormButtons = sx.classes.Component.extend({
            go: function(value)
            {
                if (value == "close")
                {
                    if (sx.Window.openerWidget()) {
                        sx.Window.openerWidget().close();
                        return false;
                    } else
                    {
                        window.location = this.get('indexUrl');
                        return false;
                    }
                    
                    return false;
                } else
                {
                    $("#" + this.get('input-id')).val(value);
                }

                return true;
            }
        });

        sx.CmsActiveFormButtons = new sx.classes.CmsActiveFormButtons({$baseDataJson});
    })(sx, sx.$, sx._);
JS
        );
        return Html::tag('div',
            $submit,
            ['class' => 'form-group sx-buttons-standart']
        );
    }


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