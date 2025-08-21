$.contentReady(function() {
    //Отрабатыает нажатие на быструю ссылку недавнего объекта
    linkLastThis = function(event) {
        var context = $(this).closest('.link-orders-block');
        var form = $('form:first', context);

        var id = $(this).closest('[data-id]').data('id');
        var title = $('.link-last-title', this).text();

        var findInput = $('[data-name]', context);
        var hiddenName = findInput.data('name');

        findInput.val(title);

        var inputHidden = findInput.siblings('input[name="' + hiddenName + '"]');
        if (inputHidden.length) {
            inputHidden.val(id);
        } else {
            $('<input type="hidden">').attr('name', hiddenName).val(id).insertAfter(findInput);
        }
        form.submit();
    };

    $('.link-orders-block').on('click', '.link-last-this', linkLastThis);
});