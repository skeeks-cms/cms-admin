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

            var classes = this.JField.attr('class').split(' ');
            var id = '';
            _.each(classes, function(value)
            {
                if (value.indexOf('field-') != -1)
                {
                    id = value.replace('field-', '');
                }
            });



            this.name       = this.JLabel.text();
            //this.id         = this.JLabel.attr('for');
            this.id         = id;

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

            if (this.Form.get('visibles', []).length)
            {
                if (_.indexOf( this.Form.get('visibles', []), this.id) == -1 )
                {
                    this.JField.addClass('sx-default-hidden');
                } else
                {
                    this.JField.removeClass('sx-default-hidden');
                    this.visible = true;
                }
            } else
            {
                if (!this.JField.hasClass('sx-default-hidden'))
                {
                    this.visible = true;
                }
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

                 $('input, checkbox, radio, select', self.JField).attr('disabled', false);
            } else
            {
                this.JField.slideUp('fast', function(){
                    self.JField.addClass('sx-default-hidden');
                });


                this.JTriggerBtn.empty().append(this.name);
                this.JTriggerBtn.addClass('sx-visible');

                $('input, checkbox, radio, select', self.JField).attr('disabled', 'disabled');
            }
        },

        hide: function()
        {
            this.visible   = false;

            if (this.Form.getVisibleFieldIds().length == 0)
            {
                this.visible   = true;
                return this;
            }

            this.update();

            this.Form.saveVisibleFields();
            return this;
        },

        show: function()
        {
            this.visible   = true;
            this.update();

            this.Form.saveVisibleFields();
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

        _initFields: function()
        {
            var self = this;

            this.Fields = [];

            this.JBtnTriggerFiedls      = $('.sx-btn-trigger-fields', this.getWrapper());
            this.JBtnTriggerFiedlsMenu  = $('.dropdown-menu', this.JBtnTriggerFiedls);
            this.JBtnDeleteFilter       = $('.sx-btn-filter-delete', this.getWrapper());
            this.JBtnSaveValuesFilter       = $('.sx-btn-filter-save-values', this.getWrapper());
            this.JBtnCloseFilter       = $('.sx-btn-filter-close', this.getWrapper());
            this.JBtnSaveAs       = $('.sx-btn-filter-save-as', this.getWrapper());
            this.JBtnCreate       = $('.sx-btn-filter-create', this.getWrapper());
            this.JCreateModal       = $('#' + this.get('createModalId'));

            this.JBtnTab       = $('.sx-tab', this.getWrapper());
            this.JForm       = $('form', this.getWrapper());

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
            }).append(this.get('showAllTitle'));

            this.JBtnHideAll = $('<a>', {
                'href' : '#',
            }).append(this.get('hideAllTitle'));

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

            this.JBtnDeleteFilter.on('click', function()
            {
                self.actionDelete();
                return false;
            });

            this.JBtnSaveValuesFilter.on('click', function()
            {
                self.actionSaveValues();
                return false;
            });

            this.JBtnTab.on('click', function()
            {
                sx.block(self.JForm);
            });

            this.JBtnCloseFilter.on('click', function()
            {
                sx.block(self.JForm);
            });

            this.JBtnSaveAs.on('click', function()
            {
                self.actionSaveAs();
            });

            this.JBtnCreate.on('click', function()
            {
                self.actionCreate();
            });


            $('input, select, radio, checkbox', this.getWrapper()).on('change', function()
            {
                self.JBtnCloseFilter.show();
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
            this._initFields();
        },

        /**
         * @returns {Array}
         */
        getVisibleFieldIds: function()
        {
            var self = this;

            var visibles = [];
            _.each(self.Fields, function(Field)
            {
                if (Field.visible === true)
                {
                    visibles.push( Field.id );
                }
            });

            return visibles;
        },


        saveVisibleFields: function()
        {
            var self = this;
            self.getVisibleFieldIds();

            var ajaxQuery = sx.ajax.preparePostQuery(this.get('backendSaveVisibles'), {
                'visibles' : self.getVisibleFieldIds()
            });

            new sx.classes.AjaxHandlerNoLoader(ajaxQuery);
            ajaxQuery.execute();
        },

        actionDelete: function()
        {
            var self = this;
            self.getVisibleFieldIds();
            sx.block(self.JForm);

            var ajaxQuery = sx.ajax.preparePostQuery(this.get('backendDelete'));


            var Handler = new sx.classes.AjaxHandlerStandartRespose(ajaxQuery);
            new sx.classes.AjaxHandlerNoLoader(ajaxQuery);

            Handler.bind('success', function()
            {
                $('.modal').modal('hide');

                _.delay(function()
                {
                    window.location.href = self.get('indexUrl');
                }, 1000);
            });

            ajaxQuery.execute();
        },

        actionSaveValues: function()
        {
            var self = this;
            self.getVisibleFieldIds();

            sx.block(self.JForm);

            var ajaxQuery = sx.ajax.preparePostQuery(this.get('backendSaveValues'), {
                'values' : self.JForm.serialize()
            });

            var Handler = new sx.classes.AjaxHandlerStandartRespose(ajaxQuery);
            new sx.classes.AjaxHandlerNoLoader(ajaxQuery);

            Handler.bind('success', function()
            {
                _.delay(function()
                {
                    window.location.reload();
                }, 1000);
            });

            ajaxQuery.execute();
        },

        actionSaveAs: function()
        {
            var self = this;
            self.JCreateModal.modal('show');

            var JCreateForm = $('form', self.JCreateModal);
            var JVisiblesInput = $('input[name=visibles]', JCreateForm);
            var JValuesInput = $('input[name=values]', JCreateForm);


            JValuesInput.val(self.JForm.serialize());
            JVisiblesInput.val(self.getVisibleFieldIds().join(','));
        },

        actionCreate: function()
        {
            var self = this;
            self.JCreateModal.modal('show');

            var JCreateForm = $('form', self.JCreateModal);
            var JVisiblesInput = $('input[name=visibles]', JCreateForm);
            var JValuesInput = $('input[name=values]', JCreateForm);

            JValuesInput.val('');
            JVisiblesInput.val('');
        }
    });
})(sx, sx.$, sx._);