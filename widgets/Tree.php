<?php
/**
 * TODO: Эту хрень нужно всю переписать... Но пока работает кое как. Получилась каша, и много хардкода. Изначально не те цели преследовались.
 *
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 05.03.2015
 */

namespace skeeks\cms\modules\admin\widgets;

use skeeks\cms\helpers\UrlHelper;
use skeeks\cms\models\CmsSite;
use skeeks\cms\modules\admin\components\UrlRule;
use skeeks\cms\modules\admin\controllers\AdminController;
use skeeks\cms\modules\admin\controllers\helpers\Action;
use skeeks\cms\modules\admin\controllers\helpers\ActionManager;
use skeeks\cms\modules\admin\controllers\helpers\ActionModel;
use skeeks\cms\modules\admin\widgets\tree\Asset;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\jui\Draggable;
use yii\jui\Sortable;
use yii\web\JsExpression;

/**
 * Class ControllerActions
 * @package skeeks\cms\modules\admin\widgets
 */
class Tree extends Widget
{
    public static $autoIdPrefix = 'cmsTreeWidget';

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var array ноды для которых строить дерево.
     */
    public $models      = [];

    public $openedRequestName           = "o";
    public $mode                        = "mode";

    public $viewFile                    = 'tree-widget';
    public $viewItemFile                = 'tree-item-widget';

    public $pjax                        = null;

    protected $_openedTmp = [];

    public function init()
    {
        parent::init();

        $this->options['id'] = $this->id;
        Html::addCssClass($this->options, 'sx-tree');

        $this->pjax = \skeeks\cms\modules\admin\widgets\Pjax::begin([
            'id' => 'sx-pjax-' . $this->id,
            //'enablePushState' => false,
            'blockPjaxContainer' => false,
            'blockContainer' => '.sx-panel',
        ]);
    }

    /**
     * @return array
     */
    protected function _getOpenIds()
    {
        if ($fromRequest = (array) \Yii::$app->request->getQueryParam($this->openedRequestName))
        {
            $opened = array_unique($fromRequest);
        } else
        {
            $opened = array_unique(\Yii::$app->getSession()->get('cms-tree-opened', []));
            if ($opened)
            {
                \Yii::$app->response->redirect(UrlHelper::construct('cms/admin-tree/index', array_merge(\Yii::$app->request->getQueryParams(), [$this->openedRequestName => $opened]))->enableAdmin());
            }
        }

        return $opened;
    }

    /**
     * @return string
     */
    protected function _getMode()
    {
        if ($mode = \Yii::$app->request->getQueryParam($this->mode))
        {
            return (string) $mode;
        }

        return '';
    }



    /**
     * @return string
     */
    public function run()
    {


            $openedModels = [];

            if (\Yii::$app->request->getQueryParam('setting-open-all'))
            {
                \skeeks\cms\models\Tree::find()->where([]);
                return \Yii::$app->response->redirect(UrlHelper::construct("cms/admin-tree/index"));
            }

            if ($opened = $this->_getOpenIds())
            {
                \Yii::$app->getSession()->set('cms-tree-opened', $opened);
                $openedModels = \skeeks\cms\models\Tree::find()->where(["id" => $opened])->all();
            }

            $this->_openedTmp = $openedModels;
            $this->registerAssets();

            echo $this->render($this->viewFile);

        \skeeks\cms\modules\admin\widgets\Pjax::end();
    }

    /**
     * @param $models
     * @return string
     */
    public function renderNodes($models)
    {
        $options["item"] = [$this, 'renderItem'];
        $ul = Html::ul($models, $options);

        return $ul;
    }

