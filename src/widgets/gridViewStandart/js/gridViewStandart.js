/*!
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 26.07.2015
 */
(function(sx, $, _)
{
    sx.createNamespace('classes.grid', sx);

    sx.classes.grid.Standart = sx.classes.Component.extend({

        _init: function()
        {
            /*this.CheckFullAll   = new sx.classes.grid.CheckFullAll(this);
            this.CheckAll       = new sx.classes.grid.CheckAll(this);*/

            this.Actions        = new sx.classes.Entity();
        },

        /**
         *
         * @param {sx.classes.grid._Action} Action
         * @returns {sx.classes.grid.Standart}
         */
        registerAction: function(Action)
        {
            this.Actions.set(Action.get('id'), Action);
            return this;
        },

        /**
         * @param id
         * @returns {sx.classes.grid._Action}
         */
        getAction: function(id)
        {
            var Action = this.Actions.get(id);
            if (!Action || !Action instanceof sx.classes.grid._Action)
            {
                console.log(Action);
                throw new Error("Некорректное действие");
            }

            return Action;
        },

        _onDomReady: function()
        {
            var self = this;

            this.JQueryGrid                 = $("#" + this.get('id'));
            this.JQueryMultiBtnsWrapper     = $(".sx-grid-multi-controlls");

            this.CheckFullAll   = new sx.classes.grid.CheckFullAll(this);
            this.CheckAll       = new sx.classes.grid.CheckAll(this);

            this.JQueryMultiBtns = $('.sx-grid-multi-btn', this.JQueryMultiBtnsWrapper);

            this.JQueryMultiBtns.on('click', function()
            {
                var id          = $(this).data('id');
                self.getAction(id).execute();
            });

            this.CheckFullAll.bind("change", function()
            {
                self.updateMultiBtns();
                return this;
            });

            this.CheckAll.bind("change", function()
            {
                self.updateMultiBtns();
                return this;
            });

            _.delay(function() {
                self.updateMultiBtns();
            }, 200);
        },

        updateMultiBtns: function()
        {
            if (!this.CheckFullAll.isChecked() && !this.CheckAll.isChecked())
            {
                this.JQueryMultiBtns.attr("disabled", "disabled");
            } else
            {
                this.JQueryMultiBtns.attr("disabled", false);
            }

            return this;
        },

        /**
         * @returns {void|*|Function|*}
         */
        getBlocker: function()
        {
            if (!this.Blocker)
            {
                var pjaxId = this.get('pjaxId');
                if (pjaxId)
                {
                    this.Blocker = new sx.classes.Blocker("#" + pjaxId);
                } else
                {
                    this.Blocker = new sx.classes.Blocker("#" + this.get('id'));
                }
            }

            return this.Blocker;
        },

        reload: function()
        {
            var self = this;

            _.delay(function()
            {
                if (self.JQueryGrid.closest("[data-pjax-container]").length > 0) {
                    console.log(self.JQueryGrid.closest("[data-pjax-container]"));
                    $.pjax.reload('#' + self.JQueryGrid.closest("[data-pjax-container]").attr("id"), {});
                    return true;
                }

                if (self.get('enabledPjax'))
                {
                    console.log('2222');

                    var pjaxId = self.get('pjaxId');
                    $.pjax.reload('#' + pjaxId, {});
                    return self;
                } else
                {
                    window.location.reload();
                }
            }, 300)


            return this;
        },

        /**
         * Данные для ajax запроса
         *
         * @returns {{all: *}}
         */
        getDataForRequest: function()
        {
            if (this.CheckFullAll.isChecked()) {
                //Выбрано все
                var data = {};
                data[this.get('requestPkParamName', 'pk')] = this.CheckFullAll.getValue();
            } else {
                //Только отмеченные
                var data = {};
                data[this.get('requestPkParamName', 'pk')] = this.CheckAll.getValue();
            }

            return data;
        }
    });

    /**
     *
     */
    sx.classes.grid._Element = sx.classes.Component.extend({

        construct: function (Grid, opts)
        {
            var self = this;
            opts = opts || {};
            this.Grid   = Grid;

            //this.parent.construct(opts);
            this.applyParentMethod(sx.classes.Component, 'construct', [opts]); // TODO: make a workaround for magic parent calling
        }
    });
    /**
     *
     */
    sx.classes.grid._Action = sx.classes.Component.extend({

        construct: function (Grid, id, opts)
        {
            var self = this;
            opts = opts || {};
            this.Grid   = Grid;
            this.id     = id;

            //this.parent.construct(opts);
            this.applyParentMethod(sx.classes.Component, 'construct', [opts]); // TODO: make a workaround for magic parent calling

            this.Grid.registerAction(this);
        },

        execute: function()
        {}
    });

    sx.classes.grid.MultiAction = sx.classes.grid._Action.extend({

        execute: function()
        {
            var self = this;

            if (!this.Grid.CheckFullAll.isChecked() && !this.Grid.CheckAll.isChecked())
            {
                sx.notify.info("Необходимо выбрать хотя бы один элемент.");
                return this;
            }

            if (this.get("confirm"))
            {
                sx.confirm(this.get("confirm"), {
                    'no' : function()
                    {},
                    'yes' : function()
                    {
                        self._go();
                    }
                });
            } else
            {
                return this._go();
            }
        },

        _go: function()
        {
            var self = this;
            //Надо делать ajax запрос
            if (this.get("request") == 'ajax')
            {
                return this.createAjaxQuery(self.Grid.getDataForRequest()).execute();
            }
        },

        /**
         * @param data
         * @returns {*|sx.classes.AjaxQuery}
         */
        createAjaxQuery: function(data)
        {
            var self = this;

            if (this.get("method", "post") == "post")
            {
                this.ajax = sx.ajax.preparePostQuery(this.get('url'));
            } else
            {
                this.ajax = sx.ajax.prepareGetQuery(this.get('url'));
            }

            this.ajax.setData(data);

            self.Grid.getBlocker().block();

            new sx.classes.AjaxHandlerNoLoader(this.ajax);

            this.ajax.onSuccess(function(e, data)
            {
                if (data.response.success)
                {
                    sx.notify.success(data.response.message);
                    self.Grid.reload();
                } else
                {
                    sx.notify.error(data.response.message);
                    self.Grid.reload();
                }

                self.Grid.getBlocker().unblock();
            });

            this.ajax.onError(function(e, response)
            {
                sx.notify.error(response.errorThrown + '. Обратитесь к разарботчикам');
                self.Grid.getBlocker().unblock();
                //self.Grid.reload();
            });

            return this.ajax;
        }
    });

    sx.classes.grid.CheckAll = sx.classes.grid._Element.extend({

        _onDomReady: function()
        {
            var self = this;

            //this.JQueryCheckboxAll = $(".select-on-check-all", this.Grid.JQueryGrid);
            //this.JQueryCheckbox = $(".sx-grid-checkbox", this.Grid.JQueryGrid);
            
            this.Grid.JQueryGrid.on("change", ".sx-grid-checkbox", function()
            {
                self.trigger("change");
            });

            this.Grid.JQueryGrid.on("change", ".select-on-check-all", function()
            {
                _.delay(function()
                {
                    self.trigger("change");
                }, 200);
            });
        },

        getJQueryCheckbox: function() {
            return $(".sx-grid-checkbox", this.Grid.JQueryGrid);
        },
        
        getJQueryCheckboxAll: function() {
            return $(".select-on-check-all", this.Grid.JQueryGrid);
        },
        
        /**
         * @returns {Array}
         */
        getValue: function()
        {
            var result = [];
            this.getJQueryCheckbox().each(function(e, data)
            {
                if ($(this).is(":checked"))
                {
                    result.push( $(this).val() );
                }
            });

            return result;
        },

        /**
         * @returns {Boolean|boolean}
         */
        isChecked: function()
        {
            return Boolean(_.size(this.getValue()) > 0 );
        }

    });

    sx.classes.grid.CheckFullAll = sx.classes.grid._Element.extend({

        _onDomReady: function()
        {
            var self = this;

            this.selectedIds = [];

            this.JQueryCheckbox = $(".sx-select-full-all", this.Grid.JQueryGrid);
            this.JQueryCheckbox.on('change', function()
            {
                var ThisJquery = $(this);
                if (ThisJquery.is(':checked'))
                {
                    sx.confirm('Вы уверены что хотите применить действие для ВСЕХ записей в списке, в том числе на других страницах списка, а не только для отмеченных флажками?', {
                        'no': function(e, data)
                        {
                            /*ThisJquery.attr("checked", false);*/
                            self.JQueryCheckbox.attr("checked", false);
                            self.trigger("change");
                        },
                        'yes': function(e, data)
                        {
                            //Собираются id записей
                            self.JQueryCheckbox.attr("checked", "checked");
                            var AjaxQuery = sx.ajax.preparePostQuery();
                            AjaxQuery.setData({
                                '__gird-all-ids' : '__gird-all-ids'
                            });
                            AjaxQuery.on('success', function(e, data) {
                                sx.notify.success('Всего выборано: ' + data.response.message.total);
                                self.selectedIds = data.response.message.pks;
                            });
                            AjaxQuery.on('error', function(e, data) {
                                sx.notify.error('Ошибка выборки');
                            });
                            AjaxQuery.execute();
                        }
                    });
                } else {
                    self.JQueryCheckbox.attr("checked", false);
                }

                self.trigger("change");
            });
        },

        /**
         * @returns {Boolean|boolean}
         */
        isChecked: function()
        {
            return Boolean( this.JQueryCheckbox.is(":checked") );
        },

        /**
         * @returns {number}
         */
        getValue: function()
        {
            return this.selectedIds;
            /*if (this.isChecked())
            {
                return 1;
            }

            return 0;*/
        }
    });





})(sx, sx.$, sx._);
