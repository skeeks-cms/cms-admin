<?php
/**
 * ActiveForm
 *
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010-2014 SkeekS (Sx)
 * @date 11.11.2014
 * @since 1.0.0
 */

namespace skeeks\cms\modules\admin\widgets;

use skeeks\cms\admin\assets\AdminFormAsset;
use skeeks\cms\backend\forms\TActiveFormHasButtons;
use skeeks\cms\forms\TActiveFormDynamicReload;
use skeeks\cms\modules\admin\traits\ActiveFormTrait;
use skeeks\cms\modules\admin\traits\AdminActiveFormTrait;
use skeeks\cms\traits\ActiveFormAjaxSubmitTrait;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * @depricated
 *
 * Class ActiveForm
 * @package skeeks\cms\modules\admin\widgets
 */
class ActiveForm extends \skeeks\cms\base\widgets\ActiveForm
{
    use AdminActiveFormTrait;
    use ActiveFormAjaxSubmitTrait;
    use TActiveFormDynamicReload;

    use TActiveFormHasButtons;

    /**
     * @var bool
     */
    public $usePjax = true;

    /**
     * @var boolAdtiveFormHasFieldSetsTrait
     */
    public $useAjaxSubmit = false;
    public $afterValidateCallback = "";

    public $validateOnChange = true;
    public $validateOnBlur = true;

    /**
     * @var array
     */
    public $pjaxOptions = [];

    /**
     * Initializes the widget.
     * This renders the form open tag.
     */
    public function init()
    {
        if ($classes = ArrayHelper::getValue($this->options, 'class')) {
            $this->options = ArrayHelper::merge($this->options, [
                'class' => $classes.' sx-form-admin',
            ]);
        } else {
            $this->options = ArrayHelper::merge($this->options, [
                'class' => 'sx-form-admin',
            ]);
        }

        if ($this->usePjax) {
            Pjax::begin(ArrayHelper::merge([
                'id'              => 'sx-pjax-form-'.$this->id,
                'enablePushState' => false,
            ], $this->pjaxOptions));

            $this->options = ArrayHelper::merge($this->options, [
                'data-pjax' => true,
            ]);

            echo \skeeks\cms\modules\admin\widgets\Alert::widget();
        }
        
        $this->_initDynamicReload();

        parent::init();
    }

    public function run()
    {
        $formHtml = parent::run();

        $clientOptions = Json::encode([
            'id'        => $this->id,
            'msg_title' => \Yii::t('skeeks/admin', 'This field is required'),
        ]);


        AdminFormAsset::register($this->view);

        $this->view->registerJs(<<<JS
(function(sx, $, _)
{
    /*new sx.classes.forms.AdminForm({$clientOptions});*/
})(sx, sx.$, sx._);
JS
        );

        if ($this->useAjaxSubmit) {
            $this->registerJs();
        }

        echo $formHtml;

        if ($this->usePjax) {
            Pjax::end();
        }
    }


    /**
     * TODO: is depricated (1.2) use buttonsStandart
     * @param Model $model
     * @return string
     */
    public function buttonsCreateOrUpdate(Model $model)
    {
        return $this->buttonsStandart($model);
    }

    public function fieldSet($name, $options = [])
    {
        return <<<HTML
        <div class="sx-form-fieldset">
            <h3 class="sx-form-fieldset-title">{$name}</h3>
            <div class="sx-form-fieldset-content">
HTML;

    }

    public function fieldSetEnd()
    {
        return <<<HTML
            </div>
        </div>
HTML;

    }


}