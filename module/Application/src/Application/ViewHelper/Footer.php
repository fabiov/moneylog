<?php

namespace Application\ViewHelper;

use Laminas\View\Helper\AbstractHelper;

/**
 * @property \Laminas\View\Renderer\RendererInterface $view
 */
class Footer extends AbstractHelper
{
    public function __invoke(): string
    {
        $year = date('Y');
        $href = $this->view->url('page', ['action' => 'privacy-policy']);
        return <<< EOC
            <footer>
                <p class="text-center">&copy; 2015 - $year Fabio Ventura - <a href="$href">Privacy&nbsp;policy</a></p>
            </footer>
            EOC;
    }
}
