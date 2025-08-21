/**
 * Плагин инилицализирует работу поля со списком пользователей
 *
 * @author ReadyScript lab.
 */
(function( $ ){
    $.fn.usersLinks = function( method ) {
        var defaults = {
                mainUser: '.users-main',
                otherUsers: '.users-other',
                userLine: '.user-line',
                addButton: '.users-add',
                removeButton: '.users-remove',
                hiddenInput:'input[type="hidden"]',
                input: 'input[type="text"]'
            },
            args = arguments;

        return this.each(function() {
            var $this = $(this),
                data = $this.data('usersLinks');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('usersLinks', data);
                    data.opt = $.extend({}, defaults, initoptions);
                    $this
                        .on('click', '.users-add', addUser)
                        .on('click', data.opt.removeButton, removeUser);
                }
            };

            var addUser = function() {
                let source = $this
                    .find(data.opt.mainUser)
                    .find(data.opt.userLine)
                    .clone();

                source.find(data.opt.removeButton).removeClass('hidden')
                source.find(data.opt.addButton).remove();
                source.find(data.opt.hiddenInput).remove();
                source.find(data.opt.input).val('');
                $this.find(data.opt.otherUsers).append(source).trigger('new-content');
            },
            removeUser = function() {
                $(this).closest(data.opt.userLine).remove();
            };

            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    };
})( jQuery );