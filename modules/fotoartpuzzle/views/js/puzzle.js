(function () {
    var wizard = document.getElementById('fap-wizard');
    if (!wizard) {
        return;
    }

    var modal = wizard.querySelector('.fap-modal');
    var launchBtn = wizard.querySelector('.fap-launch');
    var closeBtn = wizard.querySelector('.fap-modal__close');
    var overlay = wizard.querySelector('.fap-modal__overlay');
    var form = wizard.querySelector('.fap-form');
    var fileInput = form ? form.querySelector('input[type="file"]') : null;
    var previewBtn = form ? form.querySelector('.fap-preview-btn') : null;
    var previewFigure = wizard.querySelector('.fap-preview__figure');
    var previewImage = previewFigure ? previewFigure.querySelector('img') : null;
    var feedback = wizard.querySelector('.fap-feedback');

    if (!modal || !launchBtn || !form || !fileInput) {
        return;
    }

    var state = {
        filePath: null,
        previewUrl: null,
        busy: false
    };

    function setFeedback(message, type) {
        if (!feedback) {
            return;
        }
        feedback.classList.remove('fap-feedback--error', 'fap-feedback--success');
        if (!message) {
            feedback.textContent = '';
            feedback.hidden = true;
            return;
        }
        feedback.textContent = message;
        feedback.hidden = false;
        if (type === 'success') {
            feedback.classList.add('fap-feedback--success');
        } else {
            feedback.classList.add('fap-feedback--error');
        }
    }

    function setBusy(isBusy) {
        state.busy = isBusy;
        form.classList.toggle('fap-form--loading', isBusy);
        if (launchBtn) {
            launchBtn.disabled = isBusy;
        }
        var submitBtn = form.querySelector('.fap-submit');
        if (submitBtn) {
            submitBtn.disabled = isBusy;
        }
        if (previewBtn) {
            previewBtn.disabled = isBusy;
        }
    }

    function openModal() {
        setFeedback('', 'error');
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('fap-modal-open');
    }

    function closeModal(force) {
        if (state.busy && !force) {
            return;
        }
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('fap-modal-open');
        setFeedback('', 'error');
    }

    function getProductAttributeId() {
        var attributeInput = document.querySelector('#add-to-cart-or-refresh input[name="id_product_attribute"]');
        if (attributeInput && attributeInput.value) {
            return attributeInput.value;
        }
        return '0';
    }

    function getFormData() {
        var data = new URLSearchParams();
        data.append('box_text', (form.querySelector('#fap-text') || { value: '' }).value || '');
        data.append('box_color', (form.querySelector('#fap-color') || { value: '#FFFFFF' }).value || '#FFFFFF');
        data.append('box_font', (form.querySelector('#fap-font') || { value: '' }).value || '');
        var formatField = form.querySelector('#fap-format');
        if (formatField) {
            data.append('format', formatField.value || '');
        }
        return data;
    }

    function refreshPreview() {
        if (!state.filePath) {
            setFeedback(wizard.dataset.msgPreviewMissing || 'Upload a file first.', 'error');
            return;
        }
        setBusy(true);
        var data = getFormData();
        data.append('token', wizard.dataset.tokenPreview);
        data.append('file', state.filePath);

        fetch(wizard.dataset.previewUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: data
        })
            .then(asJson)
            .then(function (response) {
                if (!response.success) {
                    throw new Error(response.message || wizard.dataset.msgGenericError);
                }
                state.previewUrl = response.download_url;
                if (previewImage) {
                    previewImage.src = response.download_url;
                }
                if (previewFigure) {
                    previewFigure.hidden = false;
                }
                setFeedback('', 'error');
            })
            .catch(function (error) {
                setFeedback(error.message || wizard.dataset.msgGenericError, 'error');
            })
            .finally(function () {
                setBusy(false);
            });
    }

    function uploadFile(file) {
        if (!file) {
            return;
        }
        setBusy(true);
        setFeedback('', 'error');
        var payload = new FormData();
        payload.append('token', wizard.dataset.tokenUpload);
        payload.append('file', file);

        fetch(wizard.dataset.uploadUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: payload
        })
            .then(asJson)
            .then(function (response) {
                if (!response.success) {
                    throw new Error(response.message || wizard.dataset.msgGenericError);
                }
                state.filePath = response.file;
                state.previewUrl = response.download_url;
                if (previewImage && response.download_url) {
                    previewImage.src = response.download_url;
                }
                if (previewFigure) {
                    previewFigure.hidden = false;
                }
                setFeedback(wizard.dataset.msgUploadSuccess || 'Upload completed.', 'success');
            })
            .catch(function (error) {
                state.filePath = null;
                setFeedback(error.message || wizard.dataset.msgGenericError, 'error');
            })
            .finally(function () {
                setBusy(false);
            });
    }

    function submitCustomization(event) {
        event.preventDefault();
        if (state.busy) {
            return;
        }
        if (!state.filePath) {
            setFeedback(wizard.dataset.msgPreviewMissing || wizard.dataset.msgGenericError, 'error');
            return;
        }
        setBusy(true);
        var data = getFormData();
        data.append('token', wizard.dataset.tokenSummary);
        data.append('file', state.filePath);
        data.append('id_product', wizard.dataset.idProduct || '0');
        data.append('id_product_attribute', getProductAttributeId());

        fetch(wizard.dataset.summaryUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: data
        })
            .then(asJson)
            .then(function (response) {
                if (!response.success) {
                    throw new Error(response.message || wizard.dataset.msgGenericError);
                }
                setFeedback(wizard.dataset.msgSummarySuccess || 'Added to cart.', 'success');
                closeModal(true);
                if (window.prestashop && typeof window.prestashop.emit === 'function') {
                    window.prestashop.emit('updateCart', {
                        reason: {
                            idProduct: parseInt(wizard.dataset.idProduct || '0', 10),
                            idProductAttribute: parseInt(getProductAttributeId(), 10),
                            linkAction: 'add-to-cart'
                        }
                    });
                }
                if (window.prestashop && window.prestashop.urls && window.prestashop.urls.pages && window.prestashop.urls.pages.cart) {
                    window.location.href = window.prestashop.urls.pages.cart;
                } else {
                    window.location.reload();
                }
            })
            .catch(function (error) {
                setFeedback(error.message || wizard.dataset.msgGenericError, 'error');
            })
            .finally(function () {
                setBusy(false);
            });
    }

    function asJson(response) {
        if (!response.ok) {
            throw new Error(wizard.dataset.msgGenericError);
        }
        return response.json();
    }

    launchBtn.addEventListener('click', function () {
        if (!state.busy) {
            openModal();
        }
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            closeModal(false);
        });
    }
    if (overlay) {
        overlay.addEventListener('click', function () {
            closeModal(false);
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModal(false);
        }
    });

    fileInput.addEventListener('change', function (event) {
        if (event.target.files && event.target.files.length) {
            uploadFile(event.target.files[0]);
        }
    });

    if (previewBtn) {
        previewBtn.addEventListener('click', function (event) {
            event.preventDefault();
            refreshPreview();
        });
    }

    form.addEventListener('submit', submitCustomization);
})();
