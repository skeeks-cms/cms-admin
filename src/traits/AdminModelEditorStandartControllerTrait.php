<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 19.05.2015
 */

namespace skeeks\cms\modules\admin\traits;

use skeeks\cms\components\Cms;

/**
 * @deprecated
 */
trait AdminModelEditorStandartControllerTrait
{
    /**
     * @param $model
     * @param $action
     * @return bool
     */
    public function eachMultiActivate($model, $action)
    {
        try {
            $model->active = Cms::BOOL_Y;
            return $model->save(false);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $model
     * @param $action
     * @return bool
     */
    public function eachMultiInActivate($model, $action)
    {
        try {
            $model->active = Cms::BOOL_N;
            return $model->save(false);
        } catch (\Exception $e) {
            return false;
        }
    }

}