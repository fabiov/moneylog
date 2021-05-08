<?php

namespace MoneyLog\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Laminas\Escaper\Escaper;

class WidgetSelect extends AbstractHelper
{
    public function __invoke($data)
    {
        $escaper = new Escaper('utf-8');

        $classes = '';
        if (!empty($data['col-lg'])) {
            $classes .= ' col-lg-' . $data['col-lg'];
        }
        if (!empty($data['col-md'])) {
            $classes .= ' col-md-' . $data['col-md'];
        }
        $classes = trim($classes);

        $color = empty($data['color']) ? 'primary' : $data['color'];

        $options = '';
        foreach ($data['options'] as $k => $v) {
            $attributes = 'value="' . $escaper->escapeHtmlAttr($k) . '"';
            if ($k == $data['selected']) {
                $attributes .= ' selected="selected"';
            }
            $options .= "<option $attributes>" . $escaper->escapeHtml($v) . '</option>';
        }

        $attributes = '';
        if (!empty($data['id'])) {
            $attributes .= 'id="' . $escaper->escapeHtmlAttr($data['id']) . '"';
        }
        if (!empty($data['name'])) {
            $attributes .= ' name="' . $escaper->escapeHtmlAttr($data['name']) . '"';
        }
        $attributes = trim($attributes);

        return <<< eoc
<div class="$classes">
    <div class="panel panel-$color">
        <div class="panel-heading">
            <div class="row">
                <div class="col-xs-3"><i class="fa fa-{$data['fa']} fa-3x"></i></div>
                <div class="col-xs-9 text-right">
                    <div class="font-big">
                        <select class="form-control" $attributes>$options</select>
                    </div>
                    <div>{$data['label']}</div>
                </div>
            </div>
        </div>
    </div>
</div>
eoc;
    }
}
