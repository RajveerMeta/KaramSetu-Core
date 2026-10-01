(function() {
    const scripts = [
        'validation.js',
        'auth.js',
        'navigation.js',
        'causes.js',
        'ngo.js',
        'events.js',
        'user-events.js',
        'contributions.js',
        'route-protection.js'
    ];

    scripts.forEach(function(script) {
        const scriptEl = document.createElement('script');
        scriptEl.src = jsBaseUrl('public/js/' + script);
        scriptEl.async = false;
        document.head.appendChild(scriptEl);
    });
})();
