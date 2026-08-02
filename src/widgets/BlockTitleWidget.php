<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 02.10.2015
 */

namespace skeeks\cms\modules\admin\widgets;

use yii\base\Widget;
use yii\helpers\Html;

class BlockTitleWidget extends Widget
{
    /**
     * @var array the HTML attributes for the widget container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];
    public $content = "";

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->view->registerCss(<<<CSS
.sx-admin-block-title
{
    background: var(--sx-form-section-header-background, var(--bg-block-header-color, #f5f5f5));
    padding: var(--sx-form-section-title-padding, 8px 15px 10px) !important;
    font-weight: var(--sx-form-label-font-weight, 600);
    text-align: left;
    color: var(--sx-form-section-color, var(--sx-form-label-color, #4b6267));
    border: 1px solid var(--sx-form-section-border-color, #dedede);
    border-radius: var(--sx-form-section-radius, 8px);
    text-shadow: none;
    font-size: var(--sx-form-section-title-font-size, 14px);
    margin-bottom: 5px;
    margin-top: 5px;
}
CSS
        );
        Html::addCssClass($this->options, 'sx-admin-block-title');
        return Html::tag('div', $this->content, $this->options);
    }
}
