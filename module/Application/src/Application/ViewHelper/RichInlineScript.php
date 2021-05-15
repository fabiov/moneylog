<?php

namespace Application\ViewHelper;

use Laminas\View\Helper\AbstractHelper;

class RichInlineScript extends AbstractHelper
{
    /**
     * @var string
     */
    private $documentReady = '';

    /**
     * @var string
     */
    private $generic = '';

    public function addOnDocumentReady(string $js): void
    {
        $this->documentReady .= $js . PHP_EOL;
    }

    public function addGeneric(string $js): void
    {
        $this->generic .= $js . PHP_EOL;
    }

    /**
     * @return $this
     */
    public function __invoke()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $js = '';
        $inlineScript = $this->view->inlineScript();

        if ($this->documentReady) {
            $js = <<< js
$(document).ready(function(){
    $this->documentReady
});

js;
        }

        if ($this->generic) {
            $js .= $this->generic;
        }

        if ($js) {
            $inlineScript->appendScript($js);
        }
        return (string) $inlineScript;
    }
}
