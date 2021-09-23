<?php

namespace Application\ViewHelper;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Laminas\View\Helper\AbstractHelper;

class Pagination extends AbstractHelper
{
    /**
     * @param Paginator<mixed> $paginator
     * @param int $current
     * @param string $route
     * @param array<string, mixed> $queryParams
     * @return string
     * @throws \Exception
     */
    public function __invoke(Paginator $paginator, int $current, string $route, array $queryParams = []): string
    {
        $totalItems = $paginator->count();
        $pageSize = $paginator->getIterator()->count();
        $totalPages = ceil($totalItems / $pageSize);

        $items = '';

        if ($current > 1) {
            $queryParams['page'] = $current - 1;
            $url = $this->view->url($route, ['action' => 'index'], ['query' => $queryParams]);
            $items .= "<li><a href=\"$url\" aria-label=\"\"><span aria-hidden=\"true\">&laquo;</span></a></li>";
        }

        for ($i = 1; $i <= $totalPages; $i++) {
            if ($i === $current) {
                $items .= "<li class=\"active\"><span>$i</span></li>";
            } else {
                $queryParams['page'] = $i;
                $url = $this->view->url($route, ['action' => 'index'], ['query' => $queryParams]);
                $items .= "<li><a href=\"$url\">$i</a></li>";
            }
        }

        if ($current < $totalPages) {
            $queryParams['page'] = $current + 1;
            $url = $this->view->url($route, ['action' => 'index'], ['query' => $queryParams]);
            $items .= "<li><a href=\"$url\" aria-label=\"\"><span aria-hidden=\"true\">&raquo;</span></a></li>";
        }

        return "<nav aria-label=\"Page navigation\"><ul class=\"pagination\">$items</ul></nav>";
    }
}
