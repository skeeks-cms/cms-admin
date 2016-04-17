<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 18.02.2016
 */
namespace skeeks\cms\modules\admin\base;

use skeeks\cms\base\ConfigFormInterface;
use skeeks\cms\traits\HasComponentDescriptorTrait;
use skeeks\cms\traits\WidgetTrait;
use yii\base\Model;
use yii\base\ViewContextInterface;
use yii\widgets\ActiveForm;

/**
 * Class AdminDashboardWidget
 * @package skeeks\cms\modules\admin\base
 */
class AdminDashboardWidget extends Model implements ViewContextInterface, ConfigFormInterface
{
    /**
     * @see \yii\base\Widget
     */
    use WidgetTrait;

    //Можно задавать описание компонента.
    use HasComponentDescriptorTrait;

    /**
     *
     * Стоит форму для себя
     *
     * @param ActiveForm|null $form
     */
    public function renderConfigForm(ActiveForm $form)
    {}

    /**
     * @var null Файл в котором будет реднериться виджет
     */
    public $viewFile    = "default";

    public function run()
    {
        if ($this->viewFile)
        {
            echo $this->render($this->viewFile, [
                'widget' => $this
            ]);
        } else
        {
            return \Yii::t('app',"Template not found");
        }
    }
}