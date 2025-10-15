(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    initializeCustomizer();
  });

  function initializeCustomizer() {
    var fileInput = document.getElementById('puzzle-file');
    var saveButton = document.getElementById('puzzle-save');
    var uploadStatus = document.getElementById('puzzle-upload-status');

    if (!fileInput || !saveButton) {
      console.error('Customizer elements not found');
      return;
    }

    var state = {
      token: null,
      file: null,
      configuration: {},
      imageLoaded: false
    };

    fileInput.addEventListener('change', function (event) {
      var file = event.target.files[0];
      if (!file) return;

      if (!validateFile(file)) {
        return;
      }

      uploadFile(file);
    });

    function validateFile(file) {
      var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/tiff'];
      if (allowedTypes.indexOf(file.type) === -1) {
        showError('Invalid file type. Please upload JPG, PNG, WEBP or TIFF.');
        return false;
      }

      var maxSize = 50 * 1024 * 1024;
      if (file.size > maxSize) {
        showError('File too large. Maximum size: 50 MB');
        return false;
      }

      return true;
    }

    function uploadFile(file) {
      showStatus('Uploading...', 'info');

      var formData = new FormData();
      formData.append('file', file);
      formData.append('ajax', 1);
      if (window.puzzleCustomizer && window.puzzleCustomizer.csrfToken) {
        formData.append('token', window.puzzleCustomizer.csrfToken);
      }

      fetch(window.puzzleCustomizer.uploadUrl, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      })
        .then(function (response) {
          if (!response.ok) {
            return response
              .json()
              .then(function (data) {
                throw new Error(data.message || 'Upload failed with status: ' + response.status);
              })
              .catch(function () {
                throw new Error('Upload failed with status: ' + response.status);
              });
          }
          return response.json();
        })
        .then(function (json) {
          if (json.success) {
            state.token = json.token;
            state.file = json.file;
            state.imageLoaded = true;

            showStatus('Image uploaded successfully!', 'success');

            if (json.warnings && json.warnings.length > 0) {
              showWarnings(json.warnings);
            }

            var imageUrl = window.puzzleCustomizer.uploadsUrl + '/temp/' + json.file;
            window.puzzleEditor.setImage(imageUrl);

            enableEditorControls();

          } else {
            showError(json.message || 'Upload failed');
          }
        })
        .catch(function (error) {
          console.error('Upload error:', error);
          showError('Error: ' + error.message);
        });
    }

    function enableEditorControls() {
      var zoomSlider = document.getElementById('zoom-slider');
      if (zoomSlider) {
        zoomSlider.disabled = false;
        zoomSlider.addEventListener('input', function (e) {
          var zoom = parseFloat(e.target.value);
          window.puzzleEditor.setZoom(zoom);
          var zoomValue = document.getElementById('zoom-value');
          if (zoomValue) {
            zoomValue.textContent = Math.round(zoom * 100) + '%';
          }
        });
      }

      var rotateLeftBtn = document.getElementById('rotate-left');
      var rotateRightBtn = document.getElementById('rotate-right');
      if (rotateLeftBtn) {
        rotateLeftBtn.disabled = false;
        rotateLeftBtn.addEventListener('click', function () {
          window.puzzleEditor.rotate(-90);
        });
      }
      if (rotateRightBtn) {
        rotateRightBtn.disabled = false;
        rotateRightBtn.addEventListener('click', function () {
          window.puzzleEditor.rotate(90);
        });
      }

      var flipHBtn = document.getElementById('flip-horizontal');
      var flipVBtn = document.getElementById('flip-vertical');
      if (flipHBtn) {
        flipHBtn.addEventListener('click', function () {
          window.puzzleEditor.flipHorizontal();
        });
      }
      if (flipVBtn) {
        flipVBtn.addEventListener('click', function () {
          window.puzzleEditor.flipVertical();
        });
      }

      var cropBtn = document.getElementById('crop-button');
      var applyCropBtn = document.getElementById('apply-crop');
      var cancelCropBtn = document.getElementById('cancel-crop');

      if (cropBtn) {
        cropBtn.addEventListener('click', function () {
          window.puzzleEditor.enableCrop();
          cropBtn.style.display = 'none';
          if (applyCropBtn) applyCropBtn.style.display = 'inline-block';
          if (cancelCropBtn) cancelCropBtn.style.display = 'inline-block';
        });
      }

      if (applyCropBtn) {
        applyCropBtn.addEventListener('click', function () {
          window.puzzleEditor.applyCrop();
          if (cropBtn) cropBtn.style.display = 'inline-block';
          applyCropBtn.style.display = 'none';
          if (cancelCropBtn) cancelCropBtn.style.display = 'none';
        });
      }

      if (cancelCropBtn) {
        cancelCropBtn.addEventListener('click', function () {
          window.puzzleEditor.cancelCrop();
          if (cropBtn) cropBtn.style.display = 'inline-block';
          cancelCropBtn.style.display = 'none';
          if (applyCropBtn) applyCropBtn.style.display = 'none';
        });
      }

      var filterButtons = document.querySelectorAll('[data-filter]');
      filterButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
          var filter = this.getAttribute('data-filter');
          window.puzzleEditor.applyFilter(filter);
        });
      });

      var addTextBtn = document.getElementById('add-text');
      if (addTextBtn) {
        addTextBtn.addEventListener('click', function () {
          var text = prompt('Enter text:');
          if (text) {
            var fontSelect = document.getElementById('puzzle-font');
            var colorSelect = document.getElementById('puzzle-text-color');

            var selectedFontOption = null;
            if (fontSelect) {
              if (fontSelect.selectedOptions && fontSelect.selectedOptions.length) {
                selectedFontOption = fontSelect.selectedOptions[0];
              } else if (typeof fontSelect.selectedIndex === 'number' && fontSelect.selectedIndex >= 0) {
                selectedFontOption = fontSelect.options[fontSelect.selectedIndex];
              }
            }

            var selectedColorOption = null;
            if (colorSelect) {
              if (colorSelect.selectedOptions && colorSelect.selectedOptions.length) {
                selectedColorOption = colorSelect.selectedOptions[0];
              } else if (typeof colorSelect.selectedIndex === 'number' && colorSelect.selectedIndex >= 0) {
                selectedColorOption = colorSelect.options[colorSelect.selectedIndex];
              }
            }

            var fontFamily = selectedFontOption ? selectedFontOption.textContent.trim() : 'Arial';
            var colorHex = '#000000';

            if (selectedColorOption) {
              colorHex = selectedColorOption.getAttribute('data-hex') || selectedColorOption.value || colorHex;
            }

            window.puzzleEditor.addText(text, {
              fontFamily: fontFamily || 'Arial',
              fill: colorHex,
              fontSize: 40
            });
          }
        });
      }
    }

    saveButton.addEventListener('click', function () {
      if (!state.token || !state.file) {
        showError('Please upload an image first.');
        return;
      }

      var config = {
        token: state.token,
        file: state.file,
        option_id: getSelectedOption('puzzle-dimension'),
        box_color_id: getSelectedOption('puzzle-box-color'),
        text_color_id: getSelectedOption('puzzle-text-color'),
        text_content: getTextValue('puzzle-text-input'),
        font_id: getSelectedOption('puzzle-font'),
        id_product: null,
        csrf_token: window.puzzleCustomizer ? window.puzzleCustomizer.csrfToken : null
      };

      if (window.puzzleEditor && typeof window.puzzleEditor.exportImage === 'function') {
        config.edited_image = window.puzzleEditor.exportImage('image/jpeg', 0.95);
      }

      saveConfiguration(config);
    });

    function saveConfiguration(config) {
      showStatus('Saving...', 'info');

      fetch(window.puzzleCustomizer.saveUrl, {
        method: 'POST',
        body: JSON.stringify(config),
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'same-origin'
      })
        .then(function (response) {
          if (!response.ok) {
            throw new Error('Save failed');
          }
          return response.json();
        })
        .then(function (json) {
          if (json.success) {
            showStatus('Configuration saved successfully! ID: ' + json.id, 'success');
            if (json.added_to_cart && json.cart_url) {
              window.location.href = json.cart_url;
            }
          } else {
            showError(json.message || 'Save failed');
          }
        })
        .catch(function (error) {
          console.error('Save error:', error);
          showError('Network error during save. Please try again.');
        });
    }

    function showStatus(message, type) {
      if (!uploadStatus) {
        return;
      }
      uploadStatus.style.display = 'block';
      uploadStatus.className = 'alert alert-' + type;
      uploadStatus.textContent = message;
    }

    function showError(message) {
      showStatus(message, 'danger');
    }

    function showWarnings(warnings) {
      var warningDiv = document.getElementById('puzzle-warnings');
      if (!warningDiv) {
        warningDiv = document.createElement('div');
        warningDiv.id = 'puzzle-warnings';
        warningDiv.className = 'alert alert-warning';
        uploadStatus.parentNode.insertBefore(warningDiv, uploadStatus.nextSibling);
      }

      warningDiv.innerHTML = '<strong>Warnings:</strong><ul>' +
        warnings.map(function (w) { return '<li>' + w + '</li>'; }).join('') +
        '</ul>';
      warningDiv.style.display = 'block';
    }

    function getSelectedOption(selectId) {
      var select = document.getElementById(selectId);
      if (!select) {
        return null;
      }
      var value = select.value;
      return value ? parseInt(value, 10) : null;
    }

    function getTextValue(inputId) {
      var input = document.getElementById(inputId);
      return input ? input.value : '';
    }
  }
})();
