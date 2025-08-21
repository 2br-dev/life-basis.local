/**
 * Скрипт, отвечающий за работу диалога просмотра задачи на генерацию контента
 */
(function( $ ){
    $.contentReady(() => {
        $('.task-button.cancel').click(function(event) {
            if (!confirm(lang.t('Вы действительно желаете отменить задачу на генерацию данных?'))) {
                event.stopPropagation();
                event.preventDefault();
            }
        });

        //Запускаем обновление окна каждые 20 сек.
        $('#task-auto-updatable').each(function() {
            let timer = setTimeout(() => {
                $.ajaxQuery({
                    url:$(this).data('updateUrl'),
                    success: (response) => {
                        if (response.html) {
                            let newContent = $(response.html).find('#task-auto-updatable');
                            $(this).replaceWith(newContent);
                            newContent.trigger('new-content');
                        }
                    }
                });
            }, 20000);

            let dialog = $(this).closest('.ui-dialog');
            dialog.on('dialogclose', function() {
                clearTimeout(timer);
            });
        });
    });
})(jQuery);