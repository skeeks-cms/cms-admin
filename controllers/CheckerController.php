<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 26.04.2015
 */

namespace skeeks\cms\modules\admin\controllers;
use skeeks\cms\base\CheckComponent;
use skeeks\cms\helpers\RequestResponse;
use skeeks\cms\helpers\UrlHelper;
use skeeks\cms\models\Search;
use skeeks\cms\modules\admin\actions\AdminAction;
use skeeks\cms\modules\admin\controllers\helpers\rules\NoModel;
use skeeks\cms\modules\admin\models\forms\SshConsoleForm;
use skeeks\cms\modules\admin\widgets\ActiveForm;
use skeeks\sx\Dir;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

use Yii;
use yii\web\Response;

/**
 * Class CheckerController
 * @package skeeks\cms\modules\admin\controllers
 */
class CheckerController extends AdminController
{
    public function init()
    {
        $this->name = \Yii::t('app',"Checking system");
        parent::init();
    }

    public function actions()
    {
        return
        [
            "index" =>
            [
                "class"        => AdminAction::className(),
                "name"         => \Yii::t('app',"Testing"),
            ],
        ];
    }

    public function actionCheckTest()
    {
        $rr = new RequestResponse();

        if ($rr->isRequestAjaxPost())
        {
            if (\Yii::$app->request->post('className'))
            {
                $className = \Yii::$app->request->post('className');
                if (!class_exists($className))
                {
                    $rr->message = \Yii::t('app','Test is not found');
                    return (array) $rr;
                }

                if (!is_subclass_of($className, CheckComponent::className()))
                {
                    $rr->message = \Yii::t('app','Incorrect test');
                    return (array) $rr;
                }

                /**
                 * @var $checkTest CheckComponent
                 */
                try
                {
                    $checkTest = new $className();
                    if ($lastValue = \Yii::$app->request->post('lastValue'))
                    {
                        $checkTest->lastValue = $lastValue;
                    }
                    $checkTest->run();

                    $rr->success    = true;
                    $rr->data       = (array) $checkTest;

                } catch (\Exception $e)
                {
                    $rr->message = \Yii::t('app','Test is not done').': ' . $e->getMessage();
                }
            }
        }

        return (array) $rr;
    }




}