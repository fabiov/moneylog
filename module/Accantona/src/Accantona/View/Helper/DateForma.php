<?php
namespace Accantona\View\Helper;

// use \Zend\I18n\View\Helper\DateFormat;
use Zend\View\Helper\AbstractHelper;

class DateForma extends AbstractHelper
{
    public function __invoke($date)
    {
        return preg_replace('/([0-9]{4})-([0-9]{2})-([0-9]{2})/', '$3/$2/$1', $date);
    }
}