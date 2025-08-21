(function($){
    $.fn.templateManager = function( method ) {  
        var defaults = {
            tools: {
                rename: '.tools .rename',
                remove: '.tools .delete'
            },
            makedir: '.makedir',
            fileListContainer: '.file-list-container',
            fileList: '.file-list',
            fileListItems: '.file-list > li',
        }, 
        args = arguments;

        return this.each(function() {
            var $this = $(this), 
                data = $this.data('templateManager');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('templateManager', data);
                    data.options = $.extend({}, defaults, initoptions);

                    $(data.options.makedir).on('click.tm', onMakeDir);
                    $(data.options.tools.rename, $this).on('click.tm', onRename);
                    $(data.options.tools.remove, $this).on('click.tm', onDelete);

                    if ($.fn.lightGallery) {
                        $(data.options.fileList).lightGallery({
                            selector: "a[rel='lightbox-image-tour']",
                            thumbnail: false,
                            autoplay: false,
                            autoplayControls: false
                        });
                    }
                },
                
            }
            
            //private
            var getCurrentFolder = function() {
                return $(data.options.fileListContainer, $this).data('currentFolder') 
            },
            
            onDelete = function() {
                var crudOptions = $(this).data('crudOptions');
                var item = $(this).closest('.item');
                var _this = this;
                if (item.hasClass('file')) {
                    var item_str = lang.t('файл')+' `'+item.data('name')+'`';
                } else {
                    var item_str = lang.t('папку')+' `'+item.data('name')+'`';
                }
                
                if (confirm(lang.t('Вы действительно хотите удалить %what?', {what: item_str}))) {
                    var url = $(this).attr('href');
                    $.ajaxQuery({
                        url: url,
                        success: function() {
                            updateTarget(crudOptions, _this);
                        }
                    });
                }
                return false;
            },
            
            onRename = function() {
                var crudOptions = $(this).data('crudOptions');
                var oldValue = $(this).data('oldValue');
                var url = $(this).data('url');
                var path = $(this).closest('[data-path]').data('path');
                var _this = this;

                var new_filename = prompt(lang.t('Введите новое имя'), oldValue);
                
                if (new_filename !== null) {
                    $.ajaxQuery({
                        url: url,
                        data: {
                            path: path,
                            new_filename: new_filename
                        },
                        success: function() {
                            updateTarget(crudOptions, _this);
                        }
                    });
                }

                return false;
            },
            
            onMakeDir = function() {
                var crudOptions = $(this).data('crudOptions');
                var url = $(this).data('url');
                var name = prompt(lang.t('Введите имя папки'), lang.t('Новая папка'));
                var _this = this;

                if (name !== null) {
                    $.ajaxQuery({
                        url: url,
                        data: {
                            name: name
                        },
                        success: function() {
                            updateTarget(crudOptions, _this);
                        }
                    });
                }

                return false;
            },
            
            updateTarget = function(crudOptions, element) {
                var element = crudOptions && crudOptions.updateElement ? crudOptions.updateElement : element;
                var ajaxParam = (crudOptions && crudOptions.ajaxParam) ? crudOptions.ajaxParam : null;
                $.rs.updatable.updateTarget(element, null, null, ajaxParam);
            };
            
            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }    
})(jQuery);    
    
$.contentReady(function() {
    $('.tmanager').templateManager();
});
(function($){
    $.fn.selectTemplate = function( method ) {  
        var defaults = {
            useRelative: '#use-relative',
            parent: '*',
            handler: '.selectTemplate'
        }, 
        args = arguments;

        return this.each(function() {
            var $this = $(this), 
                data = $this.data('selectTemplate');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('selectTemplate', data);
                    data.options = $.extend({}, defaults, initoptions);
                   
                   $(data.options.handler, $this.parents(data.options.parent).get(0)).click(showDialog);
                },
            }
            
            //private
            var showDialog = function() {
                if ($this.is(':disabled')) return false;
                if ($this.val() != '') {
                    $('#templateManager').dialog('destroy');
                }
                $.rs.openDialog({
                    dialogId: 'templateManager',
                    url: data.options.dialogUrl,
                    ajaxOptions: {
                        data: {
                            start_tpl: $this.val()
                        }
                    },
                    dialogOptions: {
                        width:1010,
                        height:550,
                        dialogClass: 'templateManager',
                    },
                    afterOpen: function($dialog) {
                        function bindSelect() {
                            $('.canselect', $dialog)
                            .unbind('.selectTemplate')
                            .bind('click.selectTemplate', selectFile);
                        }
                        $dialog.bind('new-content', bindSelect);
                        bindSelect();
                    }
                });
            },
            
            selectFile = function(e) {
                var useRelative = $(data.options.useRelative).is(':checked');
                var path = $(this).closest('.item').data('path');
                if (useRelative) {
                    path = path.replace(/^theme:([\w]+)\/(.*)$/, '%THEME%/$2');
                }
                $this.focus().val(path);
                $this.change();
                closeDialog();
                return false;
            },
            
            closeDialog = function() {
                $('#templateManager').dialog('close');
            };
            
            
            
            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }    
})(jQuery);    
