<?php

namespace MoneyLog\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class DateForma extends AbstractHelper
{
    /**
     * @param \DateTime|string $date
     * @return string
     */
    public function __invoke($date): string
    {
        if ($date instanceof \DateTime) {
            return $date->format('d/m/Y');
        }
        return preg_replace('/([0-9]{4})-([0-9]{2})-([0-9]{2})/', '$3/$2/$1', $date);
    }
}
