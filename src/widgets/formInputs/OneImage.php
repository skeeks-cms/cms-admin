<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 02.03.2015
 */
namespace skeeks\cms\modules\admin\widgets\formInputs;

use skeeks\cms\backend\helpers\BackendUrlHelper;
use skeeks\cms\backend\widgets\SelectModelDialogStorageFileSrcWidget;
use skeeks\cms\Exception;
use skeeks\cms\models\Publication;
use skeeks\cms\modules\admin\Module;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Application;
use yii\widgets\InputWidget;
use Yii;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class OneImage extends SelectModelDialogStorageFileSrcWidget
{
    public $showPreview = false;
}