    public function renderItem($model)
    {
        if ($this->_getMode() == 'multi')
        {
            $controllElement = Html::checkbox('tree_id', $isSelected, [
                'value'     => $model->id,
                'class'     => 'sx-checkbox',
                'style'     => 'float: left; margin-left: 5px; margin-right: 5px;',
                'onclick'   => new JsExpression(<<<JS
    sx.Tree.select("{$model->id}");
JS
)
            ]);


        } else if ($this->_getMode() == 'single')
        {

            $controllElement = Html::radio('tree_id', $isSelected, [
                'value'     => $model->id,
                'class'     => 'sx-readio',
                'style'     => 'float: left; margin-left: 5px; margin-right: 5px;',
                'onclick'   => new JsExpression(<<<JS
    sx.Tree.selectSingle("{$model->id}");
JS
)
            ]);

        }  else if ($this->_getMode() == 'combo')
        {

            $controllElement = Html::radio('tree_id', false, [
                                'value'     => $model->id,
                                'class'     => 'sx-readio',
                                'style'     => 'float: left; margin-left: 5px; margin-right: 5px;',
                                'onclick'   => new JsExpression(<<<JS
                    sx.Tree.selectSingle("{$model->id}");
JS
            )
                ]);

            $controllElement .= Html::checkbox('tree_id', $isSelected, [
                'value'     => $model->id,
                'class'     => 'sx-checkbox',
                'style'     => 'float: left; margin-left: 5px; margin-right: 5px;',
                'onclick'   => new JsExpression(<<<JS
    sx.Tree.select("{$model->id}");
JS
)
            ]);

        } else
        {
            $controllElement = '';
        }


        return $this->render($this->viewItemFile, [
            'controllElement' => $controllElement,
            'model' => $model,
        ]);
    }


    /**
     * @param $model
     * @return $this|string
     */
    public function getLink($model)
    {
        $currentLink = "";

        if ($model->children)
        {
            $openedIds = $this->_getOpenIds();

            if ($this->isOpenNode($model))
            {
                $newOptionsOpen = [];
                foreach ($openedIds as $id)
                {
                    if ($id != $model->id)
                    {
                        $newOptionsOpen[] = $id;
                    }
                }

                $urlOptionsOpen = array_unique($newOptionsOpen);
                $params = \Yii::$app->request->getQueryParams();
                $pathInfo = \Yii::$app->request->pathInfo;
                $params[$this->openedRequestName] = $urlOptionsOpen;

                $currentLink = "/{$pathInfo}?" . http_build_query($params);
            } else
            {
                $urlOptionsOpen = array_unique(array_merge($openedIds, [$model->id]));
                $params = \Yii::$app->request->getQueryParams();
                $params[$this->openedRequestName] = $urlOptionsOpen;
                $pathInfo = \Yii::$app->request->pathInfo;

                $currentLink = "/{$pathInfo}?" . http_build_query($params);
            }
        }

        return $currentLink;
    }

    /**
     * Нода для этой модели открыта?
     *
     * @param $model
     * @return bool
     */
    public function isOpenNode($model)
    {
        $isOpen = false;

        foreach ($this->_openedTmp as $activeNode)
        {
            if ($activeNode->id == $model->id)
            {
                $isOpen = true;
                break;
            }
        }

        return $isOpen;
    }

    /**
     *
     *
     * @param $model
     * @return string
     */
    public function getNodeName($model)
    {
        /**
         * @var $model \skeeks\cms\models\Tree
         */

        $result = $model->name;

        $additionalName = '';
        if ($model->level == 0)
        {
            $site = CmsSite::findOne(['code' => $model->site_code]);
            if ($site)
            {
                $additionalName = $site->name;
            }
        } else
        {
            if ($model->name_hidden)
            {
                $additionalName = $model->name_hidden;
            }
        }

        if ($additionalName)
        {
            $result .= " [{$additionalName}]";
        }

        return $result;
    }


