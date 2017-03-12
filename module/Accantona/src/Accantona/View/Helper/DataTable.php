<?php
namespace Accantona\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Class DataTable
 * @package Accantona\View\Helper
 */
class DataTable extends AbstractHelper
{

    /**
     * @param $selector
     * @param array $options
     */
    public function __invoke($selector, array $options = [])
    {
        $json = json_encode(array_merge([
            'language'   => ['url' => '/js/data-table-Italian.json'],
            'order'      => [],
            'responsive' => true,
            'searching'  => false,
        ], $options));
        $this->view->richInlineScript()->addOnDocumentReady("$('$selector').DataTable($json);");
    }
}