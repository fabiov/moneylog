<?php
namespace Accantona\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Class Morris
 * @package Accantona\View\Helper
 */
class Morris extends AbstractHelper
{

    public function __invoke()
    {
        return $this;
    }

    public function donut($element, array $data, $labelKey = 'label', $valueKey = 'value')
    {
        if ($data) {
            $d = array();
            foreach ($data as $i) {
                $d[] = array('label' => $i[$labelKey], 'value' => $i[$valueKey]);
            }
            $jsonData = json_encode($d);
            $this->view->inlineScript()->captureStart();
            echo <<< eojs
Morris.Donut({
    "element": '$element',
    "data": $jsonData,
    "formatter": function (y, data) {
        var parts = parseFloat(Math.round(y * 100) / 100).toFixed(2).toString().split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return '€ ' + parts.join(',');
    },
    "colors": ['#0B62A4', '#337ab7', '#3980B5', '#679DC6', '#95BBD7', '#B0CCE1']
});
eojs;
            $this->view->inlineScript()->captureEnd();
        }
    }

}