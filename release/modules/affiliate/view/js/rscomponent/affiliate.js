/**
 * Инииализирует поиск по филиалам
 *
 * Зависит от autoComplete.js
 */
new class Affiliate extends RsJsCore.classes.component
{
    constructor()
    {
        super();
        let defaults = {
            confirmDialog: '.affilliate-confirm',
            confirmClose: '.modal-close',

            inputSearch: '.rs-city-search',
            autocompleteResult: '.rs-autocomplete-result',
            affiliateConfirmTemplate: '#affiliate-confirm-template'
        };

        this.settings = {...defaults, ...this.getExtendsSettings()};
    }

    /**
     * Находит все autocomplete input'ы, вутри newElement и активирует их
     *
     * @param newElement
     */
    initSearchAffiliateInDialog(newElement)
    {
        let input = newElement.querySelector(this.settings.inputSearch);
        let resultWrapper = input && input.parentNode.querySelector(this.settings.autocompleteResult);
        let cancelController;

        if (input && !input.rsInitialized) {
            input.rsInitialized = true;
            let autoCompleteInstance = new autoComplete({
                selector: () => input,
                searchEngine: () => true,
                wrapper:false,
                data: {
                    src: async () => {
                        if (cancelController) cancelController.abort();
                        cancelController = new AbortController();

                        let data = await this.utils.fetchJSON(input.dataset.urlSearch + '&' + new URLSearchParams({
                            term:autoCompleteInstance.input.value
                        }), {
                            signal: cancelController.signal
                        });

                        return data ? data.list : [];
                    },
                    keys:['label']
                },
                resultsList: {
                    class: '',
                    maxResults:20,
                    position:'beforeend',
                    destination:() => resultWrapper,
                },
                resultItem: {
                    element: (element, data) => {
                        let tpl;
                        tpl = `<a class="dropdown-item" href="${data.value.url}">
                                        <div class="col">${data.value.label}</div>
                                    </a>`;
                        element.innerHTML = tpl;
                    },
                    selected: 'selected'
                },
                events: {
                    input: {
                        selection: (event) => {
                            location.href = event.detail.selection.value.url;
                        }
                    }
                }
            });
        }
    }

    /**
     * Проверяем, нужно ли отображать диалог подтверждения определенного филиала.
     * Если да, то отображаем его
     */
    checkNeedConfirmAffiliate() {
        let template = document.querySelector(this.settings.affiliateConfirmTemplate);
        if (template) {
            document.body.appendChild(template.content);
            let win = document.querySelector(this.settings.confirmDialog);
            win.querySelectorAll(this.settings.confirmClose).forEach(it => {
                it.addEventListener('click', event => this.closeConfirmAffiliate(event));
            });
        }
    }

    /**
     * Закрывает окно подтверждения филиала
     *
     * @param event
     */
    closeConfirmAffiliate(event) {
        let context = event.target.closest(this.settings.confirmDialog);
        context && context.remove();

        this.plugins.cookie.setCookie('affiliate_already_select', 1);
    }

    /**
     * Выполняется, когда страница загружается
     */
    onDocumentReady()
    {
        this.checkNeedConfirmAffiliate();
    }

    /**
     * Выполняется, когда на странице появляется новый контент
     *
     * @param event
     */
    onContentReady(event)
    {
        this.initSearchAffiliateInDialog(event.target);
    }
};