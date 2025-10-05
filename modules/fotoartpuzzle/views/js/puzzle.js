(function () {
    const wizard = document.getElementById('fap-wizard');
    if (!wizard) {
        return;
    }

    const button = wizard.querySelector('.fap-launch');
    if (!button) {
        return;
    }

    button.addEventListener('click', function () {
        alert('Puzzle customization wizard will open here.');
    });
})();
