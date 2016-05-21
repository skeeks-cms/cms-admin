/*!
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (—ÍËÍ—)
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

            this.JLabel     = $('label', this.JField);

            this.name       = this.JLabel.text();
            this.id         = this.JLabel.attr('for');

            $('<li>').append(
                $('<a>', {
                    'href' : '#',
                    'data-id' : this.id,
                })
                .append(this.name)
            ).appendTo(self.Form.JBtnTriggerFiedlsMenu);
        },

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
                self.Fields = new sx.classes.filters.Field(self, $(this));
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