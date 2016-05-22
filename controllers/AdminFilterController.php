<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 22.05.2016
 */
namespace skeeks\cms\modules\admin\controllers;
use skeeks\cms\helpers\RequestResponse;
use skeeks\cms\modules\admin\filters\AdminAccessControl;
use skeeks\cms\modules\admin\models\CmsAdminFilter;
use skeeks\cms\modules\admin\widgets\filters\EditFilterForm;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class AuthController
 * @package skeeks\cms\modules\admin\controllers
 */
class AdminFilterController extends AdminController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),
        [
            'verbs' => [
                'class'         => VerbFilter::className(),
                'actions' => [
                   'create'     => ['post'],
                   'validate'   => ['post'],
                ]
            ]
        ]);
    }

    public function actionCreate()
    {
        $rr = new RequestResponse();

        if ($rr->isRequestAjaxPost())
        {
            $model = new EditFilterForm();
            if ($model->load(\Yii::$app->request->post()) && $model->save())
            {
                $rr->success = true;
                $rr->message = \Yii::t('app', 'Album successfully created');
            } else
            {
                $error = 'An error occurred in the time of saving' . Json::encode($model->getFirstErrors());
                \Yii::error($error, 'album');
                $rr->success = false;
                $rr->message = \Yii::t('app', $error);
            }

            return $rr;
        }

        return '1';
    }

    public function actionValidate()
    {
        $rr = new RequestResponse();

        if ($rr->isRequestAjaxPost())
        {
            $model = new EditFilterForm();
            return $rr->ajaxValidateForm($model);
        }

        return '1';
    }

}