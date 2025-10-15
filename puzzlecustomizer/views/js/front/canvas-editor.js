(function () {
  'use strict';

  if (typeof fabric === 'undefined') {
    return;
  }

  var canvasElement = document.getElementById('puzzle-canvas');
  if (!canvasElement) {
    return;
  }

  var canvas = new fabric.Canvas('puzzle-canvas');

  window.puzzleEditor = {
    setImage: function (url) {
      fabric.Image.fromURL(url, function (img) {
        canvas.clear();
        img.set({ left: 0, top: 0, selectable: true });
        canvas.add(img);
        canvas.setActiveObject(img);
        canvas.renderAll();
      }, { crossOrigin: 'anonymous' });
    }
  };
})();
