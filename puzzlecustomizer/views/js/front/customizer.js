(function () {
  'use strict';

  var fileInput = document.getElementById('puzzle-file');
  var saveButton = document.getElementById('puzzle-save');
  var uploadStatus = document.getElementById('puzzle-upload-status');

  if (!fileInput || !saveButton) {
    return;
  }

  var state = {
    token: null,
    file: null,
    configuration: {}
  };

  fileInput.addEventListener('change', function (event) {
    var file = event.target.files[0];
    if (!file) {
      return;
    }

    uploadStatus.style.display = 'block';
    uploadStatus.className = 'alert alert-info';
    uploadStatus.innerText = 'Caricamento in corso...';

    var formData = new FormData();
    formData.append('file', file);
    formData.append('ajax', 1);

    fetch(puzzleCustomizer.uploadUrl, {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    })
      .then(function (response) { return response.json(); })
      .then(function (json) {
        if (json.success) {
          uploadStatus.className = 'alert alert-success';
          uploadStatus.innerText = 'Immagine caricata correttamente';
          state.token = json.token;
          state.file = json.file;
        } else {
          uploadStatus.className = 'alert alert-danger';
          uploadStatus.innerText = json.message || 'Errore di caricamento';
        }
      })
      .catch(function () {
        uploadStatus.className = 'alert alert-danger';
        uploadStatus.innerText = 'Errore di rete durante il caricamento';
      });
  });

  saveButton.addEventListener('click', function () {
    if (!state.token || !state.file) {
      alert('Carica prima una immagine.');
      return;
    }

    fetch(puzzleCustomizer.saveUrl, {
      method: 'POST',
      body: JSON.stringify({
        token: state.token,
        file: state.file,
        configuration: state.configuration
      }),
      headers: {
        'Content-Type': 'application/json'
      },
      credentials: 'same-origin'
    })
      .then(function (response) { return response.json(); })
      .then(function (json) {
        if (json.success) {
          alert('Configurazione salvata (ID ' + json.id + ')');
        } else {
          alert(json.message || 'Errore nel salvataggio');
        }
      })
      .catch(function () {
        alert('Errore di rete nel salvataggio.');
      });
  });
})();
