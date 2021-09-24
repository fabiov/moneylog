<?php

namespace Application\ViewHelper;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Laminas\View\Helper\AbstractHelper;

class Pagination extends AbstractHelper
{
    private const RANGE = 4;

    /**
     * @link https://getbootstrap.com/docs/3.3/components/#pagination
     * @param Paginator<mixed> $paginator
     * @param int $current
     * @param string $route
     * @param array<string, mixed> $queryParams
     * @return string
     * @throws \Exception
     */
    public function __invoke(Paginator $paginator, int $current, string $route, array $queryParams = []): string
    {
        $items = '';
        $totalItems = $paginator->count();
        $pageSize = $paginator->getIterator()->count();
        $totalPages = (int) ceil($totalItems / $pageSize);
        $queryParams['limit'] = $pageSize;

//        if ($current > 1) {
//            $queryParams['page'] = $current - 1;
//            $url = $this->view->url($route, ['action' => 'index'], ['query' => $queryParams]);
//            $items .= "<li><a href=\"$url\" aria-label=\"\"><span aria-hidden=\"true\">&laquo;</span></a></li>";
//        }

        [$min, $max] = self::getLimits($current, $totalPages);

        for ($i = $min; $i <= $max; $i++) {
            if ($i === $current) {
                $items .= "<li class=\"active\"><span>$i</span></li>";
            } else {
                $queryParams['page'] = $i;
                $url = $this->view->url($route, ['action' => 'index'], ['query' => $queryParams]);
                $items .= "<li><a href=\"$url\">$i</a></li>";
            }
        }

//        if ($current < $totalPages) {
//            $queryParams['page'] = $current + 1;
//            $url = $this->view->url($route, ['action' => 'index'], ['query' => $queryParams]);
//            $items .= "<li><a href=\"$url\" aria-label=\"\"><span aria-hidden=\"true\">&raquo;</span></a></li>";
//        }

        return "<nav aria-label=\"Page navigation\"><ul class=\"pagination\">$items</ul></nav>";
    }

    /**
     * @param int $current
     * @param int $totalPages
     * @return array<int>
     */
    private static function getLimits(int $current, int $totalPages): array
    {
        $min = $current;
        $max = $current;

        while ($max - $min < 2 * self::RANGE && ($min > 1 || $max < $totalPages)) {
            if ($min > 1) {
                $min--;
            }
            if ($max < $totalPages) {
                $max++;
            }
        }

        return [$min, $max];
    }
}
