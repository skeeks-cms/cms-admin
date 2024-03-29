<?php
/**
 * GridView
 *
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010-2014 SkeekS (Sx)
 * @date 18.02.2015
 * @since 1.0.0
 */

namespace skeeks\cms\modules\admin\widgets;

use skeeks\cms\backend\helpers\BackendUrlHelper;
use skeeks\cms\components\Cms;
use skeeks\cms\grid\ImageColumn;
use skeeks\cms\modules\admin\grid\ActionColumn;
use skeeks\cms\modules\admin\widgets\gridView\GridViewSettings;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQueryInterface;
use yii\grid\CheckboxColumn;
use yii\grid\DataColumn;
use yii\grid\SerialColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\web\JsExpression;

/**
 * Расширенный грид, с настройками.
 *
 * @property GridViewSettings $settings
 * @depricated
 *
 * Class GridView
 * @package skeeks\cms\modules\admin\widgets
 */
class GridViewHasSettings extends GridView
{
    /**
     * @var array
     */
    public $settingsData = [];

    /**
     * @var bool Включение автоматического добавления колонок таблицы
     */
    public $autoColumns = true;

    /**
     * @var array исходные настройки колонок с сгенерированными ключами.
     */
    protected $_columns = [];

    /**
     * @var array Исходные нетронутые columns переданные в виджет
     */
    protected $_sourceColumns = [];

    public function init()
    {
        $this->_initGridSettings();
        $this->_applyDataProvider();

        $this->_initAutoColumns();
        $this->_configureColumns();

        
        if ($callbackEventName = BackendUrlHelper::createByParams()->setBackendParamsByCurrentRequest()->callbackEventName) {
            $this->view->registerJs(<<<JS
(function(sx, $, _)
{
    sx.classes.SelectCmsElement = sx.classes.Component.extend({

        _onDomReady: function()
        {
            $('table tr').on('dblclick', function()
            {
                $(".sx-row-action", $(this)).click();
            });
        },

        submit: function(data)
        {
            sx.Window.openerWidgetTriggerEvent('{$callbackEventName}', data);
            /*
            if (window.opener)
            {
                if (window.opener.sx)
                {
                    window.opener.sx.EventManager.trigger('{$callbackEventName}', data);
                    return this;
                }
            } else if (window.parent)
            {
                if (window.parent.sx)
                {
                    window.parent.sx.EventManager.trigger('{$callbackEventName}', data);
                    return this;
                }
            }*/

            return this;
        }
    });

    sx.SelectCmsElement = new sx.classes.SelectCmsElement();

})(sx, sx.$, sx._);
JS
            );
                    ;

                    
            $this->columns = ArrayHelper::merge([

                'sx-choose' => $this->getChooseColumn(),

            ], $this->columns);
            
            if ($this->settings->visibleColumns) {
                $this->settings->visibleColumns = ArrayHelper::merge($this->settings->visibleColumns, ['sx-choose']);
            }

        }

        parent::init();

        $this->_applyGridSettings();

    }

    protected $_chooseColumn = null;

    /**
     * @var null
     */
    public $chooseCallback = null;

    public function setChooseColumn($column)
    {
        $this->_chooseColumn = $column;
        return $this;
    }

    public function getChooseColumn()
    {
        if ($this->_chooseColumn === null) {
            $this->_chooseColumn = [
                'class' => \yii\grid\DataColumn::className(),
                'value' => function ($model) {
                    $data = $model->toArray();

                    if ($this->chooseCallback && is_callable($this->chooseCallback)) {
                        $callback = $this->chooseCallback;
                        $data = $callback($model);
                    }

                    return \yii\helpers\Html::a('<i class="fas fa-chevron-circle-left"></i> ' . \Yii::t('skeeks/cms',
                            'Choose'), '#', [
                        'class' => 'btn btn-primary sx-row-action',
                        'onclick' => 'sx.SelectCmsElement.submit(' . \yii\helpers\Json::encode($data) . '); return false;',
                        'data-pjax' => 0
                    ]);
                },
                'label' => '',
                'format' => 'raw'
            ];
        }
        return $this->_chooseColumn;
    }

