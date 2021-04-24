<?php
namespace MoneyLog\View\Helper;

use Laminas\View\Helper\AbstractHelper;

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
        $piaces = [];

        if ($filters['dateMin'] || $filters['dateMax']) {
            $piaces['date'] = '';
            if ($filters['dateMin']) {
                $piaces['date'] .= 'dal <strong>' . date('d/m/Y', strtotime($filters['dateMin'])) . '</strong>';
            }
            if ($filters['dateMax']) {
                $piaces['date'] .= ' al <strong>' . date('d/m/Y', strtotime($filters['dateMax'])) . '</strong>';
            }
        }

        if ($filters['amountMin'] || $filters['amountMax']) {
            $piaces['amount'] = '';
            if ($filters['amountMin']) {
                $piaces['amount'] .= 'da <strong>' . $filters['amountMin'] . '€</strong>';
            }
            if ($filters['amountMax']) {
                $piaces['amount'] .= ' a <strong>' . $filters['amountMax'] . '€</strong>';
            }
        }

        if ($filters['description']) {
            $piaces['description'] = 'descrizione <strong>' . $filters['description'] . '</strong>';
        }

        if ($filters['category']) {

            $category = '';
            foreach ($categories as $c) {
                if ($c->id == $filters['category']) {
                    $category = $c->descrizione;
                }
            }

            $piaces['category'] = 'categoria <strong>' . $category . '</strong>';
        }

        return $piaces ? implode(', ', $piaces) : '<strong>Motra tutto</strong>';
    }
}
