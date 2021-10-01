<?php

namespace Application\ViewHelper;

use Laminas\View\Helper\AbstractHelper;

/**
 * @property \Laminas\View\Renderer\PhpRenderer $view
 */
class SortLink extends AbstractHelper
{
    /**
     * @param string $text
     * @param string $field
     * @param string $routeName
     * @param array<string, string> $queryData
     * @return string
     */
    public function __invoke(string $text, string $field, string $routeName, array $queryData): string
    {
        $content = $this->view->escapeHtml($text);
        if ($queryData['orderField'] === $field) {
            if (strcasecmp($queryData['orderType'], 'ASC') === 0) {
                $icon = 'top';
                $orderType = 'DESC';
            } else {
                $icon = 'bottom';
                $orderType = 'ASC';
            }

            $content .= " <span class=\"glyphicon glyphicon-triangle-$icon\"></span>";
        } else {
            $orderType = 'ASC';
        }

        $newQueryData = array_merge($queryData, ['orderField' => $field, 'orderType' => $orderType]);
        $url = $this->view->url($routeName, [], ['query' => $newQueryData]);
        return "<a href=\"$url\">$content</a>";
    }
}
