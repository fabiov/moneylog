<?php
namespace Accantona\View\Helper;

// use \Zend\I18n\View\Helper\DateFormat;
use Zend\View\Helper\AbstractHelper;

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
<ul class="mfb-component--br mfb-slidein" data-mfb-toggle="click" data-mfb-state="closed">
    <li class="mfb-component__wrap">
        <a class="mfb-component__button--main">
            <i class="mfb-component__main-icon--resting glyphicon glyphicon-plus"></i>
            <i class="mfb-component__main-icon--active glyphicon glyphicon-remove"></i>
        </a>
        <ul class="mfb-component__list">
            $items;
        </ul>
    </li>
</ul>
<script src="/node_modules/mfb/src/mfb.min.js"></script>
eoc;
        }
        return $html;
    }

}