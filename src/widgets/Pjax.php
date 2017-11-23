<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 19.12.2016
 */

namespace skeeks\cms\modules\admin\widgets;

/**
 * Class Pjax
 *
 * @package skeeks\cms\modules\admin\widgets
 */
class Pjax extends \skeeks\cms\widgets\Pjax
{
    /**
     * Block container Pjax
     * @var bool
     */
    public $blockPjaxContainer = true;

    public function init()
    {
        $this->isBlock = $this->blockPjaxContainer;
        parent::init();
    }
}