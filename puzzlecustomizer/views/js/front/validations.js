(function () {
  'use strict';

  window.puzzleValidation = {
    isImage: function (fileName) {
      return /\.(jpe?g|png|gif|webp|tiff?)$/i.test(fileName || '');
    },

    validateDimensions: function (width, height, minWidth, minHeight) {
      minWidth = minWidth || 1000;
      minHeight = minHeight || 1000;

      return {
        valid: width >= minWidth && height >= minHeight,
        message: width >= minWidth && height >= minHeight
          ? ''
          : 'Image should be at least ' + minWidth + 'x' + minHeight + ' pixels.'
      };
    },

    validateTextLength: function (text, maxLength) {
      maxLength = maxLength || 500;
      if (!text) {
        return { valid: true, message: '' };
      }

      return {
        valid: text.length <= maxLength,
        message: text.length <= maxLength ? '' : 'Text is too long (' + text.length + '/' + maxLength + ').'
      };
    },

    validateFileSize: function (sizeBytes, maxSizeMB) {
      maxSizeMB = maxSizeMB || 50;
      var maxBytes = maxSizeMB * 1024 * 1024;
      return {
        valid: sizeBytes <= maxBytes,
        message: sizeBytes <= maxBytes ? '' : 'File too large. Maximum size ' + maxSizeMB + 'MB.'
      };
    },

    validateMimeType: function (mime, allowed) {
      allowed = allowed || ['image/jpeg', 'image/png', 'image/webp', 'image/tiff'];
      return {
        valid: allowed.indexOf(mime) !== -1,
        message: allowed.indexOf(mime) !== -1 ? '' : 'Unsupported file format.'
      };
    }
  };
})();