    /**
     * @return $this
     */
    protected function _initAutoColumns()
    {
        if (!$this->autoColumns) {
            return $this;
        }

        $autoColumns = [];
        $models = $this->dataProvider->getModels();
        $model = reset($models);

        if (is_array($model) || is_object($model)) {
            foreach ($model as $name => $value) {
                $autoColumns[] = [
                    'attribute' => $name,
                    'visible' => false,
                    'format' => 'raw',
                    'class' => \yii\grid\DataColumn::className(),
                    'value' => function ($model, $key, $index) use ($name) {
                        if (is_array($model->{$name})) {
                            return implode(",", $model->{$name});
                        } else {
                            return $model->{$name};
                        }
                    },
                ];
            }
        }

        if ($autoColumns) {
            $this->columns = ArrayHelper::merge($this->columns, $autoColumns);
        }

        return $this;
    }

    /**
     * @var GridViewSettings
     */
    protected $_settings;

    /**
     * @return GridViewSettings
     */
    public function getSettings()
    {
        return $this->_settings;
    }


    /**
     * @return string
     */
    public function renderBeforeTable()
    {
        $this->beforeTableRight = $this->beforeTableRight . $this->renderSettings();
        return parent::renderBeforeTable();
    }


    /**
     * @return string
     */
    public function renderSettings()
    {
        $id = $this->id . "-callable-data";

        $gridEditSettings = [
            'url' => (string)$this->settings->getCallableEditUrl(),
            'enabledPjax' => $this->enabledPjax,
            'pjax' => $this->pjax
        ];

        $gridEditSettings = Json::encode($gridEditSettings);
        $callableData = $this->settings->callableData;

        $callableDataInput = Html::textarea('callableData', base64_encode(serialize($callableData)), [
            'id' => $this->settings->callableId,
            'style' => 'display: none;'
        ]);

        return '<div class="sx-grid-settings">' . Html::a('<i class="fa fa-cog"></i>',
                $this->settings->getCallableEditUrl(), [
                    'class' => 'btn btn-default btn-sm',
                    'onclick' => new JsExpression(<<<JS
            new sx.classes.GridEditSettings({$gridEditSettings}); return false;
JS
                    )
                ]) . $callableDataInput . "</div>";

    }


    public function registerAsset()
    {
        parent::registerAsset();

        $this->view->registerJs(<<<JS
        (function(sx, $, _)
        {
            sx.classes.GridEditSettings = sx.classes.Component.extend({

                _init: function()
                {
                    var self = this;

                    this.Window = new sx.classes.Window(this.get('url'));

                    this.Window.bind('close', function(e, data)
                    {
                        self.reload();
                    });

                    this.Window.open();
                },

                reload: function()
                {
                    if (this.get('enabledPjax'))
                    {
                        var id = null;
                        var pjax = this.get('pjax');
                        if (pjax.options)
                        {
                            id = pjax.options.id;
                        }

                        if (id)
                        {
                            $.pjax.reload('#' + id, {});
                            return this;
                        }

                    }

                    window.location.reload();
                    return this;
                },

                _onDomReady: function()
                {},

                _onWindowReady: function()
                {}
            });
        })(sx, sx.$, sx._);
JS
        );
    }

    /**
     * Инициализация объекта настроек.
     * @return $this
     */
    protected function _initGridSettings()
    {
        $defaultSettingsData =
            [
                //namespace настроек по умолчанию.
                'namespace' => \Yii::$app->controller->action->getUniqueId(),
                'grid' => $this
            ];

        $settingsData = ArrayHelper::merge($defaultSettingsData, (array)$this->settingsData);

        $this->_settings = new GridViewSettings($settingsData);

        return $this;
    }


    /**
     * @return $this
     */
    protected function _applyGridSettings()
    {
        //Pjax init
        if ($this->settings->enabledPjaxPagination == Cms::BOOL_Y) {
            $this->enabledPjax = true;
        } else {
            $this->enabledPjax = false;
        }

        //Применение data provider-a
        //$this->_applyDataProvider();
        $this->_applyColumns();

        return $this;
    }

    public $allColumns = [];

    /**
     * @return $this
     */
    protected function _applyColumns()
    {
        if ($this->settings->visibleColumns) {
            $newColumns = [];
            $hiddenColumns = [];

            foreach ($this->settings->visibleColumns as $code) {
                if ($column = ArrayHelper::getValue($this->allColumns, $code)) {
                    $newColumns[$code] = $column;
                }
            }

            if ($newColumns) {
                $this->columns = $newColumns;
            }
        }

        return $this;
    }


    /**
     * Creates column objects and initializes them.
     */
    protected function initColumns()
    {
        if (empty($this->columns)) {
            $this->guessColumns();
        }

        foreach ($this->columns as $i => $column) {
            if (is_string($column)) {
                $column = $this->createDataColumn($column);
            } else {
                $column = Yii::createObject(array_merge([
                    'class' => $this->dataColumnClass ?: DataColumn::className(),
                    'grid' => $this,
                ], $column));
            }
            if (!$column->visible) {
                unset($this->columns[$i]);
                $this->allColumns[$i] = $column;
                continue;
            }
            $this->columns[$i] = $column;
            $this->allColumns[$i] = $column;
        }
    }


