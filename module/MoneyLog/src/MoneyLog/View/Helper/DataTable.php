<?php

namespace MoneyLog\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * Class DataTable
 * @package Accantona\View\Helper
 */
class DataTable extends AbstractHelper
{
    public function __invoke(string $selector, array $options = []): void
    {
        $json = json_encode(array_merge([
            'language'   => ['url' => '/js/data-table-Italian.json'],
            'order'      => [],
            'responsive' => true,
            'searching'  => false,
        ], $options));
        $this->view->richInlineScript()->addGeneric("$('$selector').DataTable($json);");
    }
}
