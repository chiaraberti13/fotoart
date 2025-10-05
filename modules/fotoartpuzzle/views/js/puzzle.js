(function () {
    const wizard = document.getElementById('fap-wizard');
    if (!wizard) {
        return;
    }

    const launchButton = wizard.querySelector('.fap-launch');
    if (!launchButton) {
        return;
    }

    const config = parseConfig(wizard.dataset.config || '{}');
    const uploadUrl = wizard.dataset.uploadUrl;
    const previewUrl = wizard.dataset.previewUrl;
    const summaryUrl = wizard.dataset.summaryUrl;
    const tokens = {
        upload: wizard.dataset.tokenUpload,
        preview: wizard.dataset.tokenPreview,
        summary: wizard.dataset.tokenSummary,
    };

    if (!uploadUrl || !previewUrl || !summaryUrl) {
        return;
    }

    const addToCartForm = document.querySelector('#add-to-cart-or-refresh') || document.querySelector('form[action*="cart"]');
    const idProductInput = addToCartForm ? addToCartForm.querySelector('input[name="id_product"]') : null;
    if (!addToCartForm || !idProductInput) {
        return;
    }

    const summaryPanel = createSummaryPanel();
    wizard.appendChild(summaryPanel.element);

    const state = {
        currentStepIndex: 0,
        file: null,
        fileUrl: null,
        fileName: '',
        format: null,
        boxText: '',
        boxColor: (config.box && config.box.colors && config.box.colors[0]) || '#000000',
        boxFont: (config.box && config.box.fonts && config.box.fonts[0]) || '',
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
        { key: 'upload', title: translate('Upload your image') },
        { key: 'format', title: translate('Choose the puzzle format') },
        { key: 'box', title: translate('Customize your box') },
        { key: 'preview', title: translate('Preview & confirm') },
    ];

    const modal = createModal();
    document.body.appendChild(modal.root);

    launchButton.addEventListener('click', function () {
        openModal();
    });

    modal.closeButton.addEventListener('click', closeModal);
    modal.backdrop.addEventListener('click', closeModal);
    modal.prevButton.addEventListener('click', function () {
        goToStep(state.currentStepIndex - 1);
    });
    modal.nextButton.addEventListener('click', async function () {
        await handleStepForward();
    });
    modal.finishButton.addEventListener('click', async function () {
        await finalizeCustomization();
    });

    let escapeHandler = null;

    function openModal() {
        state.currentStepIndex = 0;
        renderStep();
        modal.root.classList.add('is-visible');
        modal.root.setAttribute('aria-hidden', 'false');
        launchButton.setAttribute('aria-expanded', 'true');
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
        document.removeEventListener('keydown', escapeHandler);
        escapeHandler = null;
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

        switch (step.key) {
            case 'upload':
                renderUploadStep();
                break;
            case 'format':
                renderFormatStep();
                break;
            case 'box':
                renderBoxStep();
                break;
            case 'preview':
                renderPreviewStep();
                break;
            default:
                break;
        }

        updateFooter();
    }

    function updateFooter() {
        modal.prevButton.disabled = state.currentStepIndex === 0 || state.uploading || state.previewLoading || state.summaryLoading;
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
            modal.finishButton.disabled = !state.file || !state.format || state.summaryLoading || state.previewLoading;
        }
    }

    function renderUploadStep() {
        const info = document.createElement('p');
        const extensions = (config.extensions || []).map(function (ext) {
            return ext.trim().toUpperCase();
        }).filter(Boolean).join(', ');
        info.textContent = translate('Select a JPEG or PNG image up to %max% MB. Allowed extensions: %ext%')
            .replace('%max%', config.maxUploadMb || 0)
            .replace('%ext%', extensions || 'JPG, PNG');
        modal.content.appendChild(info);

        const inputWrapper = document.createElement('div');
        inputWrapper.className = 'fap-field';
        const label = document.createElement('label');
        label.className = 'fap-field__label';
        label.setAttribute('for', 'fap-upload-input');
        label.textContent = translate('Upload image');
        const input = document.createElement('input');
        input.type = 'file';
        input.id = 'fap-upload-input';
        input.accept = (config.extensions || []).map(function (ext) {
            ext = ext.trim();
            if (!ext) {
                return null;
            }
            if (ext[0] === '.') {
                return ext;
            }
            return '.' + ext;
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
        inputWrapper.appendChild(label);
        inputWrapper.appendChild(input);
        modal.content.appendChild(inputWrapper);

        if (state.uploading) {
            const uploading = document.createElement('p');
            uploading.className = 'fap-status';
            uploading.textContent = translate('Uploading image, please wait...');
            modal.content.appendChild(uploading);
        }

        if (state.file) {
            const details = document.createElement('div');
            details.className = 'fap-upload-summary';
            details.innerHTML = '<strong>' + sanitize(state.fileName) + '</strong>';
            if (state.format) {
                details.innerHTML += '<span>' + sanitize(formatLabel(state.format)) + '</span>';
            }
            modal.content.appendChild(details);
        }
    }

    function renderFormatStep() {
        if (!Array.isArray(config.formats) || !config.formats.length) {
            const notice = document.createElement('p');
            notice.textContent = translate('No puzzle formats are configured yet.');
            modal.content.appendChild(notice);
            return;
        }

        const list = document.createElement('div');
        list.className = 'fap-format-list';

        config.formats.forEach(function (item) {
            const card = document.createElement('button');
            card.type = 'button';
            card.className = 'fap-format-card';
            if (state.format && state.format.name === item.name) {
                card.classList.add('is-selected');
            }
            card.innerHTML = '<span class="fap-format-card__name">' + sanitize(item.name) + '</span>' +
                '<span class="fap-format-card__pieces">' + sanitize(item.pieces + ' ' + translate('pieces')) + '</span>' +
                '<span class="fap-format-card__size">' + sanitize(item.width + ' x ' + item.height + ' px') + '</span>';
            card.addEventListener('click', function () {
                state.format = item;
                state.previewDirty = true;
                state.message = null;
                renderStep();
            });
            list.appendChild(card);
        });

        modal.content.appendChild(list);
    }

    function renderBoxStep() {
        const fieldset = document.createElement('div');
        fieldset.className = 'fap-box-options';

        const textField = document.createElement('div');
        textField.className = 'fap-field';
        const label = document.createElement('label');
        label.className = 'fap-field__label';
        label.setAttribute('for', 'fap-box-text');
        label.textContent = translate('Box text');
        const input = document.createElement('input');
        input.type = 'text';
        input.id = 'fap-box-text';
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
            if (max) {
                counter.textContent = state.boxText.length + ' / ' + max;
            } else {
                counter.textContent = state.boxText.length + '';
            }
        }
        updateCounter();

        if (config.box && Array.isArray(config.box.colors) && config.box.colors.length) {
            const colorField = document.createElement('div');
            colorField.className = 'fap-field';
            const colorLabel = document.createElement('label');
            colorLabel.className = 'fap-field__label';
            colorLabel.setAttribute('for', 'fap-box-color');
            colorLabel.textContent = translate('Text color');
            const colorSelect = document.createElement('select');
            colorSelect.id = 'fap-box-color';
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

        if (config.box && Array.isArray(config.box.fonts) && config.box.fonts.length) {
            const fontField = document.createElement('div');
            fontField.className = 'fap-field';
            const fontLabel = document.createElement('label');
            fontLabel.className = 'fap-field__label';
            fontLabel.setAttribute('for', 'fap-box-font');
            fontLabel.textContent = translate('Text font');
            const fontSelect = document.createElement('select');
            fontSelect.id = 'fap-box-font';
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

        modal.content.appendChild(fieldset);
    }

    function renderPreviewStep() {
        const wrapper = document.createElement('div');
        wrapper.className = 'fap-preview';

        if (!state.previewUrl && !state.previewLoading) {
            ensurePreview();
        }

        if (state.previewLoading) {
            const loading = document.createElement('p');
            loading.className = 'fap-status';
            loading.textContent = translate('Generating preview...');
            wrapper.appendChild(loading);
        }

        if (state.previewUrl && !state.previewLoading) {
            const img = document.createElement('img');
            img.className = 'fap-preview__image';
            img.src = state.previewUrl;
            img.alt = translate('Preview of your custom puzzle box');
            wrapper.appendChild(img);
        }

        const details = document.createElement('div');
        details.className = 'fap-preview__details';

        if (state.fileName) {
            const fileRow = document.createElement('p');
            fileRow.innerHTML = '<strong>' + translate('Image') + ':</strong> ' + sanitize(state.fileName);
            details.appendChild(fileRow);
        }
        if (state.format) {
            const formatRow = document.createElement('p');
            formatRow.innerHTML = '<strong>' + translate('Format') + ':</strong> ' + sanitize(formatLabel(state.format));
            details.appendChild(formatRow);
        }
        if (state.boxText) {
            const textRow = document.createElement('p');
            textRow.innerHTML = '<strong>' + translate('Text') + ':</strong> ' + sanitize(state.boxText);
            details.appendChild(textRow);
        }
        const colorRow = document.createElement('p');
        colorRow.innerHTML = '<strong>' + translate('Color') + ':</strong> ' + sanitize(state.boxColor || '-');
        details.appendChild(colorRow);
        const fontRow = document.createElement('p');
        fontRow.innerHTML = '<strong>' + translate('Font') + ':</strong> ' + sanitize(state.boxFont || '-');
        details.appendChild(fontRow);

        wrapper.appendChild(details);

        const regenerate = document.createElement('button');
        regenerate.type = 'button';
        regenerate.className = 'btn btn-outline-secondary fap-preview__refresh';
        regenerate.textContent = translate('Refresh preview');
        regenerate.addEventListener('click', function () {
            ensurePreview(true);
        });
        regenerate.disabled = state.previewLoading;
        wrapper.appendChild(regenerate);

        modal.content.appendChild(wrapper);
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
            await validateImage(file);
            const response = await uploadFile(file);
            state.file = response.file;
            state.fileUrl = response.download_url;
            state.fileName = file.name;
            state.uploading = false;
            state.previewUrl = null;
            setMessage('success', translate('Image uploaded successfully.'));
            renderStep();
        } catch (error) {
            state.uploading = false;
            state.file = null;
            state.fileUrl = null;
            state.fileName = '';
            state.previewUrl = null;
            state.previewDirty = false;
            throw error;
        }
    }

    function validateImage(file) {
        return new Promise(function (resolve, reject) {
            const objectUrl = URL.createObjectURL(file);
            const image = new Image();
            image.onload = function () {
                URL.revokeObjectURL(objectUrl);
                const minWidth = config.minWidth || 0;
                const minHeight = config.minHeight || 0;
                if ((minWidth && image.width < minWidth) || (minHeight && image.height < minHeight)) {
                    reject(new Error(translate('Image dimensions are too small. Minimum is %w%x%h% pixels.')
                        .replace('%w%', minWidth)
                        .replace('%h%', minHeight)));
                    return;
                }
                resolve();
            };
            image.onerror = function () {
                URL.revokeObjectURL(objectUrl);
                reject(new Error(translate('Unable to read the selected image.')));
            };
            image.src = objectUrl;
        });
    }

    function uploadFile(file) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('token', tokens.upload || '');
        return fetch(uploadUrl, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
        }).then(handleJsonResponse);
    }

    function ensurePreview(force) {
        if (!state.file) {
            setMessage('error', translate('Please upload an image first.'));
            return;
        }
        if (!state.format) {
            setMessage('error', translate('Please choose a puzzle format.'));
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
        payload.append('token', tokens.preview || '');
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
            throw new Error(translate('Unexpected server response.'));
        }
        return response.json().then(function (json) {
            if (!json) {
                throw new Error(translate('Invalid server response.'));
            }
            if (json.success) {
                return json;
            }
            throw new Error(json.message || translate('The request failed.'));
        });
    }

    async function handleStepForward() {
        const step = steps[state.currentStepIndex];
        if (step.key === 'upload' && !state.file) {
            setMessage('error', translate('Please upload an image to continue.'));
            return;
        }
        if (step.key === 'format' && !state.format) {
            setMessage('error', translate('Please choose a format to continue.'));
            return;
        }
        if (state.currentStepIndex < steps.length - 1) {
            goToStep(state.currentStepIndex + 1);
        }
    }

    async function finalizeCustomization() {
        if (!state.file || !state.format) {
            setMessage('error', translate('Upload an image and choose a format before finishing.'));
            return;
        }

        state.summaryLoading = true;
        state.message = null;
        updateFooter();

        try {
            const payload = new URLSearchParams();
            payload.append('token', tokens.summary || '');
            payload.append('file', state.file);
            payload.append('box_text', state.boxText || '');
            payload.append('box_color', state.boxColor || '');
            payload.append('box_font', state.boxFont || '');
            payload.append('format', state.format.name || '');
            payload.append('id_product', idProductInput.value);
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
            triggerAddToCart();
            showToast(translate('Your custom puzzle has been added to the cart.'));
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
            title.textContent = translate('Customization ready');
            summaryPanel.content.appendChild(title);

            const list = document.createElement('ul');
            list.className = 'fap-summary-list';

            const fileItem = document.createElement('li');
            fileItem.innerHTML = '<span>' + translate('Image') + ':</span> ' + sanitize(state.fileName);
            list.appendChild(fileItem);

            if (state.format) {
                const formatItem = document.createElement('li');
                formatItem.innerHTML = '<span>' + translate('Format') + ':</span> ' + sanitize(formatLabel(state.format));
                list.appendChild(formatItem);
            }

            if (state.boxText) {
                const textItem = document.createElement('li');
                textItem.innerHTML = '<span>' + translate('Text') + ':</span> ' + sanitize(state.boxText);
                list.appendChild(textItem);
            }

            if (state.boxColor) {
                const colorItem = document.createElement('li');
                colorItem.innerHTML = '<span>' + translate('Color') + ':</span> ' + sanitize(state.boxColor);
                list.appendChild(colorItem);
            }

            if (state.boxFont) {
                const fontItem = document.createElement('li');
                fontItem.innerHTML = '<span>' + translate('Font') + ':</span> ' + sanitize(state.boxFont);
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
            '  <button type="button" class="fap-modal__close" aria-label="' + sanitize(translate('Close')) + '">&times;</button>' +
            '  <h2 id="fap-modal-title" class="fap-modal__title"></h2>' +
            '  <div class="fap-modal__content"></div>' +
            '  <div class="fap-modal__footer">' +
            '    <button type="button" class="btn btn-secondary fap-modal__prev">' + sanitize(translate('Back')) + '</button>' +
            '    <button type="button" class="btn btn-primary fap-modal__next">' + sanitize(translate('Next')) + '</button>' +
            '    <button type="button" class="btn btn-primary fap-modal__finish is-hidden">' + sanitize(translate('Add to cart')) + '</button>' +
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
        const pieces = format.pieces ? format.pieces + ' ' + translate('pieces') : '';
        return format.name + (pieces ? ' (' + pieces + ')' : '');
    }

    function parseConfig(json) {
        try {
            return JSON.parse(json);
        } catch (error) {
            return {};
        }
    }

    function translate(text) {
        return typeof prestashop !== 'undefined' && prestashop.trans ? prestashop.trans(text, {}, 'Modules.Fotoartpuzzle.Shop') : text;
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
            try {
                console.log(message);
            } catch (error) {
                // ignore
            }
        }
    }
})();