    protected function _applyDataProvider()
    {
        $this->dataProvider;

        $this->dataProvider->getPagination()->defaultPageSize = (int)$this->settings->pageSize;
        $this->dataProvider->getPagination()->pageParam = $this->settings->pageParamName;
        $this->dataProvider->getPagination()->pageSizeLimit = [
            $this->settings->pageSizeLimitMin,
            $this->settings->pageSizeLimitMax
        ];

        if ($this->settings->orderBy) {
            $this->dataProvider->getSort()->defaultOrder =
                [
                    $this->settings->orderBy => (int)$this->settings->order
                ];
        }

        return $this;
    }


    /**
     * Reconfigure columns with unique keys
     *
     * @return void
     */
    protected function _configureColumns()
    {
        if (empty($this->columns)) {
            $this->guessColumns();
        }
        $this->_sourceColumns = $this->columns;;

        $columnsByKey = [];
        foreach ($this->columns as $column) {
            $columnKey = $this->_getColumnKey($column);
            for ($j = 0; true; $j++) {
                $suffix = ($j) ? '_' . $j : '';
                $columnKey .= $suffix;
                if (!array_key_exists($columnKey, $columnsByKey)) {
                    break;
                }
            }
            $columnsByKey[$columnKey] = $column;
        }

        $this->columns = $columnsByKey;
    }

    /**
     * Generate an unique column key
     *
     * @param mixed $column
     *
     * @return mixed
     */
    protected function _getColumnKey($column)
    {
        if (!is_array($column)) {
            $matches = $this->_matchColumnString($column);
            $columnKey = $matches[1];
        } elseif (!empty($column['attribute'])) {
            $columnKey = $column['attribute'];
        } elseif (!empty($column['label'])) {
            $columnKey = $column['label'];
        } elseif (!empty($column['header'])) {
            $columnKey = $column['header'];
        } elseif (!empty($column['class'])) {
            $columnKey = $column['class'];
        } else {
            $columnKey = "";
        }
        return hash('crc32', $columnKey);
    }

    /**
     * Finds the matches for a string column format
     *
     * @param string $column
     *
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    protected function _matchColumnString($column)
    {
        $matches = [];
        if (!preg_match('/^([\w\.]+)(:(\w*))?(:(.*))?$/', $column, $matches)) {
            throw new InvalidConfigException(\Yii::t('skeeks/cms',
                "Invalid column configuration for '{column}'. The column must be specified in the format of 'attribute', 'attribute:format' or 'attribute:format: label'.",
                ['column' => $column]));
        }
        return $matches;
    }


    public function getColumnsKeyLabels()
    {
        $data = [];

        foreach ($this->allColumns as $code => $column) {
            if ($column instanceof ActionColumn) {
                $data[$code] = \Yii::t('skeeks/cms', 'Button actions');
            } else {
                if ($column instanceof CheckboxColumn) {
                    $data[$code] = \Yii::t('skeeks/cms', 'Selecting items');
                } else {
                    if ($column instanceof SerialColumn) {
                        $data[$code] = \Yii::t('skeeks/cms', 'Sequence number');
                    } else {
                        if ($column instanceof ImageColumn) {
                            $data[$code] = \Yii::t('skeeks/cms', 'Main Image');
                        } else {
                            if ($column instanceof DataColumn) {
                                if ($column->label === null) {
                                    $provider = $this->dataProvider;

                                    if ($provider instanceof ActiveDataProvider && $provider->query instanceof ActiveQueryInterface) {
                                        /* @var $model Model */
                                        $model = new $provider->query->modelClass;
                                        $label = $model->getAttributeLabel((string) $column->attribute);
                                    } else {
                                        $models = $provider->getModels();
                                        if (($model = reset($models)) instanceof Model) {
                                            /* @var $model Model */
                                            $label = $model->getAttributeLabel($column->attribute);
                                        } else {
                                            $label = Inflector::camel2words($column->attribute);
                                        }
                                    }
                                } else {
                                    $label = $column->label;
                                }

                                $data[$code] = $label;

                            } else {
                                $data[$code] = $code;
                            }
                        }
                    }
                }
            }

            if (!$data[$code]) {
                $data[$code] = " — " . $column->className();
            }
        }

        return $data;
    }

}