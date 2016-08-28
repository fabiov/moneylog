<?php
namespace Accantona\View\Helper;

// use \Zend\I18n\View\Helper\DateFormat;
use Zend\Debug\Debug;
use Zend\View\Helper\AbstractHelper;

class PageHeader extends AbstractHelper
{
    public function __invoke($title)
    {
        $str = $this->view->escapeHtml($title);
        return "<div class=\"row\"><div class=\"col-lg-12\"><h1 class=\"page-header\">$str</h1></div></div>";
    }
}