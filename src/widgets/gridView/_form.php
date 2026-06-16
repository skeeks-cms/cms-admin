<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 27.05.2015
 */
/* @var $this yii\web\View */
/* @var $controller \skeeks\cms\controllers\AdminCmsSiteController */
$controller = \Yii::$app->controller;
?>

<? $columns = \yii\helpers\ArrayHelper::getValue($controller->callableData, 'columns'); ?>
<? $selectedColumns = \yii\helpers\ArrayHelper::getValue($controller->callableData, 'selectedColumns'); ?>
<? $columnsUrl = \yii\helpers\ArrayHelper::getValue($controller->callableData, 'columnsUrl'); ?>

<? if ($columns || $columnsUrl) : ?>
    <?= $form->fieldSet(\Yii::t('skeeks/admin', 'Table fields')); ?>

    <? /*
                echo $form->field($model, 'visibleColumns')->widget(\skeeks\widget\duallistbox\WidgetDualListbox::className(),[
                    'items' => $columns,
                    'options' => [
                        'size' => 40,
                        'multiple' => true
                    ],
                    'clientOptions' => [
                        'moveOnSelect' => false,
                        'selectedListLabel' => 'Selected Items',
                        'nonSelectedListLabel' => 'Available Items',
                    ],
                ]);
            */ ?>

    <div class="row">
        <div class="col-lg-6">

            <label><?= \Yii::t('skeeks/admin', 'Available fields') ?></label>
            <p><?= \Yii::t('skeeks/admin', 'Double-click for item, turn it on') ?></p>
            <hr/>
            <?= \yii\helpers\Html::listBox('possibleColumns', [], $columns, [
                'size'  => "20",
                'class' => "form-control",
                'id'    => "sx-possibleColumns",
            ]); ?>

        </div>
        <div class="col-lg-6">
            <label><?= \Yii::t('skeeks/admin', 'Included fields') ?></label>
            <p><?= \Yii::t('skeeks/admin', 'Double-click for item, turn it off. You can also change the order of items by dragging them.') ?></p>
            <hr/>
            <ul id="sx-visible-selected">

            </ul>
            <div style="display: none;">
                <?= $form->field($model, 'visibleColumns')->listBox($columns, [
                    'size'     => "20",
                    'multiple' => 'multiple',
                ]); ?>
            </div>
        </div>
    </div>

    <?= $form->fieldSetEnd(); ?>
<? endif; ?>

<?= $form->fieldSet(\Yii::t('skeeks/cms', 'Pagination')); ?>
<?= $form->field($model, 'enabledPjaxPagination')->checkbox([
    'uncheck' => \skeeks\cms\components\Cms::BOOL_N,
    'value'   => \skeeks\cms\components\Cms::BOOL_Y,
]); ?>
<?= $form->field($model, 'pageSize'); ?>
<?= $form->field($model, 'pageSizeLimitMin'); ?>
<?= $form->field($model, 'pageSizeLimitMax'); ?>
<?= $form->field($model, 'pageParamName')->textInput(); ?>
<?= $form->fieldSetEnd(); ?>

<?= $form->fieldSet(\Yii::t('skeeks/cms', 'Priority')); ?>
<?= $form->fieldSelect($model, 'orderBy', (new \skeeks\cms\models\CmsContentElement())->attributeLabels()); ?>
<?= $form->fieldSelect($model, 'order', [
    SORT_ASC  => "ASC (".\Yii::t('skeeks/cms', 'from smaller to larger').")",
    SORT_DESC => "DESC (".\Yii::t('skeeks/cms', 'from highest to lowest').")",
]); ?>
<?= $form->fieldSetEnd(); ?>



<?

$this->registerCss(<<<CSS
#sx-visible-selected li
{
    list-style: none;
    margin: 3px;
    padding: 5px;
    border: 1px solid silver;
    cursor: move;
}
CSS
);


$options = [
    'id'              => \yii\helpers\Html::getInputId($model, 'visibleColumns'),
    'selectedColumns' => $model->visibleColumns ? $model->visibleColumns : $selectedColumns,
    'hasColumns'      => $model->visibleColumns,
    'columnsUrl'       => $columnsUrl,
];
$optionsString = \yii\helpers\Json::encode($options);

