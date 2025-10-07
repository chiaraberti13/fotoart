/**
 * FotoArt Puzzle - Admin Configuration Script
 */
(function() {
    'use strict';
    
    // Attendi che jQuery e il DOM siano pronti
    function initAdminConfig() {
        if (typeof jQuery === 'undefined') {
            console.error('jQuery non è ancora caricato. Riprovo...');
            setTimeout(initAdminConfig, 100);
            return;
        }
        
        var $ = jQuery;
        
        function showMessage(type, message) {
            if ($.growl) {
                var options = { title: '', message: message };
                if (type === 'error') {
                    $.growl.error(options);
                } else {
                    $.growl.notice(options);
                }
            } else {
                alert(message);
            }
        }

        function sanitizeHex(value) {
            if (!value) {
                return '';
            }
            var upper = value.toString().trim().toUpperCase();
            if (upper.charAt(0) !== '#') {
                upper = '#' + upper;
            }
            if (/^#[0-9A-F]{6}$/.test(upper)) {
                return upper;
            }
            return '';
        }

        $(function () {
            var config = window.fapAdminConfig || {};
            var products = config.products || [];
            var combinations = config.combinations || [];
            var fonts = config.fonts || [];
            var legacyMappings = config.legacyMappings || [];
            var ajaxUrl = config.ajaxUrl || '';

            var $productList = $('#fap-product-list');
            var $productInput = $('#fap-product-id');
            var $productField = $('#fap-puzzle-products');

            var $combinationField = $('#fap-box-color-combinations');
            var $colorList = $('#fap-color-combinations');
            var $boxColor = $('#fap-box-color');
            var $boxColorHex = $('#fap-box-color-hex');
            var $textColor = $('#fap-box-text-color');
            var $textColorHex = $('#fap-box-text-color-hex');

            var $fontList = $('#fap-font-list');
            var $fontInput = $('#fap-font-upload');
            var $fontField = $('#fap-custom-fonts');

            var $legacyField = $('#fap-legacy-map');
            var $legacyTable = $('#fap-legacy-table tbody');
            var $legacyInputs = {
                product: $('#fap-legacy-product'),
                attribute: $('#fap-legacy-attribute'),
                code: $('#fap-legacy-code'),
                pieces: $('#fap-legacy-pieces'),
                width: $('#fap-legacy-width'),
                height: $('#fap-legacy-height'),
                price: $('#fap-legacy-price'),
                available: $('input[name="fap-legacy-available"]')
            };

            function rebuildProductList() {
                $productList.empty();
                products.forEach(function (product) {
                    var $item = $('<div class="fap-product-item"/>').attr('data-product-id', product.id_product);
                    $('<span/>').addClass('fap-product-id').text('#' + product.id_product).appendTo($item);
                    $('<span/>').addClass('fap-product-name').text(product.name || '').appendTo($item);
                    $('<button/>')
                        .addClass('btn btn-link btn-sm fap-remove-product')
                        .attr('type', 'button')
                        .text(config.translations.remove)
                        .appendTo($item);
                    $productList.append($item);
                });
                var ids = products.map(function (product) { return product.id_product; });
                $productField.val(ids.join(','));
            }

            function rebuildColorList() {
                $colorList.empty();
                combinations.forEach(function (combination, index) {
                    var $item = $('<div class="fap-color-combination"/>').attr('data-index', index);
                    $('<div/>').addClass('fap-color-chip').css('background-color', combination.box).appendTo($item);
                    $('<span/>').addClass('fap-color-label').text('Scatola: ' + combination.box).appendTo($item);
                    $('<div/>').addClass('fap-color-chip').css('background-color', combination.text).appendTo($item);
                    $('<span/>').addClass('fap-color-label').text('Testo: ' + combination.text).appendTo($item);
                    $('<button/>')
                        .addClass('btn btn-link btn-sm fap-remove-combination')
                        .attr('type', 'button')
                        .text(config.translations.remove)
                        .appendTo($item);
                    $colorList.append($item);
                });
                $combinationField.val(JSON.stringify(combinations));
            }

            function rebuildFontList() {
                $fontList.empty();
                fonts.forEach(function (font) {
                    var fontName = font && font.name ? font.name : '';
                    var fontFile = font && font.filename ? font.filename : fontName;
                    var $item = $('<div class="fap-font-item"/>').attr('data-font-name', fontFile);
                    $('<span/>').addClass('fap-font-name').text(fontName).appendTo($item);
                    if (font && font.url) {
                        $('<span/>').addClass('fap-font-file').text(' (' + fontFile + ')').appendTo($item);
                    }
                    $('<button/>')
                        .addClass('btn btn-link btn-sm fap-remove-font')
                        .attr('type', 'button')
                        .text(config.translations.remove)
                        .appendTo($item);
                    $fontList.append($item);
                });
                var stored = fonts.map(function (font) {
                    return font && font.name ? font.name : font;
                });
                $fontField.val(JSON.stringify(stored));
            }

            function rebuildLegacyTable() {
                $legacyTable.empty();
                if (!legacyMappings.length) {
                    $('<tr/>')
                        .addClass('fap-legacy-empty')
                        .append(
                            $('<td/>')
                                .attr('colspan', 9)
                                .addClass('text-center text-muted')
                                .text(config.translations.legacyEmpty || '')
                        )
                        .appendTo($legacyTable);
                } else {
                    legacyMappings.forEach(function (mapping, index) {
                        var $row = $('<tr/>').attr('data-index', index);
                        $('<td/>').text('#' + mapping.id_product).appendTo($row);
                        $('<td/>').text(mapping.id_product_attribute ? mapping.id_product_attribute : '-').appendTo($row);
                        $('<td/>').text(mapping.legacy_code || '').appendTo($row);
                        $('<td/>').text(mapping.pieces ? mapping.pieces : '-').appendTo($row);
                        $('<td/>').text(mapping.width_mm ? mapping.width_mm : '-').appendTo($row);
                        $('<td/>').text(mapping.height_mm ? mapping.height_mm : '-').appendTo($row);
                        $('<td/>').text(mapping.price ? mapping.price : '-').appendTo($row);
                        $('<td/>').append(
                            $('<span/>')
                                .addClass('label ' + (mapping.available ? 'label-success' : 'label-danger'))
                                .text(mapping.available ? config.translations.yes : config.translations.no)
                        ).appendTo($row);
                        $('<td/>')
                            .addClass('text-right')
                            .append(
                                $('<button/>')
                                    .addClass('btn btn-link btn-sm fap-legacy-remove')
                                    .attr('type', 'button')
                                    .text(config.translations.remove)
                            )
                            .appendTo($row);
                        $legacyTable.append($row);
                    });
                }

                $legacyField.val(JSON.stringify(legacyMappings));
            }

            function resetLegacyInputs() {
                $legacyInputs.product.val('');
                $legacyInputs.attribute.val('');
                $legacyInputs.code.val('');
                $legacyInputs.pieces.val('');
                $legacyInputs.width.val('');
                $legacyInputs.height.val('');
                $legacyInputs.price.val('');
                $legacyInputs.available.filter('[value="1"]').prop('checked', true);
            }

            function getLegacyAvailability() {
                var selected = $legacyInputs.available.filter(':checked').val();
                return selected === '1';
            }

            function resolveErrorMessage(payload) {
                if (!payload) {
                    return config.translations.error;
                }

                if (payload.responseJSON && payload.responseJSON.message) {
                    return payload.responseJSON.message;
                }

                if (payload.message) {
                    return payload.message;
                }

                return config.translations.error;
            }

            function handleAjaxError(payload) {
                showMessage('error', resolveErrorMessage(payload));
            }

            $('#fap-add-product').on('click', function () {
                var id = parseInt($productInput.val(), 10);
                if (!id) {
                    showMessage('error', config.translations.error);
                    return;
                }

                var requestData = {
                    ajax: 1,
                    fap_action: 'addProduct',
                    productId: id
                };

                if (config.token) {
                    requestData.token = config.token;
                }

                $.post(ajaxUrl, requestData).done(function (response) {
                    if (!response || !response.success) {
                        handleAjaxError(response);
                        return;
                    }
                    products = response.products || [];
                    rebuildProductList();
                    $productInput.val('');
                    showMessage('notice', config.translations.success);
                }).fail(handleAjaxError);
            });

            $productList.on('click', '.fap-remove-product', function () {
                var id = parseInt($(this).closest('.fap-product-item').data('product-id'), 10);
                if (!id) {
                    return;
                }
                var requestData = {
                    ajax: 1,
                    fap_action: 'removeProduct',
                    productId: id
                };

                if (config.token) {
                    requestData.token = config.token;
                }

                $.post(ajaxUrl, requestData).done(function (response) {
                    if (!response || !response.success) {
                        handleAjaxError(response);
                        return;
                    }
                    products = response.products || [];
                    rebuildProductList();
                    showMessage('notice', config.translations.success);
                }).fail(handleAjaxError);
            });

            var currentColors = {
                box: sanitizeHex($boxColorHex.val()),
                text: sanitizeHex($textColorHex.val())
            };
            var suppressColorEvents = false;

            function getDefaultColor(type) {
                return type === 'box' ? '#FFFFFF' : '#000000';
            }

            function applyColor(type, value) {
                var sanitized = sanitizeHex(value)
                    || sanitizeHex(currentColors[type])
                    || getDefaultColor(type);

                currentColors[type] = sanitized;

                suppressColorEvents = true;
                if (type === 'box') {
                    $boxColorHex.val(sanitized);
                    $boxColor.val(sanitized.toLowerCase());
                    $('#fap-box-color-preview').css('background-color', sanitized);
                } else {
                    $textColorHex.val(sanitized);
                    $textColor.val(sanitized.toLowerCase());
                    $('#fap-box-text-color-preview').css('background-color', sanitized);
                }
                suppressColorEvents = false;
            }

            function handleColorInput(type, rawValue) {
                var sanitized = sanitizeHex(rawValue);
                if (!sanitized) {
                    applyColor(type, currentColors[type]);
                    return;
                }
                applyColor(type, sanitized);
            }

            $boxColor.on('change input', function () {
                if (suppressColorEvents) {
                    return;
                }
                handleColorInput('box', $(this).val());
            });

            $boxColorHex.on('change input', function () {
                if (suppressColorEvents) {
                    return;
                }
                handleColorInput('box', $(this).val());
            });

            $textColor.on('change input', function () {
                if (suppressColorEvents) {
                    return;
                }
                handleColorInput('text', $(this).val());
            });

            $textColorHex.on('change input', function () {
                if (suppressColorEvents) {
                    return;
                }
                handleColorInput('text', $(this).val());
            });

            applyColor('box', currentColors.box);
            applyColor('text', currentColors.text);

            $('#fap-add-color-combination').on('click', function () {
                var box = sanitizeHex($boxColorHex.val());
                var text = sanitizeHex($textColorHex.val());
                if (!box || !text) {
                    showMessage('error', config.translations.error);
                    return;
                }

                $.post(ajaxUrl, {
                    ajax: 1,
                    action: 'addColorCombination',
                    boxColor: box,
                    textColor: text
                }).done(function (response) {
                    if (!response || !response.success) {
                        handleAjaxError(response);
                        return;
                    }
                    combinations = response.combinations || [];
                    rebuildColorList();
                    showMessage('notice', config.translations.success);
                }).fail(handleAjaxError);
            });

            $colorList.on('click', '.fap-remove-combination', function () {
                var index = parseInt($(this).closest('.fap-color-combination').data('index'), 10);
                if (isNaN(index)) {
                    return;
                }

                $.post(ajaxUrl, {
                    ajax: 1,
                    action: 'removeColorCombination',
                    index: index
                }).done(function (response) {
                    if (!response || !response.success) {
                        handleAjaxError(response);
                        return;
                    }
                    combinations = response.combinations || [];
                    rebuildColorList();
                    showMessage('notice', config.translations.success);
                }).fail(handleAjaxError);
            });

            $('#fap-add-font').on('click', function () {
                var file = $fontInput[0].files && $fontInput[0].files[0];
                if (!file) {
                    showMessage('error', config.translations.error);
                    return;
                }

                var formData = new FormData();
                formData.append('ajax', 1);
                formData.append('action', 'uploadFont');
                formData.append('font', file);

                $.ajax({
                    url: ajaxUrl,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false
                }).done(function (response) {
                    if (!response || !response.success) {
                        handleAjaxError(response);
                        return;
                    }
                    fonts = response.fonts || [];
                    rebuildFontList();
                    $fontInput.val('');
                    showMessage('notice', config.translations.success);
                }).fail(handleAjaxError);
            });

            $fontList.on('click', '.fap-remove-font', function () {
                var fontName = $(this).closest('.fap-font-item').data('font-name');
                if (!fontName) {
                    return;
                }

                $.post(ajaxUrl, {
                    ajax: 1,
                    action: 'removeFont',
                    fontName: fontName
                }).done(function (response) {
                    if (!response || !response.success) {
                        handleAjaxError(response);
                        return;
                    }
                    fonts = response.fonts || [];
                    rebuildFontList();
                    showMessage('notice', config.translations.success);
                }).fail(handleAjaxError);
            });

            $('#fap-legacy-add').on('click', function () {
                var mapping = {
                    id_product: parseInt($legacyInputs.product.val(), 10) || 0,
                    id_product_attribute: parseInt($legacyInputs.attribute.val(), 10) || 0,
                    legacy_code: ($legacyInputs.code.val() || '').trim(),
                    pieces: parseInt($legacyInputs.pieces.val(), 10) || null,
                    width_mm: parseInt($legacyInputs.width.val(), 10) || null,
                    height_mm: parseInt($legacyInputs.height.val(), 10) || null,
                    price: ($legacyInputs.price.val() || '').trim(),
                    available: getLegacyAvailability()
                };

                if (!mapping.id_product || !mapping.legacy_code) {
                    showMessage('error', config.translations.legacyValidation || config.translations.error);
                    return;
                }

                if (mapping.price) {
                    var normalised = mapping.price.replace(',', '.');
                    var parsed = parseFloat(normalised);
                    if (isNaN(parsed) || parsed < 0) {
                        showMessage('error', config.translations.legacyValidation || config.translations.error);
                        return;
                    }
                    mapping.price = parsed.toFixed(2);
                } else {
                    mapping.price = null;
                }

                legacyMappings.push({
                    id_product: mapping.id_product,
                    id_product_attribute: mapping.id_product_attribute > 0 ? mapping.id_product_attribute : 0,
                    legacy_code: mapping.legacy_code,
                    pieces: mapping.pieces && mapping.pieces > 0 ? mapping.pieces : null,
                    width_mm: mapping.width_mm && mapping.width_mm > 0 ? mapping.width_mm : null,
                    height_mm: mapping.height_mm && mapping.height_mm > 0 ? mapping.height_mm : null,
                    price: mapping.price,
                    available: mapping.available
                });

                rebuildLegacyTable();
                resetLegacyInputs();
                showMessage('notice', config.translations.success);
            });

            $legacyTable.on('click', '.fap-legacy-remove', function () {
                var index = parseInt($(this).closest('tr').data('index'), 10);
                if (isNaN(index)) {
                    return;
                }

                legacyMappings.splice(index, 1);
                rebuildLegacyTable();
                showMessage('notice', config.translations.success);
            });

            rebuildProductList();
            rebuildColorList();
            rebuildFontList();
            rebuildLegacyTable();
        });
    }
    
    // Avvia l'inizializzazione
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdminConfig);
    } else {
        initAdminConfig();
    }
})();