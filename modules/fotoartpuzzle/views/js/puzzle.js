(function () {
    'use strict';

    const wizard = document.getElementById('fap-wizard');
    if (!wizard) {
        return;
    }

    const launchButton = wizard.querySelector('.fap-launch');
    if (!launchButton) {
        return;
    }

    // Configurazione e URLs
    const config = parseConfig(wizard.dataset.config || '{}');
    const uploadUrl = wizard.dataset.uploadUrl;
    const previewUrl = wizard.dataset.previewUrl;
    const summaryUrl = wizard.dataset.summaryUrl;
    const ajaxUrl = wizard.dataset.ajaxUrl || '';
    const tokens = {
        upload: wizard.dataset.tokenUpload || '',
        preview: wizard.dataset.tokenPreview || '',
        summary: wizard.dataset.tokenSummary || '',
    };

    if (!uploadUrl || !previewUrl || !summaryUrl) {
        console.error('FotoArt Puzzle: URLs mancanti');
        return;
    }

    // Trova il form add-to-cart
    const addToCartForm = document.querySelector('#add-to-cart-or-refresh') || 
                          document.querySelector('form[action*="cart"]') ||
                          document.querySelector('.product-add-to-cart form');
    
    const idProductInput = addToCartForm ? addToCartForm.querySelector('input[name="id_product"]') : null;
    
    if (!addToCartForm || !idProductInput) {
        console.error('FotoArt Puzzle: Form add-to-cart non trovato');
        return;
    }

    const idProduct = parseInt(idProductInput.value, 10);

    // Pannello riepilogo
    const summaryPanel = createSummaryPanel();
    wizard.appendChild(summaryPanel.element);

    // Stato wizard
    const state = {
        currentStepIndex: 0,
        file: null,
        fileUrl: null,
        fileName: '',
        fileWidth: 0,
        fileHeight: 0,
        orientation: null,
        formats: [],
        puzzles: [],
        boxes: [],
        printable: false,
        format: null,
        selectedBox: null,
        boxText: config.box && config.box.defaultText ? config.box.defaultText : 'Il mio puzzle',
        boxColor: (config.box && config.box.colors && config.box.colors[0]) || '#FFFFFF',
        boxFont: (config.box && config.box.fonts && config.box.fonts[0]) || 'Roboto',
        previewUrl: null,
        previewPath: null,
        thumbnailUrl: null,
        thumbnailPath: null,
        previewDirty: false,
        uploading: false,
        previewLoading: false,
        summaryLoading: false,
        message: null,
        customizationId: null,
    };

    const steps = [
        { key: 'upload', title: translate('Carica la tua immagine') },
        { key: 'format', title: translate('Scegli il formato del puzzle') },
        { key: 'box', title: translate('Personalizza la scatola') },
        { key: 'preview', title: translate('Anteprima e conferma') },
    ];

    // Crea modale
    const modal = createModal();
    document.body.appendChild(modal.root);

    // Event listeners
    launchButton.addEventListener('click', openModal);
    modal.closeButton.addEventListener('click', closeModal);
    modal.backdrop.addEventListener('click', closeModal);
    modal.prevButton.addEventListener('click', function () {
        goToStep(state.currentStepIndex - 1);
    });
    modal.nextButton.addEventListener('click', handleStepForward);
    modal.finishButton.addEventListener('click', finalizeCustomization);

    preloadReferenceData();

    let escapeHandler = null;

    function openModal() {
        state.currentStepIndex = 0;
        state.message = null;
        renderStep();
        modal.root.classList.add('is-visible');
        modal.root.setAttribute('aria-hidden', 'false');
        launchButton.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
        
        escapeHandler = function (event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        };
        document.addEventListener('keydown', escapeHandler);
    }

    function closeModal() {
        modal.root.classList.remove('is-visible');
        modal.root.setAttribute('aria-hidden', 'true');
        launchButton.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
        
        if (escapeHandler) {
            document.removeEventListener('keydown', escapeHandler);
            escapeHandler = null;
        }
    }

    function goToStep(index) {
        const safeIndex = Math.max(0, Math.min(index, steps.length - 1));
        if (safeIndex === state.currentStepIndex && modal.content.hasChildNodes()) {
            return;
        }
        state.currentStepIndex = safeIndex;
        if (steps[state.currentStepIndex].key !== 'preview') {
            state.message = null;
        }
        renderStep();
    }

    function renderStep() {
        const step = steps[state.currentStepIndex];
        modal.title.textContent = step.title;
        modal.content.innerHTML = '';

        if (state.message) {
            modal.content.appendChild(renderMessage(state.message));
        }

        const stepWrapper = document.createElement('div');
        stepWrapper.className = 'fap-step fap-step--' + step.key;
        modal.content.appendChild(stepWrapper);

        switch (step.key) {
            case 'upload':
                renderUploadStep(stepWrapper);
                break;
            case 'format':
                renderFormatStep(stepWrapper);
                break;
            case 'box':
                renderBoxStep(stepWrapper);
                break;
            case 'preview':
                renderPreviewStep(stepWrapper);
                break;
        }

        updateFooter();
    }

    function updateFooter() {
        const isLoading = state.uploading || state.previewLoading || state.summaryLoading;
        
        modal.prevButton.disabled = state.currentStepIndex === 0 || isLoading;
        modal.nextButton.classList.toggle('is-hidden', state.currentStepIndex >= steps.length - 1);
        modal.finishButton.classList.toggle('is-hidden', state.currentStepIndex !== steps.length - 1);

        if (state.currentStepIndex === 0) {
            modal.nextButton.disabled = !state.file || state.uploading;
        } else if (state.currentStepIndex === 1) {
            modal.nextButton.disabled = !state.format;
        } else if (state.currentStepIndex === 2) {
            modal.nextButton.disabled = false;
        }

        if (state.currentStepIndex === steps.length - 1) {
            modal.finishButton.disabled = !state.file || !state.format || isLoading;
        }
    }

    function preloadReferenceData() {
        const fallbackPuzzles = Array.isArray(config.puzzles) && config.puzzles.length
            ? config.puzzles
            : (Array.isArray(config.formats) ? config.formats : []);
        const fallbackBoxes = Array.isArray(config.boxes) ? config.boxes : [];

        if (!state.puzzles.length && fallbackPuzzles.length) {
            state.puzzles = fallbackPuzzles;
        }

        if (!state.boxes.length && fallbackBoxes.length) {
            state.boxes = fallbackBoxes;
        }

        if (!state.selectedBox && state.boxes.length) {
            state.selectedBox = state.boxes[0];
        }

        if (!ajaxUrl) {
            return;
        }

        requestAjax('getPuzzles').then(function (response) {
            if (response && response.success && Array.isArray(response.puzzles)) {
                state.puzzles = response.puzzles;
                if (state.currentStepIndex === 1) {
                    renderStep();
                }
            }
        });

        requestAjax('getBoxes').then(function (response) {
            if (response && response.success && Array.isArray(response.boxes)) {
                var previousId = state.selectedBox && state.selectedBox.id ? String(state.selectedBox.id) : null;
                var nextSelection = null;
                if (previousId) {
                    nextSelection = response.boxes.find(function (item) {
                        return String(item.id) === previousId;
                    }) || null;
                }
                state.boxes = response.boxes;
                if (!nextSelection && state.boxes.length) {
                    nextSelection = state.boxes[0];
                }
                state.selectedBox = nextSelection;
                if (state.currentStepIndex === 2) {
                    renderStep();
                }
            }
        });
    }

    function requestAjax(action) {
        if (!ajaxUrl) {
            return Promise.resolve(null);
        }

        const separator = ajaxUrl.indexOf('?') === -1 ? '?' : '&';
        const url = ajaxUrl + separator + 'action=' + encodeURIComponent(action);

        return fetch(url, { credentials: 'same-origin' })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .catch(function (error) {
                console.warn('FotoArt Puzzle: richiesta AJAX fallita', error);
                return null;
            });
    }

    function renderUploadStep(container) {
        const info = document.createElement('p');
        info.className = 'fap-step__description';
        const extensions = (config.extensions || ['jpg', 'jpeg', 'png']).map(function (ext) {
            return ext.trim().toUpperCase();
        }).filter(Boolean).join(', ');

        info.textContent = translate('Seleziona un\'immagine JPEG o PNG fino a {max} MB. Estensioni consentite: {ext}')
            .replace('{max}', config.maxUploadMb || 25)
            .replace('{ext}', extensions);
        container.appendChild(info);

        const dropzone = document.createElement('label');
        dropzone.className = 'fap-upload-zone';
        dropzone.setAttribute('for', 'fap-upload-input');
        dropzone.setAttribute('tabindex', '0');

        const icon = document.createElement('span');
        icon.className = 'material-icons fap-upload-zone__icon';
        icon.textContent = 'cloud_upload';
        dropzone.appendChild(icon);

        const inputWrapper = document.createElement('div');
        inputWrapper.className = 'fap-field';

        const label = document.createElement('label');
        label.className = 'fap-field__label';
        label.setAttribute('for', 'fap-upload-input');
        label.textContent = translate('Carica immagine');

        const input = document.createElement('input');
        input.type = 'file';
        input.id = 'fap-upload-input';
        input.className = 'form-control fap-upload-input';
        input.accept = (config.extensions || ['jpg', 'jpeg', 'png']).map(function (ext) {
            ext = ext.trim();
            return ext ? (ext[0] === '.' ? ext : '.' + ext) : null;
        }).filter(Boolean).join(',');

        input.addEventListener('change', function (event) {
            const file = event.target.files && event.target.files[0];
            if (!file) {
                return;
            }
            handleFileSelection(file).catch(function (error) {
                setMessage('error', error.message);
            });
        });

        dropzone.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                input.click();
            }
        });

        dropzone.addEventListener('dragover', function (event) {
            event.preventDefault();
            dropzone.classList.add('is-dragover');
        });

        dropzone.addEventListener('dragleave', function () {
            dropzone.classList.remove('is-dragover');
        });

        dropzone.addEventListener('drop', function (event) {
            event.preventDefault();
            dropzone.classList.remove('is-dragover');
            const files = event.dataTransfer && event.dataTransfer.files;
            if (files && files[0]) {
                handleFileSelection(files[0]).catch(function (error) {
                    setMessage('error', error.message);
                });
            }
        });

        const helper = document.createElement('span');
        helper.className = 'fap-upload-zone__helper';
        helper.textContent = translate('Trascina qui la tua immagine oppure clicca per selezionarla');
        dropzone.appendChild(helper);

        dropzone.appendChild(input);

        inputWrapper.appendChild(label);
        inputWrapper.appendChild(dropzone);
        container.appendChild(inputWrapper);

        if (state.uploading) {
            const uploading = document.createElement('p');
            uploading.className = 'fap-status';
            uploading.innerHTML = '<i class="icon-spinner icon-spin"></i> ' + translate('Caricamento in corso...');
            container.appendChild(uploading);
        }

        if (state.file && state.fileName) {
            const details = document.createElement('div');
            details.className = 'fap-upload-summary';
            details.innerHTML = '<strong>' + sanitize(state.fileName) + '</strong>' +
                '<span>' + state.fileWidth + ' x ' + state.fileHeight + ' px</span>';
            if (state.format) {
                details.innerHTML += '<span>' + translate('Formato: ') + sanitize(formatLabel(state.format)) + '</span>';
            }
            container.appendChild(details);
        }
    }

    function renderFormatStep(container) {
        const availableFormats = Array.isArray(state.formats) && state.formats.length
            ? state.formats
            : (state.puzzles && state.puzzles.length ? state.puzzles : (Array.isArray(config.formats) ? config.formats : []));
            : (Array.isArray(config.formats) ? config.formats : []);

        if (!availableFormats.length) {
            const notice = document.createElement('p');
            notice.textContent = translate('Nessun formato puzzle configurato.');
            container.appendChild(notice);
            return;
        }

        const intro = document.createElement('p');
        intro.className = 'fap-step__description';
        intro.textContent = translate('Scegli il formato del puzzle in base alle dimensioni della tua immagine:');
        container.appendChild(intro);

        const list = document.createElement('div');
        list.className = 'fap-format-list';

        availableFormats.forEach(function (item) {
            const card = document.createElement('button');
            card.type = 'button';
            card.className = 'fap-format-card';

            if (state.format && ((state.format.id && item.id && String(state.format.id) === String(item.id)) || state.format.name === item.name)) {

            if (state.format && state.format.name === item.name) {
                card.classList.add('is-selected');
            }

            let html = '<span class="fap-format-card__name">' + sanitize(item.name) + '</span>';

            if (item.pieces) {
                html += '<span class="fap-format-card__pieces">' + sanitize(item.pieces + ' ' + translate('pezzi')) + '</span>';
            }

            const sizeLabel = formatSizeLabel(item);
            if (sizeLabel) {
                html += '<span class="fap-format-card__size">' + sanitize(sizeLabel) + '</span>';
            }

            if (typeof item.quality !== 'undefined') {
                const quality = parseInt(item.quality, 10);
                const qualityLabel = qualityLabelForScore(quality);
                if (qualityLabel) {
                    const qualityColor = quality > 1 ? '#2e7d32' : (quality === 1 ? '#f57c00' : '#d32f2f');
                    html += '<span class="fap-format-card__quality" style="display:block;color:' + qualityColor + ';font-size:0.85em;">'
                        + sanitize(qualityLabel) + '</span>';
                }
                if (quality <= 0) {
                    card.disabled = true;
                    card.style.opacity = '0.5';
                }
            } else if (state.fileWidth && state.fileHeight && item.width && item.height) {
                if (state.fileWidth < item.width || state.fileHeight < item.height) {
                    html += '<span class="fap-format-card__warning" style="color: #d32f2f; font-size: 0.85em;">' +
                        translate('⚠ Immagine troppo piccola') + '</span>';
                    card.disabled = true;
                    card.style.opacity = '0.5';
                } else {
                    html += '<span class="fap-format-card__success" style="color: #2e7d32; font-size: 0.85em;">' +
                        translate('✓ Compatibile') + '</span>';
                }
            }

            card.innerHTML = html;

            card.addEventListener('click', function () {
                if (!card.disabled) {
                    state.format = item;
                    state.previewDirty = true;
                    state.message = null;
                    renderStep();
                }
            });

            list.appendChild(card);
        });

        container.appendChild(list);
    }

    function renderBoxStep(container) {
        const fieldset = document.createElement('div');
        fieldset.className = 'fap-box-options';

        // Campo testo
        const textField = document.createElement('div');
        textField.className = 'fap-field';
        
        const label = document.createElement('label');
        label.className = 'fap-field__label';
        label.setAttribute('for', 'fap-box-text');
        label.textContent = translate('Testo sulla scatola');
        
        const input = document.createElement('input');
        input.type = 'text';
        input.id = 'fap-box-text';
        input.className = 'form-control';
        input.value = state.boxText;
        
        if (config.box && config.box.maxChars) {
            input.maxLength = parseInt(config.box.maxChars, 10) || config.box.maxChars;
        }
        
        input.addEventListener('input', function (event) {
            let value = event.target.value || '';
            if (config.box && config.box.uppercase) {
                value = value.toUpperCase();
                event.target.value = value;
            }
            state.boxText = value;
            state.previewDirty = true;
            updateCounter();
        });
        
        textField.appendChild(label);
        textField.appendChild(input);

        const counter = document.createElement('small');
        counter.className = 'fap-char-counter';
        textField.appendChild(counter);
        fieldset.appendChild(textField);

        if (Array.isArray(state.boxes) && state.boxes.length) {
            const boxField = document.createElement('div');
            boxField.className = 'fap-field fap-field--box-selection';

            const boxLabel = document.createElement('span');
            boxLabel.className = 'fap-field__label';
            boxLabel.textContent = translate('Seleziona la scatola');
            boxField.appendChild(boxLabel);

            const boxList = document.createElement('div');
            boxList.className = 'fap-box-list';

            state.boxes.forEach(function (box) {
                const card = document.createElement('button');
                card.type = 'button';
                card.className = 'fap-box-card';

                const isSelected = state.selectedBox && ((state.selectedBox.id && box.id && String(state.selectedBox.id) === String(box.id)) || (state.selectedBox.reference && box.reference && state.selectedBox.reference === box.reference));
                if (isSelected) {
                    card.classList.add('is-selected');
                }

                const name = document.createElement('span');
                name.className = 'fap-box-card__name';
                name.textContent = box.name || translate('Scatola');
                card.appendChild(name);

                if (box.preview) {
                    const preview = document.createElement('img');
                    preview.className = 'fap-box-card__preview';
                    preview.src = box.preview;
                    preview.alt = box.name || '';
                    card.appendChild(preview);
                } else if (box.color) {
                    const swatch = document.createElement('span');
                    swatch.className = 'fap-box-card__swatch';
                    swatch.style.backgroundColor = box.color;
                    card.appendChild(swatch);
                }

                card.addEventListener('click', function () {
                    state.selectedBox = box;
                    state.previewDirty = true;
                    state.message = null;
                    renderStep();
                });

                boxList.appendChild(card);
            });

            boxField.appendChild(boxList);
            fieldset.appendChild(boxField);
        }

        function updateCounter() {
            const max = (config.box && config.box.maxChars) || 0;
            const current = state.boxText.length;
            if (max) {
                counter.textContent = current + ' / ' + max;
                if (current > max * 0.9) {
                    counter.style.color = '#d32f2f';
                } else {
                    counter.style.color = '#777';
                }
            } else {
                counter.textContent = current + ' ' + translate('caratteri');
            }
        }
        updateCounter();

        // Campo colore
        if (config.box && Array.isArray(config.box.colors) && config.box.colors.length) {
            const colorField = document.createElement('div');
            colorField.className = 'fap-field';
            
            const colorLabel = document.createElement('label');
            colorLabel.className = 'fap-field__label';
            colorLabel.setAttribute('for', 'fap-box-color');
            colorLabel.textContent = translate('Colore testo scatola');
            
            const colorSelect = document.createElement('select');
            colorSelect.id = 'fap-box-color';
            colorSelect.className = 'form-control';
            
            config.box.colors.forEach(function (color) {
                const option = document.createElement('option');
                option.value = color;
                option.textContent = color.toUpperCase();
                if (state.boxColor === color) {
                    option.selected = true;
                }
                colorSelect.appendChild(option);
            });
            
            colorSelect.addEventListener('change', function (event) {
                state.boxColor = event.target.value;
                state.previewDirty = true;
            });
            
            colorField.appendChild(colorLabel);
            colorField.appendChild(colorSelect);
            fieldset.appendChild(colorField);
        }

        // Campo font
        if (config.box && Array.isArray(config.box.fonts) && config.box.fonts.length) {
            const fontField = document.createElement('div');
            fontField.className = 'fap-field';
            
            const fontLabel = document.createElement('label');
            fontLabel.className = 'fap-field__label';
            fontLabel.setAttribute('for', 'fap-box-font');
            fontLabel.textContent = translate('Font testo scatola');
            
            const fontSelect = document.createElement('select');
            fontSelect.id = 'fap-box-font';
            fontSelect.className = 'form-control';
            
            config.box.fonts.forEach(function (font) {
                const option = document.createElement('option');
                option.value = font;
                option.textContent = font;
                if (state.boxFont === font) {
                    option.selected = true;
                }
                fontSelect.appendChild(option);
            });
            
            fontSelect.addEventListener('change', function (event) {
                state.boxFont = event.target.value;
                state.previewDirty = true;
            });
            
            fontField.appendChild(fontLabel);
            fontField.appendChild(fontSelect);
            fieldset.appendChild(fontField);
        }

        container.appendChild(fieldset);
    }

    function renderPreviewStep(container) {
        const wrapper = document.createElement('div');
        wrapper.className = 'fap-preview';

        if (!state.previewUrl && !state.previewLoading) {
            ensurePreview();
        }

        if (state.previewLoading) {
            const loading = document.createElement('p');
            loading.className = 'fap-status';
            loading.innerHTML = '<i class="icon-spinner icon-spin"></i> ' + translate('Generazione anteprima in corso...');
            wrapper.appendChild(loading);
        }

        if (state.previewUrl && !state.previewLoading) {
            const img = document.createElement('img');
            img.className = 'fap-preview__image';
            img.src = state.previewUrl;
            img.alt = translate('Anteprima della scatola puzzle personalizzata');
            wrapper.appendChild(img);
        }

        const details = document.createElement('div');
        details.className = 'fap-preview__details';

        if (state.fileName) {
            const fileRow = document.createElement('p');
            fileRow.innerHTML = '<strong>' + translate('Immagine:') + '</strong> ' + sanitize(state.fileName);
            details.appendChild(fileRow);
        }
        if (state.format) {
            const formatRow = document.createElement('p');
            formatRow.innerHTML = '<strong>' + translate('Formato:') + '</strong> ' + sanitize(formatLabel(state.format));
            details.appendChild(formatRow);
        }
        if (state.selectedBox) {
            const boxRow = document.createElement('p');
            boxRow.innerHTML = '<strong>' + translate('Scatola:') + '</strong> ' + sanitize(state.selectedBox.name || '-');
            details.appendChild(boxRow);
        }
        if (state.boxText) {
            const textRow = document.createElement('p');
            textRow.innerHTML = '<strong>' + translate('Testo:') + '</strong> ' + sanitize(state.boxText);
            details.appendChild(textRow);
        }
        const colorRow = document.createElement('p');
        colorRow.innerHTML = '<strong>' + translate('Colore:') + '</strong> ' + sanitize(state.boxColor || '-');
        details.appendChild(colorRow);
        
        const fontRow = document.createElement('p');
        fontRow.innerHTML = '<strong>' + translate('Font:') + '</strong> ' + sanitize(state.boxFont || '-');
        details.appendChild(fontRow);

        wrapper.appendChild(details);

        const regenerate = document.createElement('button');
        regenerate.type = 'button';
        regenerate.className = 'btn btn-outline-secondary fap-preview__refresh';
        regenerate.textContent = translate('Rigenera anteprima');
        regenerate.addEventListener('click', function () {
            ensurePreview(true);
        });
        regenerate.disabled = state.previewLoading;
        wrapper.appendChild(regenerate);

        container.appendChild(wrapper);
    }

    function renderMessage(message) {
        const wrapper = document.createElement('div');
        wrapper.className = 'fap-alert fap-alert--' + message.type;
        wrapper.textContent = message.text;
        return wrapper;
    }

    function setMessage(type, text) {
        state.message = { type: type, text: text };
        renderStep();
    }

    async function handleFileSelection(file) {
        state.uploading = true;
        state.message = null;
        state.previewDirty = true;
        renderStep();

        try {
            const dimensions = await validateImage(file);
            state.fileWidth = dimensions.width;
            state.fileHeight = dimensions.height;

            const response = await uploadFile(file);
            state.file = response.file || null;
            state.fileUrl = response.download_url || null;
            state.fileName = file.name;
            state.fileWidth = response.width || state.fileWidth;
            state.fileHeight = response.height || state.fileHeight;
            state.orientation = response.orientation || (state.fileWidth >= state.fileHeight ? 'landscape' : 'portrait');
            state.formats = Array.isArray(response.formats) ? response.formats : [];
            state.printable = !!response.printable;
            state.previewUrl = response.preview_url || null;
            state.previewPath = response.preview || null;
            state.thumbnailUrl = response.thumbnail_url || null;
            state.thumbnailPath = response.thumbnail || null;
            state.previewDirty = !state.previewUrl;
            state.uploading = false;

            if (!state.printable) {
                throw new Error(translate('La qualità della foto inviata non è idonea alla stampa.'));
            }

            setMessage('success', translate('Immagine caricata con successo.'));
        } catch (error) {
            state.uploading = false;
            state.file = null;
            state.fileUrl = null;
            state.fileName = '';
            state.fileWidth = 0;
            state.fileHeight = 0;
            state.orientation = null;
            state.formats = [];
            state.printable = false;
            state.previewUrl = null;
            state.previewPath = null;
            state.thumbnailUrl = null;
            state.thumbnailPath = null;
            state.previewDirty = false;
            throw error;
        }
    }

    function validateImage(file) {
        return new Promise(function (resolve, reject) {
            // Valida dimensione file
            const maxMb = config.maxUploadMb || 25;
            if ((file.size / 1024 / 1024) > maxMb) {
                reject(new Error(translate('Il file supera la dimensione massima consentita di {max} MB.').replace('{max}', maxMb)));
                return;
            }

            const objectUrl = URL.createObjectURL(file);
            const image = new Image();
            
            image.onload = function () {
                URL.revokeObjectURL(objectUrl);
                const minWidth = config.minWidth || 0;
                const minHeight = config.minHeight || 0;
                
                if ((minWidth && image.width < minWidth) || (minHeight && image.height < minHeight)) {
                    reject(new Error(translate('Le dimensioni dell\'immagine sono troppo piccole. Minimo richiesto: {w}x{h} pixel.')
                        .replace('{w}', minWidth)
                        .replace('{h}', minHeight)));
                    return;
                }
                
                resolve({ width: image.width, height: image.height });
            };
            
            image.onerror = function () {
                URL.revokeObjectURL(objectUrl);
                reject(new Error(translate('Impossibile leggere l\'immagine selezionata.')));
            };
            
            image.src = objectUrl;
        });
    }

    function uploadFile(file) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('token', tokens.upload);
        
        return fetch(uploadUrl, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
        }).then(handleJsonResponse);
    }

    function ensurePreview(force) {
        if (!state.file) {
            setMessage('error', translate('Carica prima un\'immagine.'));
            return;
        }
        if (!state.format) {
            setMessage('error', translate('Scegli un formato puzzle.'));
            return;
        }
        if (!force && !state.previewDirty && state.previewUrl) {
            return;
        }

        state.previewLoading = true;
        state.message = null;
        renderStep();

        generatePreview().then(function (response) {
            state.previewUrl = response.download_url || response.preview;
            state.previewPath = response.preview;
            state.previewDirty = false;
            state.previewLoading = false;
            renderStep();
        }).catch(function (error) {
            state.previewLoading = false;
            setMessage('error', error.message);
        });
    }

    function generatePreview() {
        const payload = new URLSearchParams();
        payload.append('token', tokens.preview);
        payload.append('file', state.file || '');
        payload.append('box_text', state.boxText || '');
        payload.append('box_color', state.boxColor || '');
        payload.append('box_font', state.boxFont || '');
        if (state.format && state.format.id) {
            payload.append('format_id', state.format.id);
        }
        if (state.format && state.format.reference) {
            payload.append('format_reference', state.format.reference);
        }
        if (state.selectedBox && state.selectedBox.id) {
            payload.append('box_id', state.selectedBox.id);
        }
        if (state.selectedBox && state.selectedBox.reference) {
            payload.append('box_reference', state.selectedBox.reference);
        }
        
        return fetch(previewUrl, {
            method: 'POST',
            body: payload.toString(),
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            credentials: 'same-origin',
        }).then(handleJsonResponse);
    }

    function handleJsonResponse(response) {
        if (!response.ok) {
            return response.text().then(function(text) {
                console.error('Server response:', text);
                throw new Error(translate('Risposta del server non valida.'));
            });
        }
        return response.json().then(function (json) {
            if (!json) {
                throw new Error(translate('Risposta del server non valida.'));
            }
            if (json.success) {
                return json;
            }
            throw new Error(json.message || translate('La richiesta è fallita.'));
        });
    }

    async function handleStepForward() {
        const step = steps[state.currentStepIndex];
        
        if (step.key === 'upload' && !state.file) {
            setMessage('error', translate('Carica un\'immagine per continuare.'));
            return;
        }
        if (step.key === 'format' && !state.format) {
            setMessage('error', translate('Scegli un formato per continuare.'));
            return;
        }
        
        if (state.currentStepIndex < steps.length - 1) {
            goToStep(state.currentStepIndex + 1);
        }
    }

    async function finalizeCustomization() {
        if (!state.file || !state.format) {
            setMessage('error', translate('Carica un\'immagine e scegli un formato prima di finire.'));
            return;
        }

        state.summaryLoading = true;
        state.message = null;
        updateFooter();

        try {
            const payload = new URLSearchParams();
            payload.append('token', tokens.summary);
            payload.append('file', state.file);
            payload.append('box_text', state.boxText || '');
            payload.append('box_color', state.boxColor || '');
            payload.append('box_font', state.boxFont || '');
            if (state.format && state.format.id) {
                payload.append('format_id', state.format.id);
            }
            if (state.format && state.format.reference) {
                payload.append('format_reference', state.format.reference);
            }
            payload.append('format', state.format.name || '');
          
            if (typeof state.format.quality !== 'undefined' && state.format.quality !== null) {
                payload.append('quality', state.format.quality);
            }
            if (typeof state.format.pieces !== 'undefined' && state.format.pieces !== null) {
                payload.append('pieces', state.format.pieces);
            }
            if (state.format && state.format.coordinates) {
                try {
                    payload.append('coordinates', JSON.stringify(state.format.coordinates));
                } catch (error) {
                    console.error('Unable to serialise coordinates', error);
                }
            }
            if (state.format && state.format.payload) {
                try {
                    payload.append('format_payload', JSON.stringify(state.format.payload));
                } catch (error) {
                    console.error('Unable to serialise format payload', error);
                }
            }
            if (state.format) {
                try {
                    payload.append('format_details', JSON.stringify(state.format));
                } catch (error) {
                    console.error('Unable to serialise format details', error);
                }
            }
            if (state.selectedBox && state.selectedBox.id) {
                payload.append('box_id', state.selectedBox.id);
            }
            if (state.selectedBox && state.selectedBox.reference) {
                payload.append('box_reference', state.selectedBox.reference);
            }
            if (state.selectedBox && state.selectedBox.name) {
                payload.append('box_name', state.selectedBox.name);
            }
            if (state.selectedBox) {
                try {
                    payload.append('box_payload', JSON.stringify(state.selectedBox));
                } catch (error) {
                    console.error('Unable to serialise box payload', error);
                }
            }
            payload.append('id_product', idProduct);

            const combinationInput = addToCartForm.querySelector('input[name="id_product_attribute"]');
            if (combinationInput && combinationInput.value) {
                payload.append('id_product_attribute', combinationInput.value);
            }

            if (state.previewPath) {
                payload.append('preview_path', state.previewPath);
            }
            if (state.previewUrl) {
                payload.append('preview_url', state.previewUrl);
            }
            if (state.thumbnailPath) {
                payload.append('thumbnail_path', state.thumbnailPath);
            }
            if (state.thumbnailUrl) {
                payload.append('thumbnail_url', state.thumbnailUrl);
            }
            if (typeof state.printable !== 'undefined') {
                payload.append('printable', state.printable ? '1' : '0');
            }
            if (state.orientation) {
                payload.append('orientation', state.orientation);
            }
            if (state.fileWidth) {
                payload.append('image_width', state.fileWidth);
            }
            if (state.fileHeight) {
                payload.append('image_height', state.fileHeight);
            }
            if (state.fileUrl) {
                payload.append('download_url', state.fileUrl);
            }
            if (state.cropSelection) {
                try {
                    payload.append('crop', JSON.stringify(state.cropSelection));
                } catch (error) {
                    console.error('Unable to serialise crop selection', error);
                }
            }

            const response = await fetch(summaryUrl, {
                method: 'POST',
                body: payload.toString(),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                credentials: 'same-origin',
            }).then(handleJsonResponse);

            state.summaryLoading = false;
            state.customizationId = response.id_customization;

            ensureCustomizationField(state.customizationId);
            updateSummaryPanel();
            closeModal();

            setTimeout(function() {
                triggerAddToCart();
                showToast(translate('Il tuo puzzle personalizzato è stato aggiunto al carrello!'));
            }, 300);

        } catch (error) {
            state.summaryLoading = false;
            setMessage('error', error.message);
        }
    }

    function ensureCustomizationField(value) {
        let input = addToCartForm.querySelector('input[name="id_customization"]');
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'id_customization';
            addToCartForm.appendChild(input);
        }
        input.value = String(value || '');
    }

    function triggerAddToCart() {
        const addButton = addToCartForm.querySelector('[data-button-action="add-to-cart"]');
        if (addButton) {
            addButton.click();
        } else {
            addToCartForm.submit();
        }
    }

    function updateSummaryPanel() {
        if (state.customizationId) {
            summaryPanel.element.classList.add('is-visible');
            summaryPanel.content.innerHTML = '';
            
            const title = document.createElement('strong');
            title.textContent = translate('Personalizzazione pronta');
            summaryPanel.content.appendChild(title);

            const list = document.createElement('ul');
            list.className = 'fap-summary-list';

            const fileItem = document.createElement('li');
            fileItem.innerHTML = '<span>' + translate('Immagine:') + '</span> ' + sanitize(state.fileName);
            list.appendChild(fileItem);

            if (state.orientation) {
                const orientationItem = document.createElement('li');
                const orientationLabel = state.orientation === 'landscape'
                    ? translate('Orizzontale')
                    : translate('Verticale');
                orientationItem.innerHTML = '<span>' + translate('Orientamento:') + '</span> ' + sanitize(orientationLabel);
                list.appendChild(orientationItem);
            }

            if (state.format) {
                const formatItem = document.createElement('li');
                formatItem.innerHTML = '<span>' + translate('Formato:') + '</span> ' + sanitize(formatLabel(state.format));
                list.appendChild(formatItem);

                if (typeof state.format.quality !== 'undefined') {
                    const qualityItem = document.createElement('li');
                    qualityItem.innerHTML = '<span>' + translate('Qualità:') + '</span> ' + sanitize(qualityLabelForScore(state.format.quality));
                    list.appendChild(qualityItem);
                }
            }

            if (state.selectedBox) {
                const boxItem = document.createElement('li');
                boxItem.innerHTML = '<span>' + translate('Scatola:') + '</span> ' + sanitize(state.selectedBox.name || '-');
                list.appendChild(boxItem);
            }

            if (state.boxText) {
                const textItem = document.createElement('li');
                textItem.innerHTML = '<span>' + translate('Testo:') + '</span> ' + sanitize(state.boxText);
                list.appendChild(textItem);
            }

            if (state.boxColor) {
                const colorItem = document.createElement('li');
                colorItem.innerHTML = '<span>' + translate('Colore:') + '</span> ' + sanitize(state.boxColor);
                list.appendChild(colorItem);
            }

            if (state.boxFont) {
                const fontItem = document.createElement('li');
                fontItem.innerHTML = '<span>' + translate('Font:') + '</span> ' + sanitize(state.boxFont);
                list.appendChild(fontItem);
            }

            summaryPanel.content.appendChild(list);
        } else {
            summaryPanel.element.classList.remove('is-visible');
            summaryPanel.content.innerHTML = '';
        }
    }

    function createModal() {
        const root = document.createElement('div');
        root.className = 'fap-modal';
        root.setAttribute('aria-hidden', 'true');
        root.innerHTML = '' +
            '<div class="fap-modal__backdrop"></div>' +
            '<div class="fap-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="fap-modal-title">' +
            '  <button type="button" class="fap-modal__close" aria-label="' + sanitize(translate('Chiudi')) + '">&times;</button>' +
            '  <h2 id="fap-modal-title" class="fap-modal__title"></h2>' +
            '  <div class="fap-modal__content"></div>' +
            '  <div class="fap-modal__footer">' +
            '    <button type="button" class="btn btn-secondary fap-modal__prev">' + sanitize(translate('Indietro')) + '</button>' +
            '    <button type="button" class="btn btn-primary fap-modal__next">' + sanitize(translate('Avanti')) + '</button>' +
            '    <button type="button" class="btn btn-primary fap-modal__finish is-hidden">' + sanitize(translate('Aggiungi al carrello')) + '</button>' +
            '  </div>' +
            '</div>';

        return {
            root: root,
            backdrop: root.querySelector('.fap-modal__backdrop'),
            closeButton: root.querySelector('.fap-modal__close'),
            title: root.querySelector('.fap-modal__title'),
            content: root.querySelector('.fap-modal__content'),
            prevButton: root.querySelector('.fap-modal__prev'),
            nextButton: root.querySelector('.fap-modal__next'),
            finishButton: root.querySelector('.fap-modal__finish'),
        };
    }

    function createSummaryPanel() {
        const element = document.createElement('div');
        element.className = 'fap-selection-summary';
        const content = document.createElement('div');
        content.className = 'fap-selection-summary__content';
        element.appendChild(content);
        return { element: element, content: content };
    }

    function formatSizeLabel(format) {
        if (!format) {
            return null;
        }

        const width = parseFloat(format.width);
        const height = parseFloat(format.height);

        if (!isFinite(width) || !isFinite(height) || width <= 0 || height <= 0) {
            return null;
        }

        const widthDisplay = normaliseDimension(width);
        const heightDisplay = normaliseDimension(height);

        if (!widthDisplay || !heightDisplay) {
            return null;
        }

        return widthDisplay.value + ' x ' + heightDisplay.value + ' ' + widthDisplay.unit;
    }

    function normaliseDimension(value) {
        if (!isFinite(value) || value <= 0) {
            return null;
        }

        let numeric = value;
        let unit = 'cm';

        if (value > 100) {
            numeric = value / 100;
        }

        const rounded = Math.round(numeric * 10) / 10;
        const display = Math.abs(rounded - Math.round(rounded)) < 0.05
            ? String(Math.round(rounded))
            : rounded.toFixed(1);

        return { value: display, unit: unit };
    }

    function qualityLabelForScore(score) {
        switch (score) {
            case 4:
                return translate('Qualità eccellente');
            case 3:
                return translate('Qualità ottima');
            case 2:
                return translate('Buona qualità');
            case 1:
                return translate('Qualità scarsa');
            default:
                return translate('Non adatto alla stampa');
        }
    }

    function formatLabel(format) {
        const pieces = format.pieces ? format.pieces + ' ' + translate('pezzi') : '';
        return format.name + (pieces ? ' (' + pieces + ')' : '');
    }

    function parseConfig(json) {
        try {
            return JSON.parse(json);
        } catch (error) {
            console.error('FotoArt Puzzle: Errore parsing configurazione', error);
            return {};
        }
    }

    function translate(text) {
        // Traduzioni statiche italiano
        const translations = {
            'Carica la tua immagine': 'Carica la tua immagine',
            'Scegli il formato del puzzle': 'Scegli il formato del puzzle',
            'Personalizza la scatola': 'Personalizza la scatola',
            'Anteprima e conferma': 'Anteprima e conferma',
            'Chiudi': 'Chiudi',
            'Indietro': 'Indietro',
            'Avanti': 'Avanti',
            'Aggiungi al carrello': 'Aggiungi al carrello',
            'Carica immagine': 'Carica immagine',
            'Caricamento in corso...': 'Caricamento in corso...',
            'Immagine caricata con successo.': 'Immagine caricata con successo.',
            'Formato: ': 'Formato: ',
            'Nessun formato puzzle configurato.': 'Nessun formato puzzle configurato.',
            'Scegli il formato del puzzle in base alle dimensioni della tua immagine:': 'Scegli il formato del puzzle in base alle dimensioni della tua immagine:',
            'pezzi': 'pezzi',
            '⚠ Immagine troppo piccola': '⚠ Immagine troppo piccola',
            '✓ Compatibile': '✓ Compatibile',
            'Testo sulla scatola': 'Testo sulla scatola',
            'caratteri': 'caratteri',
            'Colore testo scatola': 'Colore testo scatola',
            'Font testo scatola': 'Font testo scatola',
            'Generazione anteprima in corso...': 'Generazione anteprima in corso...',
            'Anteprima della scatola puzzle personalizzata': 'Anteprima della scatola puzzle personalizzata',
            'Immagine:': 'Immagine:',
            'Formato:': 'Formato:',
            'Qualità:': 'Qualità:',
            'Testo:': 'Testo:',
            'Colore:': 'Colore:',
            'Font:': 'Font:',
            'Rigenera anteprima': 'Rigenera anteprima',
            'Orientamento:': 'Orientamento:',
            'Orizzontale': 'Orizzontale',
            'Verticale': 'Verticale',
            'Carica prima un\'immagine.': 'Carica prima un\'immagine.',
            'Scegli un formato puzzle.': 'Scegli un formato puzzle.',
            'Risposta del server non valida.': 'Risposta del server non valida.',
            'La richiesta è fallita.': 'La richiesta è fallita.',
            'Carica un\'immagine per continuare.': 'Carica un\'immagine per continuare.',
            'Scegli un formato per continuare.': 'Scegli un formato per continuare.',
            'Carica un\'immagine e scegli un formato prima di finire.': 'Carica un\'immagine e scegli un formato prima di finire.',
            'Il tuo puzzle personalizzato è stato aggiunto al carrello!': 'Il tuo puzzle personalizzato è stato aggiunto al carrello!',
            'Personalizzazione pronta': 'Personalizzazione pronta',
            'Impossibile leggere l\'immagine selezionata.': 'Impossibile leggere l\'immagine selezionata.',
            'Qualità eccellente': 'Qualità eccellente',
            'Qualità ottima': 'Qualità ottima',
            'Buona qualità': 'Buona qualità',
            'Qualità scarsa': 'Qualità scarsa',
            'Non adatto alla stampa': 'Non adatto alla stampa',
            'La qualità della foto inviata non è idonea alla stampa.': 'La qualità della foto inviata non è idonea alla stampa.',
            'Trascina qui la tua immagine oppure clicca per selezionarla': 'Trascina qui la tua immagine oppure clicca per selezionarla',
            'Seleziona la scatola': 'Seleziona la scatola',
            'Scatola': 'Scatola',
            'Scatola:': 'Scatola:',
        };
        return translations[text] || text;
    }

    function sanitize(value) {
        const div = document.createElement('div');
        div.textContent = String(value || '');
        return div.innerHTML;
    }

    function showToast(message) {
        if (typeof prestashop !== 'undefined' && prestashop.emit) {
            prestashop.emit('showNotification', {
                type: 'success',
                message: message,
            });
        } else {
            alert(message);
        }
    }
})();