\yii\jui\Sortable::widget();

$this->registerJs(<<<JS
(function(sx, $, _)
{
    sx.classes.Columns = sx.classes.Component.extend({

        _init: function()
        {},

        _onDomReady: function()
        {
            var self = this;

            this.JQueryVisibleSelected      = $('#sx-visible-selected');
            this.JQuerySelect               = $('#' + this.get('id'));
            this.JQueryPossibleColumns      = $('#sx-possibleColumns');

            this.JQueryVisibleSelected.sortable({
                out: function( event, ui )
                {
                    self.updateHiddenSelect();
                }
            });

            $("option", this.JQueryPossibleColumns).on('dblclick', function()
            {
                self.appendToVisible($(this));
            });

            /*if (_.size(this.get('hasColumns')))
            {
                this.updateVisible();
            } else
            {
                this.initVisible();
            }*/

            this.initVisible();
            this.loadColumns();

        },

        loadColumns: function()
        {
            var self = this;
            var columnsUrl = this.get('columnsUrl');

            if (!columnsUrl)
            {
                return this;
            }

            $.getJSON(columnsUrl)
                .done(function(response)
                {
                    var columns = response.items || response.columns || {};

                    _.each(columns, function(label, value)
                    {
                        var exists = false;
                        $('option', self.JQueryPossibleColumns).each(function()
                        {
                            if (String($(this).attr('value')) === String(value))
                            {
                                $(this).text(label);
                                exists = true;
                            }
                        });

                        if (!exists)
                        {
                            $("<option>", {
                                'value': value
                            }).text(label)
                            .on('dblclick', function()
                            {
                                self.appendToVisible($(this));
                            })
                            .appendTo(self.JQueryPossibleColumns);
                        }

                        $('option', self.JQuerySelect).each(function()
                        {
                            if (String($(this).attr('value')) === String(value))
                            {
                                $(this).text(label);
                            }
                        });

                        $('li', self.JQueryVisibleSelected).each(function()
                        {
                            if (String($(this).data('value')) === String(value))
                            {
                                $(this).text(label);
                            }
                        });
                    });

                    self.updateHiddenSelect();
                });

            return this;
        },


        appendToVisible: function(JQuerySelect)
        {
            var self = this;
            this.JQueryVisibleSelected.append(
                $("<li>", {
                    'data-value': JQuerySelect.attr("value")
                }).text(JQuerySelect.text())
                .on('dblclick', function()
                {
                    $(this).remove();
                    self.updateHiddenSelect();
                })
            );

            this.updateHiddenSelect();
        },

        /**
        * Обновление скрытого элемента
        */
        updateHiddenSelect: function()
        {
            var self = this;

            this.JQuerySelect.empty();

            $('li', this.JQueryVisibleSelected).each(function()
            {
                $("<option>", {
                    'value': $(this).data("value"),
                    'selected': 'selected'
                }).text($(this).text())
                .appendTo(self.JQuerySelect);
            });
        },

        updateVisible: function()
        {
            var self = this;

            this.JQueryVisibleSelected.empty();

            console.log(this.JQueryVisibleSelected);

            $('option', this.JQuerySelect).each(function()
            {
                if ($(this).is(":selected"))
                {
                    $("<li>", {'data-value': $(this).attr("value") }).text($(this).text())
                    .on('dblclick', function()
                    {
                        $(this).remove();
                        self.updateHiddenSelect();
                    })
                    .appendTo(self.JQueryVisibleSelected);
                }
            });
        },

        initVisible: function()
        {
            var self = this;

            this.JQueryVisibleSelected.empty();

            _.each(this.get('selectedColumns'), function(value, key)
            {
                if (value)
                {
                    $("<li>", {
                        'data-value': value
                    }).text( $('option', self.JQuerySelect).filter(function() {
                        return String($(this).attr('value')) === String(value);
                    }).text() )
                    .on('dblclick', function()
                    {
                        $(this).remove();
                        self.updateHiddenSelect();
                    })
                    .appendTo(self.JQueryVisibleSelected);
                }
            });
        },

        _onWindowReady: function()
        {}
    });

    new sx.classes.Columns($optionsString);
})(sx, sx.$, sx._);
JS
);
?>



