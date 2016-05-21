/*!
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 20.05.2016
 */
(function(sx, $, _)
{
    sx.createNamespace('classes.filters', sx);

    sx.classes.filters.Field = sx.classes.Component.extend({

        construct: function (Form, JField, opts)
        {
            var self = this;
            opts = opts || {};

            this.Form      = Form;
            this.JField    = JField;
            this.visible   = false;

            if (!Form instanceof sx.classes.filters.Form)
            {
                throw new Error('Form must be sx.classes.filters.Form');
            }
            //this.parent.construct(opts);
            this.applyParentMethod(sx.classes.Component, 'construct', [opts]); // TODO: make a workaround for magic parent calling
        },

        _init: function()
        {
            var self = this;

            this.JLabel         = $('label', this.JField);
            this.JHideBtn     = $('.sx-btn-hide-field', this.JField);

            this.JHideBtn.on('click', function()
            {
                self.hide();
                return false;
            });

            this.name       = this.JLabel.text();
            this.id         = this.JLabel.attr('for');

            this.JTriggerBtn = $('<a>', {
                'href' : '#',
                'data-id' : this.id,
            }).append(this.name);

            this.JTriggerBtn.on('click', function()
            {
                self.triggerBtn();
                return false;
            });

            $('<li>').append(
                this.JTriggerBtn
            ).appendTo(self.Form.JBtnTriggerFiedlsMenu);

            if (!this.JField.hasClass('sx-default-hidden'))
            {
                this.visible = true;
            }

            this.update();
        },

        update: function()
        {
            var self = this;

            if (this.visible)
            {
                this.JField.slideDown('fast', function(){
                    self.JField.removeClass('sx-default-hidden');
                });

                this.JTriggerBtn.empty().append(' + ' + this.name);
                this.JTriggerBtn.addClass('sx-hidden');
            } else
            {
                this.JField.slideUp('fast', function(){
                    self.JField.addClass('sx-default-hidden');
                });


                this.JTriggerBtn.empty().append(this.name);
                this.JTriggerBtn.addClass('sx-visible');
            }
        },

        hide: function()
        {
            this.visible   = false;
            this.update();
            return this;
        },

        show: function()
        {
            this.visible   = true;
            this.update();
            return this;
        },

        triggerBtn: function()
        {
            if (this.visible)
            {
                this.hide();
            } else
            {
                this.show();
            }
            return this;
        }

    });

    sx.classes.filters.Form = sx.classes.Component.extend({

        _initvisibleFields: function()
        {
            var self = this;

            this.Fields = [];

            this.JBtnTriggerFiedls      = $('.sx-btn-trigger-fields', this.getWrapper());
            this.JBtnTriggerFiedlsMenu  = $('.dropdown-menu', this.JBtnTriggerFiedls);

            this.JBtnTriggerFiedlsMenu.empty();

            $('.form-group', this.getWrapper()).each(function()
            {
                if (!$(this).hasClass('form-group-footer'))
                {
                    self.Fields.push( new sx.classes.filters.Field(self, $(this)) );
                }
            });

            this.JBtnTriggerFiedlsMenu.append('<li class="divider"></li>');

            this.JBtnShowAll = $('<a>', {
                'href' : '#',
            }).append("Показать все условия");

            this.JBtnHideAll = $('<a>', {
                'href' : '#',
            }).append("Скрыть все условия");

            $('<li>').append(
                this.JBtnShowAll
            ).appendTo(self.JBtnTriggerFiedlsMenu);

            $('<li>').append(
                this.JBtnHideAll
            ).appendTo(self.JBtnTriggerFiedlsMenu);

            this.JBtnShowAll.on('click', function()
            {
                _.each(self.Fields, function(Field)
                {
                    Field.show();
                });
            });
            this.JBtnHideAll.on('click', function()
            {
                _.each(self.Fields, function(Field)
                {
                    Field.hide();
                });
            });

        },


        /**
         * @returns {*|HTMLElement}
         */
        getWrapper: function()
        {
            return $('#' + this.get('id') + '-wrapper');
        },

        _onDomReady: function()
        {
            this._initvisibleFields();
        }
    });
})(sx, sx.$, sx._);