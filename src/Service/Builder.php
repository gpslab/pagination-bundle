<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace AnimeDb\Bundle\PaginationBundle\Service;

use Doctrine\ORM\QueryBuilder;

/**
 * @package AnimeDb\Bundle\PaginationBundle\Service
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Builder
{
    /**
     * The number of pages displayed in the navigation
     *
     * @var int
     */
    protected $max_navigate = Configuration::DEFAULT_LIST_LENGTH;

    /**
     * @param int $max_navigate
     */
    public function __construct($max_navigate)
    {
        $this->max_navigate = $max_navigate;
    }

    /**
     * @param int $total_pages
     * @param int $current_page
     *
     * @return Configuration
     */
    public function paginate($total_pages = 0, $current_page = 1)
    {
        return (new Configuration($total_pages, $current_page))
            ->setMaxNavigate($this->max_navigate);
    }

    /**
     * @param QueryBuilder $query
     * @param int $per_page
     * @param int $current_page
     *
     * @return Configuration
     */
    public function paginateQuery(QueryBuilder $query, $per_page, $current_page = 1)
    {
        $counter = clone $query;
        $total = $counter
            ->select(sprintf('COUNT(%s)', current($query->getRootAliases())))
            ->getQuery()
            ->getSingleScalarResult();

        $query
            ->setFirstResult(($current_page - 1) * $per_page)
            ->setMaxResults($per_page);

        return (new Configuration(ceil($total / $per_page), $current_page))
            ->setMaxNavigate($this->max_navigate);
    }
}
