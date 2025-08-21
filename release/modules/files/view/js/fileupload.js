/**
 * Класс инициализирует работу блоков загрузки файлов
 * Зависит от dropzone.js
 * Инициализирует HTML разметку <div class="dropzone"></div>
 */
new class FileUpload
{
    constructor(settings)
    {
        Dropzone.autoDiscover = false;

        let defaults = {
            dropzone: '.rs-dropzone',
            dropzoneOptions: {
                uploadMultiple: true,
                paramName: 'file',
                addRemoveLinks: true,
                createImageThumbnails:false,
                parallelUploads:1,

                dictDefaultMessage: lang.t('Перетащите сюда Ваши файлы'),
                dictRemoveFile: lang.t(''),
                dictCancelUpload: lang.t(''),
                dictCancelUploadConfirmation: lang.t('Вы действительно желаете отменить загрузку файла?'),
                dictMaxFilesExceeded: lang.t('Вы не можете загрузить больше файлов'),
                dictResponseError: lang.t('Сервер вернул код ответа: {{statusCode}}'),
                dictInvalidFileType: lang.t('Вы не можете загрузить файл такого типа'),
                dictFileTooBig: lang.t('Файл очень большой ({{filesize}}Мб). Максимально допустимый файл: {{maxFilesize}}Мб.'),
                dictFallbackMessage: lang.t('Ваш браузер не поддерживает drag-n-drop загрузку файлов'),

                previewTemplate: `<div class="dz-preview dz-file-preview">
                        <div class="dz-left-part">
                            <div class="dz-filename"><a data-dz-name></a></div>
                            <div class="dz-size"><span data-dz-size></span></div>
                            <div class="dz-progress" title="`+lang.t('Процент загрузки')+`">
                                <span class="dz-upload" data-dz-uploadprogress>0</span>
                            </div>
                            <div class="dz-error-message" data-dz-errormessage></div>
                        </div>
                    </div>`,

                uploadprogress(file, progress, bytesSent) {
                    if (file.previewElement) {
                        for (let node of file.previewElement.querySelectorAll(
                            "[data-dz-uploadprogress]"
                        )) {
                            progress = parseInt(progress);
                            node.style.setProperty('--progress', `${progress}%`);
                            node.innerText = progress;
                        }
                    }
                },

                error(file, message) {
                    if (file.previewElement) {
                        file.previewElement.classList.add("dz-error");
                        if (typeof message !== "string" && message.error) {
                            message = message.error;
                        }
                        for (let node of file.previewElement.querySelectorAll(
                            "[data-dz-errormessage]"
                        )) {
                            file.popover = new bootstrap.Popover(node, {
                                content: message
                            });
                        }
                    }
                }
            }
        };
        this.settings = {...defaults, ...settings};

        document.addEventListener('DOMContentLoaded', (event) => {
            this.initDropZones(event);
        });

        document.addEventListener('new-content', (event) => {
            this.initDropZones(event);
        });

        if (window.$) { //Для совместимости с административной панелью
            $(document).on('new-content', (event) => {
                this.initDropZones(event);
            });
        }
    }

    initDropZones(event)
    {
        event.target.querySelectorAll(this.settings.dropzone).forEach((zone) => {
            if (zone.dataset.dropzoneinit) {
                return;
            }
            zone.dataset.dropzoneinit = true;

            let previewContainer = zone.parentNode.querySelector('.dropzone-preview');
            let options = {
                url: zone.dataset.uploadUrl,
                previewsContainer: previewContainer,
                accept: function(file, done) {
                    if (this.options.maxFiles && this.options.previewsContainer.children.length > this.options.maxFiles) {
                        return done(this.options.dictMaxFilesExceeded);
                    }
                    return done();
                },
                ...this.settings.dropzoneOptions
            };

            if (zone.dataset.maxFilesizeMb) {
                options.maxFilesize = zone.dataset.maxFilesizeMb;
            }

            if (zone.dataset.acceptedFiles) {
                options.acceptedFiles = zone.dataset.acceptedFiles;
            }

            if (zone.dataset.maxFiles) {
                options.maxFiles = zone.dataset.maxFiles;
            }

            if (zone.dataset.dropzoneOptions) {
                options = {...options, ...JSON.parse(zone.dataset.dropzoneOptions)};
            }

            let dropZone = new Dropzone(zone, options);

            //Удаляет файл на сервере
            dropZone.on('removedfile', (file) => {
                if (file.fileId) {
                    let url = new URL(zone.dataset.removeUrl, document.baseURI);
                    url.searchParams.append('public_hash', file.fileId);
                    this.fetchJSON(url);
                }

                if (file.popover) {
                    file.popover.dispose();
                }
            });

            //Добавляет данные о загруженном файле в форму
            dropZone.on('success', (file, response) => {
                let data = JSON.parse(response);
                if (!data) {
                    data = {success:false, error: lang.t('Неизвестный ответ сервера')};
                }

                if (data.success) {
                    file.fileId = data.public_hash;

                    let hashInput = document.createElement('input');
                    hashInput.type = 'hidden';
                    hashInput.name = zone.dataset.inputName + '[]';
                    hashInput.value = data.public_hash;
                    file.previewElement.append(hashInput);

                    file.previewElement.querySelectorAll('[data-dz-name]')
                        .forEach(it => {
                            it.href = data.link;
                            it.target = '_blank';
                        });
                } else {
                    file.status = Dropzone.ERROR;
                    dropZone.emit("error", file, data.error);
                    return false;
                }
            });

            //Удаляет ранее загруженные файлы
            previewContainer.querySelectorAll('[data-remove-file]').forEach(it => {
                it.addEventListener('click', event => {
                    let preview = event.target.closest('.dz-preview');
                    let url = new URL(zone.dataset.removeUrl, document.baseURI);
                    url.searchParams.append('public_hash', preview.dataset.publicHash);
                    this.fetchJSON(url);
                    preview.remove();
                });
            });

            zone.dataset.dropZone = dropZone;
        });
    }

    /**
     * Выполняет запрос к удаленному серверу и ожидает от него ответ в формате JSON. Обертка над системной fetch.
     *
     * @param url
     * @param options
     * @returns {Promise<any>}
     */
    fetchJSON(url, options) {
        let defaults = {
            credentials:'include',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        let parameters = {...defaults, ...options};
        return fetch(url, parameters)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(lang.t('Некорректный статус ответа сервера. ' + response.statusText));
                    return;
                }
                return response.json();
            }).catch((error) => {
                if (error.name !== 'AbortError') {
                    throw error;
                }
            });
    }

};