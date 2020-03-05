<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 11.07.2015
 */
/* @var $this yii\web\View */
/* @var $widget \skeeks\cms\modules\admin\widgets\formInputs\OneImage */
?>
    <div class="row" id="<?= $widget->id; ?>">
        <div class="col-lg-12">
            <div class="sx-one-input pull-left">
                <? if ($widget->model) : ?>
                    <?= \yii\helpers\Html::activeTextInput($widget->model, $widget->attribute, $widget->options); ?>

                <? else: ?>
                    <?= \yii\helpers\Html::textInput($widget->name, $widget->attribute, $widget->options); ?>
                <? endif; ?>
            </div>
            <div class="pull-left" style="width: 210px;">
                <button class="btn u-btn-brown sx-btn-create-file-manager pull-left">
                    <i class="fas fa-download"></i> <?= \Yii::t('skeeks/cms', 'Choose file') ?>
                </button>

                <div class="sx-one-image pull-left" <?= !$widget->showPreview ? "style='display: none;'" : "" ?>>
                    <a href="" class="sx-fancybox" data-pjax="0" target="_blank">
                        <img src=""/>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?
$jsonOptions = $widget->getJsonOptions();
$this->registerCss(<<<CSS
.sx-one-image, .sx-btn-create-file-manager {
    margin-left: 10px;
}
.sx-one-input {
    width: calc(100% - 210px);
}
.sx-one-image img
{
    /*width: 100%;*/
    max-height: 30px;
}
CSS
);

$this->registerJs(<<<JS
(function(sx, $, _)
{
    sx.classes.SelectOneImage = sx.classes.Component.extend({

        _init: function()
        {
            var self = this;

            sx.EventManager.bind(this.get('callbackEvent'), function(e, data)
            {
                console.log('111');
                self.setFile(data.file);
            });
        },

        _onDomReady: function()
        {
            var self = this;

            this.jQueryCreateBtn        = $(".sx-btn-create-file-manager", this.jQuryWrapper());
            this.jQueryInput            = $("input", this.jQuryWrapper());
            this.jQueryImage            = $(".sx-one-image img", this.jQuryWrapper());
            this.jQueryImageA           = $(".sx-one-image a", this.jQuryWrapper());

            this.jQueryCreateBtn.on("click", function() {
                console.log(self.jQuryWrapper());
                console.log($(this));
                self.createFileManager();
                return this;
            });

            this.jQueryInput.on("keyup", function()
            {
                self.update();
                return this;
            });

            this.jQueryInput.on("change", function()
            {
                self.update();
                return this;
            });

            self.update();
        },

        update: function()
        {
            this.jQueryImage.attr('src', this.jQueryInput.val());
            this.jQueryImageA.attr('href', this.jQueryInput.val());
            return this;
        },

        /**
        *
        * @param file
        */
        setFile: function(file)
        {
            $("input", this.jQuryWrapper()).val(file).change();
        },

        /**
        *
        * @returns {sx.classes.SelectOneImage}
        */
        createFileManager: function()
        {
            var self = this;
            
            var WindowFileManager = new sx.classes.Window(this.get('selectFileUrl'), 'sx-select-file-manager-' + self.get('id'));
            
            WindowFileManager.on('selectFile', function(e, data) {
                self.setFile(data.file);
                WindowFileManager.close();
            });
            
            /*WindowFileManager.on('close', function(e, data) {
                WindowFileManager.off("selectFile");
                WindowFileManager.off("close");
            });*/
            
            WindowFileManager.open();

            return this;
        },
        /**
        *
        * @returns {*|HTMLElement}
        */
        jQuryWrapper: function()
        {
            return $('#' + this.get('id'));
        }
    });

    new sx.classes.SelectOneImage({$jsonOptions});
})(sx, sx.$, sx._);
JS
)
?>