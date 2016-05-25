<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 20.05.2016
 */
namespace skeeks\cms\modules\admin\widgets\filters;

use yii\helpers\Html;
use yii\widgets\ActiveField;

class FilterActiveField extends ActiveField
{
    protected $_visible = false;

    public $options = ['class' => 'form-group sx-default-hidden'];

    public function init()
    {
        parent::init();

        Html::addCssClass($this->options, 'form-group');
    }

    public $template = "<div class='col-sm-2'>{label}</div><div class='col-sm-9'>{input}{hint}\n{error}</div><div class='col-sm-1 pull-right'>
            <a class=\"btn btn-default btn-sm pull-right sx-btn-hide-field\" href=\"#\">
                <i class=\"glyphicon glyphicon-minus\"></i>
            </a>
</div>";

    public function setVisible($value = true)
    {
        if ($value === true)
        {
            Html::removeCssClass($this->options, 'sx-default-hidden');
        } else
        {
            Html::addCssClass($this->options, 'sx-default-hidden');
        }

        $this->_visible = $value;
        return $this;
    }
}