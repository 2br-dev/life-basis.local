(function($) {
    $.fn.chatHistory = function(method) {
        let defaults = {
            inputSelector: '#messageInput',
            sendBtnSelector: '#sendBtn',
            entriesContainerSelector: '.task-log__entries',
            scrollToBottomBtnSelector: '#scrollToBottomBtn',
            newMessageCountSelector: '#newMessageCount',
            replyPreviewSelector: '.reply-preview',
            replyAuthorSelector: '.reply-author',
            replyTextSelector: '.reply-text',
            cancelReplySelector: '.cancel-reply',
            sendMessageUrl: '',
            updateUrl: '',
            taskId: null,
            maxRows: 3,
        },
        args = arguments;

        return this.each(function() {
            let $this = $(this),
                data = $this.data('chatHistory');

            let methods = {
                init: function(initoptions) {
                    if (data) return;

                    data = {};
                    $this.data('chatHistory', data);

                    methods.initParams(initoptions);

                    methods.initListeners();

                    methods.updateTextareaSize();
                },

                initParams: function(initoptions) {
                    data.opt = $.extend({}, defaults, initoptions, {
                        sendMessageUrl: $this.data('sendMessageUrl') || defaults.sendMessageUrl,
                        updateUrl: $this.data('updateUrl') || defaults.updateUrl,
                        taskId: $this.data('taskId') || defaults.taskId,
                        rootId: $this.data('rootId') || defaults.taskId,
                        lastId: $this.data('lastId') || null,
                        unreadChatMessagesCount: $this.data('unreadChatMessagesCount') || 0,
                        firstId: $this.data('firstId') || null,
                        canUpdate: $this.data('canUpdate') || false,
                    });

                    data.$input = $this.find(data.opt.inputSelector);
                    data.$sendBtn = $this.find(data.opt.sendBtnSelector);
                    data.$entries = $this.find(data.opt.entriesContainerSelector);
                    data.$scrollBtn = $(defaults.scrollToBottomBtnSelector);
                    data.$scrollCount = $(defaults.newMessageCountSelector);
                    data.$replyPreview = $this.find(defaults.replyPreviewSelector);
                    data.$replyAuthor = data.$replyPreview.find(defaults.replyAuthorSelector);
                    data.$replyText = data.$replyPreview.find(defaults.replyTextSelector);

                    data.chatActive = false;
                    data.loadingOlder = false;
                    data.loadLastMessagesInProccess = false;
                    data.noMoreMessages = false;
                    data.scrollingToQuotedMessage = false;
                    data.filesUploading = false;
                    data.isSendingMessage = false;

                    data.replyTo = null;
                    data.updateInterval = null;

                    data.newMessageCount = 0;
                    data.newMessages = [];

                    if (data.$input.length) {
                        let computedStyle = window.getComputedStyle(data.$input[0]);
                        data.opt.height = parseFloat(computedStyle.height);
                    }

                    if (data.opt.unreadChatMessagesCount > 0) {
                        methods.updateChatTabBadge(data.opt.unreadChatMessagesCount);
                    }
                },

                initListeners: function() {
                    data.$input.on('input', function() {
                        methods.updateSendBtnState();
                        methods.updateTextareaSize();
                    });

                    data.$sendBtn.on('click', function() {
                        methods.sendMessage();
                    });

                    data.$input.on('keydown', function(e) {
                        if (e.key === 'Enter') {
                            if (e.ctrlKey || e.metaKey) { // Ctrl (Windows/Linux) или ⌘ (Mac)
                                e.preventDefault();
                                if (!data.filesUploading) {
                                    methods.sendMessage();
                                }
                            } else {
                                methods.updateTextareaSize();
                            }
                        }
                    });

                    let lastScrollTop = 0;
                    data.$entries.on('scroll', function () {
                        const current = data.$entries.scrollTop();

                        if (current < 100 && current < lastScrollTop && !data.loadingOlder && !data.noMoreMessages) {
                            methods.loadOlderMessages();
                        }

                        if (current < lastScrollTop && !methods.isScrolledToBottom()) {
                            data.$scrollBtn.removeClass('hide');
                        }

                        if (methods.isScrolledToBottom()) {
                            data.$scrollBtn.addClass('hide');
                            data.$scrollBtn.find('.message-count').addClass('hide');
                            data.newMessageCount = 0;
                            methods.showNewMessages();
                        }

                        lastScrollTop = current;
                    });


                    data.$entries.on('mousedown', function () {
                        if (data.scrollingToQuotedMessage) {
                            data.scrollingToQuotedMessage = false;
                        }
                    });


                    data.$scrollBtn.on('click', function () {
                        methods.scrollToBottom();
                        data.newMessageCount = 0;
                        data.$scrollBtn.addClass('hide');
                        methods.showNewMessages();
                    });

                    $('body').on('shown.bs.tab', function(e) {
                        if (e.target.innerHTML.indexOf('Чат') >= 0) {
                            data.chatActive = true;
                            methods.scrollToBottom(false);
                            methods.updateChatTabBadge(0);

                            const boardItems = $('#crm-board').find(`.board-new-message-element[data-root-id="${data.opt.rootId}"]`);
                            if (boardItems.length) {
                                boardItems.each(function() {
                                    $(this).remove();
                                });
                            }

                            if (!data.updateInterval) {
                                methods.loadLastMessages(true);
                                methods.startUpdating();
                            }
                        } else {
                            data.chatActive = false;
                            if (data.updateInterval) {
                                clearInterval(data.updateInterval);
                                data.updateInterval = null;
                            }
                        }
                    });

                    $this.closest('.ui-dialog').on('dialogclose', function() {
                        data.chatActive = false;
                        if (data.updateInterval) {
                            clearInterval(data.updateInterval);
                            data.updateInterval = null;
                        }
                    });

                    $this.on('click', '.reply-icon', function () {
                        const $entry = $(this).closest('.task-log__entry');
                        const id = $entry.data('id');
                        const author = $entry.find('.meta').text().split('•')[0].trim();
                        const messageText = $entry.find('.text').text().trim().substring(0, 100);

                        data.replyTo = {
                            id,
                            author,
                            text: messageText
                        };

                        data.$replyAuthor.text(author);
                        data.$replyText.text(messageText);
                        data.$replyPreview.removeClass('hide');
                    });

                    data.$replyPreview.find(defaults.cancelReplySelector).on('click', function () {
                        data.replyTo = null;
                        data.$replyPreview.addClass('hide');
                    });

                    $this.on('click', '.quoted-message', function () {
                        const targetId = $(this).data('id');
                        data.scrollingToQuotedMessage = true;
                        methods.scrollToMessageRecursive(targetId);
                    });

                    methods.waitForDropzone('.rs-dropzone', function(dz) {
                        dz.on('addedfile', function() {
                            data.filesUploading = true;
                            $this.trigger('disableBottomToolbar', 'task-chat-message');
                            data.$sendBtn.prop('disabled', true);
                        });

                        dz.on('queuecomplete', function() {
                            data.filesUploading = false;
                            methods.updateSendBtnState();
                        });
                    });


                },

                updateChatTabBadge: function (count) {
                    const $chatTab = $('.tab-nav li a').filter(function() {
                        return $(this).text().trim().indexOf('Чат') >= 0;
                    });

                    if (!$chatTab.length) return;

                    $chatTab.find('.hi-count.visible.badge-r').remove();

                    if (count > 0) {
                        const $badge = $('<span class="hi-count visible badge-r"></span>').text(count);
                        $chatTab.append($badge);
                    }
                },

                waitForDropzone: function(selector, callback, timeout = 10000, interval = 100) {
                    const start = Date.now();

                    const checkInterval = setInterval(() => {
                        const el = document.querySelector(selector);

                        if (el && el.dropzone) {
                            clearInterval(checkInterval);
                            callback(el.dropzone);
                        }

                        if (Date.now() - start > timeout) {
                            clearInterval(checkInterval);
                        }
                    }, interval);
                },

                updateSendBtnState: function() {
                    const hasText = data.$input.val().trim().length > 0;
                    const hasAttachments = $('.dropzone-preview input[name="attachments[]"]').length > 0;

                    const shouldEnable = !data.filesUploading && hasText && (hasText || hasAttachments);
                    data.$sendBtn.prop('disabled', !shouldEnable);
                    if (shouldEnable) {
                        $this.trigger('disableBottomToolbar', 'task-chat-message');
                    }else {
                        $this.trigger('enableBottomToolbar', 'task-chat-message');
                    }
                },

                getOrCreateDateBlock: function(date, prepend = false) {
                    const $container = data.$entries;
                    let $dateBlock = $container.find(`.log-date__separator[data-date="${date}"]`);

                    if (!$dateBlock.length) {
                        const dateParts = date.split('-'); // формат: YYYY-MM-DD
                        const formattedDate = `${dateParts[2]}.${dateParts[1]}.${dateParts[0]}`;
                        const dateHtml = $(`<div class="log-date__separator" data-date="${date}">${formattedDate}</div>`);

                        if (prepend) {
                            const $firstBlock = $container.children('.log-date__separator, .task-log__entry').first();
                            if ($firstBlock.length) {
                                dateHtml.insertBefore($firstBlock);
                            } else {
                                $container.append(dateHtml);
                            }
                        } else {
                            $container.append(dateHtml);
                        }

                        $dateBlock = dateHtml;
                    }

                    return $dateBlock;
                },

                sendMessage: function() {
                    if (data.isSendingMessage) return;

                    let message = data.$input.val().trim();
                    let hasAttachments = $('.dropzone-preview input[name="attachments[]"]').length > 0;

                    if (!message && !hasAttachments) return;

                    data.isSendingMessage = true;
                    data.$sendBtn.prop('disabled', true);

                    let attachments = [];
                    $('.dropzone-preview input[name="attachments[]"]').each(function() {
                        attachments.push($(this).val());
                    });

                    $.ajaxQuery({
                        url: data.opt.sendMessageUrl,
                        type: 'post',
                        data: {
                            task_id: data.opt.taskId,
                            message: message,
                            reply_to_id: data.replyTo?.id || null,
                            attachments: attachments
                        },
                        success: function(response) {
                            if (response.success && response.html) {
                                const now = new Date();
                                const dateKey = now.toISOString().split('T')[0];
                                methods.getOrCreateDateBlock(dateKey, false);
                                data.$entries.append(response.html);
                                data.$input.val('');
                                data.replyTo = null;
                                data.$replyPreview.addClass('hide');
                                $('.dropzone-preview').empty();
                                methods.scrollToBottom();
                            }
                        },
                        complete: function() {
                            data.isSendingMessage = false;
                            methods.updateSendBtnState();
                            methods.updateTextareaSize();
                            methods.startUpdating();
                        }
                    });
                },

                updateTextareaSize: function () {
                    if (!data.$input || !data.$input[0]) return;

                    data.$input.css({
                        height: 'auto',
                        minHeight: '68px',
                        overflowY: 'hidden'
                    });

                    let scrollHeight = data.$input[0].scrollHeight;
                    let maxHeight = data.opt.maxRows * data.opt.height;
                    let newHeight = Math.min(scrollHeight, maxHeight);

                    data.$input.css({
                        height: newHeight + 'px',
                        overflowY: scrollHeight > maxHeight ? 'auto' : 'hidden'
                    });
                },


                startUpdating: function () {
                    if (data.updateInterval) {
                        clearInterval(data.updateInterval);
                    }
                    data.updateInterval = setInterval(methods.loadLastMessages, 10000);
                },

                loadLastMessages: function(markAsViewed = false) {
                    if (!data.chatActive || data.loadLastMessagesInProccess) return;

                    data.loadLastMessagesInProccess = true;

                    $.ajaxQuery({
                        url: data.opt.updateUrl,
                        data: {
                            task_id: data.opt.taskId,
                            after_id: data.opt.lastId || 0,
                            mark_as_viewed: markAsViewed || null
                        },
                        success: function(response) {
                            if (response.success && response.data?.messages) {
                                methods.renderMessages(response.data, { prepend: false });
                            }
                        },
                        complete: function() {
                            data.loadLastMessagesInProccess = false;
                        }
                    });
                },

                loadOlderMessages: function (callback) {
                    if (data.loadingOlder || data.noMoreMessages) {
                        if (callback) callback();
                        return;
                    }

                    data.loadingOlder = true;

                    $.ajaxQuery({
                        url: data.opt.updateUrl,
                        data: {
                            task_id: data.opt.taskId,
                            before_id: data.opt.firstId || 0
                        },
                        success: function (response) {
                            let hadMessages = false;
                            if (response.success && response.data?.messages) {
                                hadMessages = Object.keys(response.data.messages).length > 0;
                                methods.renderMessages(response.data, { prepend: true });

                                if (!hadMessages) {
                                    data.noMoreMessages = true;
                                }
                            } else {
                                data.noMoreMessages = true;
                            }
                        },
                        complete: function () {
                            data.loadingOlder = false;
                            methods.startUpdating();
                            if (callback) callback(); // ВАЖНО!
                        }
                    });
                },

                showNewMessages: function () {
                    data.newMessages.forEach($el => $el.removeClass('flash-message'));
                    data.newMessages.forEach($el => $el.addClass('flash-message'));

                    setTimeout(() => {
                        data.newMessages.forEach($el => $el.removeClass('flash-message'));
                        data.newMessages = [];
                    }, 3000);
                },

                isScrolledToBottom: function () {
                    const el = data.$entries[0];
                    return el.scrollHeight - el.scrollTop <= el.clientHeight + 20;
                },

                renderMessages: function (responseData, { prepend = false } = {}) {
                    if (!responseData || !responseData.messages) return;

                    const $container = data.$entries;

                    let previousScrollHeight, previousScrollTop;
                    if (prepend) {
                        previousScrollTop = $container.scrollTop();
                        previousScrollHeight = $container[0].scrollHeight;
                    }

                    const dates = Object.keys(responseData.messages).reverse();

                    dates.forEach(date => {
                        let messages = responseData.messages[date];

                        messages.sort((a, b) => a.time.localeCompare(b.time));
                        if (prepend) {
                            messages = messages.slice().reverse();
                        }

                        const $dateBlock = methods.getOrCreateDateBlock(date, prepend)

                        messages.forEach(msg => {
                            if ($container.find(`.task-log__entry[data-id="${msg.message_id}"]`).length) return;

                            const $msg = methods.createMessageElement(msg);
                            if (!prepend) {
                                data.newMessages.push($msg);
                            }

                            if (prepend) {
                                const $firstMsgOfDate = $dateBlock.nextUntil('.log-date__separator', '.task-log__entry').first();
                                if ($firstMsgOfDate.length) {
                                    $msg.insertBefore($firstMsgOfDate);
                                } else {
                                    $msg.insertAfter($dateBlock);
                                }
                            } else {
                                const $lastMsgOfDate = $dateBlock.nextUntil('.log-date__separator', '.task-log__entry').last();
                                if ($lastMsgOfDate.length) {
                                    $msg.insertAfter($lastMsgOfDate);
                                } else {
                                    $msg.insertAfter($dateBlock);
                                }
                            }
                        });
                    });

                    if (!prepend) {
                        if (!prepend && !methods.isScrolledToBottom()) {
                            data.newMessageCount = data.newMessages.length;
                            data.$scrollCount.text(data.newMessageCount);
                            if (data.newMessageCount > 0) {
                                data.$scrollBtn.find('.message-count').removeClass('hide');
                                data.$scrollBtn.removeClass('hide');
                            }
                        } else {
                            data.newMessageCount = 0;
                            data.$scrollBtn.addClass('hide');
                            data.$scrollBtn.find('.message-count').addClass('hide');
                        }
                    }

                    if (prepend && previousScrollHeight != null) {
                        const newScrollHeight = $container[0].scrollHeight;
                        const scrollDiff = newScrollHeight - previousScrollHeight;
                        $container.scrollTop(previousScrollTop + scrollDiff);
                    }

                    if (responseData.first_id && prepend) {
                        data.opt.firstId = parseInt(responseData.first_id);
                        $this.data('firstId', data.opt.firstId).attr('data-first-id', data.opt.firstId);
                    }
                    if (responseData.last_id && !prepend) {
                        data.opt.lastId = parseInt(responseData.last_id);
                        $this.data('lastId', data.opt.lastId).attr('data-last-id', data.opt.lastId);
                    }
                },

                createMessageElement: function (msg) {
                    const time = $('<span>').text(msg.time).html();
                    const text = msg.message;

                    if (msg.type === 'system') {
                        let changesHtml = '';
                        if (Array.isArray(msg.changes) && msg.changes.length > 0) {
                            changesHtml += '<ul>';
                            msg.changes.forEach(change => {
                                let before = change.before != null
                                    ? $('<span>').text(change.before).html()
                                    : '<em>пусто</em>';

                                let after = change.after != null
                                    ? '<strong>' + $('<span>').text(change.after).html() + '</strong>'
                                    : '<strong><em>пусто</em></strong>';

                                if (change.field === 'checklist' && change.summary) {
                                    const summary = $('<span>').text(change.summary).html();
                                    changesHtml += `<li><strong>${$('<span>').text(change.title).html()}</strong>: ${summary}</li>`;
                                } else {
                                    changesHtml += `<li><strong>${$('<span>').text(change.title).html()}</strong>: ${before} → ${after}</li>`;
                                }
                            });
                            changesHtml += '</ul>';
                        }

                        const messageHtml = `
                            <div class="task-log__entry system" data-id="${msg.message_id}">
                                ${text}
                                ${changesHtml}
                            </div>
                        `;
                        return $(messageHtml);
                    }

                    const userName = msg.user?.name ? $('<span>').text(msg.user.name).html() : 'Неизвестный';
                    const alignmentClass = msg.user?.is_current_user ? 'right' : 'left';

                    let replyHtml = '';
                    if (msg.reply_to) {
                        const replyAuthor = $('<div>').text(msg.reply_to.user.name).html();
                        const replyText = $('<div>').text(msg.reply_to.message).html().substring(0, 100);

                        replyHtml = `
                            <div class="quoted-message" data-id="${msg.reply_to.message_id}">
                                <div class="quoted-author">${replyAuthor}</div>
                                <div class="quoted-text">${replyText}</div>
                            </div>
                        `;
                    }

                    let attachmentsHtml = '';
                    if (Array.isArray(msg.attachments) && msg.attachments.length > 0) {
                        attachmentsHtml += '<div class="attachments m-t-10"><div class="ticket-attachments m-t-10">';
                        msg.attachments.forEach(file => {
                            const fileName = $('<span>').text(file.name).html();
                            const fileUrl = $('<a>').attr('href', file.url).attr('target', '_blank').html(`
                                <i class="zmdi zmdi-attachment-alt"></i>
                                <span>${fileName}</span>
                            `).prop('outerHTML');

                            const fileSize = methods.formatFileSize(parseFloat(file.size));
                            attachmentsHtml += `
                                <div class="ticket-file m-b-5">
                                    ${fileUrl}, ${fileSize}
                                </div>
                            `;
                        });
                        attachmentsHtml += '</div></div>';
                    }

                    let replyIconHtml = '';
                    if (data.opt.canUpdate) {
                        replyIconHtml = `
                            <span class="reply-icon" title="Ответить">
                                <i class="zmdi zmdi-hc-lg zmdi-mail-reply"></i>
                            </span>
                        `;
                    }


                    const messageHtml = `
                        <div class="task-log__entry message ${alignmentClass}" data-id="${msg.message_id}">
                            <div class="bubble">
                                ${replyHtml}
                                <div class="meta">${userName} • ${time}</div>
                                <div class="text">${$('<div>').text(msg.message).html()}</div>
                                ${attachmentsHtml}
                                ${replyIconHtml}
                            </div>
                        </div>
                   `;
                    return $(messageHtml);
                },

                formatFileSize: function (bytes) {
                    if (bytes === 0) return '0 Б';
                    const sizes = ['Б', 'КБ', 'МБ', 'ГБ'];
                    const i = Math.floor(Math.log(bytes) / Math.log(1024));
                    return parseFloat((bytes / Math.pow(1024, i)).toFixed(1)) + ' ' + sizes[i];
                },

                scrollToMessageRecursive: function (targetId, attempt = 1) {
                    if (
                        attempt > 20 ||
                        data.noMoreMessages ||
                        !data.scrollingToQuotedMessage
                    ) return;

                    const $target = $this.find(`.task-log__entry[data-id="${targetId}"]`);
                    if ($target.length) {
                        $target[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                        $target.addClass('flash-message');
                        setTimeout(() => $target.removeClass('flash-message'), 3000);
                        data.scrollingToQuotedMessage = false;
                        return;
                    }

                    data.$entries.animate({ scrollTop: 0 }, 200, () => {
                        methods.loadOlderMessages(() => {
                            if (!data.scrollingToQuotedMessage) return;

                            setTimeout(() => {
                                methods.scrollToMessageRecursive(targetId, attempt + 1);
                            }, 300); // можно даже сократить задержку
                        });
                    });
                },

                scrollToBottom: function(animate = true) {
                    if (!data?.$entries?.length) return;

                    const $el = data.$entries;
                    const target = $el[0].scrollHeight;

                    if (animate) {
                        data.opt.isProgrammaticScroll = true;

                        $el.stop().animate(
                            { scrollTop: target },
                            300, // продолжительность анимации
                            () => {
                                data.opt.isProgrammaticScroll = false;
                            }
                        );
                    } else {
                        $el.scrollTop(target);
                    }
                }
            };

            if (methods[method]) {
                methods[method].apply(this, Array.prototype.slice.call(args, 1));
            } else if (typeof method === 'object' || !method) {
                return methods.init.apply(this, args);
            }
        });
    };
})(jQuery);
