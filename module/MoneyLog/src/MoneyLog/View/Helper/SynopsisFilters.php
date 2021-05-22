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
     * @param array<string> $filters
     * @param array<\Application\Entity\Category> $categories
     * @return string
     */
    public function __invoke(array $filters, array $categories): string
    {
        $pieces = [];

        if ($filters['dateMin'] || $filters['dateMax']) {
            $pieces['date'] = '';
            if ($filters['dateMin']) {
                $fromDate = date('d/m/Y', (int) strtotime($filters['dateMin']));
                $pieces['date'] .= "dal <strong>$fromDate</strong>";
            }
            if ($filters['dateMax']) {
                $toDate = date('d/m/Y', (int) strtotime($filters['dateMax']));
                $pieces['date'] .= " al <strong>$toDate</strong>";
            }
        }

        if ($filters['amountMin'] || $filters['amountMax']) {
            $pieces['amount'] = '';
            if ($filters['amountMin']) {
                $pieces['amount'] .= 'da <strong>' . $filters['amountMin'] . '€</strong>';
            }
            if ($filters['amountMax']) {
                $pieces['amount'] .= ' a <strong>' . $filters['amountMax'] . '€</strong>';
            }
        }

        if ($filters['description']) {
            $pieces['description'] = 'descrizione <strong>' . $filters['description'] . '</strong>';
        }

        if ($filters['category']) {
            $category = '';
            foreach ($categories as $c) {
                if ($c->getId() == $filters['category']) {
                    $category = $c->getDescription();
                }
            }

            $pieces['category'] = 'categoria <strong>' . $category . '</strong>';
        }

        return $pieces ? implode(', ', $pieces) : '<strong>Motra tutto</strong>';
    }
}
