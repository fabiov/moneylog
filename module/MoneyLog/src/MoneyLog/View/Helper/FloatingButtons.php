<?php
namespace MoneyLog\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * Class FloatingButtons
 * @package Accantona\View\Helper
 * @link https://github.com/nobitagit/material-floating-button
 */
class FloatingButtons extends AbstractHelper
{

    /**
     * @var array
     */
    private $items = array();

    /**
     * @return $this
     */
    public function __invoke()
    {
        return $this;
    }

    /**
     * @param $item
     * @return $this
     */
    public function addRawItem($item)
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * @param $params
     * @return $this
     */
    public function addAnchorItem(array $params)
    {
        $html = '<a href="' . $params['href'] . '" data-mfb-label="' . $params['label'] . '" class="mfb-component__button--child">'
              . '<i class="mfb-component__child-icon glyphicon glyphicon-' . $params['icon'] . '"></i>'
              . '</a>';
        return $this->addRawItem($html);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $html = '';
        if ($this->items) {
            $items = '';
            foreach ($this->items as $item) {
                $items .= "<li>$item</li>";
            }
            $html .= <<< eoc
<link href="/node_modules/mfb/src/mfb.css" rel="stylesheet">
<ul class="mfb-component--br mfb-slidein" data-mfb-toggle="hover" data-mfb-state="closed">
    <li class="mfb-component__wrap">
        <a class="mfb-component__button--main">
            <i class="mfb-component__main-icon--resting glyphicon glyphicon-option-vertical"></i>
            <i class="mfb-component__main-icon--active glyphicon glyphicon-remove"></i>
        </a>
        <ul class="mfb-component__list">$items</ul>
    </li>
</ul>
<script src="/node_modules/mfb/src/mfb.min.js"></script>
eoc;
        }
        return $html;
    }
}