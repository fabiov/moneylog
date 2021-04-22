<?php
namespace MoneyLog\View\Helper;

// use \Laminas\I18n\View\Helper\DateFormat;
use Laminas\View\Helper\AbstractHelper;

class CurrencyForma extends AbstractHelper
{
    public function __invoke($amount)
    {
        return $this->view->currencyFormat($amount, 'EUR', true, 'it_IT');
    }
}