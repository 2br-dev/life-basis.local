/**
 * Плагин для TinyMCE, который позволяет вставлять ссылки на товары в визуальном редакторе
 */
tinymce.PluginManager.add('rsproductlink', function(editor, url) {
    let id = 'rs-product-link-div' + editor.id;
    $('<div id="' + id + '" class="hide-group-cb hide-product-cb"><a class="select-button hidden"></a></div>')
        .data('urls', editor.settings.rsProductDialog.url)
        .appendTo('body');

    $('#' + id).selectProduct({
        dialog: 'productLinkDialog-' + editor.id,
        selectButtonText: false,
        onSelectProduct: function(params) {
            //Запрашиваем у сервера ссылку на товар по ID
            $.ajaxQuery({
                url: editor.settings.rsProductDialog.url.getProductLink,
                data: {
                    "product_id": params.productId,
                    "link_type": editor.settings.rsProductDialog.linkType
                },
                success: function (response) {
                    let link = $('<a>')
                        .attr('href', response.absolute_url)
                        .text(params.productTitle).prop('outerHTML');

                    editor.execCommand('mceInsertContent', false, link + ' ');
                }
            })

            params.dialog.dialog('close');
        }
    });

    let openDialog = function(editor) {
        $('#'+ id + ' .select-button').click();
    }

    /* Добавляем кнопку */
    editor.ui.registry.addButton('rsproductlink', {
        tooltip: lang.t('Вставить ссылку на товар'),
        icon: 'plus',
        onAction: function () {
            openDialog(editor);
        }
    });

    return {
        getMetadata: function () {
            return {
                name: 'ReadyScript product link plugin',
                url: 'https://readyscript.ru'
            };
        }
    }
});