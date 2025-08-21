/**
 * Плагин для TinyMCE, который позволяет вставлять ссылки на товары в визуальном редакторе
 */
tinymce.PluginManager.add('rscategorylink', function(editor, url) {
    let id = 'rs-category-link-div' + editor.id;

    $('<div id="' + id + '" class="hide-group-cb hide-product-cb"><a class="select-button hidden"></a></div>')
        .appendTo('body');

    $('#' + id).selectProduct({
        dialog: 'categoryLinkDialog-' + editor.id,
        urls: editor.settings.rsProductDialog.url,
        onResult: function(params) {
            var element = params.openDialogEvent.target;
            let selectItem = $('#categoryLinkDialog-' + editor.id + ' .admin-category .act');
            let catTitle = selectItem.text();
            let catId = selectItem.closest('[qid]').attr('qid');

            //Запрашиваем у сервера ссылку на товар по ID
            $.ajaxQuery({
                url: editor.settings.rsProductDialog.url.getCategoryLink,
                data: {
                    "dir_id": catId,
                    "link_type": editor.settings.rsProductDialog.linkType
                },
                success: function (response) {
                    if (response.success) {
                        let link = $('<a>')
                            .attr('href', response.absolute_url)
                            .text(catTitle).prop('outerHTML');

                        editor.execCommand('mceInsertContent', false, link + ' ');
                    }
                }
            });
        }
    });

    let openDialog = function(editor) {
        $('#' + id + ' .select-button').click();
    }

    /* Добавляем кнопку */
    editor.ui.registry.addButton('rscategorylink', {
        tooltip: lang.t('Вставить ссылку на категорию товаров'),
        icon: 'non-breaking',
        onAction: function () {
            openDialog(editor);
        }
    });

    return {
        getMetadata: function () {
            return {
                name: 'ReadyScript category link plugin',
                url: 'https://readyscript.ru'
            };
        }
    }
});