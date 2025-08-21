/**
 * Класс позволяет выполнять запросы, которые возвращают результат в виде потока частиц
 */
class StreamFetcher
{
    constructor() {
        this.abortController = new AbortController;
    }

    /**
     * Возвращает контроллер, который может остановить выполнение запроса
     *
     * @returns {AbortController}
     */
    getAbortController()
    {
        return this.abortController;
    }

    /**
     * Устанавливает функцию, которая будет вызвана при получении каждой частицы ответа
     *
     * @param callback
     */
    setStreamCallback(callback)
    {
        this.streamCallback = callback;
        return this;
    }

    /**
     * Выполняет запрос на получение потога генеративных данных
     *
     * @param url
     * @param formData
     * @returns {Promise<any>}
     */
    async fetchStream(url, formData)
    {
        return new Promise(async (resolve, reject) => {
            let jsonData;
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData,
                    signal: this.abortController.signal
                });

                if (response.headers.get('Content-type') === 'application/stream+json') {
                    //Обрабатываем поток данных
                    const reader = response.body.getReader();

                    //Запоминаем предыдущее значение для отмены ввода через CTRL+Z
                    const textDecoder = new TextDecoder();
                    let buffer = '';
                    let fullText = '';
                    let iteration = 0;

                    while (true) {
                        const {done, value} = await reader.read();
                        if (done) break;

                        // Декодируем бинарные данные в текст
                        buffer += textDecoder.decode(value, {stream: true});

                        // Разделяем буфер по \n и обрабатываем каждый JSON
                        const lines = buffer.split('\n');
                        // Оставляем на следующую итерацию, то, что было после последнего \n
                        buffer = lines.pop();

                        for (const line of lines) {
                            if (line.trim() === '') continue; // Пропускаем пустые строки
                            try {
                                jsonData = JSON.parse(line);
                                if (jsonData.balance) {
                                    //Сообщаем другим модулям о новом балансе
                                    window.dispatchEvent(new CustomEvent('gptBalanceChange', {
                                        bubbles:true,
                                        detail: {
                                            balance: jsonData.balance,
                                            source: 'button'
                                        }
                                    }));
                                }
                                fullText = fullText + jsonData.text;
                                if (this.streamCallback) {
                                    this.streamCallback(fullText, jsonData, iteration);
                                }
                                iteration++;
                            } catch (error) {
                                console.log(error);
                            }
                        }
                    }
                    resolve({fullText, lastChunk: jsonData});
                } else {
                    //Обрабатываем обычный не потоковый ответ
                    return response.json().then(jsonData => {
                        $.rs.checkAuthorization(jsonData);
                        $.rs.checkWindowRedirect(jsonData);
                        $.rs.checkMessages(jsonData);
                        if (typeof(jsonData) == 'object' && jsonData.messages) {
                            reject(jsonData.messages[0].text);
                        } else {
                            resolve(jsonData);
                        }
                    });
                }
            } catch(error) {
                if (!(/^AbortError/.test(error))) {
                    $.messenger('show', {
                        theme: 'error',
                        text: error
                    });
                }
                reject(error);
            }
        });
    }
};