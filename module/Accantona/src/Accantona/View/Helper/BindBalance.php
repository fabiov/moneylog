<?php
namespace Accantona\View\Helper;

use Zend\View\Helper\AbstractHelper;

class BindBalance extends AbstractHelper
{

    public function __invoke($cssSelector)
    {
        if (!preg_match('/^(\.|#)[a-z0-9]+$/i', $cssSelector)) {
            throw new \Exception("Invalid css selector '$cssSelector'");
        }
        $js = <<< eojs
$('$cssSelector').click(function() {
    var button = $(this);
    bootbox.prompt("Inserisci l'importo del saldo:", function(result) {
        if (result !== null) {
            var pattern = /^[\-\+]?\d+(,\d+)?$/;
            result = result.trim();
            if (pattern.test(result)) {
                $.ajax({
                    "url": button.data('href') + '?amount=' + result,
                    "success": function() {
                        location.reload();
                    }
                });
            } else {
                bootbox.alert("Importo non valido");
            }
        }
    });
});
eojs;
        $this->view->richInlineScript()->addGeneric($js);
    }

}