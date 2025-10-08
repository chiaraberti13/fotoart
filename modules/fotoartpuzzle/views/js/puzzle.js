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
    const state = createInitialState(config);

    const uploadUrl = wizard.dataset.uploadUrl;
    const previewUrl = wizard.dataset.previewUrl;
    const summaryUrl = wizard.dataset.summaryUrl;
    const ajaxUrl = wizard.dataset.ajaxUrl || '';
    const tokens = {
        upload: wizard.dataset.tokenUpload || '',
        preview: wizard.dataset.tokenPreview || '',
        summary: wizard.dataset.tokenSummary || '',
        ajax: wizard.dataset.tokenAjax || '',
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
    if (!isNaN(idProduct)) {
        state.idProduct = idProduct;
    }

    // Pannello riepilogo
    const summaryPanel = createSummaryPanel();
    wizard.appendChild(summaryPanel.element);

    const steps = [
        { key: 'upload', title: translate('Carica la tua immagine') },
        { key: 'format', title: translate('Scegli il formato del puzzle') },
        { key: 'crop', title: translate('Inquadra e ritaglia') },
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
            case 'crop':
                renderCropStep(stepWrapper);
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
        const step = steps[state.currentStepIndex];
        const isLoading = state.uploading || state.previewLoading || state.summaryLoading || state.qualityLoading;

        modal.prevButton.disabled = state.currentStepIndex === 0 || isLoading;
        modal.nextButton.classList.toggle('is-hidden', state.currentStepIndex >= steps.length - 1);
        modal.finishButton.classList.toggle('is-hidden', state.currentStepIndex !== steps.length - 1);

        if (!step) {
            modal.nextButton.disabled = true;
            modal.finishButton.disabled = true;
            return;
        }

        switch (step.key) {
            case 'upload':
                modal.nextButton.disabled = !state.file || isLoading;
                break;
            case 'format':
                modal.nextButton.disabled = !state.format || isLoading;
                break;
            case 'crop':
                modal.nextButton.disabled = !state.cropSelection || isLoading;
                break;
            case 'box':
                modal.nextButton.disabled = false;
                break;
            case 'preview':
                modal.nextButton.disabled = true;
                break;
        }

        if (step.key === 'preview') {
            modal.finishButton.disabled = !state.file || !state.format || isLoading;
        } else {
            modal.finishButton.disabled = true;
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

        updateBoxPreviewUrl();

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
                updateBoxPreviewUrl();
                if (state.currentStepIndex === 2) {
                    renderStep();
                }
            }
        });
    }

    function requestAjax(action, data) {
        if (!ajaxUrl) {
            return Promise.resolve(null);
        }

        const separator = ajaxUrl.indexOf('?') === -1 ? '?' : '&';
        let url = ajaxUrl + separator + 'action=' + encodeURIComponent(action);
        if (tokens.ajax) {
            url += '&token=' + encodeURIComponent(tokens.ajax);
        }

        const options = {
            method: data ? 'POST' : 'GET',
            credentials: 'same-origin',
            headers: {}
        };

        if (tokens.ajax) {
            options.headers['X-FAP-Token'] = tokens.ajax;
        }

        if (data) {
            options.headers['Content-Type'] = 'application/json; charset=UTF-8';
            options.body = JSON.stringify(data);
        } else if (Object.keys(options.headers).length === 0) {
            delete options.headers;
        }

        return fetch(url, options)
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

            if (isFormatSelected(item)) {
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

            const qualityData = getQualityDataForFormat(item);
            if (qualityData && typeof qualityData.quality === 'number') {
                const quality = qualityData.quality;
                const qualityLabel = qualityLabelForScore(quality);
                if (qualityLabel) {
                    const qualityColor = quality > 1 ? '#2e7d32' : (quality === 1 ? '#f57c00' : '#d32f2f');
                    html += '<span class="fap-format-card__quality" style="display:block;color:' + qualityColor + ';font-size:0.85em;">'
                        + sanitize(qualityLabel) + '</span>';
                }
                if (quality <= 0) {
                    card.disabled = true;
                    card.classList.add('is-disabled');
                }
            } else if (state.fileWidth && state.fileHeight && item.width && item.height) {
                if (state.fileWidth < item.width || state.fileHeight < item.height) {
                    html += '<span class="fap-format-card__warning" style="color: #d32f2f; font-size: 0.85em;">' +
                        translate('⚠ Immagine troppo piccola') + '</span>';
                    card.disabled = true;
                    card.classList.add('is-disabled');
                } else {
                    html += '<span class="fap-format-card__success" style="color: #2e7d32; font-size: 0.85em;">' +
                        translate('✓ Compatibile') + '</span>';
                }
            }

            card.innerHTML = html;

            card.addEventListener('click', function () {
                if (!card.disabled) {
                    selectFormat(item);
                }
            });

            list.appendChild(card);
        });

        container.appendChild(list);

        if (state.format) {
            const qualityIndicator = renderQualityIndicator();
            container.appendChild(qualityIndicator);
        }
    }

    function renderCropStep(container) {
        if (!state.file || !state.format) {
            const notice = document.createElement('p');
            notice.className = 'fap-step__description';
            notice.textContent = translate('Carica un\'immagine e scegli un formato per continuare.');
            container.appendChild(notice);
            return;
        }

        ensureSourceImage();
        ensureCropSelection(!state.cropSelection);
        updateOrientation();

        if (!state.sourceImageReady) {
            const loading = document.createElement('p');
            loading.className = 'fap-status';
            loading.innerHTML = '<i class="icon-spinner icon-spin"></i> ' + translate('Caricamento anteprima in corso...');
            container.appendChild(loading);
            return;
        }

        const description = document.createElement('p');
        description.className = 'fap-step__description';
        description.textContent = translate('Regola il ritaglio per adattare l\'immagine al formato scelto. Trascina l\'area attiva o usa i controlli per ruotare e zoomare.');
        container.appendChild(description);

        const cropper = document.createElement('div');
        cropper.className = 'fap-cropper';

        const viewport = document.createElement('div');
        viewport.className = 'fap-cropper__viewport';
        const canvas = document.createElement('canvas');
        canvas.className = 'fap-cropper__canvas';
        viewport.appendChild(canvas);
        cropper.appendChild(viewport);

        const overlayInfo = document.createElement('div');
        overlayInfo.className = 'fap-cropper__info';
        cropper.appendChild(overlayInfo);

        container.appendChild(cropper);

        const controls = document.createElement('div');
        controls.className = 'fap-crop-controls';

        const rotateGroup = document.createElement('div');
        rotateGroup.className = 'fap-crop-controls__group';
        const rotateLabel = document.createElement('span');
        rotateLabel.className = 'fap-crop-controls__label';
        rotateLabel.textContent = translate('Rotazione');
        rotateGroup.appendChild(rotateLabel);

        const rotateButtons = document.createElement('div');
        rotateButtons.className = 'fap-crop-controls__buttons';

        const rotateLeft = document.createElement('button');
        rotateLeft.type = 'button';
        rotateLeft.className = 'btn btn-outline-secondary';
        rotateLeft.textContent = translate('Ruota a sinistra');
        rotateLeft.addEventListener('click', function () {
            state.rotation = normaliseRotation(state.rotation - 90);
            state.previewDirty = true;
            updateOrientation();
            scheduleQualityEvaluation();
            scheduleSessionSync();
            schedulePreviewRefresh();
            refreshCanvas();
        });

        const rotateRight = document.createElement('button');
        rotateRight.type = 'button';
        rotateRight.className = 'btn btn-outline-secondary';
        rotateRight.textContent = translate('Ruota a destra');
        rotateRight.addEventListener('click', function () {
            state.rotation = normaliseRotation(state.rotation + 90);
            state.previewDirty = true;
            updateOrientation();
            scheduleQualityEvaluation();
            scheduleSessionSync();
            schedulePreviewRefresh();
            refreshCanvas();
        });

        rotateButtons.appendChild(rotateLeft);
        rotateButtons.appendChild(rotateRight);
        rotateGroup.appendChild(rotateButtons);

        const zoomGroup = document.createElement('div');
        zoomGroup.className = 'fap-crop-controls__group';
        const zoomLabel = document.createElement('span');
        zoomLabel.className = 'fap-crop-controls__label';
        zoomLabel.textContent = translate('Zoom');
        zoomGroup.appendChild(zoomLabel);

        const zoomInput = document.createElement('input');
        zoomInput.type = 'range';
        zoomInput.min = '0';
        zoomInput.max = '100';
        zoomInput.value = String(Math.max(0, Math.min(100, state.cropZoom || 0)));
        zoomInput.className = 'form-range fap-crop-controls__slider';

        const zoomValue = document.createElement('span');
        zoomValue.className = 'fap-crop-controls__value';

        zoomGroup.appendChild(zoomInput);
        zoomGroup.appendChild(zoomValue);

        controls.appendChild(rotateGroup);
        controls.appendChild(zoomGroup);

        const qualityIndicator = renderQualityIndicator();
        qualityIndicator.classList.add('fap-quality-indicator--inline');
        controls.appendChild(qualityIndicator);

        container.appendChild(controls);

        function updateZoomValueLabel(value) {
            zoomValue.textContent = translate('Zoom {value}%').replace('{value}', value);
        }

        function updateCropInfo() {
            const crop = getCropPayload();
            if (!crop) {
                overlayInfo.textContent = translate('Definisci il ritaglio trascinando l\'area attiva.');
                return;
            }
            overlayInfo.textContent = translate('Area di stampa: {w} x {h} px · {orientation}')
                .replace('{w}', Math.round(crop.width))
                .replace('{h}', Math.round(crop.height))
                .replace('{orientation}', state.orientation === 'portrait' ? translate('Verticale') : translate('Orizzontale'));
        }

        function refreshCanvas() {
            updateZoomValueLabel(Math.max(0, Math.min(100, state.cropZoom || 0)));
            const result = drawCropPreview(canvas);
            canvasState = result;
            updateCropInfo();
            updateQualityIndicator(qualityIndicator);
        }

        updateZoomValueLabel(Math.max(0, Math.min(100, state.cropZoom || 0)));

        let canvasState = null;
        refreshCanvas();

        zoomInput.addEventListener('input', function (event) {
            const value = parseInt(event.target.value, 10);
            updateCropZoom(isNaN(value) ? 0 : value);
            state.previewDirty = true;
            updateOrientation();
            scheduleQualityEvaluation();
            scheduleSessionSync();
            schedulePreviewRefresh();
            refreshCanvas();
        });

        let pointerActive = false;
        let pointerId = null;
        let pointerStart = null;
        let pointerRect = null;

        canvas.addEventListener('pointerdown', function (event) {
            if (!canvasState || !canvasState.rectScaled) {
                return;
            }
            const position = getCanvasPointerPosition(canvas, event);
            if (!position || !isPointInsideRect(position, canvasState.rectScaled)) {
                return;
            }
            pointerActive = true;
            pointerId = event.pointerId;
            pointerStart = position;
            pointerRect = Object.assign({}, canvasState.rect);
            canvas.setPointerCapture(pointerId);
        });

        canvas.addEventListener('pointermove', function (event) {
            if (!pointerActive || !canvasState) {
                return;
            }
            const position = getCanvasPointerPosition(canvas, event);
            if (!position) {
                return;
            }
            const deltaX = (position.x - pointerStart.x) / canvasState.scale;
            const deltaY = (position.y - pointerStart.y) / canvasState.scale;
            const boundsWidth = canvasState.rotatedWidth - pointerRect.width;
            const boundsHeight = canvasState.rotatedHeight - pointerRect.height;
            const nextRect = {
                x: clamp(pointerRect.x + deltaX, 0, boundsWidth < 0 ? 0 : boundsWidth),
                y: clamp(pointerRect.y + deltaY, 0, boundsHeight < 0 ? 0 : boundsHeight),
                width: pointerRect.width,
                height: pointerRect.height,
            };
            state.cropSelection = mapRectFromRotationToOriginal(nextRect, state.rotation, state.fileWidth, state.fileHeight);
            state.previewDirty = true;
            updateOrientation();
            scheduleQualityEvaluation();
            scheduleSessionSync();
            schedulePreviewRefresh();
            refreshCanvas();
        });

        function releasePointer() {
            if (pointerActive && pointerId !== null) {
                canvas.releasePointerCapture(pointerId);
            }
            pointerActive = false;
            pointerId = null;
            pointerStart = null;
            pointerRect = null;
        }

        canvas.addEventListener('pointerup', releasePointer);
        canvas.addEventListener('pointerleave', releasePointer);
        canvas.addEventListener('pointercancel', releasePointer);
    }

    function renderBoxStep(container) {
        const fieldset = document.createElement('div');
        fieldset.className = 'fap-box-options';

        updateBoxPreviewUrl();

        if (state.previewDirty && !state.previewLoading && state.file && state.format) {
            schedulePreviewRefresh();
        }

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
            schedulePreviewRefresh();
            scheduleSessionSync();
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

                const previewUrl = resolveBoxPreview(box);
                if (previewUrl) {
                    const preview = document.createElement('img');
                    preview.className = 'fap-box-card__preview';
                    preview.src = previewUrl;
                    preview.alt = box.name || '';
                    card.appendChild(preview);
                } else if (box.color) {
                    const swatch = document.createElement('span');
                    swatch.className = 'fap-box-card__swatch';
                    swatch.style.backgroundColor = box.color;
                    card.appendChild(swatch);
                }

                card.addEventListener('click', function () {
                    selectBox(box);
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
                schedulePreviewRefresh();
                scheduleSessionSync();
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
                schedulePreviewRefresh();
                scheduleSessionSync();
            });
            
            fontField.appendChild(fontLabel);
            fontField.appendChild(fontSelect);
            fieldset.appendChild(fontField);
        }

        container.appendChild(fieldset);

        const previewContainer = document.createElement('div');
        previewContainer.className = 'fap-box-preview';

        const gallery = document.createElement('div');
        gallery.className = 'fap-preview-gallery';

        const puzzleItem = document.createElement('div');
        puzzleItem.className = 'fap-preview-gallery__item';
        const puzzleTitle = document.createElement('span');
        puzzleTitle.className = 'fap-preview-gallery__title';
        puzzleTitle.textContent = translate('Anteprima personalizzata');
        puzzleItem.appendChild(puzzleTitle);

        const puzzleBody = document.createElement('div');
        puzzleBody.className = 'fap-preview-gallery__body';
        if (state.previewLoading) {
            puzzleBody.innerHTML = '<span class="fap-status"><i class="icon-spinner icon-spin"></i> ' + translate('Generazione anteprima in corso...') + '</span>';
        } else if (state.previewUrl) {
            const img = document.createElement('img');
            img.src = state.previewUrl;
            img.alt = translate('Anteprima della scatola puzzle personalizzata');
            puzzleBody.appendChild(img);
        } else {
            puzzleBody.textContent = translate('Anteprima non ancora disponibile');
        }
        puzzleItem.appendChild(puzzleBody);
        gallery.appendChild(puzzleItem);

        const templateItem = document.createElement('div');
        templateItem.className = 'fap-preview-gallery__item';
        const templateTitle = document.createElement('span');
        templateTitle.className = 'fap-preview-gallery__title';
        templateTitle.textContent = translate('Template scatola');
        templateItem.appendChild(templateTitle);

        const templateBody = document.createElement('div');
        templateBody.className = 'fap-preview-gallery__body';
        if (state.boxPreviewUrl) {
            const boxImg = document.createElement('img');
            boxImg.src = state.boxPreviewUrl;
            boxImg.alt = state.selectedBox && state.selectedBox.name ? state.selectedBox.name : '';
            templateBody.appendChild(boxImg);
        } else {
            templateBody.textContent = translate('Anteprima non ancora disponibile');
        }
        templateItem.appendChild(templateBody);
        gallery.appendChild(templateItem);

        previewContainer.appendChild(gallery);

        const previewActions = document.createElement('div');
        previewActions.className = 'fap-box-preview__actions';

        const refresh = document.createElement('button');
        refresh.type = 'button';
        refresh.className = 'btn btn-outline-secondary';
        refresh.textContent = translate('Rigenera anteprima');
        refresh.disabled = state.previewLoading;
        refresh.addEventListener('click', function () {
            ensurePreview(true);
        });
        previewActions.appendChild(refresh);

        if (state.previewHiResUrl) {
            const hiResLink = document.createElement('a');
            hiResLink.className = 'btn btn-link';
            hiResLink.href = state.previewHiResUrl;
            hiResLink.target = '_blank';
            hiResLink.rel = 'noopener';
            hiResLink.textContent = translate('Apri anteprima in una nuova scheda');
            previewActions.appendChild(hiResLink);
        }

        previewContainer.appendChild(previewActions);
        container.appendChild(previewContainer);
    }

    function renderPreviewStep(container) {
        const wrapper = document.createElement('div');
        wrapper.className = 'fap-preview';

        if (!state.previewUrl && !state.previewLoading) {
            ensurePreview();
        }

        const qualityIndicator = renderQualityIndicator();
        qualityIndicator.classList.add('fap-quality-indicator--inline');
        wrapper.appendChild(qualityIndicator);

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
        if (typeof state.qualityScore === 'number') {
            const qualityRow = document.createElement('p');
            qualityRow.innerHTML = '<strong>' + translate('Qualità:') + '</strong> ' + sanitize(qualityLabelForScore(state.qualityScore));
            details.appendChild(qualityRow);
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

        const crop = getCropPayload();
        if (crop) {
            const cropRow = document.createElement('p');
            cropRow.innerHTML = '<strong>' + translate('Ritaglio:') + '</strong> ' + sanitize(Math.round(crop.width) + ' x ' + Math.round(crop.height) + ' px');
            details.appendChild(cropRow);
        }

        if (state.orientation) {
            const orientationRow = document.createElement('p');
            const orientationLabel = state.orientation === 'portrait' ? translate('Verticale') : translate('Orizzontale');
            orientationRow.innerHTML = '<strong>' + translate('Orientamento:') + '</strong> ' + sanitize(orientationLabel);
            details.appendChild(orientationRow);
        }

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

        if (state.previewHiResUrl) {
            const downloadLink = document.createElement('a');
            downloadLink.className = 'btn btn-link fap-preview__download';
            downloadLink.href = state.previewHiResUrl;
            downloadLink.target = '_blank';
            downloadLink.rel = 'noopener';
            downloadLink.textContent = translate('Apri anteprima in una nuova scheda');
            wrapper.appendChild(downloadLink);
        }

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
        state.assetMap = {};
        state.previewHiResUrl = null;
        state.qualityScore = null;
        state.qualityCoordinates = [];
        state.qualityByFormat = {};
        state.sessionId = null;
        state.format = null;
        state.cropSelection = null;
        state.rotation = 0;
        state.cropZoom = 0;
        state.customizationId = null;
        updateSummaryPanel();
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

            state.assetMap = {
                original: {
                    path: response.file || null,
                    url: state.fileUrl || null,
                    width: state.fileWidth,
                    height: state.fileHeight,
                },
            };

            if (response.preview) {
                state.assetMap.preview = {
                    path: response.preview,
                    url: state.previewUrl || response.preview_url || null,
                };
            }
            if (response.thumbnail) {
                state.assetMap.thumbnail = {
                    path: response.thumbnail,
                    url: state.thumbnailUrl || response.thumbnail_url || null,
                };
            }

            state.previewHiResUrl = response.preview_url || null;

            ensureSourceImage();
            ensureCropSelection(true);
            updateOrientation();

            await refreshFormatsWithQuality();
            await initSessionWithUpload(response);

            updateBoxPreviewUrl();
            schedulePreviewRefresh();
            scheduleSessionSync();
            updateSummaryPanel();

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
            state.assetMap = {};
            state.previewHiResUrl = null;
            state.boxPreviewUrl = null;
            state.sourceImage = null;
            state.sourceImageReady = false;
            state.qualityScore = null;
            state.qualityCoordinates = [];
            state.qualityByFormat = {};
            state.sessionId = null;
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

        if (state.previewLoading) {
            state.previewDirty = true;
            return;
        }

        state.previewLoading = true;
        state.message = null;
        renderStep();

        generatePreview().then(function (response) {
            state.previewUrl = response.download_url || response.preview;
            state.previewPath = response.preview;
            state.previewHiResUrl = response.download_url || state.previewHiResUrl;
            state.previewDirty = false;
            state.previewLoading = false;
            if (!state.assetMap) {
                state.assetMap = {};
            }
            if (response.preview) {
                state.assetMap.preview = {
                    path: response.preview,
                    url: response.download_url || state.previewUrl || null,
                };
            }
            updateBoxPreviewUrl();
            updateSummaryPanel();
            scheduleSessionSync();
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
        if (state.selectedBox && state.selectedBox.template) {
            payload.append('box_template', state.selectedBox.template);
        }
        const cropPayload = getCropPayload();
        if (cropPayload) {
            try {
                payload.append('crop', JSON.stringify(cropPayload));
            } catch (error) {
                console.error('Unable to serialise crop payload', error);
            }
        }
        payload.append('rotation', normaliseRotation(state.rotation || 0));
        if (state.sessionId) {
            payload.append('session_id', state.sessionId);
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
            if (typeof state.qualityScore !== 'undefined' && state.qualityScore !== null) {
                payload.append('quality', state.qualityScore);
            }
            if (typeof state.format.pieces !== 'undefined' && state.format.pieces !== null) {
                payload.append('pieces', state.format.pieces);
            }
            if (state.qualityCoordinates && state.qualityCoordinates.length) {
                try {
                    payload.append('coordinates', JSON.stringify(state.qualityCoordinates));
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
            payload.append('rotation', normaliseRotation(state.rotation || 0));
            if (state.fileWidth) {
                payload.append('image_width', state.fileWidth);
            }
            if (state.fileHeight) {
                payload.append('image_height', state.fileHeight);
            }
            if (state.fileUrl) {
                payload.append('download_url', state.fileUrl);
            }
            const cropPayload = getCropPayload();
            if (cropPayload) {
                try {
                    payload.append('crop', JSON.stringify(cropPayload));
                } catch (error) {
                    console.error('Unable to serialise crop payload', error);
                }
            }
            if (state.assetMap && Object.keys(state.assetMap).length) {
                try {
                    payload.append('asset_map', JSON.stringify(state.assetMap));
                } catch (error) {
                    console.error('Unable to serialise asset map', error);
                }
            }
            if (state.previewHiResUrl) {
                payload.append('preview_hi_res_url', state.previewHiResUrl);
            }
            if (state.boxPreviewUrl) {
                payload.append('box_preview_url', state.boxPreviewUrl);
            }
            if (state.sessionId) {
                payload.append('session_id', state.sessionId);
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

            scheduleSessionSync();
            showToast(translate('Personalizzazione salvata. Ora aggiungi il prodotto al carrello per proseguire.'));

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
            }

            if (typeof state.qualityScore === 'number') {
                const qualityItem = document.createElement('li');
                qualityItem.innerHTML = '<span>' + translate('Qualità:') + '</span> ' + sanitize(qualityLabelForScore(state.qualityScore));
                list.appendChild(qualityItem);
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

            const crop = getCropPayload();
            if (crop) {
                const cropItem = document.createElement('li');
                cropItem.innerHTML = '<span>' + translate('Ritaglio:') + '</span> ' + sanitize(Math.round(crop.width) + ' x ' + Math.round(crop.height) + ' px');
                list.appendChild(cropItem);
            }

            summaryPanel.content.appendChild(list);
        } else {
            summaryPanel.element.classList.remove('is-visible');
            summaryPanel.content.innerHTML = '';
        }
    }

    function renderQualityIndicator() {
        const indicator = document.createElement('div');
        indicator.className = 'fap-quality-indicator';
        updateQualityIndicator(indicator);
        return indicator;
    }

    function updateQualityIndicator(element) {
        if (!element) {
            return;
        }
        element.className = 'fap-quality-indicator';
        if (state.qualityLoading) {
            element.textContent = translate('Valutazione qualità...');
            element.classList.add('is-loading');
            return;
        }
        const score = typeof state.qualityScore === 'number' ? state.qualityScore : null;
        if (score === null) {
            element.textContent = translate('Qualità non disponibile');
            element.classList.add('is-unknown');
            return;
        }
        element.textContent = qualityLabelForScore(score);
        element.classList.add('fap-quality-indicator--score' + score);
    }

    function isFormatSelected(format) {
        if (!state.format || !format) {
            return false;
        }
        if (state.format.id && format.id && String(state.format.id) === String(format.id)) {
            return true;
        }
        return state.format.name === format.name;
    }

    function selectFormat(format) {
        state.format = format;
        state.previewDirty = true;
        state.message = null;
        const qualityData = getQualityDataForFormat(format);
        state.qualityScore = qualityData && typeof qualityData.quality === 'number' ? qualityData.quality : null;
        state.qualityCoordinates = qualityData && Array.isArray(qualityData.coordinates) ? qualityData.coordinates : [];
        ensureCropSelection(true);
        updateOrientation();
        scheduleQualityEvaluation();
        scheduleSessionSync();
        schedulePreviewRefresh();
        renderStep();
    }

    function selectBox(box) {
        state.selectedBox = box;
        state.previewDirty = true;
        state.message = null;
        updateBoxPreviewUrl();
        schedulePreviewRefresh();
        scheduleSessionSync();
        renderStep();
    }

    function updateBoxPreviewUrl() {
        state.boxPreviewUrl = resolveBoxPreview(state.selectedBox);
    }

    function ensureSourceImage() {
        const candidateUrl = state.fileUrl || state.previewUrl;
        if (!candidateUrl) {
            return;
        }
        if (state.sourceImage && state.sourceImageUrl === candidateUrl) {
            return;
        }

        const image = new Image();
        state.sourceImage = image;
        state.sourceImageReady = false;
        state.sourceImageUrl = candidateUrl;
        image.onload = function () {
            state.sourceImageReady = true;
            const cropIndex = getStepIndex('crop');
            if (cropIndex !== null && state.currentStepIndex === cropIndex) {
                renderStep();
            }
        };
        image.onerror = function () {
            state.sourceImageReady = false;
        };
        image.crossOrigin = 'anonymous';
        image.src = candidateUrl;
    }

    function normaliseRotation(value) {
        let rotation = parseInt(value, 10) || 0;
        rotation %= 360;
        if (rotation < 0) {
            rotation += 360;
        }
        return rotation;
    }

    function getFormatRatio() {
        if (!state.format) {
            return null;
        }
        const width = parseFloat(state.format.width || state.format.width_cm || (state.format.payload && state.format.payload.width));
        const height = parseFloat(state.format.height || state.format.height_cm || (state.format.payload && state.format.payload.height));
        if (isFinite(width) && isFinite(height) && width > 0 && height > 0) {
            return width / height;
        }
        if (state.fileWidth && state.fileHeight) {
            return state.fileWidth / state.fileHeight;
        }
        return null;
    }

    function computeCenteredCrop(ratio) {
        const imageWidth = state.fileWidth || 1;
        const imageHeight = state.fileHeight || 1;
        let cropWidth = imageWidth;
        let cropHeight = Math.round(cropWidth / ratio);
        if (cropHeight > imageHeight) {
            cropHeight = imageHeight;
            cropWidth = Math.round(cropHeight * ratio);
        }
        if (cropWidth > imageWidth) {
            cropWidth = imageWidth;
            cropHeight = Math.round(cropWidth / ratio);
        }
        cropWidth = Math.max(1, Math.min(imageWidth, cropWidth));
        cropHeight = Math.max(1, Math.min(imageHeight, cropHeight));
        const x = clamp(Math.round((imageWidth - cropWidth) / 2), 0, imageWidth - cropWidth);
        const y = clamp(Math.round((imageHeight - cropHeight) / 2), 0, imageHeight - cropHeight);
        return { x: x, y: y, width: cropWidth, height: cropHeight };
    }

    function adaptCropToRatio(selection, ratio) {
        const base = computeCenteredCrop(ratio);
        if (!selection) {
            return base;
        }
        const imageWidth = state.fileWidth || 1;
        const imageHeight = state.fileHeight || 1;
        const centerX = selection.x + selection.width / 2;
        const centerY = selection.y + selection.height / 2;
        let width = selection.width;
        let height = Math.round(width / ratio);
        if (height > imageHeight) {
            height = imageHeight;
            width = Math.round(height * ratio);
        }
        if (width > imageWidth) {
            width = imageWidth;
            height = Math.round(width / ratio);
        }
        width = Math.max(1, Math.min(imageWidth, width));
        height = Math.max(1, Math.min(imageHeight, height));
        const x = clamp(Math.round(centerX - width / 2), 0, imageWidth - width);
        const y = clamp(Math.round(centerY - height / 2), 0, imageHeight - height);
        return { x: x, y: y, width: width, height: height };
    }

    function computeZoomFromSelection(selection, base) {
        if (!selection || !base || !base.width) {
            return 0;
        }
        const factorRange = 1.7;
        const ratio = base.width / Math.max(1, selection.width);
        const value = Math.round((Math.max(1, ratio) - 1) / factorRange * 100);
        return clamp(value, 0, 100);
    }

    function ensureCropSelection(force) {
        const ratio = getFormatRatio();
        if (!ratio) {
            return;
        }
        if (!state.cropSelection || force === true) {
            state.cropSelection = computeCenteredCrop(ratio);
            state.cropZoom = 0;
        } else {
            state.cropSelection = adaptCropToRatio(state.cropSelection, ratio);
            const base = computeCenteredCrop(ratio);
            state.cropZoom = computeZoomFromSelection(state.cropSelection, base);
        }
    }

    function updateCropZoom(value) {
        const ratio = getFormatRatio() || 1;
        const base = computeCenteredCrop(ratio);
        const factorRange = 1.7;
        const factor = 1 + (value / 100) * factorRange;
        let width = Math.round(base.width / factor);
        const minWidth = Math.round(base.width * 0.3);
        if (width < minWidth) {
            width = minWidth;
        }
        let height = Math.round(width / ratio);
        if (height > state.fileHeight) {
            height = state.fileHeight;
            width = Math.round(height * ratio);
        }
        if (width > state.fileWidth) {
            width = state.fileWidth;
            height = Math.round(width / ratio);
        }
        width = Math.max(1, Math.min(state.fileWidth, width));
        height = Math.max(1, Math.min(state.fileHeight, height));
        const centerX = state.cropSelection ? state.cropSelection.x + state.cropSelection.width / 2 : state.fileWidth / 2;
        const centerY = state.cropSelection ? state.cropSelection.y + state.cropSelection.height / 2 : state.fileHeight / 2;
        const x = clamp(Math.round(centerX - width / 2), 0, state.fileWidth - width);
        const y = clamp(Math.round(centerY - height / 2), 0, state.fileHeight - height);
        state.cropSelection = { x: x, y: y, width: width, height: height };
        state.cropZoom = value;
    }

    function getEffectiveDimensions() {
        if (!state.cropSelection) {
            return {
                width: state.fileWidth,
                height: state.fileHeight,
            };
        }
        const rotation = normaliseRotation(state.rotation || 0);
        if (rotation === 90 || rotation === 270) {
            return {
                width: state.cropSelection.height,
                height: state.cropSelection.width,
            };
        }
        return {
            width: state.cropSelection.width,
            height: state.cropSelection.height,
        };
    }

    function getCropPayload() {
        if (!state.cropSelection) {
            return null;
        }
        const effective = getEffectiveDimensions();
        return {
            x: Math.round(state.cropSelection.x),
            y: Math.round(state.cropSelection.y),
            width: Math.round(state.cropSelection.width),
            height: Math.round(state.cropSelection.height),
            rotation: normaliseRotation(state.rotation || 0),
            source_width: state.fileWidth,
            source_height: state.fileHeight,
            effective_width: Math.round(effective.width || 0),
            effective_height: Math.round(effective.height || 0),
        };
    }

    function drawCropPreview(canvas) {
        if (!canvas || !state.sourceImage || !state.sourceImageReady) {
            return { scale: 1, rect: null, rectScaled: null, rotatedWidth: 0, rotatedHeight: 0 };
        }

        const rotation = normaliseRotation(state.rotation || 0);
        const sourceWidth = state.fileWidth || state.sourceImage.naturalWidth || 1;
        const sourceHeight = state.fileHeight || state.sourceImage.naturalHeight || 1;
        const rotatedWidth = rotation === 90 || rotation === 270 ? sourceHeight : sourceWidth;
        const rotatedHeight = rotation === 90 || rotation === 270 ? sourceWidth : sourceHeight;
        const maxSize = 520;
        const scale = Math.min(maxSize / rotatedWidth, maxSize / rotatedHeight, 1);
        const canvasWidth = Math.max(1, Math.round(rotatedWidth * scale));
        const canvasHeight = Math.max(1, Math.round(rotatedHeight * scale));
        canvas.width = canvasWidth;
        canvas.height = canvasHeight;
        canvas.style.width = canvasWidth + 'px';
        canvas.style.height = canvasHeight + 'px';

        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvasWidth, canvasHeight);
        ctx.save();
        ctx.translate(canvasWidth / 2, canvasHeight / 2);
        ctx.rotate(rotation * Math.PI / 180);
        const drawWidth = sourceWidth * scale;
        const drawHeight = sourceHeight * scale;
        ctx.drawImage(state.sourceImage, -drawWidth / 2, -drawHeight / 2, drawWidth, drawHeight);
        ctx.restore();

        const crop = state.cropSelection || { x: 0, y: 0, width: sourceWidth, height: sourceHeight };
        const rotatedRect = mapRectFromOriginalToRotation(crop, rotation, sourceWidth, sourceHeight);
        const rectScaled = {
            x: rotatedRect.x * scale,
            y: rotatedRect.y * scale,
            width: rotatedRect.width * scale,
            height: rotatedRect.height * scale,
        };

        ctx.save();
        ctx.fillStyle = 'rgba(15, 31, 49, 0.55)';
        ctx.fillRect(0, 0, canvasWidth, canvasHeight);
        ctx.clearRect(rectScaled.x, rectScaled.y, rectScaled.width, rectScaled.height);
        ctx.strokeStyle = '#42a5f5';
        ctx.lineWidth = 2;
        ctx.strokeRect(rectScaled.x + 1, rectScaled.y + 1, Math.max(0, rectScaled.width - 2), Math.max(0, rectScaled.height - 2));
        ctx.restore();

        return {
            scale: scale,
            rect: rotatedRect,
            rectScaled: rectScaled,
            rotatedWidth: rotatedWidth,
            rotatedHeight: rotatedHeight,
        };
    }

    function mapRectFromOriginalToRotation(rect, rotation, width, height) {
        switch (rotation) {
            case 90:
                return {
                    x: rect.y,
                    y: width - (rect.x + rect.width),
                    width: rect.height,
                    height: rect.width,
                };
            case 180:
                return {
                    x: width - (rect.x + rect.width),
                    y: height - (rect.y + rect.height),
                    width: rect.width,
                    height: rect.height,
                };
            case 270:
                return {
                    x: height - (rect.y + rect.height),
                    y: rect.x,
                    width: rect.height,
                    height: rect.width,
                };
            default:
                return {
                    x: rect.x,
                    y: rect.y,
                    width: rect.width,
                    height: rect.height,
                };
        }
    }

    function mapRectFromRotationToOriginal(rect, rotation, width, height) {
        switch (rotation) {
            case 90:
                return {
                    x: width - (rect.y + rect.height),
                    y: rect.x,
                    width: rect.height,
                    height: rect.width,
                };
            case 180:
                return {
                    x: width - (rect.x + rect.width),
                    y: height - (rect.y + rect.height),
                    width: rect.width,
                    height: rect.height,
                };
            case 270:
                return {
                    x: rect.y,
                    y: height - (rect.x + rect.width),
                    width: rect.height,
                    height: rect.width,
                };
            default:
                return {
                    x: rect.x,
                    y: rect.y,
                    width: rect.width,
                    height: rect.height,
                };
        }
    }

    function getCanvasPointerPosition(canvas, event) {
        if (!canvas) {
            return null;
        }
        const rect = canvas.getBoundingClientRect();
        if (!rect.width || !rect.height) {
            return null;
        }
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        return {
            x: (event.clientX - rect.left) * scaleX,
            y: (event.clientY - rect.top) * scaleY,
        };
    }

    function isPointInsideRect(point, rect) {
        if (!point || !rect) {
            return false;
        }
        return point.x >= rect.x && point.x <= rect.x + rect.width && point.y >= rect.y && point.y <= rect.y + rect.height;
    }

    function clamp(value, min, max) {
        return Math.min(Math.max(value, min), max);
    }

    function scheduleQualityEvaluation() {
        if (!ajaxUrl || !state.format || !state.format.id || !state.fileWidth || !state.fileHeight) {
            return;
        }
        if (state.pendingQualityEvaluation) {
            clearTimeout(state.pendingQualityEvaluation);
        }
        state.pendingQualityEvaluation = setTimeout(function () {
            state.pendingQualityEvaluation = null;
            evaluateQuality();
        }, 250);
    }

    async function evaluateQuality() {
        if (!ajaxUrl || !state.format || !state.format.id) {
            return;
        }
        const effective = getEffectiveDimensions();
        if (!effective.width || !effective.height) {
            return;
        }

        state.qualityLoading = true;

        try {
            const response = await requestAjax('getQuality', {
                format_id: state.format.id,
                imageWidth: Math.round(effective.width),
                imageHeight: Math.round(effective.height),
            });
            if (response && response.success) {
                const quality = typeof response.quality === 'number' ? response.quality : null;
                const coordinates = Array.isArray(response.coordinates) ? response.coordinates : [];
                state.qualityScore = quality;
                state.qualityCoordinates = coordinates;
                storeQualityForFormat(state.format, quality, coordinates);
            }
        } catch (error) {
            console.warn('FotoArt Puzzle: impossibile valutare la qualità', error);
        } finally {
            state.qualityLoading = false;
            if (steps[state.currentStepIndex] && (steps[state.currentStepIndex].key === 'crop' || steps[state.currentStepIndex].key === 'preview')) {
                renderStep();
            }
        }
    }

    function storeQualityForFormat(format, quality, coordinates) {
        if (!format) {
            return;
        }
        const key = format.id ? String(format.id) : (format.name || '');
        if (!key) {
            return;
        }
        state.qualityByFormat[key] = {
            quality: typeof quality === 'number' ? quality : null,
            coordinates: Array.isArray(coordinates) ? coordinates : [],
        };
    }

    function getQualityDataForFormat(format) {
        if (!format) {
            return null;
        }
        const key = format.id ? String(format.id) : (format.name || '');
        if (key && state.qualityByFormat[key]) {
            return state.qualityByFormat[key];
        }
        if (typeof format.quality !== 'undefined') {
            return {
                quality: typeof format.quality === 'number' ? format.quality : parseInt(format.quality, 10),
                coordinates: Array.isArray(format.coordinates) ? format.coordinates : [],
            };
        }
        return null;
    }

    function schedulePreviewRefresh(force) {
        if (!state.file || !state.format) {
            return;
        }
        if (state.pendingPreviewTimeout) {
            clearTimeout(state.pendingPreviewTimeout);
        }
        state.pendingPreviewTimeout = setTimeout(function () {
            state.pendingPreviewTimeout = null;
            ensurePreview(force);
        }, 350);
    }

    function scheduleSessionSync() {
        if (!ajaxUrl || !state.sessionId) {
            return;
        }
        if (state.pendingSessionTimeout) {
            clearTimeout(state.pendingSessionTimeout);
        }
        state.pendingSessionTimeout = setTimeout(function () {
            state.pendingSessionTimeout = null;
            sendSessionSync();
        }, 500);
    }

    function sendSessionSync() {
        if (!ajaxUrl || !state.sessionId) {
            return;
        }
        requestAjax('updateSession', {
            session_id: state.sessionId,
            data: buildSessionPayload(),
        }).catch(function (error) {
            console.warn('FotoArt Puzzle: sincronizzazione sessione fallita', error);
        });
    }

    function buildSessionPayload() {
        const payload = {
            id_product: state.idProduct,
            file: state.file,
            file_url: state.fileUrl,
            box_text: state.boxText,
            box_color: state.boxColor,
            box_font: state.boxFont,
            crop: getCropPayload(),
            rotation: normaliseRotation(state.rotation || 0),
            quality: typeof state.qualityScore === 'number' ? state.qualityScore : null,
            quality_coordinates: state.qualityCoordinates,
            preview_dirty: state.previewDirty,
            preview: {
                path: state.previewPath,
                url: state.previewUrl,
                hi_res: state.previewHiResUrl,
            },
            thumbnail: {
                path: state.thumbnailPath,
                url: state.thumbnailUrl,
            },
            asset_map: state.assetMap,
        };

        if (state.format) {
            payload.format = {
                id: state.format.id,
                name: state.format.name,
                reference: state.format.reference,
            };
        }

        if (state.selectedBox) {
            payload.box = {
                id: state.selectedBox.id,
                name: state.selectedBox.name,
                reference: state.selectedBox.reference,
            };
            if (state.boxPreviewUrl) {
                payload.box_preview_url = state.boxPreviewUrl;
            }
        }

        if (state.customizationId) {
            payload.id_customization = state.customizationId;
        }

        return payload;
    }

    async function initSessionWithUpload(response) {
        if (!ajaxUrl) {
            return;
        }

        const payload = {
            session_id: state.sessionId || undefined,
            id_product: state.idProduct,
            file: state.file,
            file_url: state.fileUrl,
            image: {
                width: state.fileWidth,
                height: state.fileHeight,
                name: state.fileName,
            },
            asset_map: state.assetMap,
        };

        try {
            const result = await requestAjax('manageSession', payload);
            if (result && result.success && result.session && result.session.session_id) {
                state.sessionId = result.session.session_id;
            }
        } catch (error) {
            console.warn('FotoArt Puzzle: impossibile inizializzare la sessione', error);
        }
    }

    async function refreshFormatsWithQuality() {
        if (!ajaxUrl || !state.fileWidth || !state.fileHeight) {
            return;
        }

        try {
            const response = await requestAjax('getPuzzles', {
                imageWidth: Math.round(state.fileWidth),
                imageHeight: Math.round(state.fileHeight),
            });
            if (response && response.success && Array.isArray(response.puzzles)) {
                state.formats = response.puzzles;
                state.puzzles = response.puzzles;
                response.puzzles.forEach(function (item) {
                    const qualityData = getQualityDataForFormat(item);
                    storeQualityForFormat(item, qualityData ? qualityData.quality : item.quality, qualityData ? qualityData.coordinates : item.coordinates);
                });
                if (steps[state.currentStepIndex] && steps[state.currentStepIndex].key === 'format') {
                    renderStep();
                }
            }
        } catch (error) {
            console.warn('FotoArt Puzzle: impossibile aggiornare i formati', error);
        }
    }

    function resolveBoxPreview(box) {
        if (!box) {
            return null;
        }
        if (box.preview_url) {
            return box.preview_url;
        }
        if (box.payload && box.payload.preview_url) {
            return box.payload.preview_url;
        }
        if (box.preview && box.preview.indexOf('http') === 0) {
            return box.preview;
        }
        if (box.preview && box.preview.indexOf('/') === 0) {
            return box.preview;
        }
        if (box.preview && typeof prestashop !== 'undefined' && prestashop.urls && prestashop.urls.base_url) {
            return prestashop.urls.base_url.replace(/\/$/, '') + '/' + String(box.preview).replace(/^\/+/, '');
        }
        return box.preview || null;
    }

    function updateOrientation() {
        const effective = getEffectiveDimensions();
        if (!effective.width || !effective.height) {
            state.orientation = null;
            return;
        }
        state.orientation = effective.width >= effective.height ? 'landscape' : 'portrait';
    }

    function getStepIndex(key) {
        for (var i = 0; i < steps.length; i += 1) {
            if (steps[i].key === key) {
                return i;
            }
        }
        return null;
    }

    function createInitialState(config) {
        return {
            idProduct: null,
            currentStepIndex: 0,
            file: null,
            fileUrl: null,
            fileName: '',
            fileWidth: 0,
            fileHeight: 0,
            orientation: null,
            rotation: 0,
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
            previewHiResUrl: null,
            previewPath: null,
            thumbnailUrl: null,
            thumbnailPath: null,
            boxPreviewUrl: null,
            previewDirty: false,
            uploading: false,
            previewLoading: false,
            summaryLoading: false,
            qualityLoading: false,
            qualityScore: null,
            qualityCoordinates: [],
            qualityByFormat: {},
            cropSelection: null,
            cropZoom: 0,
            message: null,
            customizationId: null,
            sessionId: null,
            assetMap: {},
            sourceImage: null,
            sourceImageReady: false,
            sourceImageUrl: null,
            pendingQualityEvaluation: null,
            pendingPreviewTimeout: null,
            pendingSessionTimeout: null,
        };
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
            '    <button type="button" class="btn btn-primary fap-modal__finish is-hidden">' + sanitize(translate('Salva personalizzazione')) + '</button>' +
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
            'Inquadra e ritaglia': 'Inquadra e ritaglia',
            'Anteprima e conferma': 'Anteprima e conferma',
            'Chiudi': 'Chiudi',
            'Indietro': 'Indietro',
            'Avanti': 'Avanti',
            'Aggiungi al carrello': 'Aggiungi al carrello',
            'Salva personalizzazione': 'Salva personalizzazione',
            'Carica immagine': 'Carica immagine',
            'Caricamento in corso...': 'Caricamento in corso...',
            'Caricamento anteprima in corso...': 'Caricamento anteprima in corso...',
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
            'Ritaglio:': 'Ritaglio:',
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
            'Regola il ritaglio per adattare l\'immagine al formato scelto. Trascina l\'area attiva o usa i controlli per ruotare e zoomare.': 'Regola il ritaglio per adattare l\'immagine al formato scelto. Trascina l\'area attiva o usa i controlli per ruotare e zoomare.',
            'Rotazione': 'Rotazione',
            'Ruota a sinistra': 'Ruota a sinistra',
            'Ruota a destra': 'Ruota a destra',
            'Zoom': 'Zoom',
            'Zoom {value}%': 'Zoom {value}%',
            'Definisci il ritaglio trascinando l\'area attiva.': 'Definisci il ritaglio trascinando l\'area attiva.',
            'Area di stampa: {w} x {h} px · {orientation}': 'Area di stampa: {w} x {h} px · {orientation}',
            'Anteprima personalizzata': 'Anteprima personalizzata',
            'Template scatola': 'Template scatola',
            'Anteprima non ancora disponibile': 'Anteprima non ancora disponibile',
            'Apri anteprima in una nuova scheda': 'Apri anteprima in una nuova scheda',
            'Valutazione qualità...': 'Valutazione qualità...',
            'Qualità non disponibile': 'Qualità non disponibile',
            'Personalizzazione salvata. Ora aggiungi il prodotto al carrello per proseguire.': 'Personalizzazione salvata. Ora aggiungi il prodotto al carrello per proseguire.',
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
