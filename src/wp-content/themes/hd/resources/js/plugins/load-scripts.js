(function () {
    const timeout = 4000;
    const loadScriptsTimer = setTimeout(loadScripts, timeout);
    const userInteractionEvents = ['mouseover', 'keydown', 'touchstart', 'touchmove', 'wheel'];

    userInteractionEvents.forEach((event) => {
        window.addEventListener(event, triggerScriptLoader, { once: true, passive: true });
    });

    function triggerScriptLoader() {
        loadScripts();
        clearTimeout(loadScriptsTimer);
    }
    function loadScripts() {
        document.querySelectorAll("script[data-type='lazy']").forEach((elem) => {
            elem.setAttribute('src', elem.getAttribute('data-src'));
            elem.removeAttribute('data-src');
            elem.removeAttribute('data-type');
        });
    }
})();
