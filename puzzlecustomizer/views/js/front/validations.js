(function () {
  'use strict';
  window.puzzleValidation = {
    isImage: function (fileName) {
      return /\.(jpe?g|png|gif|webp|tiff?)$/i.test(fileName || '');
    }
  };
})();
