<?php

namespace Application\ViewHelper;

use Laminas\View\Helper\AbstractHelper;

class CommonJavascript extends AbstractHelper
{
    /**
     * @return string
     */
    public function __invoke()
    {
        return <<< EOC
<script type="text/javascript">
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/service-worker.js').then(function(registration) {
            // Registration was successful
            console.log('ServiceWorker registration successful with scope: ', registration.scope);
        }, function(err) {
            // registration failed :(
            console.log('ServiceWorker registration failed: ', err);
        });
    });
}

// google analitics code
$(document).ready(function(){
    $.cookieBar({
        "acceptText": 'Conferma',
        "bottom": true,
        "fixed": true,
        "message": "Questo sito usa i cookies, continuando la navigazione assumiamo che tu ne accetti l'utilizzo.",
        "policyButton": true,
        "policyText": 'Privacy Policy',
        "policyURL": '/privacy-policy',
        "zindex": 1
    });
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
    ga('create', 'UA-79448446-1', 'auto');
    ga('send', 'pageview');
});
</script>
EOC;
    }
}
