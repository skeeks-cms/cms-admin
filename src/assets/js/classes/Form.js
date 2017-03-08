/*!
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.02.2017
 */

(function(sx, $, _)
{
    sx.createNamespace('classes.forms', sx);

    sx.classes.forms.AdminForm = sx.classes.Component.extend({

        _init: function()
        {},

        _onDomReady: function()
        {
            var self = this;

            $('#' + this.get('id') + ' .form-group.required label').each(function()
            {
                $(this).append($('<span class="sx-from-required" title="' + self.get('msg_title') + '">').text(' *'));
            });
        },

        _onWindowReady: function()
        {}
    });

})(sx, sx.$, sx._);