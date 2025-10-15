(function () {
  'use strict';

  if (typeof fabric === 'undefined') {
    console.error('Fabric.js not loaded!');
    return;
  }

  var canvasElement = document.getElementById('puzzle-canvas');
  if (!canvasElement) {
    console.error('Canvas element not found!');
    return;
  }

  var canvas = new fabric.Canvas('puzzle-canvas', {
    backgroundColor: '#f0f0f0',
    selection: true
  });

  var editorState = {
    originalImage: null,
    cropRect: null,
    currentZoom: 1,
    currentRotation: 0
  };

  window.puzzleEditor = {

    setImage: function (url) {
      fabric.Image.fromURL(url, function (img) {
        canvas.clear();
        canvas.backgroundColor = '#f0f0f0';

        var scale = Math.min(
          canvas.width / img.width,
          canvas.height / img.height
        ) * 0.9;

        img.set({
          left: canvas.width / 2,
          top: canvas.height / 2,
          originX: 'center',
          originY: 'center',
          scaleX: scale,
          scaleY: scale,
          selectable: true,
          hasControls: true
        });

        canvas.add(img);
        canvas.setActiveObject(img);
        canvas.renderAll();

        editorState.originalImage = img;

      }, { crossOrigin: 'anonymous' });
    },

    enableCrop: function () {
      if (!editorState.originalImage) {
        alert('Please load an image first');
        return;
      }

      editorState.originalImage.set({ selectable: false });

      var cropRect = new fabric.Rect({
        left: 100,
        top: 100,
        width: canvas.width - 200,
        height: canvas.height - 200,
        fill: 'rgba(255, 255, 255, 0.3)',
        stroke: '#00ff00',
        strokeWidth: 2,
        strokeDashArray: [5, 5],
        selectable: true,
        hasControls: true
      });

      canvas.add(cropRect);
      canvas.setActiveObject(cropRect);
      canvas.renderAll();

      editorState.cropRect = cropRect;
    },

    applyCrop: function () {
      if (!editorState.cropRect || !editorState.originalImage) {
        alert('No crop area defined');
        return;
      }

      var cropRect = editorState.cropRect;
      var img = editorState.originalImage;

      var left = cropRect.left - img.left;
      var top = cropRect.top - img.top;
      var width = cropRect.width * cropRect.scaleX;
      var height = cropRect.height * cropRect.scaleY;

      var cropped = new fabric.Image(img.getElement(), {
        left: canvas.width / 2,
        top: canvas.height / 2,
        originX: 'center',
        originY: 'center',
        cropX: left / img.scaleX,
        cropY: top / img.scaleY,
        width: img.width,
        height: img.height,
        scaleX: img.scaleX,
        scaleY: img.scaleY
      });

      canvas.clear();
      canvas.add(cropped);
      canvas.renderAll();

      editorState.originalImage = cropped;
      editorState.cropRect = null;
    },

    cancelCrop: function () {
      if (editorState.cropRect) {
        canvas.remove(editorState.cropRect);
        editorState.cropRect = null;
      }
      if (editorState.originalImage) {
        editorState.originalImage.set({ selectable: true });
      }
      canvas.renderAll();
    },

    setZoom: function (zoomLevel) {
      if (!editorState.originalImage) return;

      var img = editorState.originalImage;
      var baseScale = Math.min(
        canvas.width / img.width,
        canvas.height / img.height
      ) * 0.9;

      var newScale = baseScale * zoomLevel;

      img.set({
        scaleX: newScale,
        scaleY: newScale
      });

      canvas.renderAll();
      editorState.currentZoom = zoomLevel;
    },

    rotate: function (degrees) {
      if (!editorState.originalImage) return;

      var img = editorState.originalImage;
      var newAngle = (img.angle + degrees) % 360;

      img.rotate(newAngle);
      canvas.renderAll();

      editorState.currentRotation = newAngle;
    },

    flipHorizontal: function () {
      if (!editorState.originalImage) return;

      var img = editorState.originalImage;
      img.set('flipX', !img.flipX);
      canvas.renderAll();
    },

    flipVertical: function () {
      if (!editorState.originalImage) return;

      var img = editorState.originalImage;
      img.set('flipY', !img.flipY);
      canvas.renderAll();
    },

    applyFilter: function (filterType) {
      if (!editorState.originalImage) return;

      var img = editorState.originalImage;
      img.filters = [];

      switch (filterType) {
        case 'grayscale':
          img.filters.push(new fabric.Image.filters.Grayscale());
          break;
        case 'sepia':
          img.filters.push(new fabric.Image.filters.Sepia());
          break;
        case 'invert':
          img.filters.push(new fabric.Image.filters.Invert());
          break;
        case 'brightness':
          img.filters.push(new fabric.Image.filters.Brightness({ brightness: 0.2 }));
          break;
        case 'contrast':
          img.filters.push(new fabric.Image.filters.Contrast({ contrast: 0.3 }));
          break;
        case 'none':
          break;
      }

      img.applyFilters();
      canvas.renderAll();
    },

    addText: function (text, options) {
      options = options || {};

      var textObj = new fabric.Text(text || 'Your Text', {
        left: canvas.width / 2,
        top: canvas.height / 2,
        originX: 'center',
        originY: 'center',
        fontFamily: options.fontFamily || 'Arial',
        fontSize: options.fontSize || 40,
        fill: options.fill || '#000000',
        stroke: options.stroke || '',
        strokeWidth: options.strokeWidth || 0
      });

      canvas.add(textObj);
      canvas.setActiveObject(textObj);
      canvas.renderAll();

      return textObj;
    },

    removeSelected: function () {
      var activeObject = canvas.getActiveObject();
      if (activeObject) {
        canvas.remove(activeObject);
        canvas.renderAll();
      }
    },

    exportImage: function (format, quality) {
      format = format || 'image/jpeg';
      quality = quality || 0.95;

      return canvas.toDataURL({
        format: format,
        quality: quality
      });
    },

    getCanvas: function () {
      return canvas;
    },

    reset: function () {
      if (editorState.originalImage) {
        canvas.clear();
        this.setImage(editorState.originalImage.getSrc());
      }
    }
  };

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Delete' || e.key === 'Backspace') {
      var activeObject = canvas.getActiveObject();
      if (activeObject && activeObject !== editorState.originalImage) {
        canvas.remove(activeObject);
        canvas.renderAll();
      }
      e.preventDefault();
    }
  });
})();
