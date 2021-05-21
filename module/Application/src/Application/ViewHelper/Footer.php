<?php

namespace Application\ViewHelper;

use Laminas\View\Helper\AbstractHelper;

/**
 * @property \Laminas\View\Renderer\RendererInterface $view
 */
class Footer extends AbstractHelper
{
    /**
     * @return string
     */
    public function __invoke()
    {
        $year = date('Y');
        $href = $this->view->url('page', ['action' => 'privacy-policy']);
        return <<< EOC
<footer>
    <p class="text-center">
        &copy; 2015 - $year by Fabio Ventura. Tutti i diritti riservati. - <a href="$href">Privacy policy</a>
    </p>
</footer>
EOC;
    }
}
