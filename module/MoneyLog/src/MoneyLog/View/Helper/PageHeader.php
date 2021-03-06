<?php

namespace MoneyLog\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class PageHeader extends AbstractHelper
{
    public function __invoke(string $title): string
    {
        $str = $this->view->escapeHtml($title);
        $this->view->headTitle($str);
        return "<div class=\"row\"><div class=\"col-lg-12\"><h1 class=\"page-header\">$str</h1></div></div>";
    }
}
