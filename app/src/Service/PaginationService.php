<?php

declare(strict_types=1);

namespace App\Service;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;

readonly class PaginationService
{
    /**
     * @param PaginationInterface<int, mixed> $pagination
     */
    public static function getPages(PaginationInterface $pagination): int
    {
        if ($pagination instanceof SlidingPagination) {
            $pages = $pagination->getPageCount();
        } else {
            $pages = (int) ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage());
        }

        return $pages;
    }
}
