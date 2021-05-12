<?php

namespace MoneyLog\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class CurrencyForma extends AbstractHelper
{
    public function __invoke($amount): string
    {
        return $this->view->currencyFormat($amount, 'EUR', true, 'it_IT');
    }
}
