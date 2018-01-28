<?php
namespace Accantona\View\Helper;

use Zend\Debug\Debug;
use Zend\View\Helper\AbstractHelper;

/**
 * Class SynopsisFilters
 * @package Accantona\View\Helper
 */
class SynopsisFilters extends AbstractHelper
{
    /**
     * @param array $filters
     * @return string
     */
    public function __invoke(array $filters, array $categories)
    {
        $text = '';
        if ($filters['dateMin']) {
            $text .= 'dal <strong>' . date('d/m/Y', strtotime($filters['dateMin'])) . '</strong>';
        }
        if ($filters['dateMax']) {
            $text .= ' al <strong>' . date('d/m/Y', strtotime($filters['dateMax'])) . '</strong>';
        }
        $text .= ', ';

        if ($filters['amountMin']) {
            $text .= 'da <strong>' . $filters['amountMin'] . '€</strong>';
        }
        if ($filters['amountMax']) {
            $text .= ' a <strong>' . $filters['amountMax'] . '€</strong>';
        }
        $text .= ', ';

        if ($filters['description']) {
            $text .= ' descrizione <strong>' . $filters['description'] . '</strong>';
        }
        $text .= ', ';

        if ($filters['category']) {

            $category = '';
            foreach ($categories as $c) {
                if ($c->id == $filters['category']) {
                    $category = $c->descrizione;
                }
            }

            $text .= ' categoria <strong>' . $category . '</strong>';
        }

        return trim($text, ', ') ? : '<strong>Motra tutto</strong>';
    }
}