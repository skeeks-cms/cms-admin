<?php
/**
 * ActiveFormUseTab
 *
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010-2014 SkeekS (Sx)
 * @date 27.01.2015
 * @since 1.0.0
 */
namespace skeeks\cms\modules\admin\widgets\form;

use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * @deprecated
 *
 * Class ActiveFormUseTab
 * @package skeeks\cms\modules\admin\widgets\form
 */
class ActiveFormUseTab  extends \skeeks\cms\modules\admin\widgets\ActiveForm
{
    protected $_tabs = [];

    public function fieldSet($name, $options = [])
    {
        if (!$id = ArrayHelper::getValue($options, 'id'))
        {
            $options['id']         = "sx-form-tab-id-" . md5($name);
        }

        $this->_tabs[$id] = $name;

        return <<<HTML
        <div class="sx-form-tab tab-pane" id="{$options['id']}" data-name="{$name}" role="tabpanel">
HTML;

    }

    public function fieldSetEnd()
    {
        return <<<HTML
        </div>
HTML;

    }



    public function init()
    {
        $this->options = ArrayHelper::merge($this->options, [
            //'class' => 'sx-bg-primary'
        ]);

        parent::init();

        $loadText = \Yii::t('skeeks/cms', 'Loading');
        echo<<<HTML
        <div class="sx-pre-loader-form" style="text-align: center; padding: 20px;">
            {$loadText}...
        </div>
        <div role="tabpanel" class="sx-form-tab-panel" style="display: none;">
            <ul class="nav nav-tabs" role="tablist">
            </ul>
            <div class="tab-content">
HTML;
    }

    /**
     * Runs the widget.
     * This registers the necessary javascript code and renders the form close tag.
     * @throws InvalidCallException if `beginField()` and `endField()` calls are not matching
     */
    public function run()
    {
        $view = $this->getView();

        $jsOptions = Json::encode([
            'id' => $this->id
        ]);

        $view->registerJs(<<<JS
        (function(sx, $, _)
        {
            sx.classes.FormUseTabs = sx.classes.Component.extend({

                _init: function()
                {
                    this.activeTab = window.location.hash.replace("#","");
                },

                _onDomReady: function()
                {
                    var self = this;

                    var JForm = $("#" + this.get('id'));
                    
                    var counter = 0;
                    $('.sx-form-tab').each(function(i,s)
                    {
                        counter = counter + 1;

                        var Jcontroll = $("<a>", {
                            'href' : '#' + $(this).attr('id'),
                            'aria-controls' : $(this).attr('id'),
                            'role' : 'tab',
                            'data-toggle' : 'tab',
                            'class' : 'nav-link',
                        }).append($(this).data('name'));

                        Jcontroll.on('click', function()
                        {
                            location.href = $(this).attr("href");
                        });

                        var Jli = $("<li>", {
                            'role' : 'presentation',
                            'class' : 'presentation nav-item',
                        }).append(Jcontroll);


                        if (self.activeTab)
                        {
                             if (self.activeTab == $(this).attr('id'))
                            {
                                Jli.addClass("active");
                                Jcontroll.addClass("active");
                                $(this).addClass("active");
                            }
                        } else
                        {
                            if (counter == 1)
                            {
                                Jli.addClass("active");
                                Jcontroll.addClass("active");
                                $(this).addClass("active");
                            }
                        }

                        $('.sx-form-tab-panel .nav').append(Jli);
                    });
                    
                    _.defer(function()
                    {
                        $('.sx-pre-loader-form', JForm).hide();
                        $('.sx-form-tab-panel', JForm).fadeIn();
                    });
                },
            });

            new sx.classes.FormUseTabs({$jsOptions});

        })(sx, sx.$, sx._);
JS
);

        $html = <<<HTML
            </div>
        </div>
HTML;

        return $html . parent::run();
    }
}