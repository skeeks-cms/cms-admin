<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 16.03.2017
 */
namespace skeeks\cms\modules\admin\actions;
use skeeks\cms\backend\ViewBackendAction;

/**
 * Class AdminAction
 *
 * @package skeeks\cms\modules\admin\actions
 */
class AdminAction extends ViewBackendAction
{
    public $visible = true;

    public function init()
    {
        parent::init();
    }
}