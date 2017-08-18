<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 02.03.2015
 */
namespace skeeks\cms\modules\admin\widgets\formInputs;

use skeeks\cms\backend\helpers\BackendUrlHelper;
use skeeks\cms\Exception;
use skeeks\cms\models\CmsContentElement;
use skeeks\cms\models\Publication;
use skeeks\cms\modules\admin\Module;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Application;
use yii\widgets\InputWidget;
use Yii;

/**
 * @property $modelData
 *
 * Class SelectModelDialogInput
 * @package skeeks\cms\modules\admin\widgets\formInputs
 */
class SelectModelDialogInput extends InputWidget
{
    public static $autoIdPrefix = 'SelectModelDialogInput';

    /**
     * @var array
     */
    public $clientOptions = [];

    /**
     * @var string Путь к выбору
     */
    public $selectUrl = '';

    /**
     * @var string
     */
    public $baseRoute;
    public $routeParams = [];

    /**
     * @var boolean whether to show deselect button on single select
     */
    public $allowDeselect = true;

    /**
     * @var bool
     */
    public $closeWindow = true;


    public $viewFile  = 'select-model-dialog-input';


    public function init()
    {
        parent::init();

        if (!$this->selectUrl)
        {
            $additionalData = [$this->baseRoute];

            if ($this->routeParams)
            {
                $additionalData = ArrayHelper::merge($additionalData, $this->routeParams);
            }

            $additionalData = BackendUrlHelper::createByParams($additionalData)
                ->enableEmptyLayout()
                ->setCallbackEventName($this->getCallbackEvent())
                ->params
            ;

            $this->selectUrl = Url::to($additionalData);
        }

    }

    /**
     * @return null|static
     */
    public function getModelData()
    {
        if ($id = $this->model->{$this->attribute})
        {
            return [
                'id' => $id
            ];
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        try
        {
            echo $this->render($this->viewFile, [
                'widget' => $this,
            ]);

        } catch (Exception $e)
        {
            echo $e->getMessage();
        }
    }

    /**
     * @return string
     */
    public function getCallbackEvent()
    {
        return $this->id . '-select-dialog';
    }

    /**
     * @return string
     */
    public function getJsonOptions()
    {
        return Json::encode([
            'id'                        => $this->id,
            'callbackEvent'             => $this->getCallbackEvent(),
            'selectUrl'                 => $this->selectUrl,
            'closeWindow'               => (int) $this->closeWindow,
        ]);

    }
}