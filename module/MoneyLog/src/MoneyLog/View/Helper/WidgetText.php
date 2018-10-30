<?php
namespace MoneyLog\View\Helper;

use Zend\View\Helper\AbstractHelper;

class WidgetText extends AbstractHelper
{
    public function __invoke($data)
    {
        $classes = '';
        if (!empty($data['col-lg'])) {
            $classes .= ' col-lg-' . $data['col-lg'];
        }
        if (!empty($data['col-md'])) {
            $classes .= ' col-md-' . $data['col-md'];
        }
        $classes = trim($classes);

        $color = empty($data['color']) ? 'primary' : $data['color'];

        return <<< eoc
<div class="$classes">
    <div class="panel panel-$color">
        <div class="panel-heading">
            <div class="row">
                <div class="col-xs-3">
                    <i class="fa fa-{$data['fa']} fa-3x"></i>
                </div>
                <div class="col-xs-9 text-right">
                    <div class="font-big">{$data['text']}</div>
                    <div>{$data['label']}</div>
                </div>
            </div>
        </div>
    </div>
</div>
eoc;
    }
}