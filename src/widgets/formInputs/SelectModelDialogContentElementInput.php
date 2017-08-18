<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 02.03.2015
 */
namespace skeeks\cms\modules\admin\widgets\formInputs;

use skeeks\cms\Exception;
use skeeks\cms\models\CmsContentElement;
use skeeks\cms\models\CmsUser;
use skeeks\cms\models\Publication;
use skeeks\cms\modules\admin\Module;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Application;
use yii\widgets\InputWidget;
use Yii;

/**
 * @property $modelData
 *
 * Class SelectModelDialogInput
 * @package skeeks\cms\modules\admin\widgets\formInputs
 */
class SelectModelDialogContentElementInput extends SelectModelDialogInput
{
    /**
     * @var string
     */
    public $baseRoute = '/cms/admin-cms-content-element';
    public $content_id = null;

    /**
     * @var string
     */
    public $viewFile  = 'select-model-dialog-content-element-input';

    public function init()
    {
        if (!$this->routeParams)
        {
            $this->routeParams = ['content_id' => $this->content_id];
        } else
        {
            $this->routeParams = ArrayHelper::merge($this->routeParams, ['content_id' => $this->content_id]);
        }

        parent::init();
    }
    /**
     * @return CmsUser
     */
    public function getModelData()
    {
        if ($this->model && $this->model->{$this->attribute})
        {
            $id = $this->model->{$this->attribute};
            return CmsContentElement::findOne($id);
        }
    }
}