    public function registerAssets()
    {
        Sortable::widget();

        $options    = Json::encode([
            'id' => $this->id,
            'pjaxid' => $this->pjax->id
        ]);

        Asset::register($this->getView());
        $this->getView()->registerJs(<<<JS

        (function(window, sx, $, _)
        {
            sx.createNamespace('classes', sx);

            sx.classes.Tree = sx.classes.Component.extend({

                _init: function()
                {
                    var self = this;
                },

                _onDomReady: function()
                {
                    $('.sx-tree-node').on('dblclick', function(event)
                    {
                        event.stopPropagation();
                        $(this).find(".sx-row-action:first").click();
                    });

                    $(".sx-tree ul").find("ul").sortable(
                    {
                        cursor: "move",
                        handle: ".sx-tree-move",
                        forceHelperSize: true,
                        forcePlaceholderSize: true,
                        opacity: 0.5,
                        placeholder: "ui-state-highlight",

                        out: function( event, ui )
                        {
                            var Jul = $(ui.item).closest("ul");
                            var newSort = [];
                            Jul.children("li").each(function(i, element)
                            {
                                newSort.push($(this).data("id"));
                            });

                            var blocker = sx.block(Jul);

                            var ajax = sx.ajax.preparePostQuery(
                                "resort",
                                {
                                    "ids" : newSort,
                                    "changeId" : $(ui.item).data("id")
                                }
                            );

                            new sx.classes.AjaxHandlerNoLoader(ajax); //отключение глобального загрузчика
                            new sx.classes.AjaxHandlerNotify(ajax, {
                                'error': "Изменения не сохранились",
                                'success': "Изменения сохранены",
                            }); //отключение глобального загрузчика

                            ajax.onError(function(e, data)
                            {
                                sx.notify.info("Подождите сейчас страница будет перезагружена");
                                _.delay(function()
                                {
                                    window.location.reload();
                                }, 2000);
                            })
                            .onSuccess(function(e, data)
                            {
                                blocker.unblock();
                            })
                            .execute();
                        }
                    });

                    var self = this;

                    $('.add-tree-child').on('click', function()
                    {
                        var jNode = $(this);
                        sx.prompt("Введите название нового раздела", {
                            'yes' : function(e, result)
                            {
                                var blocker = sx.block(jNode);

                                var ajax = sx.ajax.preparePostQuery(
                                        "new-children",
                                        {
                                            "pid" : jNode.data('id'),
                                            "Tree" : {"name" : result},
                                            "no_redirect": true
                                        }
                                );

                                new sx.classes.AjaxHandlerNoLoader(ajax); //отключение глобального загрузчика

                                new sx.classes.AjaxHandlerNotify(ajax, {
                                    'error': "Не удалось добавить новый раздел",
                                    'success': "Новый раздел добавлен"
                                }); //отключение глобального загрузчика

                                ajax.onError(function(e, data)
                                {
                                    $.pjax.reload('#' + self.get('pjaxid'), {});
                                    /*sx.notify.info("Подождите сейчас страница будет перезагружена");
                                    _.delay(function()
                                    {
                                        window.location.reload();
                                    }, 2000);*/
                                })
                                .onSuccess(function(e, data)
                                {
                                    blocker.unblock();

                                    $.pjax.reload('#' + self.get('pjaxid'), {});
                                    /*sx.notify.info("Подождите сейчас страница будет перезагружена");
                                    _.delay(function()
                                    {
                                        window.location.reload();
                                    }, 2000);*/
                                })
                                .execute();
                            }
                        });

                        return false;
                    });

                    $('.show-at-site').on('click', function()
                    {
                        window.open($(this).attr('href'));

                        return false;
                    });
                },

                select: function(id)
                {
                    var selected = [];
                    $("input[type='checkbox']:checked").each(function()
                    {
                        selected.push($(this).val());
                    });

                    this.trigger("select", {
                        'selected': selected,
                        'select': id
                    });
                },

                selectSingle: function(id)
                {
                    this.trigger("selectSingle", {
                        'id': id
                    });
                },

                setSingle: function(id)
                {
                    var Jelement = $(".sx-tree .sx-readio[value='" + id + "']");
                    if (!Jelement.is(":checked"))
                    {
                        Jelement.click();
                    };
                },

                setSelect: function(ids)
                {
                    if (ids)
                    {
                        _.each(ids, function(id)
                        {
                            var Jelement = $(".sx-tree .sx-checkbox[value='" + id + "']");
                            if (!Jelement.is(":checked"))
                            {
                                Jelement.click();
                            };
                        });
                    }
                },
            });

            sx.Tree = new sx.classes.Tree({$options});

        })(window, sx, sx.$, sx._);
JS
    );

        $this->getView()->registerCss(<<<CSS


CSS
);
    }
}