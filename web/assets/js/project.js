function toggleLocales(event) {
    var localeList = document.getElementsByClassName(event.target.dataset.className);
    var toggleButton = event.target;
    var hideLocales;

    if (localeList.length > 0) {
        if (toggleButton.classList.contains('active')) {
            // I need to restore elements' visibility
            toggleButton.classList.remove('active');
            toggleButton.innerHTML = toggleButton.innerHTML.replace('Show', 'Hide');
            hideLocales = false;
        } else {
            // I need to hide locales
            toggleButton.classList.add('active');
            toggleButton.innerHTML = toggleButton.innerHTML.replace('Hide', 'Show');
            hideLocales = true;
        }

        for (var i = 0; i < localeList.length; i ++) {
            if (hideLocales === true) {
                localeList[i].classList.add('hidden_locale');
            } else {
                localeList[i].classList.remove('hidden_locale');
            }
        }

        // Display message with the number of hidden locales
        var hiddenLocalesMessage = document.getElementById('hidden_message');
        var hiddenElements = document.getElementsByClassName('hidden_locale');

        if (hiddenElements.length === 0) {
            hiddenLocalesMessage.classList.add('hidden');
        } else {
            if (hiddenElements.length === 1) {
                hiddenLocalesMessage.innerHTML = '1 locale hidden';
            } else {
                hiddenLocalesMessage.innerHTML = hiddenElements.length +
                                                 ' locales hidden';
            }
            hiddenLocalesMessage.classList.remove('hidden');
        }
    }
}

window.onload = function() {
    // Find all toggle buttons
    var buttons = document.getElementsByClassName('filter_buttons');

    // Associate listeners to buttons if there are locales to filter
    for (var i = 0, n = buttons.length; i < n; i++) {
        var localeList = document.getElementsByClassName(buttons[i].dataset.className);
        if (localeList.length > 0) {
            buttons[i].addEventListener('click', toggleLocales);
        } else {
            buttons[i].setAttribute('disabled', 'true');
        }
    }
};
