<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 02.06.2015
 */

namespace skeeks\cms\modules\admin\widgets;

use skeeks\cms\grid\GridViewPjaxTrait;
use skeeks\cms\modules\admin\traits\GridViewSortableTrait;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Простейший крид амдинки.
 *
 * Class GridView
 * @package skeeks\cms\modules\admin\widgets
 */
class GridView extends \yii\grid\GridView
{
    use GridViewSortableTrait;
    use GridViewPjaxTrait;

    public $tableOptions = ['class' => 'table table-striped table-bordered sx-table'];
    public $options = ['class' => 'grid-view sx-grid-view'];

    public function init()
    {
        parent::init();

        $this->pjaxClassName = Pjax::className();
    }


    /**
     * @var string
     */
    public $afterTableLeft = "";
    /**
     * @var string
     */
    public $afterTableRight = "";

    /**
     * @var string
     */
    public $beforeTableLeft = "";
    /**
     * @var string
     */
    public $beforeTableRight = "";

    /**
     * @var string the layout that determines how different sections of the list view should be organized.
     * The following tokens will be replaced with the corresponding section contents:
     *
     * - `{summary}`: the summary section. See [[renderSummary()]].
     * - `{errors}`: the filter model error summary. See [[renderErrors()]].
     * - `{items}`: the list items. See [[renderItems()]].
     * - `{sorter}`: the sorter. See [[renderSorter()]].
     * - `{pager}`: the pager. See [[renderPager()]].
     * - `{beforeTable}`: the pager. See [[renderPager()]].
     * - `{afterTable}`: the pager. See [[renderPager()]].
     */
    public $layout = "{beforeTable}\n
                      <div class='sx-table-wrapper'>
                          {items}\n
                      </div>
                      {afterTable}
                      <div class='sx-table-additional'>
                      \n<div class=sx-table-additional-left'>
                      <div class='sx-pager'>{pager}</div>
                      \n<div class='sx-perpager'>{perPage}</div>
                      </div>
                      \n<!--<div class='pull-left'>{sorter}</div>-->
                        <div class='sx-table-additional-right'>{summary}</div>
                      </div>";


    /**
     * Runs the widget.
     */
    public function run()
    {
        $this->pjaxBegin();

        parent::run();
        $this->registerAsset();

        $this->pjaxEnd();
    }

    public function registerAsset()
    {
        $this->registerSortableJs();
        \skeeks\cms\admin\assets\AdminGridAsset::register($this->view);
    }


    /**
     * @param string $name
     * @return bool|string
     */
    public function renderSection($name)
    {
        switch ($name) {
            case "{beforeTable}":
                return $this->renderBeforeTable();
            case "{afterTable}":
                return $this->renderAfterTable();
            case "{perPage}":
                return $this->renderPerPage();
            default:
                return parent::renderSection($name);
        }
    }


    /**
     * @return string
     */
    public function renderAfterTable()
    {
        if ($this->afterTableLeft || $this->afterTableRight) {
            return "<div class='sx-after-table'>
                        <div class='float-left'>{$this->afterTableLeft}</div>
                        <div class='float-right'>{$this->afterTableRight}</div>
                    </div>";
        } else {
            return "";
        }

    }

    /**
     * @return string
     */
    public function renderPerPage()
    {
        $pagination = $this->dataProvider->getPagination();

        $min = $pagination->pageSizeLimit[0];
        $max = $pagination->pageSizeLimit[1];

        $items = [];
        $i = 0;
        for ($i >= $min; $i <= $max; $i++) {
            if ($i % 5 == 0 && $i > 0) {
                $items[$i] = $i;
            }
        }

        $id = $this->id . "-per-page";

        $get = \Yii::$app->request->get();
        ArrayHelper::remove($get, $pagination->pageSizeParam);
        $get[$pagination->pageSizeParam] = "";

        $url = '/' . \Yii::$app->request->pathInfo . "?" . http_build_query($get);

        $this->view->registerJs(<<<JS
(function(sx, $, _)
{
    sx.classes.GridPerPage = sx.classes.Component.extend({

        _onDomReady: function()
        {
            var self = this;
            var JSelect = $("#" + this.get('id'));
            JSelect.on("change", function()
            {
                $(this).val();

                var JLink = $("<a>", {
                    'href' : self.get('url') + $(this).val(),
                    'style' : 'display: none;',
                }).text('link');

                $(this).closest('form').append(JLink);
                JLink.click();
            });
        }
    });

    new sx.classes.GridPerPage({
        'id' : '{$id}',
        'url' : '{$url}'
    });
})(sx, sx.$, sx._);
JS
        );


        return "<div class='sx-per-page'><form method='get' action='" . $url . "'> <span class='per-page-label'>" . \Yii::t('skeeks/cms',
                'On the page') . ":</span>"
            . Html::dropDownList($pagination->pageSizeParam, [$pagination->pageSize], $items, [
                'id' => $id
            ]) . "</form></div>";
    }

    /**
     * @return string
     */
    public function renderBeforeTable()
    {
        if ($this->beforeTableLeft || $this->beforeTableRight) {

            return <<<HTML
        <div class='sx-before-table'>
            <div class='sx-before-table-left'>{$this->beforeTableLeft}</div>
            <div class='sx-before-table-right'>{$this->beforeTableRight}</div>
          </div>
HTML;
        } else {
            return '';
        }

    }
}