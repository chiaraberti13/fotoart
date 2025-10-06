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
        format: null,
        boxText: config.box && config.box.defaultText ? config.box.defaultText : 'Il mio puzzle',
        boxColor: (config.box && config.box.colors && config.box.colors[0]) || '#FFFFFF',
        boxFont: (config.box && config.box.fonts && config.box.fonts[0]) || 'Roboto',
        previewUrl: null,
        previewPath: null,
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
        if (!Array.isArray(config.formats) || !config.formats.length) {
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

        config.formats.forEach(function (item) {
            const card = document.createElement('button');
            card.type = 'button';
            card.className = 'fap-format-card';
            
            if (state.format && state.format.name === item.name) {
                card.classList.add('is-selected');
            }

            let html = '<span class="fap-format-card__name">' + sanitize(item.name) + '</span>';
            
            if (item.pieces) {
                html += '<span class="fap-format-card__pieces">' + sanitize(item.pieces + ' ' + translate('pezzi')) + '</span>';
            }
            
            if (item.width && item.height) {
                html += '<span class="fap-format-card__size">' + sanitize(item.width + ' x ' + item.height + ' px') + '</span>';
            }

            // Verifica compatibilità con immagine caricata
            if (state.fileWidth && state.fileHeight && item.width && item.height) {
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
            state.file = response.file;
            state.fileUrl = response.download_url;
            state.fileName = file.name;
            state.uploading = false;
            state.previewUrl = null;
            
            setMessage('success', translate('Immagine caricata con successo.'));
        } catch (error) {
            state.uploading = false;
            state.file = null;
            state.fileUrl = null;
            state.fileName = '';
            state.fileWidth = 0;
            state.fileHeight = 0;
            state.previewUrl = null;
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
            payload.append('format', state.format.name || '');
            payload.append('id_product', idProduct);
            
            // Gestione combinazione prodotto
            const combinationInput = addToCartForm.querySelector('input[name="id_product_attribute"]');
            if (combinationInput && combinationInput.value) {
                payload.append('id_product_attribute', combinationInput.value);
            }
            
            if (state.previewPath) {
                payload.append('preview_path', state.previewPath);
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
            
            // Aspetta un momento prima di aggiungere al carrello
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

            if (state.format) {
                const formatItem = document.createElement('li');
                formatItem.innerHTML = '<span>' + translate('Formato:') + '</span> ' + sanitize(formatLabel(state.format));
                list.appendChild(formatItem);
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
            'Testo:': 'Testo:',
            'Colore:': 'Colore:',
            'Font:': 'Font:',
            'Rigenera anteprima': 'Rigenera anteprima',
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
            'Trascina qui la tua immagine oppure clicca per selezionarla': 'Trascina qui la tua immagine oppure clicca per selezionarla',
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