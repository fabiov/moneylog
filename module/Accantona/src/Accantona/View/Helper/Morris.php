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
        $data = array_filter($data, function ($item) use ($valueKey) {
            return $item[$valueKey] > 0;
        });
        if ($data) {
            $d = [];
            foreach ($data as $i) {
                $d[] = ['label' => $i[$labelKey], 'value' => $i[$valueKey]];
            }
            $colors = [
                '#6d9e00', // verde scuro
                '#7fb800', // verde
                '#91d100', // verde chiaro
                '#ebc400', // arancione chiaro
                '#eb9d00', // arancione
                '#e67300', // arancione scuro
                '#d22300', // rosso
                '#b80000', // rosso scuro
            ];
            $jsonData = json_encode($d);
            $jsonColors = json_encode(array_splice($colors, 0, count($data)));
            $js = <<< eojs
Morris.Donut({
    "element": '$element',
    "data": $jsonData,
    "formatter": function (y, data) {
        var parts = parseFloat(Math.round(y * 100) / 100).toFixed(2).toString().split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return '€ ' + parts.join(',');
    },
    "colors": $jsonColors
});
eojs;
            $this->view->richInlineScript()->addGeneric($js);
        }
    }
}