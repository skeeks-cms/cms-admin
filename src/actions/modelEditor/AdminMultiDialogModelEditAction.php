<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */
namespace skeeks\cms\modules\admin\actions\modelEditor;

use skeeks\cms\helpers\RequestResponse;
use skeeks\cms\models\Search;
use skeeks\cms\modules\admin\widgets\GridViewStandart;
use skeeks\cms\rbac\CmsManager;
use yii\authclient\AuthAction;
use yii\base\View;
use yii\behaviors\BlameableBehavior;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\web\Application;
use yii\web\ViewAction;
use \skeeks\cms\modules\admin\controllers\AdminController;

/**
 * Class AdminMultiDialogModelEditAction
 * @package skeeks\cms\modules\admin\actions\modelEditor
 */
class AdminMultiDialogModelEditAction extends AdminMultiModelEditAction
{
    public $viewDialog      = "";

    public $dialogOptions   = [
        'style' => 'min-height: 500px; min-width: 600px;'
    ];

    /**
     * @param GridView $grid
     * @return string
     */
    public function registerForGrid($grid)
    {
        $dialogId = $this->getGridActionId($grid);

        $clientOptions = Json::encode(ArrayHelper::merge($this->getClientOptions(), [
            'dialogId' => $dialogId
        ]));

        $grid->view->registerJs(<<<JS
(function(sx, $, _)
{

    sx.createNamespace('sx.classes.grid', sx);

    sx.classes.grid.MultiDialogAction = sx.classes.grid.MultiAction.extend({

        _onDomReady: function()
        {
            var self = this;

            this.jDialog = $( '#' + this.get('dialogId') );
            this.jDialogContent = $( '.modal-content', this.jDialog );

            this.Blocker = new sx.classes.Blocker(this.jDialogContent);

            $('form', this.jDialog).on('submit', function()
            {
                var data = _.extend({
                    'formData' : $(this).serialize()
                }, self.Grid.getDataForRequest());

                self.Blocker.block();

                var ajax = self.createAjaxQuery(data);
                ajax.onComplete(function()
                {
                    self.jDialog.modal('hide');
                    self.Blocker.unblock();
                    /*_.delay(function()
                    {
                        self.jDialog.modal('hide');
                    }, 1000);
*/
                });
                ajax.execute();

                return false;
            });

        },

        _go: function()
        {
            var self = this;
            self.jDialog.modal('show');
        },

    });

    new sx.classes.grid.MultiDialogAction({$grid->gridJsObject}, '{$this->id}' ,{$clientOptions});
})(sx, sx.$, sx._);
JS
);
        $content = '';
        if ($this->viewDialog)
        {
            $content = $this->controller->view->render($this->viewDialog, [
                'action' => $this,
            ]);
        }

        return \Yii::$app->view->render('@skeeks/cms/modules/admin/actions/modelEditor/views/multi-dialog', [
            'dialogId'  => $dialogId,
            'content'   => $content
        ], $this);
    }


    /**
     * @param GridViewStandart $grid
     * @return string
     */
    public function getGridActionId($grid)
    {
        return $grid->id . "-" . $this->id;
    }



}