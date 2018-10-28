<?php
namespace MoneyLog\View\Helper;

// use \Zend\I18n\View\Helper\DateFormat;
use Zend\View\Helper\AbstractHelper;

class CurrencyForma extends AbstractHelper
{
    public function __invoke($amount)
    {
        return $this->view->currencyFormat($amount, 'EUR', true, 'it_IT');
    }
}