<?php

namespace MoneyLog\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class DateForma extends AbstractHelper
{
    public function __invoke(\DateTime $date): string
    {
        return $date->format('d/m/Y');
    }
}
