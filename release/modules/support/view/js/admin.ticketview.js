/**
 * Скрипт, инициализирует автоматическое обновление сообщений в административной панели
 */
document.addEventListener('DOMContentLoaded', function() {
    class TicketView
    {
        initAutoUpdate()
        {
            let messagesContainer = document.querySelector('.ticket-messages');
            if (messagesContainer.dataset.enableAutoupdate) {
                this.interval = setInterval(() => this.getNewMessages(), 10000);
            }
        }

        getNewMessages()
        {
            let messagesContainer = document.querySelector('.ticket-messages');

            $.ajaxQuery({
                url: messagesContainer.dataset.refreshUrl,
                loadingProgress:false,
                data: {
                    'last_message_id': messagesContainer.dataset.lastMessageId,
                    'topic_id': messagesContainer.dataset.topicId
                },
                success: (response) => {
                    if (response.count > 0) {
                        let div = document.createElement('div');
                        div.innerHTML = response.html;
                        div.querySelectorAll('.ticket-card').forEach((element) => {
                            element.classList.add('new');
                        })
                        messagesContainer.insertAdjacentHTML('beforeend', div.innerHTML);
                        messagesContainer.dataset.lastMessageId = response.last_message_id;

                        if (messagesContainer.dataset.newMessageMp3Url) {
                            new Audio(messagesContainer.dataset.newMessageMp3Url).play();
                        }
                    }
                }
            });
        }


        onDocumentReady()
        {
            this.initAutoUpdate();
        }
    }

    let ticketView = new TicketView();
    ticketView.onDocumentReady();
});
